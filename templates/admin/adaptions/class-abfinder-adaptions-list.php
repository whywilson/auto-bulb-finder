<?php

/**
 * Adaption list template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Adaptions;

defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('ABFinder_Adaptions_List')) {
    /**
     * Managers products
     */
    class ABFinder_Adaptions_List extends \WP_List_Table
    {
        /**
         * Table name global variable
         *
         * @var Object
         */
        protected $table_name = '';
        /**
         * Helper global variable
         *
         * @var Object
         */
        protected $helper = '';
        /**
         * DB global variable
         *
         * @var Database
         */
        protected $wpdb;

        /**
         * Class constructor
         */
        public function __construct()
        {
            global $wpdb;
            $this->table_name = $wpdb->prefix . 'posts';
            $this->wpdb       = $wpdb;

            $this->helper = new ABFinder_Adaptions();

            parent::__construct(
                array(
                    'singular' => esc_html__('Product(s)', 'auto-bulb-finder'),
                    'plural'   => esc_html__('Product(s)', 'auto-bulb-finder'),
                    'ajax'     => false,
                )
            );
        }

        /**
         * Prepare Items
         *
         * @return void
         */
        public function prepare_items()
        {
            $search_val = '';
            // We're only interested in when the importer completes.
            // phpcs:disable WordPress.Security.NonceVerification.Recommended
            if (!empty($_REQUEST['s'])) {
                $search_val = $_REQUEST['s'];
            }
            $columns               = $this->get_columns();
            $sortable              = $this->get_sortable_columns();
            $hidden                = $this->get_hidden_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->process_bulk_action();
            $per_page     = $this->get_items_per_page('adaption_per_page', 20);
            $current_page = $this->get_pagenum();
            $total_items  = self::abfinder_qrecord_count($search_val);

            $this->set_pagination_args(
                array(
                    'total_items' => $total_items,
                    'per_page'    => $per_page,
                )
            );

            $this->items = self::abfinder_get_assigned_adaptions($per_page, $current_page, $search_val);
        }

        /**
         * Record count
         *
         * @param string $search_val Search product .
         * @return $count
         */
        public function abfinder_qrecord_count($search_val)
        {
            $count           = 0;
            $table_name      = $this->wpdb->prefix . 'abfinder_adaptions';
            $table_name_post = $this->wpdb->prefix . 'posts';
            if ('' !== $search_val) { ?>
                Search results for: <strong><?php echo $search_val; ?></strong>
                <?php
            }
            $count = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            return $count;
        }

        /**
         * Assign product.
         *
         * @param int  $per_page per page.
         * @param string $current_page current page.
         * @param string $search_val search value.
         */
        public function abfinder_get_assigned_adaptions($per_page, $current_page = 1, $search_val)
        {
            $data   = array();
            $offset = ($current_page - 1) * $per_page;
            $result = $this->helper->abfinder_get_allocated_adaptions($offset, $per_page, $search_val); // WPCS: db call ok; WPCS: cache ok; WPCS: unprepared SQL ok.

            $today        = gmdate('Y-m-d');
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $data[] = array(
                        'id'     => $value->id,
                        'name'     => $value->name,
                        'size'     => $value->size,
                        'products'     => $value->products,
                        'fits_on'     => $value->fits_on,
                        'status'     => $value->status,
                    );
                }
            }

            return apply_filters('abfinder_adaptions_list_data', $data, $result);
        }

        /**
         *  No items
         *
         * @return void
         */
        public function no_items()
        {
            echo esc_html__('Add adaptions to bundle products to the bulb model.', 'auto-bulb-finder');
        }
        /**
         *  Actions
         */
        public function get_bulk_actions()
        {
            $actions = array(
                'disable'  => 'Disable',
                'enable'  => 'Enable',
            );
            return $actions;
        }

        /**
         * Hidden Columns
         *
         * @return Array
         */
        public function get_hidden_columns()
        {
            return array();
        }

        /**
         *  Associative array of columns
         *
         * @return array
         */
        public function get_columns()
        {
            $columns = array(
                'cb'     => '<input type="checkbox" />',
                'name'   => 'Name',
                'size'   => 'Size',
                'products'  => 'Products',
                'fits_on'    => 'Fits On',
                'status'    => 'Status',
            );

            return apply_filters('abfinder_adaptions_list_columns', $columns);
        }
        /**
         * Render a column when no column specific method exists.
         *
         * @param [type] $item array.
         * @param [type] $column_name string.
         * @return mixed
         */
        public function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'name':
                case 'size':
                case 'products':
                    return $item[$column_name];
                case 'fits_on':
                    $fits_on_items = explode(",", $item[$column_name]);
                    $fits_on_html = '';
                    $fits_on_show_count = 0;
                    foreach ($fits_on_items as $fits_on_item) {
                        if ($fits_on_show_count < 5) {
                            $fits_on_html .= '<div class="fits_on_item">' . trim($fits_on_item) . '</div>';
                        }
                        $fits_on_show_count += 1;
                    }
                    if ($fits_on_show_count > 5) {
                        $fits_on_html .= ' <div> and ' . strval($fits_on_show_count - 5) . ' more.</div>';
                    }
                    return $fits_on_html;
                case 'status':
                    if ($item['status'] == "0") {
                        return '<a style="color: green; font-weight: bold;">Enabled</a>';
                    }
                    return '<a style="color: red; font-weight: bold;">Disabled</a>';
                default:
                    return !empty($item[$column_name]) ? $item[$column_name] : $item;
            }
        }

        public function column_name($item)
        {
            $item_json = json_decode(json_encode($item), true);
            $actions = array(
                'edit'   => sprintf('<a href="admin.php?page=auto-bulb-finder-adaption&action=edit&aid=%d">' . esc_html__('Edit', 'auto-bulb-finder') . '</a>', $item['id']),
                'delete' => sprintf('<a href="admin.php?page=auto-bulb-finder-adaption&action=delete&aid=%d&_abfinder_nonce=%s">' . esc_html__('Delete', 'auto-bulb-finder') . '</a>', $item['id'], wp_create_nonce('abfinder-list-action-nonce')),
            );
            return '<em>' . sprintf('%s %s', $item_json['name'], $this->row_actions($actions)) . '</em>';
        }

        /**
         * Process bulk actions
         */
        public function process_bulk_action()
        {
            if (isset($_REQUEST['action'])) {
                if ('delete' === $_REQUEST['action']) {
                    if (isset($_REQUEST['aid']) && !empty($_REQUEST['aid'])) {
                        $aids = $_REQUEST['aid'];
                        if (!is_array($aids)) {
                            $aids = array($aids);
                        }

                        foreach ($aids as $aid) {
                            $aid = wp_unslash($_REQUEST['aid']);
                            $result = $this->helper->abfinder_delete_adaption($aid);
                        }
                        if ($result) {
                ?>
                            <div class="notice notice-success is-dismissible">
                                <p> Adaption deleted successfully! </p>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p> There is some issue on deleting adaption! </p>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p> Adaption must be selected first! </p>
                        </div>
                        <?php
                    }
                } elseif ('disable' === $_REQUEST['action']) {
                    if (isset($_REQUEST['aid']) && !empty($_REQUEST['aid'])) {
                        $aids = $_REQUEST['aid'];
                        if (!is_array($aids)) {
                            $aids = array($aids);
                        }

                        foreach ($aids as $aid) {
                            $aid = wp_unslash($_REQUEST['aid']);
                            $result = $this->helper->abfinder_disable_adaption($aid);
                        }
                        if ($result) {
                        ?>
                            <div class="notice notice-success is-dismissible">
                                <p> Adaption Disable successfully! </p>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p> There is some issue on disable adaption! </p>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p> Adaption must be selected first! </p>
                        </div>
                        <?php
                    }
                } elseif ('enable' === $_REQUEST['action']) {
                    if (isset($_REQUEST['aid']) && !empty($_REQUEST['aid'])) {
                        $aids = $_REQUEST['aid'];
                        if (!is_array($aids)) {
                            $aids = array($aids);
                        }

                        foreach ($aids as $aid) {
                            $aid = wp_unslash($_REQUEST['aid']);

                            $result = $this->helper->abfinder_enable_adaption($aid);
                        }
                        if ($result) {
                        ?>
                            <div class="notice notice-success is-dismissible">
                                <p> Adaption Enable successfully! </p>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p> There is some issue on deleting adaption! </p>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p> Adaption must be selected first! </p>
                        </div>
<?php
                    }
                }
            }
        }

        /**
         * Columns to make sortable.
         *
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'id'      => array('id', true),
                'created' => array('created', true),
                'status'  => array('status', true),
            );

            return apply_filters('abfinder_adaption_list_sortable_columns', $sortable_columns);
        }

        /**
         * Render the bulk edit checkbox
         *
         * @param [array] $item .
         *
         * @return string
         */
        public function column_cb($item)
        {
            /* translators: %s: adaption status. */
            return sprintf(
                '<input type="checkbox" name="aid[]" value="%s" />',
                $item['id']
            );
        }
    }
}
