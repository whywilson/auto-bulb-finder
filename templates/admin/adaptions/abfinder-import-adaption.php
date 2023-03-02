<?php

/**
 * Add adaption import template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Adaptions;

defined('ABSPATH') || exit;

$helper = new ABFinder_Adaptions();

?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html__('Import Adaptions', 'auto-bulb-finder'); ?>
	</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-adaption')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
	<hr />

	<h2>Import adaptions from a CSV file</h2>
	<p>Click to download the adaption template.</p>
	<a href="<?php echo ABFINDER_PLUGIN_URL . 'assets/templates/adaptions-template.csv'; ?>" target="_blank" class="button button-primary">Download Template</a>
	<p>Upload the CSV file below.</p>

	<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
		<input type="hidden" name="action" value="abfinder_import_adaption" />
		<?php wp_nonce_field('abfinder_import_adaption'); ?>

		<table class="form-table abfinder-form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="abfinder_adaption_csv"><?php echo esc_html__('CSV File', 'auto-bulb-finder'); ?></label>
					</th>
					<td>
						<input type="file" name="abfinder_adaption_csv" id="abfinder_adaption_csv" accept=".csv" class="regular-text" />
					</td>
				</tr>
				<tr>
					<td>
						<a id="abf_adaption_import" class="button button-primary">Import Now</a>
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
		$('#abf_adaption_import').click(function(e) {
			e.preventDefault();
			$('#abf_action_loader').show();
			$('#abf_adaption_import').hide();

			$fileChoosed = $('#abfinder_adaption_csv')[0].files[0];
			if ($fileChoosed == undefined) {
				alert('Please choose a file to import.');
				return false;
			}
			
			var form_data = new FormData();
			form_data.append('action', 'auto_bulb_finder');
			form_data.append('fn', 'import_adaptions');
			form_data.append('upload', $fileChoosed);
			form_data.append('_wpnonce', '<?php echo wp_create_nonce('abfinder_import_adaption_nonce'); ?>');

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
							window.location.href = '<?php echo admin_url('admin.php?page=auto-bulb-finder-adaption'); ?>';
						}, 2000);
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