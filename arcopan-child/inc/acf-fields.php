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

	// Product Details Field Group
	acf_add_local_field_group( array(
		'key'      => 'arcopan_group_product_details',
		'title'    => 'Ürün Detayları',
		'fields'   => array(
			// Hero Image
			array(
				'key'           => 'arcopan_product_hero_image',
				'label'         => 'Ürün Görüntüsü',
				'name'          => 'hero_image',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			// Product Intro
			array(
				'key'      => 'arcopan_product_intro',
				'label'    => 'Ürün Tanıtımı',
				'name'     => 'product_intro',
				'type'     => 'textarea',
				'rows'     => 4,
				'new_lines' => 'br',
			),
			// Specifications Table (Repeater)
			array(
				'key'        => 'arcopan_product_spec_table',
				'label'      => 'Teknik Özellikler',
				'name'       => 'spec_table',
				'type'       => 'repeater',
				'layout'     => 'table',
				'button_label' => 'Özellik Ekle',
				'sub_fields' => array(
					array(
						'key'   => 'arcopan_product_spec_label',
						'label' => 'Özellik Adı',
						'name'  => 'label',
						'type'  => 'text',
					),
					array(
						'key'   => 'arcopan_product_spec_value',
						'label' => 'Değer',
						'name'  => 'value',
						'type'  => 'text',
					),
				),
			),
			// Datasheet PDF
			array(
				'key'           => 'arcopan_product_datasheet_pdf',
				'label'         => 'Teknik Şartname (PDF)',
				'name'          => 'datasheet_pdf',
				'type'          => 'file',
				'return_format' => 'array',
				'library'       => 'all',
			),
			// Temperature Range
			array(
				'key'   => 'arcopan_product_temperature_range',
				'label' => 'Sıcaklık Aralığı',
				'name'  => 'temperature_range',
				'type'  => 'text',
				'placeholder' => 'Örn: -40°C ile +85°C',
			),
			// Certifications
			array(
				'key'     => 'arcopan_product_certifications',
				'label'   => 'Sertifikalar',
				'name'    => 'certifications',
				'type'    => 'checkbox',
				'choices' => array(
					'CE'   => 'CE',
					'UL'   => 'UL',
					'RoHS' => 'RoHS',
					'ATEX' => 'ATEX',
				),
				'layout' => 'vertical',
			),
			// Related Products
			array(
				'key'          => 'arcopan_product_related_products',
				'label'        => 'İlişkili Ürünler',
				'name'         => 'related_products',
				'type'         => 'relationship',
				'post_type'    => array( 'product' ),
				'return_format' => 'id',
				'filters'      => array( 'search', 'post_type' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'product',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => 'Ürün sayfaları için detaylı bilgiler',
	) );

	// Project Details Field Group
	acf_add_local_field_group( array(
		'key'      => 'arcopan_group_project_details',
		'title'    => 'Proje Detayları',
		'fields'   => array(
			// Project Client
			array(
				'key'   => 'arcopan_project_client',
				'label' => 'Müşteri Adı',
				'name'  => 'project_client',
				'type'  => 'text',
			),
			// Project Industry
			array(
				'key'     => 'arcopan_project_industry',
				'label'   => 'Sektör',
				'name'    => 'project_industry',
				'type'    => 'select',
				'choices' => array(
					'energy'      => 'Enerji',
					'construction' => 'İnşaat',
					'industry'    => 'Endüstri',
					'infrastructure' => 'Altyapı',
				),
				'return_format' => 'value',
				'allow_null'    => true,
			),
			// Project Year
			array(
				'key'   => 'arcopan_project_year',
				'label' => 'Proje Yılı',
				'name'  => 'project_year',
				'type'  => 'number',
				'min'   => 1900,
				'max'   => 2100,
			),
			// Project Gallery
			array(
				'key'           => 'arcopan_project_gallery',
				'label'         => 'Proje Görselleri',
				'name'          => 'project_gallery',
				'type'          => 'gallery',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			// Project Highlight Stat
			array(
				'key'      => 'arcopan_project_highlight_stat',
				'label'    => 'Öne Çıkan İstatistik',
				'name'     => 'project_highlight_stat',
				'type'     => 'text',
				'placeholder' => 'Örn: +%50 Verimlilik Artışı',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'project',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => 'Proje sayfaları için detaylı bilgiler',
	) );
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
