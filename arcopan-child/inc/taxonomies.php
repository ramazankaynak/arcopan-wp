<?php
/**
 * Taxonomies Registration
 *
 * Registers all custom taxonomies for ARCOPAN child theme
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Register custom taxonomies
 */
function arcopan_register_custom_taxonomies() {
	// Taxonomy 1: Industry
	register_taxonomy( 'arc_industry', 'arcopan_project', array(
		'label'              => __( 'Industry', 'arcopan' ),
		'labels'             => array(
			'name'                       => __( 'Industries', 'arcopan' ),
			'singular_name'              => __( 'Industry', 'arcopan' ),
			'menu_name'                  => __( 'Industries', 'arcopan' ),
			'all_items'                  => __( 'All Industries', 'arcopan' ),
			'edit_item'                  => __( 'Edit Industry', 'arcopan' ),
			'view_item'                  => __( 'View Industry', 'arcopan' ),
			'update_item'                => __( 'Update Industry', 'arcopan' ),
			'add_new_item'               => __( 'Add New Industry', 'arcopan' ),
			'new_item_name'              => __( 'New Industry Name', 'arcopan' ),
			'parent_item'                => __( 'Parent Industry', 'arcopan' ),
			'parent_item_colon'          => __( 'Parent Industry:', 'arcopan' ),
			'search_items'               => __( 'Search Industries', 'arcopan' ),
			'popular_items'              => __( 'Popular Industries', 'arcopan' ),
			'separate_items_with_commas' => __( 'Separate industries with commas', 'arcopan' ),
			'add_or_remove_items'        => __( 'Add or remove industries', 'arcopan' ),
			'choose_from_most_used'      => __( 'Choose from the most used industries', 'arcopan' ),
			'not_found'                  => __( 'No industries found', 'arcopan' ),
			'no_terms'                   => __( 'No industries', 'arcopan' ),
			'items_list_navigation'      => __( 'Industries list navigation', 'arcopan' ),
			'items_list'                 => __( 'Industries list', 'arcopan' ),
			'back_to_items'              => __( 'Back to Industries', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'show_in_nav_menus'  => true,
		'show_tag_cloud'     => false,
		'query_var'          => true,
		'rewrite'            => array(
			'slug'         => 'industry',
			'with_front'   => true,
			'hierarchical' => true,
		),
		'capabilities'       => array(),
	) );

	// Taxonomy 2: Solution
	register_taxonomy( 'arc_solution', 'arcopan_project', array(
		'label'              => __( 'Solution', 'arcopan' ),
		'labels'             => array(
			'name'                       => __( 'Solutions', 'arcopan' ),
			'singular_name'              => __( 'Solution', 'arcopan' ),
			'menu_name'                  => __( 'Solutions', 'arcopan' ),
			'all_items'                  => __( 'All Solutions', 'arcopan' ),
			'edit_item'                  => __( 'Edit Solution', 'arcopan' ),
			'view_item'                  => __( 'View Solution', 'arcopan' ),
			'update_item'                => __( 'Update Solution', 'arcopan' ),
			'add_new_item'               => __( 'Add New Solution', 'arcopan' ),
			'new_item_name'              => __( 'New Solution Name', 'arcopan' ),
			'parent_item'                => __( 'Parent Solution', 'arcopan' ),
			'parent_item_colon'          => __( 'Parent Solution:', 'arcopan' ),
			'search_items'               => __( 'Search Solutions', 'arcopan' ),
			'popular_items'              => __( 'Popular Solutions', 'arcopan' ),
			'separate_items_with_commas' => __( 'Separate solutions with commas', 'arcopan' ),
			'add_or_remove_items'        => __( 'Add or remove solutions', 'arcopan' ),
			'choose_from_most_used'      => __( 'Choose from the most used solutions', 'arcopan' ),
			'not_found'                  => __( 'No solutions found', 'arcopan' ),
			'no_terms'                   => __( 'No solutions', 'arcopan' ),
			'items_list_navigation'      => __( 'Solutions list navigation', 'arcopan' ),
			'items_list'                 => __( 'Solutions list', 'arcopan' ),
			'back_to_items'              => __( 'Back to Solutions', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'show_in_nav_menus'  => true,
		'show_tag_cloud'     => false,
		'query_var'          => true,
		'rewrite'            => array(
			'slug'         => 'solution',
			'with_front'   => true,
			'hierarchical' => true,
		),
		'capabilities'       => array(),
	) );
}

add_action( 'init', 'arcopan_register_custom_taxonomies' );

/**
 * Flush rewrite rules once when the theme is switched.
 *
 * @return void
 */
function arcopan_child_flush_rewrite_rules_taxonomies() {
	arcopan_register_custom_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcopan_child_flush_rewrite_rules_taxonomies' );
