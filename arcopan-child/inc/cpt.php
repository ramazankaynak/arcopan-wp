<?php
/**
 * Custom Post Types for ARCOPAN.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register a single post type from config.
 *
 * @param string $post_type Post type slug.
 * @param array  $config    Post type labels and settings.
 * @return void
 */
function arcopan_register_single_cpt( $post_type, array $config ) {
	$defaults = array(
		'singular'           => ucfirst( $post_type ),
		'plural'             => ucfirst( $post_type ) . 's',
		'menu_icon'          => 'dashicons-admin-post',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
		'taxonomies'         => array(),
		'rewrite'            => array( 'slug' => $post_type, 'with_front' => false ),
		'public'             => true,
		'has_archive'        => true,
		'show_in_rest'       => true,
		'exclude_from_search'=> false,
	);

	$settings = wp_parse_args( $config, $defaults );

	$labels = array(
		'name'                  => $settings['plural'],
		'singular_name'         => $settings['singular'],
		'menu_name'             => $settings['plural'],
		'name_admin_bar'        => $settings['singular'],
		'add_new'               => __( 'Add New', 'arcopan-child' ),
		'add_new_item'          => sprintf( __( 'Add New %s', 'arcopan-child' ), $settings['singular'] ),
		'edit_item'             => sprintf( __( 'Edit %s', 'arcopan-child' ), $settings['singular'] ),
		'new_item'              => sprintf( __( 'New %s', 'arcopan-child' ), $settings['singular'] ),
		'view_item'             => sprintf( __( 'View %s', 'arcopan-child' ), $settings['singular'] ),
		'view_items'            => sprintf( __( 'View %s', 'arcopan-child' ), $settings['plural'] ),
		'search_items'          => sprintf( __( 'Search %s', 'arcopan-child' ), $settings['plural'] ),
		'not_found'             => sprintf( __( 'No %s found', 'arcopan-child' ), strtolower( $settings['plural'] ) ),
		'not_found_in_trash'    => sprintf( __( 'No %s found in Trash', 'arcopan-child' ), strtolower( $settings['plural'] ) ),
		'all_items'             => sprintf( __( 'All %s', 'arcopan-child' ), $settings['plural'] ),
		'archives'              => sprintf( __( '%s Archives', 'arcopan-child' ), $settings['singular'] ),
		'attributes'            => sprintf( __( '%s Attributes', 'arcopan-child' ), $settings['singular'] ),
		'insert_into_item'      => sprintf( __( 'Insert into %s', 'arcopan-child' ), strtolower( $settings['singular'] ) ),
		'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'arcopan-child' ), strtolower( $settings['singular'] ) ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => (bool) $settings['public'],
		'has_archive'        => (bool) $settings['has_archive'],
		'show_in_rest'       => (bool) $settings['show_in_rest'],
		'exclude_from_search'=> (bool) $settings['exclude_from_search'],
		'rewrite'            => (array) $settings['rewrite'],
		'supports'           => (array) $settings['supports'],
		'taxonomies'         => (array) $settings['taxonomies'],
		'menu_icon'          => (string) $settings['menu_icon'],
		'menu_position'      => 20,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
	);

	register_post_type( $post_type, $args );
}

/**
 * Register ARCOPAN custom post types.
 *
 * @return void
 */
function arcopan_register_cpts() {
	$cpts = array(
		'arc_product'  => array(
			'singular'  => __( 'Product', 'arcopan-child' ),
			'plural'    => __( 'Products', 'arcopan-child' ),
			'menu_icon' => 'dashicons-products',
			'rewrite'   => array( 'slug' => 'products', 'with_front' => false ),
		),
		'arc_solution' => array(
			'singular'  => __( 'Solution', 'arcopan-child' ),
			'plural'    => __( 'Solutions', 'arcopan-child' ),
			'menu_icon' => 'dashicons-lightbulb',
			'rewrite'   => array( 'slug' => 'solutions', 'with_front' => false ),
		),
		'arc_industry' => array(
			'singular'  => __( 'Industry', 'arcopan-child' ),
			'plural'    => __( 'Industries', 'arcopan-child' ),
			'menu_icon' => 'dashicons-building',
			'rewrite'   => array( 'slug' => 'industries', 'with_front' => false ),
		),
		'arc_project'  => array(
			'singular'  => __( 'Project', 'arcopan-child' ),
			'plural'    => __( 'Projects', 'arcopan-child' ),
			'menu_icon' => 'dashicons-portfolio',
			'rewrite'   => array( 'slug' => 'projects', 'with_front' => false ),
		),
		'arc_resource' => array(
			'singular'  => __( 'Resource', 'arcopan-child' ),
			'plural'    => __( 'Resources', 'arcopan-child' ),
			'menu_icon' => 'dashicons-media-document',
			'rewrite'   => array( 'slug' => 'resources', 'with_front' => false ),
		),
	);

	/**
	 * Filter CPT map to align with project document revisions.
	 *
	 * @param array $cpts CPT definitions.
	 */
	$cpts = apply_filters( 'arcopan_cpt_map', $cpts );

	foreach ( $cpts as $post_type => $config ) {
		arcopan_register_single_cpt( $post_type, (array) $config );
	}
}
add_action( 'init', 'arcopan_register_cpts' );

/**
 * Flush rewrite rules once when the theme is switched.
 *
 * @return void
 */
function arcopan_child_flush_rewrite_rules() {
	arcopan_register_cpts();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcopan_child_flush_rewrite_rules' );
