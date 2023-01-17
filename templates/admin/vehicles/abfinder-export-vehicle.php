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

?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html__('Export Vehicles', 'auto-bulb-finder'); ?>
	</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-vehicle')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
	<hr />
	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<input type="hidden" name="action" value="abfinder_export_vehicle" />
		<?php wp_nonce_field('abfinder_export_vehicle'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="abfinder-export-all-vehicles"><?php echo esc_html__('Check to export all vehicles.', 'auto-bulb-finder'); ?></label>
					</th>
					<td>
						<input type="checkbox" name="abfinder-export-all-vehicles" id="abfinder-export-all-vehicles" value="1" checked />
					</td>
				</tr>
				<tr>
				<tr>
					<td>
						<a id="abf_vehicle_export" class="button button-primary">Export Now</a>
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
		$('#abf_vehicle_export').click(function() {
			$('#abf_action_loader').show();
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'auto_bulb_finder',
					fn: 'export_vehicles',
					all: $('#abfinder-export-all-vehicles').is(':checked') ? 1 : 0,
					security: '<?php echo wp_create_nonce('abf_export_vehicle'); ?>'
				},
				success: function(response) {
					let data = JSON.parse(response);
					if (data.success) {
						let blob = new Blob([data.content], {
							type: "text/csv;"
						});
						let a = document.createElement('a');
						a.href = window.URL.createObjectURL(blob);
						a.download = data.name;
						document.body.appendChild(a);
						a.click();
						document.body.removeChild(a);
						window.URL.revokeObjectURL(a.href);

					}
					$('#abf_action_loader').hide();
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