<?php

/**
 * Add Warehouse template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Vehicles;

defined('ABSPATH') || exit;

require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-vehicles.php';

$helper = new ABFinder_Vehicles();

$abfinder_vehicle_year = '';
$abfinder_vehicle_make = '';
$abfinder_vehicle_submodel = '';
$abfinder_vehicle_bodytype = '';
$abfinder_vehicle_qualifier = '';
$abfinder_vehicle_bulb_size = '';
$abfinder_status = '0';
if ($id) {
	$vehicle_data = $helper->abfinder_get_vehicle(intval($id));
	if (!empty($vehicle_data)) {
		$abfinder_vehicle_year = $vehicle_data->year;
		$abfinder_vehicle_make = $vehicle_data->make;
		$abfinder_vehicle_model = $vehicle_data->model;
		$abfinder_vehicle_submodel = $vehicle_data->submodel;
		$abfinder_vehicle_bodytype = $vehicle_data->bodytype;
		$abfinder_vehicle_qualifier = $vehicle_data->qualifier;
		$abfinder_vehicle_bulb_size = $vehicle_data->bulb_size;
		$abfinder_status = $vehicle_data->status;
	}
}

$status_list = array(
	'0' => 'Include',
	'1' => 'Exclude',
);
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html__($id ? 'Edit Vehicle' : 'Add Vehicle', 'auto-bulb-finder'); ?>
	</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
	<hr />

	<form method="POST" action="" id="abfinder-add-gbuy-form">
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label><?php echo esc_html__('Year', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					$abfinder_vehicle_year = isset($_POST['abfinder_vehicle_year']) ? sanitize_text_field($_POST['abfinder_vehicle_year']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_year" id="abfinder-vehicle-name" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_year); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php echo esc_html__('Make', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					$abfinder_vehicle_make = isset($_POST['abfinder_vehicle_make']) ? sanitize_text_field($_POST['abfinder_vehicle_make']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_make" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_make); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php echo esc_html__('Model', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					$abfinder_vehicle_model = isset($_POST['abfinder_vehicle_model']) ? sanitize_text_field($_POST['abfinder_vehicle_model']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_model" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_model); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php echo esc_html__('Submodel', 'auto-bulb-finder'); ?></label>
					</th>
					<?php
					$abfinder_vehicle_submodel = isset($_POST['abfinder_vehicle_submodel']) ? sanitize_text_field($_POST['abfinder_vehicle_submodel']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_submodel" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_submodel); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php echo esc_html__('Body Type', 'auto-bulb-finder'); ?></label>
					</th>
					<?php
					$abfinder_vehicle_bodytype = isset($_POST['abfinder_vehicle_bodytype']) ? sanitize_text_field($_POST['abfinder_vehicle_bodytype']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_bodytype" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_bodytype); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label><?php echo esc_html__('Qualifier', 'auto-bulb-finder'); ?></label>
					</th>
					<?php
					$abfinder_vehicle_qualifier = isset($_POST['abfinder_vehicle_qualifier']) ? sanitize_text_field($_POST['abfinder_vehicle_qualifier']) : '';
					?>
					<td>
						<input type="text" name="abfinder_vehicle_qualifier" class="abf-vehicle-item" value="<?php echo esc_attr($abfinder_vehicle_qualifier); ?>" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="abfinder-adaption-bulb-size"><?php echo esc_html__('Bulb Size', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					$abfinder_vehicle_bulb_size = isset($_POST['abfinder_vehicle_bulb_size']) ? sanitize_text_field($_POST['abfinder_vehicle_bulb_size']) : '';
					?>
					<td>
						<textarea type="text" placeholder="eg: Fog Light:H11;Brack Light:194" name="abfinder_vehicle_bulb_size" id="abfinder-adaption-bulb-size" class="bulb-size-textarea" rows="6" cols="32"><?php echo esc_attr($abfinder_vehicle_bulb_size); ?></textarea>
					</td>
				</tr>
				<tr>
					<th>
						<label for="abfinder-status">
							<?php echo esc_html__('Status', 'auto-bulb-finder'); ?>
						</label>
					</th>
					<td>
						<select id="abfinder-status" name="abfinder_status" class="abf-vehicle-item">
							<?php
							foreach ($status_list as $key => $value) {
							?>
								<option value="<?php echo esc_attr($key); ?>" <?php
																				if ($key == $abfinder_status) {
																					echo 'selected';
																				} ?>>
									<?php echo esc_attr($value); ?>
								</option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<input type="hidden" name="e_id" value="<?php echo esc_attr($id); ?>" />

		<?php
		if (!empty($id)) {
			wp_nonce_field('abfinder_vehicle_nonce_action', 'abfinder_vehicle_nonce');
			submit_button(esc_html__('Update Vehicle', 'auto-bulb-finder'), 'primary', 'update_vehicle');
		} else {
			wp_nonce_field('abfinder_vehicle_nonce_action', 'abfinder_vehicle_nonce');
			submit_button(esc_html__('Save Vehicle', 'auto-bulb-finder'), 'primary', 'save_vehicle');
		}
		?>
	</form>
</div>