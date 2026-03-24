<?php
/**
 * Trust Bar Template Part
 *
 * Displays company trust indicators with client logos and certifications
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get ACF options
$trust_logos = get_field( 'trust_logos', 'option' );
$cert_logos  = get_field( 'cert_logos', 'option' );

// Return early if no logos
if ( empty( $trust_logos ) && empty( $cert_logos ) ) {
	return;
}
?>

<section class="trust-bar">
	<div class="trust-bar__container">
		<div class="trust-bar__grid">
			<?php if ( ! empty( $trust_logos ) ) : ?>
				<div class="trust-bar__column trust-bar__column--clients">
					<h2 class="trust-bar__heading">
						<?php _e( 'Trusted by', 'arcopan' ); ?>
					</h2>

					<div class="trust-bar__logos">
						<?php foreach ( $trust_logos as $logo_id ) : ?>
							<?php
							$logo_alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
							$logo_url = wp_get_attachment_url( $logo_id );
							?>

							<figure class="trust-bar__logo">
								<img
									src="<?php echo esc_url( $logo_url ); ?>"
									alt="<?php echo esc_attr( $logo_alt ); ?>"
									class="trust-bar__logo-image"
									loading="lazy"
								>
							</figure>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $cert_logos ) ) : ?>
				<div class="trust-bar__column trust-bar__column--certifications">
					<h2 class="trust-bar__heading">
						<?php _e( 'Certifications', 'arcopan' ); ?>
					</h2>

					<div class="trust-bar__logos">
						<?php foreach ( $cert_logos as $cert_id ) : ?>
							<?php
							$cert_alt = get_post_meta( $cert_id, '_wp_attachment_image_alt', true );
							$cert_url = wp_get_attachment_url( $cert_id );
							?>

							<figure class="trust-bar__logo">
								<img
									src="<?php echo esc_url( $cert_url ); ?>"
									alt="<?php echo esc_attr( $cert_alt ); ?>"
									class="trust-bar__logo-image"
									loading="lazy"
								>
							</figure>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
