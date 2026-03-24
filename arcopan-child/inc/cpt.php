<?php
/**
 * Custom Post Types for ARCOPAN.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register ARCOPAN custom post types.
 *
 * @return void
 */
function arcopan_register_custom_post_types() {
	// CPT 1: Product
	register_post_type( 'arcopan_product', array(
		'label'              => __( 'Product', 'arcopan' ),
		'labels'             => array(
			'name'               => __( 'Products', 'arcopan' ),
			'singular_name'      => __( 'Product', 'arcopan' ),
			'menu_name'          => __( 'Products', 'arcopan' ),
			'add_new'            => __( 'Add New Product', 'arcopan' ),
			'add_new_item'       => __( 'Add New Product', 'arcopan' ),
			'edit_item'          => __( 'Edit Product', 'arcopan' ),
			'view_item'          => __( 'View Product', 'arcopan' ),
			'view_items'         => __( 'View Products', 'arcopan' ),
			'search_items'       => __( 'Search Products', 'arcopan' ),
			'not_found'          => __( 'No products found', 'arcopan' ),
			'not_found_in_trash' => __( 'No products found in trash', 'arcopan' ),
			'all_items'          => __( 'All Products', 'arcopan' ),
			'archives'           => __( 'Product Archives', 'arcopan' ),
			'back_to_items'      => __( 'Back to Products', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'has_archive'        => true,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-archive',
		'supports'           => array( 'title', 'thumbnail', 'editor', 'custom-fields' ),
		'rewrite'            => array(
			'slug'       => 'products',
			'with_front' => true,
		),
		'capability_type'    => 'post',
		'taxonomies'         => array(),
	) );

	// CPT 2: Project
	register_post_type( 'arcopan_project', array(
		'label'              => __( 'Project', 'arcopan' ),
		'labels'             => array(
			'name'               => __( 'Projects', 'arcopan' ),
			'singular_name'      => __( 'Project', 'arcopan' ),
			'menu_name'          => __( 'Projects', 'arcopan' ),
			'add_new'            => __( 'Add New Project', 'arcopan' ),
			'add_new_item'       => __( 'Add New Project', 'arcopan' ),
			'edit_item'          => __( 'Edit Project', 'arcopan' ),
			'view_item'          => __( 'View Project', 'arcopan' ),
			'view_items'         => __( 'View Projects', 'arcopan' ),
			'search_items'       => __( 'Search Projects', 'arcopan' ),
			'not_found'          => __( 'No projects found', 'arcopan' ),
			'not_found_in_trash' => __( 'No projects found in trash', 'arcopan' ),
			'all_items'          => __( 'All Projects', 'arcopan' ),
			'archives'           => __( 'Project Archives', 'arcopan' ),
			'back_to_items'      => __( 'Back to Projects', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'has_archive'        => true,
		'menu_position'      => 6,
		'menu_icon'          => 'dashicons-portfolio',
		'supports'           => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'            => array(
			'slug'       => 'projects',
			'with_front' => true,
		),
		'capability_type'    => 'post',
		'taxonomies'         => array(),
	) );

	// CPT 3: Team Member
	register_post_type( 'arcopan_team_member', array(
		'label'              => __( 'Team Member', 'arcopan' ),
		'labels'             => array(
			'name'               => __( 'Team Members', 'arcopan' ),
			'singular_name'      => __( 'Team Member', 'arcopan' ),
			'menu_name'          => __( 'Team Members', 'arcopan' ),
			'add_new'            => __( 'Add New Team Member', 'arcopan' ),
			'add_new_item'       => __( 'Add New Team Member', 'arcopan' ),
			'edit_item'          => __( 'Edit Team Member', 'arcopan' ),
			'view_item'          => __( 'View Team Member', 'arcopan' ),
			'view_items'         => __( 'View Team Members', 'arcopan' ),
			'search_items'       => __( 'Search Team Members', 'arcopan' ),
			'not_found'          => __( 'No team members found', 'arcopan' ),
			'not_found_in_trash' => __( 'No team members found in trash', 'arcopan' ),
			'all_items'          => __( 'All Team Members', 'arcopan' ),
			'archives'           => __( 'Team Member Archives', 'arcopan' ),
			'back_to_items'      => __( 'Back to Team Members', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'has_archive'        => false,
		'menu_position'      => 7,
		'menu_icon'          => 'dashicons-groups',
		'supports'           => array( 'title', 'thumbnail', 'custom-fields' ),
		'rewrite'            => array(
			'slug'       => 'team',
			'with_front' => true,
		),
		'capability_type'    => 'post',
		'taxonomies'         => array(),
	) );

	// CPT 4: Certificate
	register_post_type( 'arcopan_certificate', array(
		'label'              => __( 'Certificate', 'arcopan' ),
		'labels'             => array(
			'name'               => __( 'Certificates', 'arcopan' ),
			'singular_name'      => __( 'Certificate', 'arcopan' ),
			'menu_name'          => __( 'Certificates', 'arcopan' ),
			'add_new'            => __( 'Add New Certificate', 'arcopan' ),
			'add_new_item'       => __( 'Add New Certificate', 'arcopan' ),
			'edit_item'          => __( 'Edit Certificate', 'arcopan' ),
			'view_item'          => __( 'View Certificate', 'arcopan' ),
			'view_items'         => __( 'View Certificates', 'arcopan' ),
			'search_items'       => __( 'Search Certificates', 'arcopan' ),
			'not_found'          => __( 'No certificates found', 'arcopan' ),
			'not_found_in_trash' => __( 'No certificates found in trash', 'arcopan' ),
			'all_items'          => __( 'All Certificates', 'arcopan' ),
			'archives'           => __( 'Certificate Archives', 'arcopan' ),
			'back_to_items'      => __( 'Back to Certificates', 'arcopan' ),
		),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'has_archive'        => false,
		'menu_position'      => 8,
		'menu_icon'          => 'dashicons-awards',
		'supports'           => array( 'title', 'custom-fields' ),
		'rewrite'            => array(
			'slug'       => 'certificates',
			'with_front' => true,
		),
		'capability_type'    => 'post',
		'taxonomies'         => array(),
	) );
}

add_action( 'init', 'arcopan_register_custom_post_types' );

/**
 * Flush rewrite rules once when the theme is switched.
 *
 * @return void
 */
function arcopan_child_flush_rewrite_rules() {
	arcopan_register_custom_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcopan_child_flush_rewrite_rules' );
