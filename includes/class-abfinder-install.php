<?php

/**
 * Schema create on Activation
 *
 * @package Auto Bulb Finder
 * @version 1.0.0
 */

defined('ABSPATH') || exit();

if (!class_exists('ABFinder_Install')) {
    /**
     * Install Email
     */
    class ABFinder_Install
    {
        /**
         * Globle database.
         *
         * @var wpdb
         */
        protected $wpdb;

        protected $abfinder_db_version = 3;
        /**
         * Function initialization
         */
        public function init()
        {
            $this->abfinder_create_table();
            $this->upgrade();
        }
        /**
         * Functions Construct
         *
         * @return void
         */
        public function __construct()
        {
            global $wpdb;
            $this->wpdb = $wpdb;
        }

        public function upgrade()
        {
            $installed_verion = get_option("abfinder_db_version");
            if ($installed_verion != $this->abfinder_db_version) {
                $this->abfinder_upgrade_table();
            } else {
                $this->abfinder_create_table();
            }
            update_option("abfinder_db_version", $this->abfinder_db_version);
        }

        /**
         * Create pages that the plugin relies on, storing page id's in variables.
         *
         * @return void
         */
        public function abfinder_create_page()
        {
            register_post_type('abfinder');

            $pages = apply_filters(
                'abfinder_create_page',
                array(
                    'auto_bulb' =>
                    array(
                        'name'    => esc_html__('Auto Bulb', 'auto-bulb-finder-for-wp-wc'),
                        'title'   => esc_html__('Auto Bulb', 'auto-bulb-finder-for-wp-wc'),
                        'content' => '[' . apply_filters('abfinder_auto_bulb_finder_shortcode', 'auto_bulb_finder') . ']',
                    ),
                )
            );

            foreach ($pages as $key => $page) {
                $this->abfinder_process_page_creation(esc_sql($page['name']), $key . '_page_id', $page['title'], $page['content']);
            }
        }

        /**
         * Create pages.
         *
         * @param string $slug slug.
         * @param string $option option.
         * @param string $page_title page title.
         * @param string $page_content pag content.
         */
        public function abfinder_process_page_creation($slug, $option = '', $page_title = '', $page_content = '')
        {
            $option_value = get_option($option);
            if ($option_value > 0 && get_post($option_value)) {
                return -1;
            }

            $page_found = null;

            if (strlen($page_content) > 0) {
                // Search for an existing page with the specified page content (typically a shortcode).
                $page_found = $this->wpdb->get_var($this->wpdb->prepare('SELECT ID FROM ' . $this->wpdb->posts . " WHERE post_type='abfinder' AND post_content esc_like %s LIMIT 1;", "%{$page_content}%")); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            } else {
                // Search for an existing page with the specified page slug.
                $page_found = $this->wpdb->get_var($this->wpdb->prepare('SELECT ID FROM ' . $this->wpdb->posts . " WHERE post_type='abfinder' AND post_name = %s LIMIT 1;", $slug)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            }

            if ($page_found) {
                if (!$option_value) {
                    update_option($option, $page_found);
                }
                return $page_found;
            }

            $user_id = is_user_logged_in();

            $mp_post_type = ('Auto Bulb Finder' === $page_title) ? 'page' : 'abfinder';

            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => $mp_post_type,
                'post_author'    => $user_id,
                'post_name'      => $slug,
                'post_title'     => $page_title,
                'post_content'   => $page_content,
                'post_parent'    => '',
                'comment_status' => 'closed',
            );

            $page_id = wp_insert_post($page_data);

            if ($option) {
                update_option($option, $page_id);
            }

            return $page_id;
        }

        /**
         * Create Table
         */
        public function abfinder_create_table()
        {
            $charset_collate = $this->wpdb->get_charset_collate();
            $abfinder_adaption = $this->wpdb->prefix . 'abfinder_adaptions';
            $createAbfAdaptionTable = "CREATE TABLE IF NOT EXISTS $abfinder_adaption (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`name` text NOT NULL,
				`size` text,  
				`products` text,  
				`fits_on` text,  
				`status` boolean DEFAULT 0 NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($createAbfAdaptionTable);

            $abfinder_vehicle = $this->wpdb->prefix . 'abfinder_vehicles';
            $createAbfVehicleTable = "CREATE TABLE IF NOT EXISTS $abfinder_vehicle (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `year` int(4) NOT NULL,
                `make` text NOT NULL,
                `model` text NOT NULL,
                `submodel` text,
                `bodytype` text,
                `qualifier` text,
                `bulb_size` text,
                `status` boolean DEFAULT 0 NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
            dbDelta($createAbfVehicleTable);

            $abfinder_vehicle_query_history = $this->wpdb->prefix . 'abfinder_vehicle_query_history';
            $createAbfVehicleQueryHistoryTable = "CREATE TABLE IF NOT EXISTS $abfinder_vehicle_query_history (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `vid` bigint(20) NOT NULL,
                `year` int(4) NOT NULL,
                `make` text NOT NULL,
                `model` text NOT NULL,
                `submodel` text,
                `bodytype` text,
                `qualifier` text,
                `ip_address` text,
                `time` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
            dbDelta($createAbfVehicleQueryHistoryTable);

            update_option('abfinder_db_version', $this->abfinder_db_version);
        }

        public function abfinder_upgrade_table()
        {
            $old_db_version = get_option('abfinder_db_version');
            switch ($old_db_version) {
                case 1:
                case 2:
                    $this->abfinder_create_vehicle_table();
                case 3:
                    $this->abfinder_create_vehicle_query_history_table();
                default:
                    break;
            }
            update_option('abfinder_db_version', $this->abfinder_db_version);
        }

        private function abfinder_create_vehicle_table()
        {
            $charset_collate = $this->wpdb->get_charset_collate();
            $abfinder_vehicle = $this->wpdb->prefix . 'abfinder_vehicles';
            $createAbfTable = "CREATE TABLE IF NOT EXISTS $abfinder_vehicle (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `year` int(4) NOT NULL,
                `make` text NOT NULL,
                `model` text NOT NULL,
                `submodel` text,
                `bodytype` text,
                `qualifier` text,
                `bulb_size` text,
                `status` boolean DEFAULT 0 NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($createAbfTable);
        }

        // create vehicle query history table.
        public function abfinder_create_vehicle_query_history_table()
        {
            $charset_collate = $this->wpdb->get_charset_collate();
            $abfinder_vehicle_query_history = $this->wpdb->prefix . 'abfinder_vehicle_query_history';
            $createAbfVehicleQueryHistoryTable = "CREATE TABLE IF NOT EXISTS $abfinder_vehicle_query_history (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `vid` bigint(20) NOT NULL,
                `year` int(4) NOT NULL,
                `make` text NOT NULL,
                `model` text NOT NULL,
                `submodel` text,
                `bodytype` text,
                `qualifier` text,
                `bulb_size` text,
                `ip_address` text,
                `time` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($createAbfVehicleQueryHistoryTable);
        }
    }
}
