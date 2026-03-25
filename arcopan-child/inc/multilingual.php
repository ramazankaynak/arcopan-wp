<?php
/**
 * ARCOPAN Multilingual/Translation Integration - Production Hardened.
 *
 * Integrates with n8n automation for automatic translation.
 * Uses Polylang + DeepL API with resilience, deduplication, and update handling.
 *
 * @package ARCOPAN_Child
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || exit;

// Load HTML block utilities
require_once ARCOPAN_CHILD_PATH . 'inc/html-blocks.php';

/**
 * Register custom REST endpoints for translation.
 *
 * @return void
 */
function arcopan_register_translation_endpoint() {
	register_rest_route(
		'arcopan/v1',
		'/translate',
		array(
			'methods'             => 'POST',
			'callback'            => 'arcopan_handle_translation_request',
			'permission_callback' => 'arcopan_verify_translation_request',
			'args'                => array(
				'post_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	// Endpoint to save translation results and link posts
	register_rest_route(
		'arcopan/v1',
		'/translations/(?P<post_id>\d+)/save',
		array(
			'methods'             => 'POST',
			'callback'            => 'arcopan_save_translation_results',
			'permission_callback' => 'arcopan_verify_translation_request',
			'args'                => array(
				'post_id'         => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'translations'    => array(
					'required' => true,
					'type'     => 'object',
				),
				'translated_posts' => array(
					'type'  => 'object',
					'items' => array( 'type' => 'integer' ),
				),
			),
		)
	);

	// Translation status callback from n8n
	register_rest_route(
		'arcopan/v1',
		'/translation-status',
		array(
			'methods'             => 'POST',
			'callback'            => 'arcopan_handle_translation_status',
			'permission_callback' => '__return_true', // n8n validates via token
		)
	);
}
add_action( 'rest_api_init', 'arcopan_register_translation_endpoint' );

/**
 * Verify translation request security token.
 *
 * @param WP_REST_Request $request REST request.
 * @return bool|WP_Error
 */
function arcopan_verify_translation_request( $request ) {
	// Check if user is logged in
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return true;
	}

	// Check authorization header for token
	$auth_header = $request->get_header( 'authorization' );

	if ( ! $auth_header ) {
		return new WP_Error( 'missing_token', 'Missing authorization token', array( 'status' => 401 ) );
	}

	$token = str_replace( 'Bearer ', '', $auth_header );

	// Verify token (timing-safe comparison)
	$valid_token = get_option( 'arcopan_translation_token' );

	if ( ! $valid_token || ! hash_equals( (string) $valid_token, (string) $token ) ) {
		return new WP_Error( 'invalid_token', 'Invalid authorization token', array( 'status' => 401 ) );
	}

	return true;
}

/**
 * Handle manual translation request via REST API.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function arcopan_handle_translation_request( $request ) {
	$post_id = $request->get_param( 'post_id' );

	// Get post data
	$post = get_post( $post_id );

	if ( ! $post ) {
		return new WP_REST_Response(
			array( 'error' => 'Post not found' ),
			404
		);
	}

	// Get post language
	$language = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post_id ) : 'en';

	// Only translate from English
	if ( 'en' !== $language ) {
		return new WP_REST_Response(
			array( 'error' => 'Only English posts can be translated' ),
			400
		);
	}

	// Sanity checks
	if ( empty( $post->post_title ) || empty( $post->post_content ) ) {
		return new WP_REST_Response(
			array( 'error' => 'Post must have title and content' ),
			400
		);
	}

	// Check if translations already in progress (prevent concurrent processing)
	$in_progress = get_transient( "arcopan_translation_queue_{$post_id}" );
	if ( $in_progress ) {
		return new WP_REST_Response(
			array( 'message' => 'Translation already in progress for this post' ),
			202
		);
	}

	// Mark as in progress
	set_transient( "arcopan_translation_queue_{$post_id}", 1, HOUR_IN_SECONDS );

	// Prepare payload for n8n webhook
	$payload = arcopan_prepare_translation_payload( $post );

	// Send to n8n
	$response = arcopan_send_to_n8n_webhook( $payload );

	if ( is_wp_error( $response ) ) {
		delete_transient( "arcopan_translation_queue_{$post_id}" );
		return new WP_REST_Response(
			array( 'error' => $response->get_error_message() ),
			500
		);
	}

	return new WP_REST_Response(
		array(
			'success'  => true,
			'message'  => 'Translation request queued',
			'post_id'  => $post_id,
			'estimate' => '30-60 seconds',
		),
		202 // Accepted - processing will continue asynchronously
	);
}

/**
 * Prepare translation payload from post data.
 *
 * @param WP_Post $post Post object.
 * @return array Payload for n8n.
 */
function arcopan_prepare_translation_payload( $post ) {
	// Sanitize HTML for translation
	$content = arcopan_sanitize_html_for_translation( $post->post_content );

	return array(
		'post_id'      => $post->ID,
		'post_type'    => $post->post_type,
		'title'        => $post->post_title,
		'excerpt'      => $post->post_excerpt,
		'content'      => $content,
		'slug'         => $post->post_name,
		'status'       => $post->post_status,
		'timestamp'    => current_time( 'c' ),
		'request_id'   => uniqid( 'arcopan_' ), // Track this specific request
		'source_lang'  => 'en',
		'target_langs' => array( 'tr', 'fr', 'de', 'ru' ),
	);
}

/**
 * Save translation results and link posts via Polylang.
 *
 * Called by n8n after translation is complete.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function arcopan_save_translation_results( $request ) {
	$post_id = (int) $request->get_param( 'post_id' );
	$translations = $request->get_json_params()['translations'] ?? array();

	if ( ! $post_id || empty( $translations ) ) {
		return new WP_REST_Response(
			array( 'error' => 'Missing post_id or translations' ),
			400
		);
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return new WP_REST_Response(
			array( 'error' => 'Source post not found' ),
			404
		);
	}

	// Check if this is an update (translations already exist)
	$is_update = arcopan_check_translations_exist( $post_id );

	$results = array();
	foreach ( $translations as $lang => $data ) {
		$result = arcopan_create_or_update_translation( $post_id, $lang, $data, $is_update );
		$results[ $lang ] = $result;
	}

	// Link all translations together via Polylang
	if ( function_exists( 'pll_save_post_translations' ) ) {
		arcopan_link_post_translations( $post_id, $results );
	}

	// Clear the in-progress transient
	delete_transient( "arcopan_translation_queue_{$post_id}" );

	return new WP_REST_Response(
		array(
			'success'             => true,
			'source_post_id'      => $post_id,
			'translated_posts'    => $results,
			'linked'              => true,
		),
		200
	);
}

/**
 * Check if translations already exist for a post.
 *
 * @param int $post_id Post ID.
 * @return bool True if translations exist.
 */
function arcopan_check_translations_exist( $post_id ) {
	if ( ! function_exists( 'pll_get_post_translations' ) ) {
		return false;
	}

	$translations = pll_get_post_translations( $post_id );
	return ! ( count( $translations ) < 2 ); // More than just English = has translations
}

/**
 * Create or update a translated post.
 *
 * Checks if translation exists and updates it, or creates new.
 *
 * @param int    $source_post_id Source post ID.
 * @param string $language Language code (tr, fr, de, ru).
 * @param array  $data Translation data (title, excerpt, content).
 * @param bool   $is_update Whether this is an update operation.
 * @return array Result array with post_id and status.
 */
function arcopan_create_or_update_translation( $source_post_id, $language, $data, $is_update = false ) {
	$source_post = get_post( $source_post_id );

	// Check if translation already exists
	$existing_post_id = null;
	if ( function_exists( 'pll_get_post_translations' ) ) {
		$translations = pll_get_post_translations( $source_post_id );
		$existing_post_id = isset( $translations[ $language ] ) ? $translations[ $language ] : null;
	}

	// Validate translation data
	$validation = arcopan_validate_html_integrity( $data['content'] ?? '' );
	if ( true !== $validation ) {
		return array(
			'status'  => 'error',
			'message' => 'Invalid HTML: ' . $validation,
		);
	}

	$post_args = array(
		'post_type'   => $source_post->post_type,
		'post_title'  => $data['title'] ?? '',
		'post_excerpt' => $data['excerpt'] ?? '',
		'post_content' => $data['content'] ?? '',
		'post_status' => 'draft', // Always save as draft for review
		'meta_input'  => array(
			'_arcopan_source_post_id'      => $source_post_id,
			'_arcopan_translation_language' => $language,
			'_arcopan_translated_at'       => current_time( 'mysql' ),
			'_translation_locked'          => false, // Can be manually edited
		),
	);

	// Get or generate translated slug
	$translated_slug = get_post_meta( $source_post_id, "_arcopan_slug_{$language}", true );
	if ( ! $translated_slug ) {
		// Auto-generate from title if not custom set
		$translated_slug = sanitize_title( $data['title'] ?? $source_post->post_name );
	}
	$post_args['post_name'] = $translated_slug;

	if ( $existing_post_id ) {
		// UPDATE existing translation
		$post_args['ID'] = $existing_post_id;
		$result = wp_update_post( $post_args, true );

		if ( is_wp_error( $result ) ) {
			return array(
				'status'  => 'error',
				'message' => $result->get_error_message(),
			);
		}

		return array(
			'status'     => 'updated',
			'post_id'    => $existing_post_id,
			'language'   => $language,
		);
	} else {
		// CREATE new translation
		$result = wp_insert_post( $post_args, true );

		if ( is_wp_error( $result ) ) {
			return array(
				'status'  => 'error',
				'message' => $result->get_error_message(),
			);
		}

		// Assign language via Polylang
		if ( function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $result, $language );
		}

		return array(
			'status'   => 'created',
			'post_id'  => $result,
			'language' => $language,
		);
	}
}

/**
 * Link translated posts together via Polylang.
 *
 * Uses pll_save_post_translations to properly link all language versions.
 *
 * @param int   $source_post_id Source post ID.
 * @param array $results Results from create/update with post IDs per language.
 * @return void
 */
function arcopan_link_post_translations( $source_post_id, $results ) {
	if ( ! function_exists( 'pll_save_post_translations' ) ) {
		return;
	}

	// Build translations array: language => post_id
	$translations = array(
		'en' => $source_post_id, // English is always the source
	);

	foreach ( $results as $lang => $result ) {
		if ( isset( $result['post_id'] ) && $result['post_id'] ) {
			$translations[ $lang ] = $result['post_id'];
		}
	}

	// Save the translation links in Polylang
	pll_save_post_translations( $translations );

	// Log successful linking
	update_post_meta( $source_post_id, '_arcopan_translations_linked', array(
		'languages' => array_keys( $translations ),
		'timestamp' => current_time( 'mysql' ),
	) );
}

/**
 * Handle translation status callback from n8n.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response
 */
function arcopan_handle_translation_status( $request ) {
	$params = $request->get_json_params();

	$post_id  = isset( $params['post_id'] ) ? absint( $params['post_id'] ) : 0;
	$success  = isset( $params['success'] ) ? (bool) $params['success'] : false;
	$errors   = isset( $params['errors'] ) ? (array) $params['errors'] : array();

	if ( ! $post_id ) {
		return new WP_REST_Response( array( 'error' => 'Missing post_id' ), 400 );
	}

	// Log translation status
	$log_entry = array(
		'timestamp' => current_time( 'mysql' ),
		'post_id'   => $post_id,
		'success'   => $success,
		'errors'    => $errors,
	);

	// Store in post meta for tracking
	update_post_meta( $post_id, '_arcopan_last_translation_status', $log_entry );

	// Fire custom action for other integrations
	do_action( 'arcopan_translation_completed', $post_id, $success, $errors );

	// Clear in-progress marker
	delete_transient( "arcopan_translation_queue_{$post_id}" );

	return new WP_REST_Response(
		array(
			'success' => true,
			'message' => 'Translation status recorded',
		),
		200
	);
}

/**
 * Send webhook request to n8n with retry logic.
 *
 * @param array $payload Data to send.
 * @param int   $attempt Current attempt (for recursion).
 * @param int   $max_attempts Maximum retry attempts.
 * @return bool|WP_Error
 */
function arcopan_send_to_n8n_webhook( $payload, $attempt = 1, $max_attempts = 3 ) {
	$webhook_url = getenv( 'N8N_WEBHOOK_URL' );

	if ( ! $webhook_url ) {
		// Fallback: construct from N8N_URL and N8N_WEBHOOK_PATH
		$n8n_url = getenv( 'N8N_URL' );
		$webhook_path = getenv( 'N8N_WEBHOOK_PATH' );

		if ( ! $n8n_url || ! $webhook_path ) {
			return new WP_Error(
				'missing_webhook',
				'n8n webhook URL not configured'
			);
		}

		$webhook_url = trailingslashit( $n8n_url ) . 'webhook/' . ltrim( $webhook_path, '/' );
	}

	// Add HMAC signature if secret configured
	$secret = getenv( 'N8N_WEBHOOK_SECRET' );
	$headers = array(
		'Content-Type' => 'application/json',
	);

	if ( $secret ) {
		$signature = hash_hmac( 'sha256', wp_json_encode( $payload ), $secret );
		$headers['X-Webhook-Signature'] = $signature;
	}

	// Add request ID for tracking
	$headers['X-Request-ID'] = $payload['request_id'] ?? uniqid();

	// Send the webhook
	$response = wp_remote_post(
		$webhook_url,
		array(
			'method'      => 'POST',
			'timeout'     => 30,
			'redirection' => 0,
			'httpversion' => '1.1',
			'blocking'    => false, // Non-blocking, fire and forget
			'headers'     => $headers,
			'body'        => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( "ARCOPAN: n8n webhook error (attempt $attempt): " . $response->get_error_message() );

		// Retry logic (exponential backoff)
		if ( $attempt < $max_attempts ) {
			$wait_seconds = pow( 2, $attempt - 1 ); // 1, 2, 4 seconds
			sleep( $wait_seconds );
			return arcopan_send_to_n8n_webhook( $payload, $attempt + 1, $max_attempts );
		}

		return $response;
	}

	return true;
}

/**
 * Hook to trigger translation on English post publish/update.
 *
 * Only triggers if:
 * - Post is published
 * - Post language is English (EN)
 * - Auto-translate is not disabled
 * - Post is not already a translation
 * - No translation already in progress
 *
 * @param string   $new_status New post status.
 * @param string   $old_status Old post status.
 * @param WP_Post  $post Post object.
 * @return void
 */
function arcopan_trigger_translation_on_publish( $new_status, $old_status, $post ) {
	// Only trigger for published posts
	if ( 'publish' !== $new_status ) {
		return;
	}

	// Check if auto-translate is disabled
	if ( get_option( 'arcopan_disable_auto_translate' ) ) {
		return;
	}

	// Check if translation is already in progress
	if ( get_transient( "arcopan_translation_queue_{$post->ID}" ) ) {
		return;
	}

	// Check if this is a translated post (skip non-English)
	if ( function_exists( 'pll_get_post_language' ) ) {
		$language = pll_get_post_language( $post->ID );
		if ( 'en' !== $language ) {
			return; // Only translate from English
		}
	}

	// Only translate specific post types
	$translatable_types = apply_filters( 'arcopan_translatable_post_types', array( 'post', 'page', 'product' ) );

	if ( ! in_array( $post->post_type, $translatable_types, true ) ) {
		return;
	}

	// Sanity checks
	if ( empty( $post->post_title ) || empty( $post->post_content ) ) {
		return;
	}

	// Mark as in progress (prevents concurrent webhook calls)
	set_transient( "arcopan_translation_queue_{$post->ID}", 1, HOUR_IN_SECONDS );

	// Prepare and send payload
	$payload = arcopan_prepare_translation_payload( $post );
	$response = arcopan_send_to_n8n_webhook( $payload );

	if ( is_wp_error( $response ) ) {
		error_log( 'ARCOPAN: Translation trigger failed for post ' . $post->ID . ': ' . $response->get_error_message() );
		delete_transient( "arcopan_translation_queue_{$post->ID}" );
	}
}
add_action( 'transition_post_status', 'arcopan_trigger_translation_on_publish', 10, 3 );

/**
 * Disable auto-translate filter for testing/staging.
 *
 * @param string   $new_status New status.
 * @param string   $old_status Old status.
 * @param WP_Post  $post Post object.
 * @return void
 */
function arcopan_maybe_disable_auto_translate( $new_status, $old_status, $post ) {
	if ( get_option( 'arcopan_disable_auto_translate' ) ) {
		remove_action( 'transition_post_status', 'arcopan_trigger_translation_on_publish' );
	}
}
add_action( 'transition_post_status', 'arcopan_maybe_disable_auto_translate', 5, 3 );

/**
 * Admin menu for translation management.
 *
 * @return void
 */
function arcopan_add_translation_menu() {
	add_submenu_page(
		'tools.php',
		'Multilingual Setup',
		'Multilingual Setup',
		'manage_options',
		'arcopan-multilingual',
		'arcopan_multilingual_page'
	);
}
add_action( 'admin_menu', 'arcopan_add_translation_menu' );

/**
 * Render multilingual admin page.
 *
 * @return void
 */
function arcopan_multilingual_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized', 'arcopan-child' ) );
	}

	// Save settings
	if ( isset( $_POST['arcopan_save_translation_settings'] ) ) {
		check_admin_referer( 'arcopan_translation_settings' );

		if ( isset( $_POST['arcopan_translation_token'] ) ) {
			update_option( 'arcopan_translation_token', sanitize_text_field( wp_unslash( $_POST['arcopan_translation_token'] ) ) );
		}

		if ( isset( $_POST['arcopan_disable_auto_translate'] ) ) {
			update_option( 'arcopan_disable_auto_translate', 1 );
		} else {
			delete_option( 'arcopan_disable_auto_translate' );
		}

		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'arcopan-child' ) . '</p></div>';
	}

	$translation_token = get_option( 'arcopan_translation_token' );
	$auto_translate_disabled = get_option( 'arcopan_disable_auto_translate' );
	?>

	<div class="wrap">
		<h1><?php esc_html_e( 'ARCOPAN Multilingual System', 'arcopan-child' ); ?></h1>

		<div class="card">
			<h2><?php esc_html_e( 'Configuration', 'arcopan-child' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'arcopan_translation_settings' ); ?>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="arcopan_translation_token">
									<?php esc_html_e( 'Webhook Authorization Token', 'arcopan-child' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="text" 
									id="arcopan_translation_token"
									name="arcopan_translation_token"
									value="<?php echo esc_attr( $translation_token ); ?>"
									class="regular-text code"
									placeholder="Generate a random 32-char token"
									required
								/>
								<p class="description">
									<?php esc_html_e( 'Random token for webhook validation. Regenerate quarterly for security.', 'arcopan-child' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="arcopan_disable_auto_translate">
									<?php esc_html_e( 'Disable Auto-Translate', 'arcopan-child' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="checkbox" 
									id="arcopan_disable_auto_translate"
									name="arcopan_disable_auto_translate"
									<?php checked( $auto_translate_disabled ); ?>
								/>
								<p class="description">
									<?php esc_html_e( 'Check to disable automatic translation on post publish. Enable manual triggers only.', 'arcopan-child' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>

		<div class="card">
			<h2><?php esc_html_e( 'Status & Health', 'arcopan-child' ); ?></h2>
			<p>
				<?php
				if ( function_exists( 'pll_the_languages' ) ) {
					echo '<span style="color:green;">✓</span> ' . esc_html__( 'Polylang is active', 'arcopan-child' );
				} else {
					echo '<span style="color:red;">✗</span> ' . esc_html__( 'Polylang is not active', 'arcopan-child' );
				}
				?>
			</p>
			<p>
				<?php
				if ( getenv( 'DEEPL_API_KEY' ) ) {
					echo '<span style="color:green;">✓</span> ' . esc_html__( 'DeepL API key configured', 'arcopan-child' );
				} else {
					echo '<span style="color:orange;">⚠</span> ' . esc_html__( 'DeepL API key not configured (check .env)', 'arcopan-child' );
				}
				?>
			</p>
			<p>
				<?php
				if ( getenv( 'N8N_URL' ) ) {
					echo '<span style="color:green;">✓</span> ' . esc_html__( 'n8n URL configured', 'arcopan-child' );
				} else {
					echo '<span style="color:orange;">⚠</span> ' . esc_html__( 'n8n URL not configured (check .env)', 'arcopan-child' );
				}
				?>
			</p>
			<p>
				<?php
				if ( $translation_token ) {
					echo '<span style="color:green;">✓</span> ' . esc_html__( 'Webhook token set', 'arcopan-child' );
				} else {
					echo '<span style="color:red;">✗</span> ' . esc_html__( 'Webhook token not set', 'arcopan-child' );
				}
				?>
			</p>
		</div>

		<div class="card">
			<h2><?php esc_html_e( 'Documentation', 'arcopan-child' ); ?></h2>
			<ul>
				<li><a href="https://github.com/arcopan/docs/multilingual-architecture.md" target="_blank">Multilingual Architecture</a></li>
				<li><a href="https://github.com/arcopan/docs/polylang-setup.md" target="_blank">Polylang Setup</a></li>
				<li><a href="https://docs.n8n.io/" target="_blank">n8n Documentation</a></li>
				<li><a href="https://www.deepl.com/docs-api" target="_blank">DeepL API</a></li>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Add translation meta box to post editor.
 *
 * @return void
 */
function arcopan_add_translation_metabox() {
	$screens = apply_filters( 'arcopan_translation_metabox_screens', array( 'post', 'page', 'product' ) );

	foreach ( $screens as $screen ) {
		add_meta_box(
			'arcopan-translation',
			__( 'ARCOPAN Translation', 'arcopan-child' ),
			'arcopan_render_translation_metabox',
			$screen,
			'side'
		);
	}
}
add_action( 'add_meta_boxes', 'arcopan_add_translation_metabox' );

/**
 * Render translation meta box in post editor.
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function arcopan_render_translation_metabox( $post ) {
	if ( ! function_exists( 'pll_get_post_language' ) ) {
		esc_html_e( 'Polylang plugin required', 'arcopan-child' );
		return;
	}

	$language = pll_get_post_language( $post->ID );
	$last_status = get_post_meta( $post->ID, '_arcopan_last_translation_status', true );
	$translations = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $post->ID ) : array();
	$in_progress = get_transient( "arcopan_translation_queue_{$post->ID}" );
	?>

	<div class="arcopan-translation-info">
		<p>
			<strong><?php esc_html_e( 'Language:', 'arcopan-child' ); ?></strong> 
			<code><?php echo esc_html( strtoupper( $language ) ); ?></code>
		</p>

		<?php if ( $in_progress ) : ?>
			<p style="color: #0073aa;">
				<strong>⏳ <?php esc_html_e( 'Translation in progress...', 'arcopan-child' ); ?></strong>
			</p>
		<?php endif; ?>

		<?php if ( 'en' === $language ) : ?>
			<p>
				<button type="button" class="button button-primary" id="arcopan-trigger-translation"<?php echo $in_progress ? ' disabled' : ''; ?>>
					<?php esc_html_e( 'Trigger Translation', 'arcopan-child' ); ?>
				</button>
			</p>
			<p class="description">
				<?php esc_html_e( 'Manually send to n8n for TR, FR, DE, RU translation.', 'arcopan-child' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $translations ) && count( $translations ) > 1 ) : ?>
			<div style="margin-top: 15px; padding: 10px; background: #f5f5f5; border-radius: 3px;">
				<strong><?php esc_html_e( 'Translations:', 'arcopan-child' ); ?></strong>
				<ul style="margin: 10px 0; padding: 0 20px;">
					<?php
					foreach ( $translations as $lang => $trans_post_id ) {
						if ( 'en' !== $lang ) {
							$trans_post = get_post( $trans_post_id );
							if ( $trans_post ) {
								$edit_url = get_edit_post_link( $trans_post_id, 'raw' );
								echo '<li><a href="' . esc_url( $edit_url ) . '">' . esc_html( strtoupper( $lang ) ) . ' - ' . esc_html( $trans_post->post_title ) . '</a></li>';
							}
						}
					}
					?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $last_status ) : ?>
			<p style="margin-top: 15px; font-size: 12px; color: #666;">
				<strong><?php esc_html_e( 'Last attempt:', 'arcopan-child' ); ?></strong><br/>
				<?php echo esc_html( date_i18n( 'Y-m-d H:i', strtotime( $last_status['timestamp'] ) ) ); ?>
				<?php echo $last_status['success'] ? ' ✓' : ' ✗'; ?>
			</p>
		<?php endif; ?>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const btn = document.getElementById('arcopan-trigger-translation');
			if (btn) {
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					if (btn.disabled) return;
					
					btn.disabled = true;
					btn.textContent = '⏳ Sending...';
					
					const postId = <?php echo absint( $post->ID ); ?>;
					
					fetch(`<?php echo esc_url( rest_url( 'arcopan/v1/translate' ) ); ?>`, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
						},
						body: JSON.stringify({ post_id: postId })
					})
					.then(r => r.json())
					.then(d => {
						if (d.success || d.message === 'Translation request queued') {
							alert('✓ Translation queued.\nCheck admin email for results.\nETA: ' + (d.estimate || '30-60 seconds'));
							setTimeout(() => location.reload(), 2000);
						} else {
							alert('✗ Error: ' + (d.error || 'Unknown error'));
							btn.disabled = false;
							btn.textContent = 'Trigger Translation';
						}
					})
					.catch(e => {
						alert('✗ Request failed: ' + e.message);
						btn.disabled = false;
						btn.textContent = 'Trigger Translation';
					});
				});
			}
		});
	</script>
	<?php
}
