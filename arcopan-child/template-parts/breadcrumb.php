<?php
/**
 * Breadcrumb Navigation Template Part
 *
 * Schema-ready breadcrumb navigation with SEO support
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Skip if Yoast SEO breadcrumb is active
if ( function_exists( 'yoast_breadcrumb' ) && apply_filters( 'wpseo_show_breadcrumbs', true ) ) {
	yoast_breadcrumb( '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'arcopan' ) . '">', '</nav>' );
	return;
}

// Build breadcrumbs array
$breadcrumbs = array();

// Home crumb
$breadcrumbs[] = array(
	'title' => __( 'Home', 'arcopan' ),
	'url'   => esc_url( home_url( '/' ) ),
);

// Current page handling
if ( is_home() || is_front_page() ) {
	// Don't add more for homepage
} elseif ( is_singular( array( 'arcopan_product', 'arcopan_project' ) ) ) {
	// CPT crumb
	$post_type      = get_post_type();
	$post_type_obj  = get_post_type_object( $post_type );
	$archive_link   = get_post_type_archive_link( $post_type );

	if ( $post_type_obj && $archive_link ) {
		$breadcrumbs[] = array(
			'title' => esc_html( $post_type_obj->labels->name ),
			'url'   => esc_url( $archive_link ),
		);
	}

	// Current page
	$breadcrumbs[] = array(
		'title' => esc_html( get_the_title() ),
		'url'   => '',
	);
} elseif ( is_page() ) {
	// Parent pages
	$post_ancestors = get_post_ancestors( get_the_ID() );

	if ( ! empty( $post_ancestors ) ) {
		$post_ancestors = array_reverse( $post_ancestors );

		foreach ( $post_ancestors as $ancestor_id ) {
			$breadcrumbs[] = array(
				'title' => esc_html( get_the_title( $ancestor_id ) ),
				'url'   => esc_url( get_permalink( $ancestor_id ) ),
			);
		}
	}

	// Current page
	$breadcrumbs[] = array(
		'title' => esc_html( get_the_title() ),
		'url'   => '',
	);
} elseif ( is_archive() ) {
	// Archive pages
	if ( is_category() || is_tag() ) {
		$breadcrumbs[] = array(
			'title' => __( 'Blog', 'arcopan' ),
			'url'   => esc_url( get_home_url() . '/blog' ),
		);
	}

	// Current archive
	$breadcrumbs[] = array(
		'title' => esc_html( get_the_archive_title() ),
		'url'   => '',
	);
} elseif ( is_tax( array( 'arc_industry', 'arc_solution' ) ) ) {
	// Taxonomy archive
	$breadcrumbs[] = array(
		'title' => esc_html( single_term_title( '', false ) ),
		'url'   => '',
	);
} elseif ( is_404() ) {
	// 404 page
	$breadcrumbs[] = array(
		'title' => __( '404 - Page Not Found', 'arcopan' ),
		'url'   => '',
	);
}

// Filter breadcrumbs
$breadcrumbs = apply_filters( 'arcopan_breadcrumbs', $breadcrumbs );

if ( empty( $breadcrumbs ) ) {
	return;
}
?>

<nav class="breadcrumb" aria-label="<?php _e( 'Breadcrumb', 'arcopan' ); ?>">
	<ol class="breadcrumb__list">
		<?php foreach ( $breadcrumbs as $index => $breadcrumb ) : ?>
			<li class="breadcrumb__item">
				<?php if ( ! empty( $breadcrumb['url'] ) ) : ?>
					<a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" class="breadcrumb__link">
						<?php echo esc_html( $breadcrumb['title'] ); ?>
					</a>
				<?php else : ?>
					<span class="breadcrumb__text" aria-current="page">
						<?php echo esc_html( $breadcrumb['title'] ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $index < count( $breadcrumbs ) - 1 ) : ?>
					<span class="breadcrumb__separator" aria-hidden="true">/</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>

	<?php
	// Build JSON-LD BreadcrumbList schema
	$schema_breadcrumbs = array();
	foreach ( $breadcrumbs as $index => $breadcrumb ) {
		$schema_breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $breadcrumb['title'],
			'item'     => ! empty( $breadcrumb['url'] ) ? $breadcrumb['url'] : esc_url( home_url() ),
		);
	}

	$breadcrumb_schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BreadcrumbList',
		'itemListElement'  => $schema_breadcrumbs,
	);
	?>

	<script type="application/ld+json">
		<?php echo wp_json_encode( $breadcrumb_schema ); ?>
	</script>
</nav>
