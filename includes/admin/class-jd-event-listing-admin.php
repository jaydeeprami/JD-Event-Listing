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

		// Register Event post type.
		add_action( 'init', array( $this, 'register_jd_events' ), 10 );
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
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
		);

		register_post_type( 'jd_event', $args );
	}
}

new JD_Event_Listing_Admin();
