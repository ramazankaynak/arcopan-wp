<?php
/**
 * Performance, media sizes, fonts preload, and WPML-related adjustments.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Caps stored revisions (core may pass -1 for unlimited).
 *
 * @param int     $num  Revision cap from WP_POST_REVISIONS.
 * @param WP_Post $post Post object.
 * @return int
 */
function arcopan_limit_revisions( $num, $post ) {
	$max = (int) apply_filters( 'arcopan_revisions_max', 5, $post );
	$num = (int) $num;

	if ( 0 === $num ) {
		return 0;
	}
	if ( $num < 0 ) {
		return max( 0, $max );
	}

	return min( $num, max( 0, $max ) );
}
add_filter( 'wp_revisions_to_keep', 'arcopan_limit_revisions', 10, 2 );

/**
 * Preload Inter WOFF2 (place file at assets/fonts/ or override via filter).
 *
 * @return void
 */
function arcopan_preload_fonts() {
	if ( is_admin() ) {
		return;
	}

	$defaults = array(
		ARCOPAN_CHILD_URI . 'assets/fonts/inter-latin-400-normal.woff2',
	);

	/**
	 * URLs for link rel=preload as=font (woff2).
	 *
	 * @param string[] $urls Absolute URLs.
	 */
	$urls = apply_filters( 'arcopan_preload_font_urls', $defaults );

	foreach ( $urls as $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			continue;
		}
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>%s',
			esc_url( $url ),
			"\n"
		);
	}
}
add_action( 'wp_head', 'arcopan_preload_fonts', 1 );

/**
 * Defer whitelisted front-end scripts.
 *
 * @param string $tag    Markup.
 * @param string $handle Handle.
 * @param string $src    Src URL.
 * @return string
 */
function arcopan_defer_scripts( $tag, $handle, $src ) {
	if ( is_admin() || false !== strpos( $tag, ' defer' ) ) {
		return $tag;
	}

	$defer = (array) apply_filters(
		'arcopan_defer_script_handles',
		array(
			'arcopan-global',
			'arcopan-main',
		)
	);

	if ( ! in_array( $handle, $defer, true ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}
add_filter( 'script_loader_tag', 'arcopan_defer_scripts', 10, 3 );

/**
 * Disable XML-RPC.
 *
 * @return void
 */
function arcopan_disable_xmlrpc() {
	add_filter( 'xmlrpc_enabled', '__return_false' );
}
arcopan_disable_xmlrpc();

/**
 * Register custom image sizes.
 *
 * @return void
 */
function arcopan_image_sizes() {
	add_image_size( 'product-thumb', 600, 400, true );
	add_image_size( 'hero-bg', 1920, 900, true );
}
add_action( 'after_setup_theme', 'arcopan_image_sizes', 20 );

/**
 * WPML: extension point for compatibility tweaks.
 *
 * @return void
 */
function arcopan_wpml_config() {
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
		return;
	}

	do_action( 'arcopan_wpml_config' );
}
add_action( 'wp_loaded', 'arcopan_wpml_config', 20 );
