<?php
/**
 * Product Card Template Part
 *
 * Reusable product card component with ACF fields and schema markup
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get post ID from args or use current
$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();

// Setup post data
setup_postdata( $post_id );

// Get ACF fields
$hero_image        = get_field( 'hero_image', $post_id );
$product_intro     = get_field( 'product_intro', $post_id );
$temperature_range = get_field( 'temperature_range', $post_id );
$datasheet_pdf     = get_field( 'datasheet_pdf', $post_id );

// Get taxonomies
$categories = get_the_terms( $post_id, 'arc_product_cat' );
$category   = ( $categories && ! is_wp_error( $categories ) ) ? $categories[0] : null;

// Build product schema
$product_schema = array(
	'@context' => 'https://schema.org/',
	'@type'    => 'Product',
	'name'     => get_the_title( $post_id ),
	'url'      => esc_url( get_permalink( $post_id ) ),
);

if ( $hero_image ) {
	$product_schema['image'] = esc_url( $hero_image['url'] );
}
?>

<article class="product-card" itemscope itemtype="https://schema.org/Product">
	<?php if ( $hero_image ) : ?>
		<div class="product-card__image">
			<?php echo wp_get_attachment_image( $hero_image['ID'], 'medium', false, array( 'class' => 'product-card__image-element' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="product-card__content">
		<?php if ( $category ) : ?>
			<span class="product-card__category">
				<?php echo esc_html( $category->name ); ?>
			</span>
		<?php endif; ?>

		<h3 class="product-card__title">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
				<?php echo esc_html( get_the_title( $post_id ) ); ?>
			</a>
		</h3>

		<?php if ( $product_intro ) : ?>
			<p class="product-card__intro">
				<?php echo esc_html( wp_trim_words( $product_intro, 20, '...' ) ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $temperature_range ) : ?>
			<div class="product-card__temperature-badge">
				<?php echo esc_html( $temperature_range ); ?>
			</div>
		<?php endif; ?>

		<div class="product-card__actions">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="arcopan-btn arcopan-btn--primary arcopan-btn--sm">
				<?php _e( 'View Product', 'arcopan' ); ?>
			</a>

			<?php if ( $datasheet_pdf ) : ?>
				<a href="<?php echo esc_url( $datasheet_pdf['url'] ); ?>" class="arcopan-btn arcopan-btn--secondary arcopan-btn--sm" download>
					<?php _e( 'Datasheet PDF', 'arcopan' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<script type="application/ld+json">
		<?php echo wp_json_encode( $product_schema ); ?>
	</script>
</article>

<?php
wp_reset_postdata();
