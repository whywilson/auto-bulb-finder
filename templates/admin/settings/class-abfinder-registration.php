<?php
$abfToken = get_option("abf_token", false);
$abfCodeStatus = get_option("abf_code_status", 0);
$abfCodeExpiration = get_option("abf_code_expired", "");
$abfCodeExpired = false;

if ($abfCodeExpiration) {
    if (strtotime($abfCodeExpiration) < date(time())) {
        $abfCodeExpired = true;
        update_option("abf_code_status", 2);
        $abfCodeStatus = 2;
    }
}

if (!$abfToken || !$abfCodeStatus || $abfCodeExpired || $abfCodeStatus != 1) : ?>
    <h2>Online Database Registration</h2>
    <div class="abf-service-status">
        <table>
            <tbody>
                <tr>
                    <td>
                        <input id="abf_code" type="password" class="" placeholder="License Code" value="">
                        <button id="abf_code_submit" style="min-height: 30px;">Submit</button>
                        <img id="abf_code_loader" src="<?php echo ABFINDER_PLUGIN_URL . 'assets/images/loading.gif'; ?>" style="height: 24px; display: none; vertical-align: middle;" />
                    </td>
                </tr>
                <?php
                if (!$abfToken) :
                    echo '<tr><td><div class="notice notice-error is-dismissible"> <p> Not registered to Auto Bulb Online Database. <a href="https://shop.mtoolstec.com/product/auto-bulb-finder-plugin-for-woocommerce" target="_blank">Purchase License Code</a></p> </div></td></tr>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
    <?php if ($abfCodeExpired || $abfCodeStatus == 2) :
        echo '<div class="notice notice-error is-dismissible"> <p> License Code Expired.</p> </div>';
    endif;
    ?>
<?php endif; ?>

<div id="site_notice"> </div>