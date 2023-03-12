<?php

/**
 * Add adaption export template
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Templates\Admin;

use ABFinder\Helper\ABFinder_Adaptions;

defined('ABSPATH') || exit;

$helper = new ABFinder_Adaptions();

require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-adaptions.php';

$helper = new ABFinder_Adaptions();

?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html__('Export Adaptions', 'auto-bulb-finder'); ?>
    </h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=auto-bulb-finder-adaption')); ?>" class="page-title-action"><?php echo esc_html__('Back', 'auto-bulb-finder'); ?></a>
    <hr />

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="abfinder_export_adaption" />
        <?php wp_nonce_field('abfinder_export_adaption'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <p>Export adaptions to CSV file.</p>
                    </th>
                <tr>
                    <td>
                        <a id="abf_adaption_export" class="button button-primary">Export Now</a>
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
        $('#abf_adaption_export').click(function() {
			$('#abf_action_loader').show();
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'auto_bulb_finder',
					fn: 'export_adaptions',
					security: '<?php echo wp_create_nonce('abf_export_adaption'); ?>'
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