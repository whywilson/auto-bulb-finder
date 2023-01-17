<?php

/**
 * Admin End Templates
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Helper;

defined('ABSPATH') || exit;

if (!class_exists('ABFinder_Adaptions')) {
    /**
     * Manager data class.
     */
    class ABFinder_Adaptions
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
            $this->table_name         = $this->wpdb->prefix . 'abfinder_adaptions';
            $this->table_name_post    = $this->wpdb->prefix . 'posts';
        }


        /**
         * Save Adaption Data
         *
         * @param [type] $data array.
         * @param [type] $aid array.
         * @return $response boolean
         */
        public function abfinder_save_adaption($data = array(), $aid = '')
        {
            $response = false;

            if (!empty($aid)) {
                $sql = $this->wpdb->update(
                    $this->table_name,
                    $data,
                    array(
                        'id' => $aid,
                    )
                );

                $insert_id = $aid;
                $response = true;
            } else {
                $sql = $this->wpdb->insert(
                    $this->table_name,
                    $data,
                    array('%s', '%s', '%s', '%s', '%d')
                );

                $response  = $sql ? $this->wpdb->insert_id : false;
                $insert_id = $response;
            }
            if (!empty($insert_id)) {
                do_action('abfinder_save_adaption', $insert_id);
            }
            return $response;
        }

        public function abfinder_get_adaption($aid)
        {
            $response = false;

            if (!empty($aid)) {
                $sql = $this->wpdb->get_row($this->wpdb->prepare("SELECT * from $this->table_name where id=%d", $aid));

                $response = $sql ? $sql : false;
            }

            return $response;
        }


        /**
         * Get abfinder Adaptions
         *
         * @return $response array
         */
        public function abfinder_get_adaptions()
        {
            $response = array();
            $sql = $this->wpdb->get_results("SELECT DISTINCT id from {$this->table_name} where status = 0 ", ARRAY_A);

            if (!empty($sql)) {
                $adaptions_id = wp_list_pluck($sql, 'id');
                foreach ($adaptions_id as $adaptions) {
                    array_push($response, $adaptions);
                }
            }

            return $response;
        }

        //Return products id by searching adaption fits_on
        public function abfinder_get_adaption_by_size($size = null)
        {
            $size = trim($size);
            $response = array();
            $sql = $this->wpdb->get_results("SELECT id, products, fits_on from {$this->table_name} where status = 0 and fits_on like '%" . strtoupper($size) . "%'", ARRAY_A);
            if (!empty($sql)) {
                $productFits = array_combine(wp_list_pluck($sql, 'products'), wp_list_pluck($sql, 'fits_on'));
                foreach ($productFits as $products => $fits) {
                    $fits_on_size = explode(',', trim($fits));
                    if (in_array($size, $fits_on_size)) {
                        $response = explode(',', $products);
                        break;
                    }
                }
            }
            return $response;
        }

        /**
         * Get all allocated adaptions
         *
         * @param [type] $offset int.
         * @param [type] $perpage int.
         * @param [type] $search_val value.
         * @return Array
         */
        public function abfinder_get_allocated_adaptions($offset, $per_page, $search_val)
        {
            if ('' !== $search_val) {
                $result = $this->wpdb->get_results(
                    "SELECT * from {$this->table_name} where name like '%" . $search_val . "%' or size like '%" . $search_val . "%' or products like '%" . $search_val . "%' or fits_on like '%" . $search_val . "%' order by id desc limit $per_page OFFSET $offset"
                );
            } else {
                $result = $this->wpdb->get_results("Select * from {$this->table_name} LIMIT $per_page OFFSET $offset");
            }

            return apply_filters('abfinder_allocated_adaptions', $result);
        }

        /**
         * Get availablity to group buy
         *
         * @param [type] $product_id int.
         * @return Array
         */
        public function abfinder_is_available($product_id)
        {
            $product = wc_get_product($product_id);
            $result = $this->wpdb->get_row($this->wpdb->prepare("Select * from {$this->table_name} where product_id=%d AND status=0", $product_id));

            if (!empty($result)) {
                $start_date   = $result->start_date;
                $end_date     = $result->end_date;
                $price        = $result->price;
                $limit        = $result->limit;
                $today        = gmdate('Y-m-d H:i:s');
                $current_time = strtotime($today);
                $start_time   = strtotime($start_date);
                $end_time     = strtotime($end_date);

                if ($start_time <= $current_time && $current_time <= $end_time) {
                    $this->start_time   = $start_time;
                    $this->end_time     = $end_time;
                    $this->current_time = $current_time;
                    $this->end_time     = $end_time;

                    return true;
                }
            }

            return false;
        }

        public function abfinder_enable_adaption($aid)
        {
            $response = false;
            if (!empty($aid)) {
                if (is_array($aid)) {
                    $aid = $aid;
                } else {
                    $aid = array($aid);
                }

                $aid_str = implode(',', $aid);
                $this->wpdb->query("UPDATE $this->table_name SET status = 0 WHERE id IN ( $aid_str )");
                $response = true;
            }

            return $response;
        }

        public function abfinder_disable_adaption($aid)
        {
            $response = false;
            if (!empty($aid)) {
                if (is_array($aid)) {
                    $aid = $aid;
                } else {
                    $aid = array($aid);
                }

                $aid_str = implode(',', $aid);
                $this->wpdb->query("UPDATE $this->table_name SET status = 1 WHERE id IN ( $aid_str )");
                $response = true;
            }

            return $response;
        }

        public function abfinder_delete_adaption($aid)
        {
            $response = false;
            if (!empty($aid)) {
                if (is_array($aid)) {
                    $aid = $aid;
                } else {
                    $aid = array($aid);
                }

                $aid_str = implode(',', $aid);
                $this->wpdb->query("DELETE FROM $this->table_name WHERE id IN ( $aid_str )");
                $response = true;
            }

            return $response;
        }
    }
}
