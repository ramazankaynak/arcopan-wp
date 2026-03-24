<?php
/**
 * Project Card Template Part
 *
 * Reusable project card component with ACF fields
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
$headline_metric    = get_field( 'headline_metric', $post_id );
$project_location   = get_field( 'project_location', $post_id );
$year_completed     = get_field( 'year_completed', $post_id );
$gallery            = get_field( 'gallery', $post_id );
$project_scope      = get_field( 'project_scope', $post_id );

// Get taxonomy
$industries = get_the_terms( $post_id, 'arc_industry' );
$industry   = ( $industries && ! is_wp_error( $industries ) ) ? $industries[0] : null;
?>

<article class="project-card">
	<?php if ( $gallery ) : ?>
		<div class="project-card__image-wrapper">
			<?php
			$featured_image = $gallery[0];
			echo wp_get_attachment_image( $featured_image['ID'], 'medium', false, array( 'class' => 'project-card__image' ) );
			?>

			<div class="project-card__overlay">
				<div class="project-card__overlay-content">
					<?php if ( $year_completed ) : ?>
						<span class="project-card__year">
							<?php echo esc_html( $year_completed ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $project_location ) : ?>
						<span class="project-card__location">
							<?php echo esc_html( $project_location ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="project-card__content">
		<?php if ( $industry ) : ?>
			<span class="project-card__industry-badge">
				<?php echo esc_html( $industry->name ); ?>
			</span>
		<?php endif; ?>

		<h3 class="project-card__title">
			<?php echo esc_html( get_the_title( $post_id ) ); ?>
		</h3>

		<?php if ( $headline_metric ) : ?>
			<div class="project-card__metric">
				<?php echo esc_html( $headline_metric ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $project_scope ) : ?>
			<p class="project-card__scope">
				<?php echo esc_html( wp_trim_words( $project_scope, 17, '...' ) ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="arcopan-btn arcopan-btn--primary arcopan-btn--sm">
			<?php _e( 'View Project', 'arcopan' ); ?>
		</a>
	</div>
</article>

<?php
wp_reset_postdata();
