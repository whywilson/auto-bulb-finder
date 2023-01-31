<?php

/**
 * Vehicle list template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Vehicles;

defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('ABFinder_Vehicles_List')) {
	class ABFinder_Vehicles_List extends \WP_List_Table
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

			$this->helper = new ABFinder_Vehicles();

			parent::__construct(
				array(
					'singular' => esc_html__('Vehicle(s)', 'auto-bulb-finder'),
					'plural'   => esc_html__('Vehicle(s)', 'auto-bulb-finder'),
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
			if (!empty($_REQUEST['s'])) {
				$search_val = $_REQUEST['s'];
			}
			$columns               = $this->get_columns();
			$sortable              = $this->get_sortable_columns();
			$hidden                = $this->get_hidden_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			$this->process_bulk_action();
			$per_page     = $this->get_items_per_page('vehicle_per_page', 20);
			$current_page = $this->get_pagenum();
			$total_items  = self::abfinder_qrecord_count($search_val);

			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil($total_items / $per_page),
				)
			);

			$this->items = self::abfinder_get_assigned_vehicles($per_page, $current_page, $search_val);
		}

		/**
		 * Record count
		 *
		 * @param string $search_val Search product .
		 * @return int $count
		 */
		public function abfinder_qrecord_count($search_val)
		{
			$count           = 0;
			$table_name      = $this->wpdb->prefix . 'abfinder_vehicles';
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
		 * @param int  $per_page per pahe.
		 * @param string $current_page current page.
		 * @param string $search_val search value.
		 */
		public function abfinder_get_assigned_vehicles($per_page, $current_page = 1, $search_val)
		{
			$data   = array();
			$offset = ($current_page - 1) * $per_page;
			$result = $this->helper->abfinder_get_allocated_vehicles($offset, $per_page, $search_val);

			if (!empty($result)) {
				foreach ($result as $key => $value) {
					$data[] = array(
						'id'     => $value->id,
						'year'   => $value->year,
						'make'   => $value->make,
						'model'  => $value->model,
						'submodel'  => $value->submodel,
						'bodytype'  => $value->bodytype,
						'qualifier'  => $value->qualifier,
						'bulb_size'  => $value->bulb_size,
						'status'     => $value->status,
					);
				}
			}

			return apply_filters('abfinder_vehicles_list_data', $data, $result);
		}

		/**
		 *  No items
		 *
		 * @return void
		 */
		public function no_items()
		{
			echo esc_html__('You can add bulb size information of your own vehicles here.', 'auto-bulb-finder');
		}
		/**
		 *  Actions
		 */
		public function get_bulk_actions()
		{
			$actions = array(
				'include'  => 'Include',
				'exclude'  => 'Exclude',
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
				'year'   => 'Year',
				'make'   => 'Make',
				'model'  => 'Model',
				'submodel'    => 'Submodel',
				'bodytype'    => 'Bodytype',
				'qualifier'    => 'Qualifier',
				'bulb_size'    => 'Bulb Size',
				'status'    => 'Status',
			);

			return apply_filters('abfinder_vehicles_list_columns', $columns);
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
				case 'make':
				case 'model':
				case 'submodel':
				case 'bodytype':
				case 'qualifier':
					return $item[$column_name];
				case 'bulb_size':
					$bulb_size = $item[$column_name];
					$bulb_sizes = explode(';', trim($bulb_size));
					$bulb_size_html = '';
					foreach ($bulb_sizes as $key => $value) {
						if (empty($value)) continue;
						$bulb_size_html .= '<span class="abfinder-bulb-size">' . trim($value) . '</span>';
					}
					return $bulb_size_html;
				default:
					return !empty($item[$column_name]) ? $item[$column_name] : $item;
			}
		}

		public function column_year($item)
		{
			$item_json = json_decode(json_encode($item), true);
			$actions = array(
				'edit'   => sprintf('<a href="admin.php?page=auto-bulb-finder-vehicle&action=edit&id=%d">' . esc_html__('Edit', 'auto-bulb-finder') . '</a>', $item['id']),
				'delete' => sprintf('<a href="admin.php?page=auto-bulb-finder-vehicle&action=delete&id=%d&_abfinder_nonce=%s">' . esc_html__('Delete', 'auto-bulb-finder') . '</a>', $item['id'], wp_create_nonce('abfinder-list-action-nonce')),
			);
			return '<em>' . sprintf('%s %s', $item_json['year'], $this->row_actions($actions)) . '</em>';
		}

		public function column_status($item)
		{
			if ($item['status'] == "0") {
				return '<a style="color: green; font-weight: bold;">Include</a>';
			}
			return '<a style="color: red; font-weight: bold;">Exclude</a>';
		}

		/**
		 * Process bulk actions
		 */
		public function process_bulk_action()
		{
			if (isset($_REQUEST['action'])) {
				if ('delete' === $_REQUEST['action']) {
					if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
						$id = wp_unslash($this->sanitize_id_array($_REQUEST['id']));
						$result = $this->helper->abfinder_delete_vehicle($id);
						if ($result) {
				?>
							<div class="notice notice-success is-dismissible">
								<p> Vehicle deleted successfully! </p>
							</div>
						<?php
						} else {
						?>
							<div class="notice notice-error is-dismissible">
								<p> There is some issue on deleting vehicle! </p>
							</div>
						<?php
						}
					} else {
						?>
						<div class="notice notice-error is-dismissible">
							<p> Vehicle must be selected first! </p>
						</div>
						<?php
					}
				} elseif ('exclude' === $_REQUEST['action']) {
					if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
						$id = wp_unslash($this->sanitize_id_array($_REQUEST['id']));
						$result = $this->helper->abfinder_disable_vehicle($id);
						if ($result) {
						?>
							<div class="notice notice-success is-dismissible">
								<p> Vehicle Disable successfully! </p>
							</div>
						<?php
						} else {
						?>
							<div class="notice notice-error is-dismissible">
								<p> There is some issue on disable vehicle! </p>
							</div>
						<?php
						}
					} else {
						?>
						<div class="notice notice-error is-dismissible">
							<p> Vehicle must be selected first! </p>
						</div>
						<?php
					}
				} elseif ('include' === $_REQUEST['action']) {
					if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
						$id = wp_unslash($this->sanitize_id_array($_REQUEST['id']));
						$result = $this->helper->abfinder_enable_vehicle($id);
						if ($result) {
						?>
							<div class="notice notice-success is-dismissible">
								<p> Vehicle Enable successfully! </p>
							</div>
						<?php
						} else {
						?>
							<div class="notice notice-error is-dismissible">
								<p> There is some issue on deleting vehicle! </p>
							</div>
						<?php
						}
					} else {
						?>
						<div class="notice notice-error is-dismissible">
							<p> Vehicle must be selected first! </p>
						</div>
<?php
					}
				}
			}
		}

		protected function sanitize_id_array($ids)
        {
            $ids = array_map('intval', $ids);
            $ids = array_filter($ids);
            return $ids;
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

			return apply_filters('abfinder_vehicle_list_sortable_columns', $sortable_columns);
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
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />',
				$item['id']
			);
		}
	}
}
