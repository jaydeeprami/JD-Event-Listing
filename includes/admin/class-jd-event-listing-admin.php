<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    JD_Event_Listing
 * @subpackage JD_Event_Listing/admin
 */
class JD_Event_Listing_Admin {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Enqueue Script and Style for Admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );

		// Register Event Meta box.
		add_action( 'add_meta_boxes', array( $this, 'register_event_meta_boxes' ) );

		// Save Event details.
		add_action( 'save_post', array( $this, 'save_events_details' ), 10, 2 );

		// Show admin notice.
		add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_styles() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueuing give fee recovery admin side css.
		wp_register_style( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/css/jd-event-listing-admin' . $suffix . '.css', array( 'jquery-ui-style' ), JD_EVENT_LISTING_VERSION, 'all' );
		wp_enqueue_style( JD_EVENT_LISTING_SLUG );

		wp_enqueue_style( 'datetimepicker-style',JD_EVENT_LISTING_PLUGIN_URL . 'assets/css/jquery.datetimepicker.min.css' );

		// Enqueue jQuery UI.
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param string $hook Page hook.
	 */
	public function enqueue_scripts( $hook ) {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
			global $post;
			$post_id = $post->ID;

			// Enqueuing give fee recovery admin JS script.
			wp_register_script( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/js/jd-event-listing-admin' . $suffix . '.js', array(
				'jquery'
			), JD_EVENT_LISTING_VERSION, false );

			wp_enqueue_script( JD_EVENT_LISTING_SLUG );

			wp_enqueue_script( 'datetimepicker-script',JD_EVENT_LISTING_PLUGIN_URL . 'assets/js/jquery.datetimepicker.full.min.js' );

			$jd_event_listing = get_option( 'jd_event_listing' );
			$api_key          = ( isset( $jd_event_listing['jd_event_google_map_api'] ) && ! empty( $jd_event_listing['jd_event_google_map_api'] ) ) ? $jd_event_listing['jd_event_google_map_api'] : '';

			wp_enqueue_script( 'google-maps-native', "http://maps.googleapis.com/maps/api/js?key=" . $api_key );
			$jd_event_lat = get_post_meta( $post_id, 'jd_event_lat', true );
			$jd_event_lat = ! empty( $jd_event_lat ) ? $jd_event_lat : '42.698334';

			$jd_event_long = get_post_meta( $post_id, 'jd_event_long', true );
			$jd_event_long = ! empty( $jd_event_long ) ? $jd_event_long : '23.319941';

			$show_google_map = get_post_meta( $post_id, 'jd_event_show_google_map', true );

			wp_localize_script( JD_EVENT_LISTING_SLUG, 'getLatLong', array(
				'lat'                => $jd_event_lat,
				'long'               => $jd_event_long,
				'is_map_show'        => $show_google_map,
				'geo_code_error_msg' => __( 'Geocode was not successful for the following reason:', 'jd-event-list' ),
			) );
		}
	}



	/**
	 * Register Event meta box in Event custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_event_meta_boxes() {

		// Event Details.
		add_meta_box( 'jd_event_details', 'Event Details', array( $this, 'callback_event_details_meta_box' ), 'events', 'normal', 'default' );
	}

	/**
	 * Output the HTML for the Event details.
	 *
	 * @since 1.0.0
	 */
	public function callback_event_details_meta_box() {
		global $post;

		$post_id = $post->ID;

		// Event Start date.
		$jd_event_start_date = get_post_meta( $post_id, 'jd_event_start_date', true );
		$jd_event_start_date = ! empty( $jd_event_start_date ) ? $jd_event_start_date : '';

		// Event End date.
		$jd_event_end_date = get_post_meta( $post_id, 'jd_event_end_date', true );
		$jd_event_end_date = ! empty( $jd_event_end_date ) ? $jd_event_end_date : '';

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

		wp_nonce_field( 'jd_event_details', 'event_details' );
		?>
		<div id="jd_event_date_section">
			<p>
				<label for="jd_event_date">
					<strong><?php esc_html_e( 'Start date: ', 'jd-event-list' ); ?></strong>
					<input placeholder="<?php esc_html_e( 'Pick a start date', 'jd-event-list' ); ?>" type="text" id="jd_event_start_date" name="jd_event_start_date" value="<?php echo esc_attr( $jd_event_start_date ); ?>" />

					<strong><?php esc_html_e( 'End date: ', 'jd-event-list' ); ?></strong>
					<input type="text" placeholder="<?php esc_html_e( 'Pick a end date', 'jd-event-list' ); ?>" id="jd_event_end_date" name="jd_event_end_date" value="<?php echo esc_attr( $jd_event_end_date ); ?>" />
				</label>
			</p>
		</div>

		<div id="jd_event_url_section">
			<p>
				<label>
					<strong><?php esc_html_e( 'URL', 'jd-event-listing' ); ?></strong>
					<input type="url" placeholder="<?php esc_html_e( 'Enter Event URL', 'jd-event-listing' ); ?>" name="jd_event_url" id="jd_event_url" value="<?php echo esc_attr( $jd_event_url ); ?>">
				</label>
			</p>
		</div>

		<div id="jd_event_location_section">
			<p>
				<label>
					<strong><?php esc_html_e( 'Show Google Map', 'jd-event-listing' ); ?></strong>
					<input type="checkbox" id="jd_event_show_google_map" name="jd_event_show_google_map" <?php checked( 1, $show_google_map ); ?> value="false" />
				</label>
			</p>

			<p>
				<label>
					<strong><?php esc_html_e( 'Location', 'jd-event-listing' ); ?></strong>
					<input type="text" placeholder="<?php esc_html_e( 'Enter Event Location', 'jd-event-listing' ); ?>" name="jd_event_address" id="jd_event_address" value="<?php echo esc_attr( $jd_event_address ); ?>" />
				</label>
			</p>
			<div class="js_event_location_map" id="js_event_location_map"></div>
			<input type="hidden" name="jd_event_lat" id="jd_event_lat" value="<?php echo esc_attr( $jd_event_lat ); ?>">
			<input type="hidden" name="jd_event_long" id="jd_event_long" value="<?php echo esc_attr( $jd_event_long ); ?>">
		</div>

		<?php
	}

	/**
	 * Save Event details.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 *
	 * @return mixed
	 */
	public function save_events_details( $post_id, $post ) {

		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Return if post type not match.
		if ( 'events' !== $post->post_type ) {
			return $post_id;
		}

		// Safe global post array.
		$global_post = filter_input_array( INPUT_POST );

		// Return if not set.
		if ( ! isset( $global_post ) ) {
			return $post_id;
		}

		// Check nonce.
		if ( ! wp_verify_nonce( $global_post['event_details'], 'jd_event_details' ) ) {
			return $post_id;
		}

		// Event Start date.
		$jd_event_start_date = ! empty( $global_post['jd_event_start_date'] ) ? sanitize_text_field( $global_post['jd_event_start_date'] ) : date( 'd/m/y' );
		update_post_meta( $post_id, 'jd_event_start_date', $jd_event_start_date );

		// Event End date.
		$jd_event_end_date = ! empty( $global_post['jd_event_end_date'] ) ? sanitize_text_field( $global_post['jd_event_end_date'] ) : date( 'd/m/y' );
		update_post_meta( $post_id, 'jd_event_end_date', $jd_event_end_date );

		// Event URL.
		$jd_event_url = ! empty( $global_post['jd_event_url'] ) ? esc_url( $global_post['jd_event_url'] ) : '';
		update_post_meta( $post_id, 'jd_event_url', $jd_event_url );

		// Show Google Map or not.
		$jd_event_show_google_map = isset( $global_post['jd_event_show_google_map'] ) ? true : false;
		update_post_meta( $post_id, 'jd_event_show_google_map', $jd_event_show_google_map );

		// Event location address.
		$jd_event_address = ! empty( $global_post['jd_event_address'] ) ? sanitize_text_field( $global_post['jd_event_address'] ) : '';
		update_post_meta( $post_id, 'jd_event_address', $jd_event_address );

		// Save lat and long only if google map show.
		if ( $jd_event_show_google_map ) {
			$jd_event_lat  = ! empty( $global_post['jd_event_lat'] ) ? sanitize_text_field( $global_post['jd_event_lat'] ) : '42.698334';
			$jd_event_long = ! empty( $global_post['jd_event_long'] ) ? sanitize_text_field( $global_post['jd_event_long'] ) : '23.319941';

			update_post_meta( $post_id, 'jd_event_lat', $jd_event_lat );
			update_post_meta( $post_id, 'jd_event_long', $jd_event_long );
		}
	}

	/**
	 * Show admin notice.
	 *
	 * Display admin notice if Google map api key not set.
	 *
	 * @since 1.0.0
	 */
	public function display_admin_notice() {

		$class            = 'notice notice-error';
		$jd_event_listing = get_option( 'jd_event_listing' );
		$api_key          = ( isset( $jd_event_listing['jd_event_google_map_api'] ) && ! empty( $jd_event_listing['jd_event_google_map_api'] ) ) ? $jd_event_listing['jd_event_google_map_api'] : '';

		$message = sprintf( '<h4>%1$s</h4> 
				<p><a href="%3$s">%2$s</a> %4$s</p>',
			__( 'Enter a Google Maps API key', 'jd-event-listing' ),
			__( 'Please enter your api key', 'jd-event-listing' ),
			esc_url( admin_url( 'options-general.php?page=jd-event-settings' ) ),
			__( 'to use Google map in your site.', 'jd-event-listing' )
		);

		if ( empty( $api_key ) ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message) );
		}
	}
}

new JD_Event_Listing_Admin();
