<?php
/**
 * Project Card Template Part
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get ACF fields
$project_client         = get_field( 'project_client' );
$project_industry       = get_field( 'project_industry' );
$project_year           = get_field( 'project_year' );
$project_gallery        = get_field( 'project_gallery' );
$project_highlight_stat = get_field( 'project_highlight_stat' );
?>

<div class="arcopan-project-card">
	<?php if ( $project_gallery ) : ?>
		<div class="arcopan-project-card__gallery">
			<?php foreach ( $project_gallery as $index => $image ) : ?>
				<figure 
					class="arcopan-project-card__gallery-item <?php echo 0 === $index ? 'is-featured' : ''; ?>"
				>
					<img 
						src="<?php echo esc_url( $image['url'] ); ?>" 
						alt="<?php echo esc_attr( $image['alt'] ); ?>"
						class="arcopan-project-card__gallery-image"
					>
				</figure>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="arcopan-project-card__content">
		<div class="arcopan-project-card__header">
			<?php if ( $project_client ) : ?>
				<div class="arcopan-project-card__client">
					<strong>Müşteri:</strong>
					<span><?php echo esc_html( $project_client ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $project_year ) : ?>
				<div class="arcopan-project-card__year">
					<strong>Yıl:</strong>
					<span><?php echo esc_html( $project_year ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $project_industry ) : ?>
			<div class="arcopan-project-card__industry">
				<span class="arcopan-project-card__industry-badge">
					<?php echo esc_html( $project_industry ); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( $project_highlight_stat ) : ?>
			<div class="arcopan-project-card__highlight">
				<div class="arcopan-project-card__highlight-content">
					<?php echo wp_kses_post( $project_highlight_stat ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="arcopan-project-card__footer">
			<?php
			// Get post title and link
			$post_id    = get_the_ID();
			$post_title = get_the_title( $post_id );
			$post_url   = get_permalink( $post_id );
			?>
			<a 
				href="<?php echo esc_url( $post_url ); ?>" 
				class="arcopan-project-card__link"
			>
				<?php echo esc_html( $post_title ); ?>
				<span class="arcopan-project-card__link-arrow">→</span>
			</a>
		</div>
	</div>
</div>
