<?php

defined('ABSPATH') || exit;
?>

<div>
    <h1 style="display:inline-block;font-size: 2em;">Auto Bulb Finder</h1>
    <hr class="solid" style="margin-bottom: 24px">
    <h2> Year Make Model Selector </h2>
    <div>
        <p>Add the short code <strong>[abf]</strong> or <strong>[abf] Find My Vehicle [/abf]</strong> in any post or page.</p>
        <p><strong>Find My Vehicle</strong> is the title above selector.</p>
        <p> 1. If it is only text, it will be contained with h2 label.</p>
        <p> 2. If it is html content, it will be displayed directly.</p>
    </div>
    <h2>Size Adaption</h2>
    <div>
        <p>
            Auto Bulb Finder will match Adaption according to fits_on column, and return the product information with product id.
        </p>
    </div>
    <h2>Query Result</h2>
    <div class="configuation-intro">
        <p>
            The query result will show the vehicles from Auto Bulb Finder server and the vehicles from the store database. <br>You can choose the result from store only or merge the result from server.
        </p>
        <p>
            <ul>
                <li>Store Only: The result will only show the vehicles from store database.</li>
                <li>Store & Online Database: Over 50000 vehicles will be load on your store.</li>
            </ul>
        </p>
    </div>
    <div>
        <select id="search-result-priority">
            <option value=0 <?php echo get_option("abf_search_result_priority", 0) == 1 ? 'selected' : ''; ?>>Store Only</option>
            <option value=1 <?php echo get_option("abf_search_result_priority", 0) == 1 ? 'selected' : ''; ?>>Store & Online Database</option>
        </select>
    </div>
    <div id="abf-service-registration-status" style="display: none;">
        <?php require_once ABFINDER_PLUGIN_FILE . 'templates/admin/settings/class-abfinder-registration.php'; ?>
    </div>
    <?php require_once ABFINDER_PLUGIN_FILE . 'templates/admin/settings/class-abfinder-status.php'; ?>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery("#search-result-priority").change(function() {
            if (jQuery(this).val() == 1) {
                jQuery("#abf-service-registration-status").show();
            } else {
                jQuery("#abf-service-registration-status").hide();
            }
        });
        jQuery("#search-result-priority").trigger("change");
    });
</script>

<div>
    <h2>Content After Result</h2>
    <form method="POST" action="">
        <table class="form-table" style="max-width: 600px;"">
            <tr style=" vertical-align: top;">
            <td>
                <label>Promotion Content(Html or ShortCode)</label>
                <?php
                $content = get_option("app_promotion_html", abfinder_get_default_app_promotion_html());
                $editor_id = 'app-promotion';
                $settings = array(
                    'textarea_name' => 'app-promotion',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                    'teeny' => true,
                    'quicktags' => true,
                    'tinymce' => array(
                        'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,unlink,|,undo,redo',
                        'toolbar2' => '',
                        'toolbar3' => '',
                        'toolbar4' => ''
                    )
                );
                wp_editor($content, $editor_id, $settings);
                ?>
            </td>
            </tr>
            <tr valign="top">
                <td>
                    <button id="save_abf_settings" class="button-primary"> Save All Settings</button>
                    <img id="abf_loader" src="<?php echo ABFINDER_PLUGIN_URL . 'assets/images/loading.gif'; ?>" style="display: none; vertical-align: middle;" />
                    <a id="save_abf_settings_result"></a>
                </td>
            </tr>
        </table>
    </form>
</div>

<style>
    .abf-status-table tr td {
        border-radius: 24px;
        padding: 0 24px;
        color: white;
        font-size: 10px;
        font-weight: bold;
        background: #a6a6a6;
        text-align: center;
    }

    .abf-status-table .notice {
        margin: 5px 15px 2px 0;
    }

    .status-label {
        width: 72PX;
        text-align: left;
    }

    .abf-status.valid {
        background: green;
    }

    .abf-status.invalid {
        background: #ff6f6f;
    }

    .abf-status.warning {
        background: #ff741f;
    }
</style>