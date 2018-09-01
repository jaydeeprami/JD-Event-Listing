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

		// Register Event post type.
		add_action( 'init', array( $this, 'register_jd_events' ), 10 );

		// Register Event Meta box.
		add_action( 'add_meta_boxes', array( $this, 'register_event_meta_boxes' ) );
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
				'jquery',
				'jquery-ui-datepicker',
			), JD_EVENT_LISTING_VERSION, false );

			wp_enqueue_script( JD_EVENT_LISTING_SLUG );

			$api_key = 'AIzaSyA41O0v8uA8x89hWFe0_oB6oAZ2dTa3INg';
			wp_enqueue_script( 'google-maps-native', "http://maps.googleapis.com/maps/api/js?key=" . $api_key );

			$jd_event_lat = get_post_meta( $post_id, 'jd_event_lat', true );
			$jd_event_lat = ! empty( $jd_event_lat ) ? $jd_event_lat : '';

			$jd_event_long = get_post_meta( $post_id, 'jd_event_long', true );
			$jd_event_long = ! empty( $jd_event_long ) ? $jd_event_long : '';

			wp_localize_script( 'jd_lat_long', 'getLatLong', array(
				'lat'  => $jd_event_lat,
				'long' => $jd_event_long,
			) );
		}
	}

	/**
	 * Register Custom Post type for Event.
	 *
	 * @since 1.0.0
	 */
	public function register_jd_events() {
		$labels = array(
			'name'               => _x( 'Events', 'event type general name', 'jd-event-list' ),
			'singular_name'      => _x( 'Event', 'event type singular name', 'jd-event-list' ),
			'menu_name'          => _x( 'Events', 'admin menu', 'jd-event-list' ),
			'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'jd-event-list' ),
			'add_new'            => _x( 'Add New', 'event', 'jd-event-list' ),
			'add_new_item'       => __( 'Add New Event', 'jd-event-list' ),
			'new_item'           => __( 'New Event', 'jd-event-list' ),
			'edit_item'          => __( 'Edit Event', 'jd-event-list' ),
			'view_item'          => __( 'View Event', 'jd-event-list' ),
			'all_items'          => __( 'All Events', 'jd-event-list' ),
			'search_items'       => __( 'Search Events', 'jd-event-list' ),
			'parent_item_colon'  => __( 'Parent Events:', 'jd-event-list' ),
			'not_found'          => __( 'No events found.', 'jd-event-list' ),
			'not_found_in_trash' => __( 'No events found in Trash.', 'jd-event-list' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Event Listing', 'jd-event-list' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'events' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-calendar-alt',
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( 'jd_event', $args );
	}

	/**
	 * Register Event meta box in Event custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_event_meta_boxes() {

		// Event Details.
		add_meta_box( 'jd_event_details', 'Event Details', array( $this, 'callback_event_details_meta_box' ), 'jd_event', 'normal', 'default' );
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
		$show_google_map = get_post_meta( $post_id, 'show_google_map', true );

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
					<input type="checkbox" name="show_google_map" value="" />
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
}

new JD_Event_Listing_Admin();
