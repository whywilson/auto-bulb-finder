<?php

/**
 * Add vehicle import template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Vehicles;

defined('ABSPATH') || exit;

require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-vehicles.php';

$helper = new ABFinder_Vehicles();


?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html__('Import Vehicles', 'auto-bulb-finder'); ?>
	</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
	<hr />

	<h2>Import vehicles from a CSV file</h2>
	<p>Click to download the vehicle template.</p>
	<a href="<?php echo ABFINDER_PLUGIN_URL . 'assets/templates/vehicles-template.csv'; ?>" target="_blank" class="button button-primary">Download Template</a>
	<p>Upload the CSV file below.</p>

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
		<input type="hidden" name="action" value="abfinder_import_vehicle" />
		<?php wp_nonce_field('abfinder_import_vehicle'); ?>

		<table class="form-table abfinder-form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="abfinder_vehicle_csv"><?php echo esc_html__('CSV File', 'auto-bulb-finder'); ?></label>
					</th>
					<td>
						<input type="file" name="abfinder_vehicle_csv" id="abfinder_vehicle_csv" accept=".csv" class="regular-text" />
					</td>
				</tr>
				<tr>
					<td>
						<a id="abf_vehicle_import" class="button button-primary">Import Now</a>
					</td>
					<td>
						<img id="abf_action_loader" src="<?php echo ABFINDER_PLUGIN_URL . 'assets/images/loading.gif'; ?>" style="height: 24px; display: none; vertical-align: middle;" />
					</td>
				</tr>
			</tbody>
		</table>


	</form>
</div>

<script>
	jQuery(document).ready(function($) {
		//click #abf_vehicle_import
		$('#abf_vehicle_import').click(function() {

			$fileChoosed = $('#abfinder_vehicle_csv')[0].files[0];
			if ($fileChoosed == undefined) {
				alert('Please choose a file to import.');
				return false;
			}

			$('#abf_action_loader').show();

			//get form data
			var form_data = new FormData();
			form_data.append('action', 'auto_bulb_finder');
			form_data.append('fn', 'import_vehicles');
			form_data.append('upload', $fileChoosed);
			form_data.append('_wpnonce', '<?php echo wp_create_nonce('abfinder_import_vehicle_nonce'); ?>');

			//ajax call
			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: form_data,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					var json = JSON.parse(response);
					$('#abf_action_loader').hide();
					if (json.success) {
						$(".abfinder-form-table").after('<div class="notice notice-success is-dismissible"><p>' + json.msg + '</p></div>');
						setTimeout(function() {
							window.location.href = '<?php echo admin_url('admin.php?page=auto-bulb-finder-vehicle'); ?>';
						}, 3000);
					} else {
						alert(json.msg);
					}
				},
				complete: function() {
					$('#abf_action_loader').hide();
				},
				error: function(response) {
					$('#abf_action_loader').hide();
					console.log(response);
				}
			});
		});
	});
</script>