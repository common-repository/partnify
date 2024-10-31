<?php
/**
 * Plugin Name: Partnify
 * Plugin URI: https://www.partnify.com/wordpress-partner-plugin/
 * Description: Partnify for WordPress by Partnify. Inserts via a widget selectable display ads to your WordPress site to earn income.
 * Version: 1.0.0
 * Author: Partnify
 * Author URI: https://partnify.com
 *
 * Text Domain: partnify
 *
 * @package Partnify
 * @category Core
 * @author 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Partnify' ) ) :

	/**
	 * Main Partnify Class (singleton).
	 *
	 * @since 1.0.0
	 */
	final class Partnify {

		/**
		 * Assets Path.
		 *
		 * @var string
		 */
		public $assets_path;

		/**
		 * Partnify version.
		 *
		 * @var string
		 */
		public $version = '1.0.0';
		/**
		 * The single instance of the class.
		 *
		 * @var Partnify
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->define_constants();
			$this->assets_path = plugin_dir_url( PARTNIFY_PLUGIN_FILE ) . 'assets/';
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Define Constants.
		 */
		private function define_constants() {
			$this->define( 'PARTNIFY_PLUGIN_FILE', __FILE__ );
			$this->define( 'PARTNIFY_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'PARTNIFY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'PARTNIFY_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define( 'PARTNIFY_VERSION', $this->version );
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'admin_menu', 'partnify_menu' );
			// add_action( 'admin_init', 'partnify_save_settings' );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ) );

			// Save Settings.
			add_action('wp_ajax_partnify_save_settings', 'partnify_save_settings_callback');
			add_action('wp_ajax_nopriv_partnify_save_settings', 'partnify_save_settings_callback');

			add_action('wp_ajax_partnify_get_campaign', 'partnify_get_campaign_callback');
			add_action('wp_ajax_nopriv_partnify_get_campaign', 'partnify_get_campaign_callback');

			add_action('wp_ajax_partnify_get_campaign_assets', 'partnify_get_campaign_assets_callback');
			add_action('wp_ajax_nopriv_partnify_get_campaign_assets', 'partnify_get_campaign_assets_callback');
					
		}

		function add_action_links ( $links ) {
			if ( isset( $links['deactivate'] ) ) {
				$deactivate_link = $links['deactivate'];
				unset($links['deactivate']);
				$links['settings'] = '<a href="' . admin_url( 'admin.php?page=partnify-settings' ) . '">Settings</a>';
				$links['deactivate'] = $deactivate_link;
			}
			return $links; 
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name  Name of constant.
		 * @param  string $value Value of constant.
		 * @return void
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @return void
		 */
		function includes() {
			include_once sprintf( '%s/inc/helpers.php', PARTNIFY_ABSPATH );
			include_once sprintf( '%s/inc/functions.php', PARTNIFY_ABSPATH );
			include_once sprintf( '%s/inc/frontend-assets.php', PARTNIFY_ABSPATH );

			include_once sprintf( '%s/inc/widgets/class-partnify-widget.php', PARTNIFY_ABSPATH );



			if ( is_admin() ) {
				include_once sprintf( '%s/inc/admin/admin-helpers.php', PARTNIFY_ABSPATH );
				include_once sprintf( '%s/inc/admin/admin-functions.php', PARTNIFY_ABSPATH );
				include_once sprintf( '%s/inc/admin/admin-assets.php', PARTNIFY_ABSPATH );
			}
		}
	}
endif;
/**
 * Main instance.
 *
 * Returns the main instance of PARTNIFY to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return PARTNIFY
 */
function PARTNIFY() {
	return PARTNIFY::instance();
}


PARTNIFY();
