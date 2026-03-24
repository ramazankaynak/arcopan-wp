<?php
/**
 * Trust Bar Template Part
 *
 * Displays company trust indicators, certifications, and social proof
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get ACF options or post fields for trust indicators
$trust_items = apply_filters( 'arcopan_trust_items', array() );

// If no items provided via filter, get from theme options or post meta
if ( empty( $trust_items ) ) {
	$trust_items = get_field( 'trust_items' ) ?: array();
}
?>

<div class="arcopan-trust-bar">
	<div class="arcopan-trust-bar__container">
		<?php if ( ! empty( $trust_items ) ) : ?>
			<div class="arcopan-trust-bar__items">
				<?php foreach ( $trust_items as $item ) : ?>
					<div class="arcopan-trust-bar__item">
						<?php if ( isset( $item['icon'] ) && ! empty( $item['icon'] ) ) : ?>
							<div class="arcopan-trust-bar__icon">
								<?php
								if ( is_array( $item['icon'] ) && isset( $item['icon']['url'] ) ) {
									// ACF image field
									?>
									<img 
										src="<?php echo esc_url( $item['icon']['url'] ); ?>" 
										alt="<?php echo esc_attr( $item['icon']['alt'] ?? $item['label'] ?? '' ); ?>"
										class="arcopan-trust-bar__icon-image"
									>
									<?php
								} else {
									// Icon class or emoji string
									echo wp_kses_post( $item['icon'] );
								}
								?>
							</div>
						<?php endif; ?>

						<?php if ( isset( $item['label'] ) && ! empty( $item['label'] ) ) : ?>
							<div class="arcopan-trust-bar__label">
								<?php echo esc_html( $item['label'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( isset( $item['description'] ) && ! empty( $item['description'] ) ) : ?>
							<div class="arcopan-trust-bar__description">
								<?php echo wp_kses_post( $item['description'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="arcopan-trust-bar__empty">
				<p><?php esc_html_e( 'Güven göstergeleri henüz eklenmemiştir.', 'arcopan' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
