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
}
add_action( 'wp_enqueue_scripts', 'arcopan_enqueue_assets', 20 );

/**
 * Load child includes.
 *
 * @return void
 */
function arcopan_load_includes() {
	$includes = array(
		'inc/cpt.php',
		'inc/acf-fields.php',
	);

	foreach ( $includes as $include_file ) {
		$path = ARCOPAN_CHILD_PATH . ltrim( $include_file, '/' );
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
arcopan_load_includes();
