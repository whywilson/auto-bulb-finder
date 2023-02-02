<?php

/**
 * Admin End Templates
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Helper;

defined('ABSPATH') || exit;

if (!class_exists('ABFinder_Vehicles')) {
    /**
     * Manager data class.
     */
    class ABFinder_Vehicles
    {
        /**
         * DB global variable
         *
         * @var Object
         */
        protected $wpdb;
        /**
         * The table name
         *
         * @var Object
         */
        protected $table_name;
        /**
         * The start_time name
         *
         * @var Object
         */
        protected $start_time;
        /**
         * The current_time variable
         *
         * @var Object
         */
        protected $current_time;
        /**
         * The end_time variable
         *
         * @var Object
         */
        protected $end_time;
        /**
         * The formatted_days name
         *
         * @var Object
         */
        protected $formatted_days;
        /**
         * The table name post
         *
         * @var Object
         */
        public $table_name_post;
        /**
         * Construct function
         */
        public function __construct()
        {
            global $wpdb;
            $this->wpdb               = $wpdb;
            $this->table_name         = $this->wpdb->prefix . 'abfinder_vehicles';
            $this->table_name_post    = $this->wpdb->prefix . 'posts';
        }


        /**
         * Save vehicle Data
         *
         * @param [type] $data array.
         * @param [type] $id array.
         * @return $response boolean
         */
        public function abfinder_save_vehicle($data = array(), $id = '')
        {
            $response = false;
            if (!empty($id)) {
                $sql = $this->wpdb->update(
                    $this->table_name,
                    $data,
                    array(
                        'id' => $id,
                    )
                );
                $insert_id = $id;
                $response = true;
            } else {
                $sql = $this->wpdb->insert(
                    $this->table_name,
                    $data,
                    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
                );
                $response  = $sql ? $this->wpdb->insert_id : false;
                $insert_id = $response;
            }
            if (!empty($insert_id)) {
                do_action('abfinder_save_vehicle', $insert_id);
            }
            return $response;
        }

        //get vehicle id by year/make/model/submodel/bodytype/qualifier
        public function get_vehicle_id($year, $make, $model, $submodel, $bodytype, $qualifier)
        {
            $sql = "SELECT id FROM $this->table_name WHERE year = '$year' AND make = '$make' AND model = '$model' AND submodel = '$submodel' AND bodytype = '$bodytype' AND qualifier = '$qualifier'";
            $result = $this->wpdb->get_row($sql);
            return $result;
        }

        public function abfinder_get_vehicle($id)
        {
            $response = false;

            if (!empty($id)) {
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $sql = $this->wpdb->get_row($this->wpdb->prepare("SELECT * from $this->table_name where id=%d", $id));

                $response = $sql ? $sql : false;
            }

            return $response;
        }


        /**
         * Get abfinder Vehicles 
         *
         * @return $response array
         */
        public function abfinder_get_vehicles($include = 1)
        {
            $response = array();
            $condition = '';
            if ($include == 1) {
                $condition = '';
            } else {
                $condition = "WHERE status  = 0";
            }
            $sql = $this->wpdb->get_results("SELECT * from {$this->table_name} " . $condition . " order by id", ARRAY_A);

            if (!empty($sql)) {
                $response = $sql;
            }

            return $response;
        }

        //Return products id by searching vehicle fits_on
        public function abfinder_get_vehicle_by_size($size = null)
        {
            $response = array();
            $sql = $this->wpdb->get_results("SELECT id, products, fits_on from {$this->table_name} where status = 0 and fits_on like '%" . strtoupper($size) . "%'", ARRAY_A);
            if (!empty($sql)) {
                $productFits = array_combine(wp_list_pluck($sql, 'products'), wp_list_pluck($sql, 'fits_on'));
                foreach ($productFits as $products => $fits) {
                    $fits_on_size = explode(',', $fits);
                    if (in_array($size, $fits_on_size)) {
                        $response = explode(',', $products);
                        break;
                    }
                }
            }
            return $response;
        }

        /**
         * Get all allocated vehicles
         *
         * @param [type] $offset int.
         * @param [type] $perpage int.
         * @param [type] $search_val value.
         * @return Array
         */
        public function abfinder_get_allocated_vehicles($offset, $per_page, $search_val)
        {
            if ('' !== $search_val) {
                $result = $this->wpdb->get_results(
                    "SELECT * from {$this->table_name} where year like '%" . $search_val . "%' or make like '%" . $search_val . "%' or model like '%" . $search_val . "%' or submodel like '%" . $search_val . "%' or bodytype like '%" . $search_val . "%' or qualifier like '%" . $search_val . "%' order by id desc limit $per_page OFFSET $offset"
                );
            } else {
                $result = $this->wpdb->get_results("Select * from {$this->table_name} LIMIT $per_page OFFSET $offset");
            }

            return apply_filters('abfinder_allocated_vehicles', $result);
        }

        public function abfinder_enable_vehicle($id)
        {
            $response = false;
            if (!empty($id)) {
                if (is_array($id)) {
                    $id = $id;
                } else {
                    $id = array($id);
                }

                $id_str = implode(',', $id);
                $this->wpdb->query("UPDATE $this->table_name SET status = 0 WHERE id IN ( $id_str )");
                $response = true;
            }

            return $response;
        }

        public function abfinder_disable_vehicle($id)
        {
            $response = false;
            if (!empty($id)) {
                if (is_array($id)) {
                    $id = $id;
                } else {
                    $id = array($id);
                }

                $id_str = implode(',', $id);
                $this->wpdb->query("UPDATE $this->table_name SET status = 1 WHERE id IN ( $id_str )");
                $response = true;
            }

            return $response;
        }

        public function abfinder_delete_vehicle($id)
        {
            $response = false;
            if (!empty($id)) {
                if (is_array($id)) {
                    $id = $id;
                } else {
                    $id = array($id);
                }

                $id_str = implode(',', $id);
                $this->wpdb->query("DELETE FROM $this->table_name WHERE id IN ( $id_str )");
                $response = true;
            }

            return $response;
        }
    }
}
