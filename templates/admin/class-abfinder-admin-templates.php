<?php

/**
 * Admin End Templates
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

defined('ABSPATH') || exit;

if (!class_exists('abfinder_Admin_Templates')) {
    /**
     * Admin hooks class
     */
    class abfinder_Admin_Templates
    {

        /**
         * AB Finder Setting Page 
         *
         * @return void
         */
        public function auto_bulb_finder_config_html()
        {
            require 'settings/class-abfinder-setting.php';
        }

        /**
         * Adaption list
         *
         * @return void
         */
        public function abfinder_adaption_list_html()
        {
            require 'adaptions/class-abfinder-adaptions-list.php';
            $_obj = new ABFinder_Adaptions_List(); ?>
            <div class="wrap abfinder-products">
                <h1 class="wp-heading-inline">Product Adaptions</h1>
                <a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-adaption&action=add')); ?>" class="page-title-action">Add Adaption</a>
                <form method="post">
                    <?php
                    $_obj->prepare_items();
                    $_obj->search_box("Search", 'search');
                    $_obj->display(); ?>
                </form>
            </div>
            <?php
        }

        /**
         * Add manager form
         *
         * @param Integer $aid .
         * @return void .
         */
        public function abfinder_add_adaption_html($aid = '')
        {
            if (isset($_POST['save_adaption']) || isset($_POST['update_adaption'])) {
                if (!isset($_POST['abfinder_adaption_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abfinder_adaption_nonce'])), 'abfinder_adaption_nonce_action')) {  // input var okay.
            ?>
                    <div class='notice notice-error is-dismissible'>
                        <p><?php echo esc_html__('Sorry, your nonce did not verify.', 'auto-bulb-finder'); ?></p>
                    </div>
                <?php
                } else {
                    do_action('abfinder_add_adaption', $_POST, $aid);
                }
            }
            require 'adaptions/abfinder-add-adaption.php';
        }

        //Add Vehicle 
        public function abfinder_add_vehicle_html($id = '')
        {
            if (isset($_POST['save_vehicle']) || isset($_POST['update_vehicle'])) {
                if (!isset($_POST['abfinder_vehicle_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abfinder_vehicle_nonce'])), 'abfinder_vehicle_nonce_action')) {  // input var okay.
                ?>
                    <div class='notice notice-error is-dismissible'>
                        <p><?php echo esc_html__('Sorry, your nonce did not verify.', 'auto-bulb-finder'); ?></p>
                    </div>
            <?php
                } else {
                    do_action('abfinder_add_vehicle', $_POST, $id);
                }
            }
            require 'vehicles/abfinder-add-vehicle.php';
        }

        // Vehicle List
        public function abfinder_vehicle_list_html()
        {
            require 'vehicles/class-abfinder-vehicles-list.php';
            $_obj = new ABFinder_Vehicles_List(); ?>
            <div class="wrap abfinder-vehicles">
                <h1 class="wp-heading-inline">Vehicles</h1>
                <a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle&action=add')); ?>" class="page-title-action">Add Vehicle</a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle&action=import_vehicles')); ?>" class="page-title-action">Import</a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle&action=export_vehicles')); ?>" class="page-title-action">Export</a>
                <div class="abfinder-debug-mode" style="display: none;">
                    <label for="abfinder-debug-mode">Debug Mode</label>
                    <input type="checkbox" name="abfinder-debug-mode" id="abfinder-debug-mode" <?php echo (get_option('abfinder_debug_mode') == 'on') ? 'checked' : ''; ?>>
                </div>

                <form method="post">
                    <?php
                    $_obj->prepare_items();
                    $_obj->search_box("Search", 'search');
                    $_obj->display(); ?>
                </form>
            </div>
<?php
        }

        // Vehicle Import
        public function abfinder_import_vehicle_html()
        {
            require 'vehicles/abfinder-import-vehicle.php';
        }

        // Vehicle Export
        public function abfinder_export_vehicle_html()
        {
            require 'vehicles/abfinder-export-vehicle.php';
        }
    }
}
