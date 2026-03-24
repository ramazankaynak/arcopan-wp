<?php
/**
 * ACF field group registrations for ARCOPAN CPTs.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register ACF fields in PHP.
 *
 * @return void
 */
function arcopan_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_arcopan_product_details',
			'title'    => __( 'Product Details', 'arcopan-child' ),
			'fields'   => array(
				array(
					'key'   => 'field_arcopan_product_code',
					'label' => __( 'Product Code', 'arcopan-child' ),
					'name'  => 'product_code',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_arcopan_product_short_specs',
					'label'         => __( 'Short Specs', 'arcopan-child' ),
					'name'          => 'short_specs',
					'type'          => 'textarea',
					'rows'          => 4,
					'new_lines'     => 'br',
					'instructions'  => __( 'Short technical highlights for product cards.', 'arcopan-child' ),
				),
				array(
					'key'          => 'field_arcopan_product_download',
					'label'        => __( 'Datasheet File', 'arcopan-child' ),
					'name'         => 'datasheet_file',
					'type'         => 'file',
					'return_format'=> 'array',
					'library'      => 'all',
					'mime_types'   => 'pdf',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'arc_product',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_arcopan_solution_details',
			'title'    => __( 'Solution Details', 'arcopan-child' ),
			'fields'   => array(
				array(
					'key'   => 'field_arcopan_solution_icon',
					'label' => __( 'Icon', 'arcopan-child' ),
					'name'  => 'solution_icon',
					'type'  => 'image',
					'return_format' => 'array',
					'preview_size'  => 'thumbnail',
				),
				array(
					'key'           => 'field_arcopan_solution_cta_label',
					'label'         => __( 'CTA Label', 'arcopan-child' ),
					'name'          => 'solution_cta_label',
					'type'          => 'text',
					'default_value' => __( 'Request a Quote', 'arcopan-child' ),
				),
				array(
					'key'           => 'field_arcopan_solution_cta_url',
					'label'         => __( 'CTA URL', 'arcopan-child' ),
					'name'          => 'solution_cta_url',
					'type'          => 'url',
					'default_value' => home_url( '/contact' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'arc_solution',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'arcopan_register_acf_field_groups' );

/**
 * Point ACF JSON sync to child theme folder.
 *
 * @param string $path Existing save path.
 * @return string
 */
function arcopan_acf_json_save_path( $path ) {
	return trailingslashit( get_stylesheet_directory() ) . 'acf-json';
}
add_filter( 'acf/settings/save_json', 'arcopan_acf_json_save_path' );

/**
 * Add child theme ACF JSON load path.
 *
 * @param array $paths Existing load paths.
 * @return array
 */
function arcopan_acf_json_load_path( $paths ) {
	$paths[] = trailingslashit( get_stylesheet_directory() ) . 'acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'arcopan_acf_json_load_path' );
