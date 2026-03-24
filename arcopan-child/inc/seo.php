<?php
/**
 * JSON-LD structured data for ARCOPAN.
 *
 * @package ArcopanChild
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Outputs Schema.org JSON-LD in the document head.
 */
function arcopan_schema_output(): void {
	$is_json = function_exists( 'wp_is_json_request' ) && wp_is_json_request();
	if ( is_feed() || is_admin() || wp_doing_ajax() || $is_json ) {
		return;
	}

	$graph = [];

	if ( is_front_page() ) {
		$org = arcopan_schema_get_organization();
		if ( is_array( $org ) ) {
			$graph[] = $org;
		}
	}

	if ( ! is_front_page() ) {
		$crumbs = arcopan_schema_get_breadcrumb_list();
		if ( is_array( $crumbs ) ) {
			$graph[] = $crumbs;
		}
	}

	if ( is_singular( 'arcopan_product' ) ) {
		$product = arcopan_schema_get_product();
		if ( is_array( $product ) ) {
			$graph[] = $product;
		}
	}

	if ( is_singular( 'post' ) ) {
		$article = arcopan_schema_get_article();
		if ( is_array( $article ) ) {
			$graph[] = $article;
		}
	}

	/**
	 * Filter the full Schema.org @graph array before output.
	 *
	 * @param array<int, array<string, mixed>> $graph Schema nodes.
	 */
	$graph = apply_filters( 'arcopan_schema_graph', $graph );

	if ( $graph === [] ) {
		return;
	}

	$data = [
		'@context' => 'https://schema.org',
		'@graph'   => array_values( $graph ),
	];

	try {
		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
	} catch ( JsonException $e ) {
		return;
	}
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD, encoded.
	echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
}
add_action( 'wp_head', 'arcopan_schema_output', 5 );

/**
 * @return array<string, mixed>|null
 */
function arcopan_schema_get_organization(): ?array {
	$logo_url = '';
	$logo_id  = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id > 0 ) {
		$logo_url = (string) wp_get_attachment_image_url( $logo_id, 'full' );
	}

	$org = [
		'@type' => 'Organization',
		'@id'   => trailingslashit( home_url( '/', 'https' ) ) . '#organization',
		'name'  => get_bloginfo( 'name', 'display' ),
		'url'   => home_url( '/' ),
	];

	if ( $logo_url !== '' ) {
		$org['logo'] = [
			'@type' => 'ImageObject',
			'url'   => esc_url_raw( $logo_url ),
		];
	}

	$desc = get_bloginfo( 'description', 'display' );
	if ( is_string( $desc ) && $desc !== '' ) {
		$org['description'] = wp_strip_all_tags( $desc );
	}

	return apply_filters( 'arcopan_schema_organization', $org );
}

/**
 * @return array<string, mixed>|null
 */
function arcopan_schema_get_breadcrumb_list(): ?array {
	$items = arcopan_schema_breadcrumb_items();
	if ( $items === [] ) {
		return null;
	}

	$list = [];
	foreach ( $items as $i => $item ) {
		$list[] = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		];
	}

	return apply_filters(
		'arcopan_schema_breadcrumb_list',
		[
			'@type'           => 'BreadcrumbList',
			'@id'             => arcopan_schema_current_canonical_url() . '#breadcrumb',
			'itemListElement' => $list,
		]
	);
}

/**
 * @return list<array{name: string, url: string}>
 */
