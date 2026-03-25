<?php
/**
 * ARCOPAN Child theme bootstrap.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ARCOPAN_CHILD_VERSION' ) ) {
	define( 'ARCOPAN_CHILD_VERSION', '1.0.0' );
}

if ( ! defined( 'ARCOPAN_CHILD_PATH' ) ) {
	define( 'ARCOPAN_CHILD_PATH', trailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'ARCOPAN_CHILD_URI' ) ) {
	define( 'ARCOPAN_CHILD_URI', trailingslashit( get_stylesheet_directory_uri() ) );
}

/**
 * Load child theme textdomain.
 *
 * @return void
 */
function arcopan_load_textdomain() {
	load_child_theme_textdomain( 'arcopan-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'arcopan_load_textdomain' );

/**
 * Enqueue global theme styles.
 *
 * @return void
 */
function arcopan_enqueue_assets() {
	$global_css_path = ARCOPAN_CHILD_PATH . 'assets/css/global.css';
	$global_css_ver  = file_exists( $global_css_path ) ? (string) filemtime( $global_css_path ) : ARCOPAN_CHILD_VERSION;

	wp_enqueue_style(
		'arcopan-global',
		ARCOPAN_CHILD_URI . 'assets/css/global.css',
		array(),
		$global_css_ver
	);

	$main_js_path = ARCOPAN_CHILD_PATH . 'assets/js/main.js';
	$main_js_ver  = file_exists( $main_js_path ) ? (string) filemtime( $main_js_path ) : ARCOPAN_CHILD_VERSION;

	wp_enqueue_script(
		'arcopan-main',
		ARCOPAN_CHILD_URI . 'assets/js/main.js',
		array(),
		$main_js_ver,
		true
	);

	wp_localize_script(
		'arcopan-main',
		'arcopanTheme',
		array(
			'restUrl'    => esc_url_raw( rest_url( 'wp/v2/' ) ),
			'restNonce'  => wp_create_nonce( 'wp_rest' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'arcopan_enqueue_assets', 20 );

/**
 * Load main.js as an ES module.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @return string
 */
function arcopan_main_script_as_module( $tag, $handle ) {
	if ( 'arcopan-main' !== $handle || false !== strpos( $tag, 'type=' ) ) {
		return $tag;
	}
	return str_replace( '<script ', '<script type="module" ', $tag );
}
add_filter( 'script_loader_tag', 'arcopan_main_script_as_module', 12, 2 );

/**
 * Load child includes.
 *
 * @return void
 */
function arcopan_load_includes() {
	$includes = array(
		'inc/taxonomies.php',
		'inc/cpt.php',
		'inc/performance.php',
		'inc/acf-fields.php',
		'inc/seo.php',
		'inc/forms.php',
		'inc/multilingual.php',
	);

	foreach ( $includes as $include_file ) {
		$path = ARCOPAN_CHILD_PATH . ltrim( $include_file, '/' );
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
arcopan_load_includes();
