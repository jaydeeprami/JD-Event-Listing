<?php

/**
 * The admin settings.
 *
 * @package    JD_Event_Listing
 * @subpackage JD_Event_Listing/admin
 */
class JD_Eevent_Listing_Admin_Setting {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var $options
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_page() {
		// This page will be under "Settings".
		add_options_page( 'Event Setting', 'Event Setting', 'manage_options', 'jd-event-settings', array( $this, 'create_admin_page' ) );
	}

	/**
	 * Options page callback
	 *
	 * @since 1.0.0
	 */
	public function create_admin_page() {
		// Set class property.
		$this->options = get_option( 'jd_event_listing' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Event Setting', 'jd-event-listing' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields.
				settings_fields( 'jd_event_listing' );
				do_settings_sections( 'jd-event-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 *
	 * @since 1.0.0
	 */
	public function page_init() {
		register_setting( 'jd_event_listing', // Option group.
			'jd_event_listing', // Option name.
			array( $this, 'sanitize' ) // Sanitize.
		);

		add_settings_section( 'setting_section_id', // ID.
			'JD Event Settings', // Title.
			array( $this, 'print_section_info' ), // Callback.
			'jd-event-setting-admin' // Page.
		);

		add_settings_field( 'jd_event_google_map_api', 'Google Map API', array( $this, 'google_map_api_callback' ), 'jd-event-setting-admin', 'setting_section_id' );
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array $new_input
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['jd_event_google_map_api'] ) ) {
			$new_input['jd_event_google_map_api'] = sanitize_text_field( $input['jd_event_google_map_api'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 *
	 * @since 1.0.0
	 */
	public function print_section_info() {
		esc_html_e( 'Enter your settings below:', 'jd-event-listing' );
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 */
	public function google_map_api_callback() {
		printf( '<input type="text" id="title" name="jd_event_listing[jd_event_google_map_api]" value="%s" />', isset( $this->options['jd_event_google_map_api'] ) ? esc_attr( $this->options['jd_event_google_map_api'] ) : '' );
	}
}

new JD_Eevent_Listing_Admin_Setting();
