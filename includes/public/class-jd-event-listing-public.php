<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    JD_Event_Listing
 * @subpackage JD_Event_Listing/public
 */
class JD_Event_Listing_Public {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		// Enqueue Script for Public.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Enqueue Styles for Public.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_scripts() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Registering the recovery plugin JS script.
		wp_register_script( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/js/jd-event-listing-frontend' . $suffix . '.js', array(
			'jquery',
			'jquery-ui-dialog',
		), JD_EVENT_LISTING_VERSION, false );
		wp_enqueue_script( JD_EVENT_LISTING_SLUG );

		$jd_event_listing = get_option( 'jd_event_listing' );
		$api_key          = ( isset( $jd_event_listing['jd_event_google_map_api'] ) && ! empty( $jd_event_listing['jd_event_google_map_api'] ) ) ? $jd_event_listing['jd_event_google_map_api'] : '';

		wp_enqueue_script( 'google-maps-native', 'http://maps.googleapis.com/maps/api/js?key=' . $api_key );

		wp_enqueue_script( 'google-calendar', 'https://apis.google.com/js/api.js' );

		wp_localize_script( JD_EVENT_LISTING_SLUG, 'getLatLong', array(
			'lat'                => '42.698334',
			'long'               => '23.319941',
			'geo_code_error_msg' => __( 'Geocode was not successful for the following reason:', 'jd-event-list' ),
		) );

		wp_localize_script( JD_EVENT_LISTING_SLUG, 'JDCalendarEventObject', array(
			'client_id'        => '774611125158-fn2gu7cstpl8shtqjqshjv7oeb4d9r7q.apps.googleusercontent.com',
			'api_key'          => 'AIzaSyCGBr6J-qneiYJXHDo93xo9IZD6LsKI-tU',
			'event_inserted'   => __( 'Event inserted successfully.', 'jd-event-list' ),
			'event_failed'     => __( 'Failed:', 'jd-event-list' ),
			'google_authorize' => __( 'Please authorize before create event.', 'jd-event-list' ),
		) );

	}


	/**
	 * Register the Style for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_styles() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueuing give fee recovery frontend side css.
		wp_register_style( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/css/jd-event-listing-frontend' . $suffix . '.css', array(), JD_EVENT_LISTING_VERSION, 'all' );
		wp_enqueue_style( JD_EVENT_LISTING_SLUG );

		wp_enqueue_style( 'jd-event-jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

	}
}

new JD_Event_Listing_Public();