function arcopan_schema_breadcrumb_items(): array {
	$items   = [];
	$items[] = [
		'name' => __( 'Home', 'arcopan-child' ),
		'url'  => home_url( '/' ),
	];

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$pt_object = get_post_type_object( $post->post_type );
			if ( $pt_object !== null && $pt_object->has_archive ) {
				$items[] = [
					'name' => $pt_object->labels->name,
					'url'  => get_post_type_archive_link( $post->post_type ) ?: home_url( '/' ),
				];
			}

			$ancestors = get_post_ancestors( $post );
			$ancestors = array_reverse( array_map( 'intval', $ancestors ) );
			foreach ( $ancestors as $ancestor_id ) {
				$p = get_post( $ancestor_id );
				if ( $p instanceof WP_Post ) {
					$items[] = [
						'name' => get_the_title( $p ),
						'url'  => get_permalink( $p ),
					];
				}
			}

			$items[] = [
				'name' => get_the_title( $post ),
				'url'  => get_permalink( $post ),
			];
		}
	} elseif ( is_post_type_archive() ) {
		$pt = get_query_var( 'post_type' );
		if ( is_string( $pt ) && $pt !== '' ) {
			$obj = get_post_type_object( $pt );
			if ( $obj !== null ) {
				$items[] = [
					'name' => $obj->labels->name,
					'url'  => get_post_type_archive_link( $pt ) ?: home_url( '/' ),
				];
			}
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$items[] = [
				'name' => $term->name,
				'url'  => get_term_link( $term ),
			];
		}
	} elseif ( is_search() ) {
		$items[] = [
			/* translators: %s: search query */
			'name' => sprintf( __( 'Search results for "%s"', 'arcopan-child' ), get_search_query() ),
			'url'  => get_search_link(),
		];
	} elseif ( is_404() ) {
		return [];
	}

	return apply_filters( 'arcopan_schema_breadcrumb_items', $items );
}

/**
 * @return array<string, mixed>|null
 */
function arcopan_schema_get_product(): ?array {
	if ( ! is_singular( 'arcopan_product' ) ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$url     = get_permalink( $post );
	$name    = get_the_title( $post );
	$excerpt = get_the_excerpt( $post );
	$desc    = $excerpt !== '' ? wp_strip_all_tags( $excerpt ) : wp_strip_all_tags( wp_trim_words( (string) $post->post_content, 40 ) );

	$product = [
		'@type'       => 'Product',
		'@id'         => is_string( $url ) ? $url . '#product' : arcopan_schema_current_canonical_url() . '#product',
		'name'        => $name,
		'description' => $desc,
		'url'         => $url,
	];

	$img = get_the_post_thumbnail_url( $post, 'full' );
	if ( is_string( $img ) && $img !== '' ) {
		$product['image'] = [ esc_url_raw( $img ) ];
	}

	// Optional ACF: hero_image for image when no featured image.
	if ( function_exists( 'get_field' ) ) {
		$hero = get_field( 'hero_image', $post->ID );
		if ( is_array( $hero ) && ! empty( $hero['url'] ) && empty( $product['image'] ) ) {
			$product['image'] = [ esc_url_raw( (string) $hero['url'] ) ];
		}
	}

	return apply_filters( 'arcopan_schema_product', $product, $post );
}

/**
 * @return array<string, mixed>|null
 */
function arcopan_schema_get_article(): ?array {
	if ( ! is_singular( 'post' ) ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$url       = get_permalink( $post );
	$modified  = get_post_modified_time( 'c', true, $post );
	$published = get_post_time( 'c', true, $post );

	$article = [
		'@type'            => 'Article',
		'@id'              => is_string( $url ) ? $url . '#article' : arcopan_schema_current_canonical_url() . '#article',
		'headline'         => get_the_title( $post ),
		'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
		'datePublished'    => $published,
		'dateModified'     => $modified,
		'mainEntityOfPage' => [
			'@type' => 'WebPage',
			'@id'   => $url,
		],
	];

	$img = get_the_post_thumbnail_url( $post, 'full' );
	if ( is_string( $img ) && $img !== '' ) {
		$article['image'] = [
			'@type' => 'ImageObject',
			'url'   => esc_url_raw( $img ),
		];
	}

	$author_id = (int) $post->post_author;
	if ( $author_id > 0 ) {
		$article['author'] = [
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		];
	}

	return apply_filters( 'arcopan_schema_article', $article, $post );
}

function arcopan_schema_current_canonical_url(): string {
	if ( is_singular() ) {
		$url = get_permalink();
		if ( is_string( $url ) ) {
			return $url;
		}
	}
	if ( is_home() && ! is_front_page() ) {
		return get_post_type_archive_link( 'post' ) ?: home_url( '/' );
	}
	return home_url( add_query_arg( [] ) );
}
