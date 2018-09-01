<?php
/**
 * The template for displaying lists of events.
 *
 * @package    JD_Event_Listing
 * @subpackage JD_Event_Listing/templates
 * @since      1.0.0
 */
get_header(); ?>

<div class="wrap">

	<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<h1><?php esc_html_e( 'Event Listing', 'jd-event-listing' ); ?></h1>
		</header><!-- .page-header -->
	<?php endif; ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			$args = array(
				'post_type'  => 'events',
				'meta_key'   => 'jd_event_start_date',
				'orderby'    => 'meta_value',
				'order'      => 'ASC',
				'paged'      => $paged,
				'meta_query' => array(
					array(
						'key'     => 'jd_event_start_date',
						'type'    => 'numeric',
						'compare' => '>=',
					),
				),
			);

			$wp_query = new WP_Query( $args );

			if ( have_posts() ) :

				while ( have_posts() ) :
					the_post();
					jd_event_get_template_part( 'event-listing' );

				endwhile;

			else :

				jd_event_get_template_part( 'no-event' );

			endif;

			?>
			<div class="jd_events_pagination">
				<?php
				$big = 999999999;
				echo paginate_links( array(
					'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'  => '?paged=%#%',
					'current' => max( 1, get_query_var( 'paged' ) ),
					'total'   => $wp_query->max_num_pages,
				) );
				?>
			</div>
			<?php
			// Reset wp_query.
			wp_reset_postdata();
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer(); ?>
