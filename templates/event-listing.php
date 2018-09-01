<?php
/**
 * Event Listing.
 */

$post_id = get_the_ID();
$post    = get_post( $post_id );

$event_title = isset( $post->post_title ) ? $post->post_title : '';
$event_link  = get_post_permalink( $post_id );

// Event Start date.
$jd_event_start_date = get_post_meta( $post_id, 'jd_event_start_date', true );
$jd_event_start_date = ! empty( $jd_event_start_date ) ? $jd_event_start_date : '';

// Event End date.
$jd_event_end_date = get_post_meta( $post_id, 'jd_event_end_date', true );
$jd_event_end_date = ! empty( $jd_event_end_date ) ? $jd_event_end_date : '';

$event_date = '';
if ( $jd_event_start_date === $jd_event_end_date ) {
	$event_date = $jd_event_start_date;
} else {
	$event_date = "{$jd_event_start_date} - {$jd_event_end_date}";
}

// Location event options.
$show_google_map = get_post_meta( $post_id, 'jd_event_show_google_map', true );

// Event URL.
$jd_event_url = get_post_meta( $post_id, 'jd_event_url', true );
$jd_event_url = ! empty( $jd_event_url ) ? $jd_event_url : '';

// Latitude.
$jd_event_lat = get_post_meta( $post_id, 'jd_event_lat', true );
$jd_event_lat = ! empty( $jd_event_lat ) ? $jd_event_lat : '';

// Longitude.
$jd_event_long = get_post_meta( $post_id, 'jd_event_long', true );
$jd_event_long = ! empty( $jd_event_long ) ? $jd_event_long : '';

// Event address.
$jd_event_address = get_post_meta( $post_id, 'jd_event_address', true );
$jd_event_address = ! empty( $jd_event_address ) ? $jd_event_address : '';

$jd_event_listing = get_option( 'jd_event_listing' );
$api_key          = ( isset( $jd_event_listing['jd_event_google_map_api'] ) && ! empty( $jd_event_listing['jd_event_google_map_api'] ) ) ? $jd_event_listing['jd_event_google_map_api'] : '';
?>
<article id="<?php echo absint( $post_id ); ?>" class="jd-event-listing">

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php echo esc_url( $event_link ); ?>" rel="bookmark"><?php echo esc_html( $event_title ); ?></a>
		</h2>

		<?php if ( ! is_single() ) : ?>
			<div class="post-thumbnail">
				<?php
				$event_image = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

				// Set placeholder image if empty.
				if ( empty( $event_image ) ) {
					$event_image = JD_EVENT_LISTING_PLUGIN_URL . '/assets/images/placeholder-image.jpg';
				}
				?>
				<a href="<?php echo esc_url( $event_link ); ?>">
					<img width="150" height="150" src="<?php echo esc_url( $event_image ); ?>" alt="<?php echo esc_attr( $event_title ); ?> " />
				</a>
			</div><!-- .post-thumbnail -->
		<?php endif; ?>

		<div class="jd_event_details">
			<div class="event-detail">
				<i class="event_date" aria-hidden="true"><?php esc_html_e( 'Event Date: ', 'jd-event-listing' ); ?></i>
				<?php echo esc_html( $event_date ); ?>
			</div>

			<div class="event-detail">
				<i class="event_url"><?php esc_html_e( 'Event URL: ', 'jd-event-listing' ); ?></i>
				<a target="_blank" href="<?php echo esc_url( $jd_event_url ); ?>">
					<?php echo esc_url( $jd_event_url ); ?>
				</a>
			</div>

			<div class="event-detail">
				<i class="event_address"><?php esc_html_e( 'Event Address: ', 'jd-event-listing' ); ?></i>
				<span class="jd_event_address"><?php echo esc_html( $jd_event_address ); ?></span>
			</div>

			<?php if ( ! empty( $api_key ) ) : ?>
				<div class="event-detail">
					<button class="show_google_map"><?php esc_html_e( 'Show Google Map', 'jd-event-listing' ); ?></button>
				</div>
			<?php endif; ?>
		</div>
	</header><!-- .entry-header -->

	<div id="jd-event-google-map-modal" title="<?php echo esc_html( $jd_event_address ); ?>">
		<div style="width: 100%; height: 100%;" class="js_event_location_map" id="js_event_location_map_<?php echo absint( $post_id ); ?>"></div>
	</div>

</article><!-- #post-## -->

