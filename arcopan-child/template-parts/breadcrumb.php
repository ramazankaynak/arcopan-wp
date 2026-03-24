<?php
/**
 * Breadcrumb Navigation Template Part
 *
 * Displays hierarchical breadcrumb navigation for SEO and UX
 *
 * @package ARCOPAN Child Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get breadcrumb data
$breadcrumbs = apply_filters( 'arcopan_breadcrumbs', arcopan_get_breadcrumbs() );

if ( empty( $breadcrumbs ) ) {
	return; // No breadcrumbs to display
}
?>

<nav class="arcopan-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'arcopan' ); ?>">
	<ol class="arcopan-breadcrumb__list">
		<?php foreach ( $breadcrumbs as $index => $breadcrumb ) : ?>
			<li class="arcopan-breadcrumb__item">
				<?php if ( isset( $breadcrumb['url'] ) && ! empty( $breadcrumb['url'] ) ) : ?>
					<a 
						href="<?php echo esc_url( $breadcrumb['url'] ); ?>" 
						class="arcopan-breadcrumb__link"
					>
						<?php echo esc_html( $breadcrumb['title'] ); ?>
					</a>
				<?php else : ?>
					<span class="arcopan-breadcrumb__text">
						<?php echo esc_html( $breadcrumb['title'] ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $index < count( $breadcrumbs ) - 1 ) : ?>
					<span class="arcopan-breadcrumb__separator">/</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>

<?php
/**
 * Helper function to generate breadcrumbs
 * Can be customized or moved to functions.php
 *
 * @return array Breadcrumb items array
 */
if ( ! function_exists( 'arcopan_get_breadcrumbs' ) ) {
	function arcopan_get_breadcrumbs() {
		$breadcrumbs = array();

		// Always add home
		$breadcrumbs[] = array(
			'title' => esc_html__( 'Ana Sayfa', 'arcopan' ),
			'url'   => esc_url( home_url( '/' ) ),
		);

		// Current page
		if ( is_home() || is_front_page() ) {
			// On home page, don't add more breadcrumbs
			return array_slice( $breadcrumbs, 0, 1 );
		} elseif ( is_singular() ) {
			// Singular post types
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj && 'post' !== $post_type ) {
				// Add post type archive link
				$archive_link = get_post_type_archive_link( $post_type );
				if ( $archive_link ) {
					$breadcrumbs[] = array(
						'title' => esc_html( $post_type_obj->labels->name ),
						'url'   => esc_url( $archive_link ),
					);
				}
			}

			// Add current page
			$breadcrumbs[] = array(
				'title' => esc_html( get_the_title() ),
				'url'   => '',
			);
		} elseif ( is_archive() ) {
			// Archive pages
			if ( is_category() || is_tag() ) {
				$breadcrumbs[] = array(
					'title' => esc_html__( 'Blog', 'arcopan' ),
					'url'   => esc_url( get_home_url() . '/blog' ),
				);
			}

			$breadcrumbs[] = array(
				'title' => esc_html( get_the_archive_title() ),
				'url'   => '',
			);
		} elseif ( is_404() ) {
			$breadcrumbs[] = array(
				'title' => esc_html__( '404 - Sayfa Bulunamadı', 'arcopan' ),
				'url'   => '',
			);
		}

		return $breadcrumbs;
	}
}
?>
