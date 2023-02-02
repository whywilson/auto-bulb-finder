<?php

/**
 * This file handles Errors
 *
 * @version 2.0.0
 * @package Auto Bulb Finder 
 * @since 1.0.0
 */

namespace ABFinder\Inc;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ABFinder_Errors')) {
	/**
	 * Manager Error class
	 */
	class ABFinder_Errors
	{
		/**
		 * Construct function
		 *
		 * @var Object
		 */
		public $error_code = 0;
		/**
		 * Construct function
		 *
		 * @param array $error_code Data.
		 */
		public function __construct($error_code = 0)
		{
			$this->error_code = $error_code;
		}

		/**
		 * Set_error function
		 *
		 * @param array $code Data.
		 */
		public function abfinder_set_error_code($code)
		{
			if (!empty($code)) {
				$this->error_code = $code;
			}
		}
		/**
		 * Get_error function
		 */
		public function abfinder_get_error_code()
		{
			return $this->error_code;
		}
		/**
		 * Print notification function.
		 *
		 * @param array $message Data.
		 */
		public function abfinder_print_notification($message)
		{

			if (is_admin()) {

				if (0 === $this->error_code) {

					echo '<div class="notice notice-success"><p>' . esc_html($message) . '</p> </div>';
				} elseif (1 === $this->error_code) {

					echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
				}
			} else {

				if (0 === $this->error_code) {

					echo '<div class="abfinder-success"><p>' . esc_html($message) . '</p> </div>';
				} elseif (1 === $this->error_code) {

					echo '<div class="abfinder-error"><p>' . esc_html($message) . '</p></div>';
				}
			}
		}
	}
}
