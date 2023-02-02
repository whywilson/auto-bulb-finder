<?php

/**
 * Add Warehouse template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Adaptions;

defined('ABSPATH') || exit;

$helper = new ABFinder_Adaptions();

$abfinder_adaption_name = '';
$abfinder_adaption_size = '';
$abfinder_adaption_products = '';
$abfinder_adaption_fits_on = '';
$abfinder_status = '0';

if ($aid) {
	$adaption_data = $helper->abfinder_get_adaption(intval($aid));
	if (!empty($adaption_data)) {
		$abfinder_adaption_name = $adaption_data->name;
		$abfinder_adaption_size = $adaption_data->size;
		$abfinder_adaption_products = $adaption_data->products;
		$abfinder_adaption_fits_on = $adaption_data->fits_on;
		$abfinder_status = $adaption_data->status;
	}
}

$status_list = array(
	'0' => 'Enable',
	'1' => 'Disable',
);

?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php echo esc_html__('Add Adaption', 'auto-bulb-finder'); ?>
	</h1>
	<a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-adaption')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
	<hr />

	<form method="POST" action="" id="abfinder-add-adaption-form">
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label><?php echo esc_html__('Name', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					if (isset($_POST['abfinder_adaption_name'])) {
						$abfinder_adaption_name = isset($_POST['abfinder_adaption_name']) ? sanitize_text_field($_POST['abfinder_adaption_name']) : '';
					} else {
						$abfinder_adaption_name = $abfinder_adaption_name;
					}
					?>
					<td>
						<input type="text" name="abfinder_adaption_name" placeholder="Adaption Name" id="abfinder-adaption-name" class="regular-text" value="<?php echo esc_attr($abfinder_adaption_name); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<label><?php echo esc_html__('size', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					if (isset($_POST['abfinder_adaption_size'])) {
						$abfinder_adaption_size = isset($_POST['abfinder_adaption_size']) ? sanitize_text_field($_POST['abfinder_adaption_size']) : '';
					} else {
						$abfinder_adaption_size = $abfinder_adaption_size;
					}
					?>
					<td>
						<input type="text" name="abfinder_adaption_size" placeholder="Adaption Size" id="abfinder-adaption-size" class="regular-text" value="<?php echo esc_attr($abfinder_adaption_size); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<label for="abfinder-adaption-products"><?php echo esc_html__('Products', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
					</th>
					<?php
					if (isset($_POST['abfinder_adaption_products'])) {
						$abfinder_adaption_products = isset($_POST['abfinder_adaption_products']) ? sanitize_text_field($_POST['abfinder_adaption_products']) : '';
					} else {
						$abfinder_adaption_products = $abfinder_adaption_products;
					}
					?>
					<td>
						<input type="text" name="abfinder_adaption_products" placeholder="Product ids, separated by comma." id="abfinder-adaption-products" class="regular-text" value="<?php echo esc_attr($abfinder_adaption_products); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<label for="abfinder-adaption-fits-on"><?php echo esc_html__('Fits On', 'auto-bulb-finder'); ?></label>
						<span class="required">*</span>
						<div class="bulb-size-similar-search" style="border: solid 1px gray; padding: 10px; border-radius: 5px; margin: 10px 0;">
							<div class="abf-search-field" style="display: flex; padding-bottom: 10px;">
								<input type="text" name="abfinder_adaption_fits_on_query" placeholder="Bulb Size" id="abfinder-adaption-fits-on-query" class="regular-text" value="" style="width: 200px; margin-right: 10px;" />
								<button type="button" class="button button-accent" id="abfinder-adaption-fits-on-query-button"><?php echo esc_html__('Search Similar', 'auto-bulb-finder'); ?></button>
							</div>
							<div class="abf-search-result-field" style="display: flex;">
								<textarea name="abfinder_adaption_fits_on" placeholder="Similar bulb sizes" id="abfinder-bulb-size-search-result" class="regular-text" rows="5" cols="20" style="width: 200px; margin-right: 10px;"></textarea>
								<button type="button" class="button button-accent" id="abfinder-bulb-size-search-result-button" style="height: 32px;"><?php echo esc_html__('Append', 'auto-bulb-finder'); ?></button>
								<img id="abf_action_loader" src="<?php echo esc_url(ABFINDER_PLUGIN_URL . 'assets/images/loading.gif'); ?>" style="margin-left: 10px; height: 24px; display: none; vertical-align: middle;" />
							</div>
						</div>
					</th>
					<?php
					if (isset($_POST['abfinder_adaption_fits_on'])) {
						$abfinder_adaption_fits_on = isset($_POST['abfinder_adaption_fits_on']) ? sanitize_text_field($_POST['abfinder_adaption_fits_on']) : '';
					} else {
						$abfinder_adaption_fits_on = $abfinder_adaption_fits_on;
					}
					?>
					<td>
						<textarea type="text" name="abfinder_adaption_fits_on" placeholder="eg: H11,H11-55W,H11-65W" id="abfinder-adaption-fits-on" class="fits-on-textarea"><?php echo esc_attr($abfinder_adaption_fits_on); ?></textarea>
					</td>
				</tr>

				<tr>
					<th>
						<label for="abfinder-status">
							<?php echo esc_html__('Status', 'auto-bulb-finder'); ?>
							<span class="required">*</span>
						</label>
					</th>
					<td>
						<select id="abfinder-status" name="abfinder_status" class="regular-text">
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

		<input type="hidden" name="e_id" value="<?php echo esc_attr($aid); ?>" />

		<?php
		if (!empty($aid)) {
			wp_nonce_field('abfinder_adaption_nonce_action', 'abfinder_adaption_nonce');
			submit_button(esc_html__('Update to Auto Bulb Finder', 'auto-bulb-finder'), 'primary', 'update_adaption');
		} else {
			wp_nonce_field('abfinder_adaption_nonce_action', 'abfinder_adaption_nonce');
			submit_button(esc_html__('Add to Auto Bulb Finder', 'auto-bulb-finder'), 'primary', 'save_adaption');
		}
		?>

	</form>

	<script>
		jQuery('#abfinder-bulb-size-search-result-button').click(function() {
			var bulb_size_search_result = jQuery('#abfinder-bulb-size-search-result').val();
			var current_fits_on = jQuery('#abfinder-adaption-fits-on').val();
			if (current_fits_on.endsWith(','))
				jQuery('#abfinder-adaption-fits-on').val(jQuery('#abfinder-adaption-fits-on').val() + bulb_size_search_result);
			else if (current_fits_on.length > 0 && !current_fits_on.endsWith(','))
				jQuery('#abfinder-adaption-fits-on').val(jQuery('#abfinder-adaption-fits-on').val() + ',' + bulb_size_search_result);
			else if (current_fits_on.length == 0)
				jQuery('#abfinder-adaption-fits-on').val(bulb_size_search_result);
		});
		jQuery('#abfinder-adaption-fits-on-query-button').click(function() {
			var query = jQuery('#abfinder-adaption-fits-on-query').val().trim();

			if (query.length < 3) {
				alert('Please enter at least 3 characters.');
				return;
			}
			if (query.length >= 3) {
				jQuery('#abf_action_loader').show();
				jQuery('#abfinder-adaption-fits-on-query-button').prop('disabled', true);
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					type: 'POST',
					data: {
						action: 'auto_bulb_finder',
						fn: 'query_similar_bulbs',
						search: query
					},
					success: function(response) {
						var query_result = JSON.parse(response);
						console.log(query_result);
						if (query_result['data'].length > 0) {
							jQuery('#abfinder-bulb-size-search-result').val(query_result['data']);
						} else {
							jQuery('#abfinder-bulb-size-search-result').val('No similar bulb size found.');
						}
					},
					complete: function() {
						jQuery('#abf_action_loader').hide();
						jQuery('#abfinder-adaption-fits-on-query-button').prop('disabled', false);
					}
				});
			}
		});
	</script>
	<style>
		.bulb-size-similar-search ::placeholder {
			color: lightslategray;
			font-weight: normal;
		}
	</style>
</div>