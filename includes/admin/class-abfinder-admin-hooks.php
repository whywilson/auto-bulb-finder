<?php
/**
 * Admin End Hooks
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Includes\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ABFinder_Admin_Hooks' ) ) {
	/**
	 * Admin hooks class
	 */
	class ABFinder_Admin_Hooks {
		/**
		 * Admin end hooks construct
		 */
		public function __construct() {
			require_once ABFINDER_PLUGIN_FILE . 'includes/admin/class-abfinder-admin-functions.php';
			$function_handler = new ABFinder_Admin_Functions();
			add_action( 'admin_menu', array( $function_handler, 'auto_bulb_finder_admin_menu' ) );
			add_action( 'admin_menu', array( $function_handler, 'auto_bulb_finder_admin_scripts' ) );
			add_action( 'abfinder_add_adaption', array( $function_handler, 'abfinder_create_adaption' ), 10, 1 );
			add_action( 'abfinder_add_vehicle', array( $function_handler, 'abfinder_create_vehicle' ), 10, 1 );
		}
	}
}