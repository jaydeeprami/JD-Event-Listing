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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
		wp_register_style( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/css/jd-event-listing-admin' . $suffix . '.css', array(), JD_EVENT_LISTING_VERSION, 'all' );
		wp_enqueue_style( JD_EVENT_LISTING_SLUG );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_scripts() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueuing give fee recovery admin JS script.
		wp_register_script( JD_EVENT_LISTING_SLUG, JD_EVENT_LISTING_PLUGIN_URL . 'assets/js/jd-event-listing-admin' . $suffix . '.js', array( 'jQuery' ), JD_EVENT_LISTING_VERSION, false );
		wp_enqueue_script( JD_EVENT_LISTING_SLUG );

	}
}
