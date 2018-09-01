<?php
/**
 * The plugin main file.
 *
 * @link              https://profiles.wordpress.org/jaydeep-rami/
 * @since             1.0.0
 * @package           JD_Event_Listing
 *
 * @wordpress-plugin
 * Plugin Name:       Event Listing
 * Plugin URI:
 * Description:       List Events with integrate Google Calendar and Map.
 * Version:           1.0.0
 * Author:            Jaydeep Rami
 * Author URI:        https://profiles.wordpress.org/jaydeep-rami/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jd-event-listing
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'JD_Event_Listing' ) ) :

	/**
	 * Main JD_Event_Listing Class
	 *
	 * @since 1.0.0
	 */
	final class JD_Event_Listing {

		/** Singleton *************************************************************/

		/**
		 * JD_EVENT_LISTING Instance
		 *
		 * @since  1.0.0
		 * @access private
		 *
		 * @var    jd_event_listing() The one true JD_Event_Listing
		 */
		protected static $_instance;

		/**
		 * Main JD_Event_Listing Instance
		 *
		 * Ensures that only one instance of JD_Event_Listing exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since     1.0.0
		 * @access    public
		 *
		 * @static
		 * @see       jd_event_listing()
		 *
		 * @return    JD_Event_Listing
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * JD_Event_Listing Constructor.
		 */
		public function __construct() {
			// PHP version.
			if ( ! defined( 'JD_EVENT_LISTING_REQUIRED_PHP_VERSION' ) ) {
				define( 'JD_EVENT_LISTING_REQUIRED_PHP_VERSION', '5.4' );
			}

			// Bailout: Need minimum php version to load plugin.
			if ( function_exists( 'phpversion' ) && version_compare( JD_EVENT_LISTING_REQUIRED_PHP_VERSION, phpversion(), '>' ) ) {
				add_action( 'admin_notices', array( $this, 'minimum_phpversion_notice' ) );

				return;
			}

			$this->setup_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'jd_event_listing_loaded' );
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since  1.0.0
		 */
		private function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'init' ), 0 );

			// Set up localization on init Hook.
			add_action( 'init', array( $this, 'load_textdomain' ), 0 );

			// Register Event post type.
			add_action( 'init', array( $this, 'register_events_cpt' ) );

			// Call plugin custom archive page.
			add_filter( 'template_include', array( $this, 'jd_event_set_template' ), 99, 1 );
		}


		/**
		 * Init JD_Event_Listing when WordPress Initializes.
		 *
		 * @since 1.0.0
		 */
		public function init() {

			/**
			 * Fires before the JD_Event_Listing is initialized.
			 *
			 * @since 1.0.0
			 */
			do_action( 'before_jd_event_listing_init' );

			// Set up localization.
			$this->load_textdomain();

			/**
			 * Fire the action after JD_Event_Listing loads.
			 *
			 * @param object JD_Event_Listing.
			 *
			 * @since 1.0.0
			 */
			do_action( 'jd_event_listing_init', $this );

		}

		/**
		 * Setup plugin constants
		 *
		 * @since  1.0.0
		 * @access private
		 *
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'JD_EVENT_LISTING_VERSION' ) ) {
				define( 'JD_EVENT_LISTING_VERSION', '1.0.0' );
			}

			// Plugin Slug.
			if ( ! defined( 'JD_EVENT_LISTING_SLUG' ) ) {
				define( 'JD_EVENT_LISTING_SLUG', 'jd-event-listing' );
			}

			// Plugin Root File.
			if ( ! defined( 'JD_EVENT_LISTING_PLUGIN_FILE' ) ) {
				define( 'JD_EVENT_LISTING_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Folder Path.
			if ( ! defined( 'JD_EVENT_LISTING_PLUGIN_DIR' ) ) {
				define( 'JD_EVENT_LISTING_PLUGIN_DIR', plugin_dir_path( JD_EVENT_LISTING_PLUGIN_FILE ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'JD_EVENT_LISTING_PLUGIN_URL' ) ) {
				define( 'JD_EVENT_LISTING_PLUGIN_URL', plugin_dir_url( JD_EVENT_LISTING_PLUGIN_FILE ) );
			}

			if ( ! defined( 'JD_EVENT_LISTING_PLUGIN_BASENAME' ) ) {
				define( 'JD_EVENT_LISTING_PLUGIN_BASENAME', plugin_basename( JD_EVENT_LISTING_PLUGIN_FILE ) );
			}

		}

		/**
		 * Include required files
		 *
		 * @since  1.0.0
		 * @access private
		 *
		 * @return void
		 */
		private function includes() {

			require_once JD_EVENT_LISTING_PLUGIN_DIR . '/includes/public/class-jd-event-listing-public.php';
			require_once JD_EVENT_LISTING_PLUGIN_DIR . '/includes/jd-event-listing-helper.php';

			if ( is_admin() ) {
				require_once JD_EVENT_LISTING_PLUGIN_DIR . '/includes/admin/class-jd-event-listing-admin.php';

				require_once JD_EVENT_LISTING_PLUGIN_DIR . '/includes/admin/class-jd-event-listing-admin-settings.php';
			}
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool
		 */
		public function load_textdomain() {

			// Traditional WordPress plugin locale filter.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'jd-event-listing' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'jd-event-listing', $locale );

			// Setup paths to current locale file.
			$mofile_local = trailingslashit( plugin_dir_path( JD_EVENT_LISTING_PLUGIN_FILE ) . 'languages' ) . $mofile;

			if ( file_exists( $mofile_local ) ) {
				// Look in the /wp-content/plugins/jd-event-listing/languages/ folder.
				load_textdomain( 'jd-event-listing', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'jd-event-listing', false, trailingslashit( plugin_dir_path( JD_EVENT_LISTING_PLUGIN_FILE ) . 'languages' ) );
			}

			return false;
		}


		/**
		 *  Show minimum PHP version notice.
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function minimum_phpversion_notice() {
			// Bailout.
			if ( ! is_admin() ) {
				return;
			}

			$notice_desc = '<p><strong>' . __( 'Your site could be faster and more secure with a newer PHP version.', 'jd-event-listing' ) . '</strong></p>';
			$notice_desc .= '<p>' . __( 'Hey, we\'ve noticed that you\'re running an outdated version of PHP. Please upgrade your PHP version to use this Plugin.', 'jd-event-listing' ) . '</p>';

			echo sprintf( '<div class="notice notice-error">%1$s</div>', wp_kses_post( $notice_desc ) );
		}

		/**
		 * Register Custom Post type for Event.
		 *
		 * @since 1.0.0
		 */
		public function register_events_cpt() {
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
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-calendar-alt',
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
			);

			register_post_type( 'events', $args );
		}

		/**
		 * Load Event template.
		 *
		 * @since 1.0.0
		 *
		 * @param string $template Absolute path to template.
		 *
		 * @return string Absolute path to template
		 */
		public function jd_event_set_template( $template ) {

			$post_type = get_post_type();
			if ( 'events' === $post_type ) {
				$template = JD_EVENT_LISTING_PLUGIN_DIR . 'templates/archive-events.php';
			}

			if ( is_post_type_archive( 'events' ) ) {
				$template = JD_EVENT_LISTING_PLUGIN_DIR . 'templates/archive-events.php';
			}

			return $template;
		}

	}

endif; // End if class_exists check.


/**
 * Start JD_Event_Listing
 *
 * The main function responsible for returning the one true JD_Event_Listing instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $jd_event_listing = JD_Event_Listing(); ?>
 *
 * @since 1.0.0
 * @return object|JD_Event_Listing
 */
function jd_event_listing() {
	return JD_Event_Listing::instance();
}

jd_event_listing();
