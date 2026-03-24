<?php
/**
 * Product Card Template Part
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get ACF fields
$hero_image           = get_field( 'hero_image' );
$product_intro        = get_field( 'product_intro' );
$spec_table           = get_field( 'spec_table' );
$datasheet_pdf        = get_field( 'datasheet_pdf' );
$temperature_range    = get_field( 'temperature_range' );
$certifications       = get_field( 'certifications' );
$related_products     = get_field( 'related_products' );
?>

<div class="arcopan-product-card">
	<?php if ( $hero_image ) : ?>
		<div class="arcopan-product-card__image">
			<img 
				src="<?php echo esc_url( $hero_image['url'] ); ?>" 
				alt="<?php echo esc_attr( $hero_image['alt'] ); ?>"
				class="arcopan-product-card__image-element"
			>
		</div>
	<?php endif; ?>

	<div class="arcopan-product-card__content">
		<?php if ( $product_intro ) : ?>
			<div class="arcopan-product-card__intro">
				<?php echo wp_kses_post( $product_intro ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $spec_table ) : ?>
			<div class="arcopan-product-card__specs">
				<h3 class="arcopan-product-card__specs-title">Özellikler</h3>
				<table class="arcopan-product-card__specs-table">
					<tbody>
						<?php foreach ( $spec_table as $spec ) : ?>
							<tr class="arcopan-product-card__specs-row">
								<td class="arcopan-product-card__specs-label">
									<?php echo esc_html( $spec['label'] ); ?>
								</td>
								<td class="arcopan-product-card__specs-value">
									<?php echo esc_html( $spec['value'] ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

		<?php if ( $temperature_range ) : ?>
			<div class="arcopan-product-card__temperature">
				<strong>Sıcaklık Aralığı:</strong>
				<span><?php echo esc_html( $temperature_range ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $certifications ) : ?>
			<div class="arcopan-product-card__certifications">
				<h4 class="arcopan-product-card__certifications-title">Sertifikalar</h4>
				<ul class="arcopan-product-card__certifications-list">
					<?php foreach ( $certifications as $cert ) : ?>
						<li class="arcopan-product-card__certifications-item">
							<?php echo esc_html( $cert ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( $datasheet_pdf ) : ?>
			<div class="arcopan-product-card__datasheet">
				<a 
					href="<?php echo esc_url( $datasheet_pdf['url'] ); ?>" 
					class="arcopan-product-card__datasheet-link"
					target="_blank"
					rel="noopener noreferrer"
				>
					<span class="arcopan-product-card__datasheet-icon">📄</span>
					<?php echo esc_html( $datasheet_pdf['title'] ?? 'Teknik Şartname' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $related_products ) : ?>
			<div class="arcopan-product-card__related">
				<h4 class="arcopan-product-card__related-title">İlişkili Ürünler</h4>
				<div class="arcopan-product-card__related-list">
					<?php foreach ( $related_products as $related_product ) : ?>
						<div class="arcopan-product-card__related-item">
							<a href="<?php echo esc_url( get_permalink( $related_product ) ); ?>">
								<?php echo esc_html( get_the_title( $related_product ) ); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
