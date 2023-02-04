<?php

/**
 * Admin End Functions
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Includes\Admin;

use ABFinder\Templates\Admin;
use ABFinder\Helper\ABFinder_Adaptions;
use ABFinder\Helper\ABFinder_Vehicles;
use ABFinder\Inc\ABFinder_Errors;

defined('ABSPATH') || exit;

require_once ABFINDER_PLUGIN_FILE . 'inc/class-abfinder-errors.php';

if (!class_exists('ABFinder_Adaptions')) {
    // require_once ABFINDER_PLUGIN_FILE . 'helper/adaptions/class-abfinder-adaptions.php';
}

if (!class_exists('ABFinder_Admin_Functions')) {
    /**
     * Admin functions class
     */
    class ABFinder_Admin_Functions extends ABFinder_Errors
    {
        /**
         * Template handler
         *
         * @var $template_handler
         */
        protected $template_handler;

        /**
         * Admin Functions Construct
         *
         * @return void
         */
        public function __construct()
        {
            $this->template_handler = new Admin\ABFinder_Admin_Templates();
        }

        public function auto_bulb_finder_admin_menu()
        {
            add_menu_page('Auto Bulb Finder', 'Auto Bulb', 'manage_options', 'auto-bulb-finder-for-wp-wc', array($this, 'abfinder_config_menu'), ABFINDER_PLUGIN_URL . 'assets/images/lightbulb-fill.svg', 20);
            add_submenu_page('auto-bulb-finder-for-wp-wc', 'Adaptions', 'Adaptions', 'edit_pages', 'auto-bulb-finder-adaption', array($this, 'auto_bulb_finder_adaption_menu'));
            add_submenu_page('auto-bulb-finder-for-wp-wc', 'Vehicles', 'Vehicles', 'edit_pages', 'auto-bulb-finder-vehicle', array($this, 'auto_bulb_finder_adaption_menu'));
        }

        function abfinder_admin_script()
        {
            wp_enqueue_style('abf-settings-style');
        }

        public function auto_bulb_finder_adaption_menu()
        {
            if (isset($_GET['page'])) {
                if ('auto-bulb-finder-adaption' === $_GET['page']) {
                    if (isset($_GET['action']) && 'add' === $_GET['action']) {
                        $this->template_handler->abfinder_add_adaption_html();
                    } elseif (isset($_GET['action']) && 'edit' === $_GET['action'] && isset($_GET['aid']) && !empty($_GET['aid'])) {
                        $aid = intval($_GET['aid']);
                        $this->template_handler->abfinder_add_adaption_html($aid);
                    } else {
                        $this->template_handler->abfinder_adaption_list_html();
                    }
                } else if ('auto-bulb-finder-vehicle' === $_GET['page']) {
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'add':
                                $this->template_handler->abfinder_add_vehicle_html();
                                break;
                            case 'edit':
                                if (isset($_GET['id']) && !empty($_GET['id'])) {
                                    $this->template_handler->abfinder_add_vehicle_html(intval($_GET['id']));
                                }
                                break;
                            case 'import_vehicles':
                                // wp_enqueue_script('simpleUpload.js');
                                $this->template_handler->abfinder_import_vehicle_html();
                                break;
                            case 'export_vehicles':
                                $this->template_handler->abfinder_export_vehicle_html();
                                break;
                            default:
                                $this->template_handler->abfinder_vehicle_list_html();
                                break;
                        }
                    }else{
                        $this->template_handler->abfinder_vehicle_list_html();
                    }
                }
            }
        }

        /**
         * Auto Bulb Finder Config Menu
         *
         */
        public function abfinder_config_menu()
        {
            wp_enqueue_script('abf-settings-js');
            $this->template_handler->auto_bulb_finder_config_html();
        }

        /**
         *  Auto Bulb Finder Adaption Menu
         *
         */
        public function abfinder_adaption_menu()
        {
            $this->template_handler->abfinder_adaption_list_html();
        }

        /**
         *  Auto Bulb Finder Add Adaption Menu
         *
         */
        public function abfinder_add_adaption_menu()
        {
            $this->template_handler->abfinder_add_adaption_html();
        }

        /**
         * Create abfinder adaption callback
         *
         *  @param array $data Data.
         */
        public function abfinder_create_adaption($data)
        {
            if ($data) {
                global $wpdb;
                $name             = isset($data['abfinder_adaption_name']) ? sanitize_text_field($data['abfinder_adaption_name']) : '';
                $size               = isset($data['abfinder_adaption_size']) ? sanitize_text_field($data['abfinder_adaption_size']) : '';
                $products          = isset($data['abfinder_adaption_products']) ? sanitize_text_field($data['abfinder_adaption_products']) : '';
                $fits_on            = isset($data['abfinder_adaption_fits_on']) ? sanitize_text_field($data['abfinder_adaption_fits_on']) : '';
                $status              = isset($data['abfinder_status']) ? intval($data['abfinder_status']) : 1;

                if (empty($name)) {
                    $message    = esc_html__('Adaption name cannot be null.', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (empty($size)) {
                    $message    = esc_html__('Adaption size cannot be null.', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (empty($products)) {
                    $message    = esc_html__('Adaption products cannot be null.', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (empty($fits_on)) {
                    $message    = esc_html__('Adaption fits_on cannot be null.', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (0 !== $status && 1 !== $status) {
                    $message = esc_html__('please enter a valid status', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                $check_error = apply_filters('abfinder_add_error_checks', array());

                foreach ($check_error as $key => $message) {
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                $product_check = false;
                if (isset($data['save_adaption']) && empty($data['aid'])) {
                    if ($product_check) {
                        $message = esc_html__('Adaption for <b>' . $size . '</b> already exits.', 'auto-bulb-finder');
                        parent::abfinder_set_error_code(1);
                        parent::abfinder_print_notification($message);
                    }
                }

                if (0 === parent::abfinder_get_error_code()) {
                    $helper = new ABFinder_Adaptions();

                    $fits_ons = explode(',', $fits_on);
                    
                    $fits_ons = array_unique($fits_ons);

                    $abfinder_adaption = array(
                        'name' => $name,
                        'size'      => $size,
                        'products' => $products,
                        'fits_on'   => implode(',', $fits_ons),
                        'status'   => $status,
                    );

                    if (isset($data['save_adaption']) && empty($_GET['aid'])) {
                        $result = $helper->abfinder_save_adaption($abfinder_adaption, '');

                        if ($result) {
                            $message = esc_html__('Adaption added successfully 😃', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(0);
                            parent::abfinder_print_notification($message);
                        } else {
                            $message = esc_html__('There is some issue while saving adaption 😢', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(1);
                            parent::abfinder_print_notification($message);
                        }
                    } elseif (isset($data['update_adaption']) && !empty($_GET['aid'])) {
                        $aid   = intval($_GET['aid']);
                        $result = $helper->abfinder_save_adaption($abfinder_adaption, $aid);

                        if ($result) {
                            $message = esc_html__('Adaption updated successfully 🎉', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(0);
                            parent::abfinder_print_notification($message);
                        } else {
                            $message = esc_html__('There is some issue while updating adaption 😢', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(1);
                            parent::abfinder_print_notification($message);
                        }
                    }
                } else {
                    $message = esc_html__('Please fill up all the required fields 😢', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }
            }
        }

        //Create abfinder create vehicle call back
        public function abfinder_create_vehicle($data)
        {
            if ($data) {
                $year             = isset($data['abfinder_vehicle_year']) ? sanitize_text_field($data['abfinder_vehicle_year']) : '';
                $make = isset($data['abfinder_vehicle_make']) ? sanitize_text_field($data['abfinder_vehicle_make']) : '';
                $model = isset($data['abfinder_vehicle_model']) ? sanitize_text_field($data['abfinder_vehicle_model']) : '';
                $submodel = isset($data['abfinder_vehicle_submodel']) ? sanitize_text_field($data['abfinder_vehicle_submodel']) : '';
                $bodytype = isset($data['abfinder_vehicle_bodytype']) ? sanitize_text_field($data['abfinder_vehicle_bodytype']) : '';
                $qualifier = isset($data['abfinder_vehicle_qualifier']) ? sanitize_text_field($data['abfinder_vehicle_qualifier']) : '';
                $bulbsize = isset($data['abfinder_vehicle_bulb_size']) ? sanitize_text_field($data['abfinder_vehicle_bulb_size']) : '';
                $status              = isset($data['abfinder_status']) ? intval($data['abfinder_status']) : 1;

                if (empty($year)) {
                    $message    = esc_html__('Adaption name cannot be null.', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (0 !== $status && 1 !== $status) {
                    $message = esc_html__('please enter a valid status', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                $check_error = apply_filters('abfinder_add_error_checks', array());

                foreach ($check_error as $key => $message) {
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }

                if (0 === parent::abfinder_get_error_code()) {
                    require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-vehicles.php';

                    $helper = new ABFinder_Vehicles();

                    $abfinder_vehicle = array(
                        'year' => $year,
                        'make' => $make,
                        'model' => $model,
                        'submodel' => $submodel,
                        'bodytype' => $bodytype,
                        'qualifier' => $qualifier,
                        'bulb_size' => $bulbsize,
                        'status'   => $status,
                    );

                    if (isset($data['save_vehicle']) && empty($_GET['id'])) {
                        $result = $helper->abfinder_save_vehicle($abfinder_vehicle, '');

                        if ($result) {
                            $message = esc_html__('Vehicle added successfully. 😃', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(0);
                            parent::abfinder_print_notification($message);
                        } else {
                            $plugin_version = get_option('abfinder_version');
                            if (version_compare($plugin_version, '2.0.0', '<=')) {
                                $message = esc_html__('Please deactivate Auto Bulb Finder plugin and Active again.', 'auto-bulb-finder');
                            } else {
                                $message = esc_html__('There is some issue while saving vehicle. 😢', 'auto-bulb-finder');
                            }
                            parent::abfinder_set_error_code(1);
                            parent::abfinder_print_notification($message);
                        }
                    } elseif (isset($data['update_vehicle']) && !empty($_GET['id'])) {
                        $id   = intval($_GET['id']);
                        $result = $helper->abfinder_save_vehicle($abfinder_vehicle, $id);

                        if ($result) {
                            $message = esc_html__('Vehicle updated successfully 🎉', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(0);
                            parent::abfinder_print_notification($message);
                        } else {
                            $message = esc_html__('There is some issue while updating vehicle 😢', 'auto-bulb-finder');
                            parent::abfinder_set_error_code(1);
                            parent::abfinder_print_notification($message);
                        }
                    }
                } else {
                    $message = esc_html__('Please fill up all the required fields 😢', 'auto-bulb-finder');
                    parent::abfinder_set_error_code(1);
                    parent::abfinder_print_notification($message);
                }
            }
        }
        /**
         * Create manual transaction callback
         *
         * @param [type] $ids array.
         */
        public function wkmpevent_add_screen_id($ids)
        {
            $ids[] = 'woocommerce_page_auto-bulb-finder-config';
            return $ids;
        }
    }
}
