<?php

use ABFinder\Helper\ABFinder_Database; ?>

<?php include ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-loader.php'; ?>
<div id="abf-block" style="background: transparent;">
    <?php
    if ($content) {
        if (strpos($content, 'h1') !== false  || strpos($content, 'h2') !== false) {
        } else {
            $content = '<h2 style="text-align: center;margin-top: 6px;">' . $content . '</h2>';
        }

        echo wp_kses_post($content);
    }
    ?>
    <div id="selects" class="row" style="margin: 1px;">
        <div class="cell medium-3"> <select id="year" data-placeholder="Year">
                <option>Year</option>
                <?php
                $abfinderDb = new ABFinder_Database();
                $localVehhicleYears = $abfinderDb->query_local_vehicle_years();
                $localYears = array();
                foreach ($localVehhicleYears as $localVehhicleYear) {
                    $localYears[] = intval($localVehhicleYear['year']);
                }

                $abf_search_result_priority = get_option("abf_search_result_priority", 0);

                if ($abf_search_result_priority == 0) {
                    $yearRange = array_unique(array_merge($localYears));
                } else {
                    $yearRange = range(2022, 1960);
                    $yearRange = array_unique(array_merge($localYears, $yearRange));
                }

                rsort($yearRange);

                foreach ($yearRange as $year) {
                    echo wp_kses('<option value="' . $year . '" data-connection="makeSelect">' . $year . '</option>', array('option' => array('value' => array(), 'data-connection' => array())));
                }

                ?>
            </select>
        </div>
    </div>
</div>

<div id="bulb_result" style="background: white; display:none">
    <div id="bulb-size-list-content"></div>
</div>
<div id="app_promotion" style="display: none;padding: 10px;">
    <?php
    $appPromotion = get_option("app_promotion_html", abfinder_get_default_app_promotion_html());
    if ($appPromotion) {
        echo wp_kses_post($appPromotion);
    }
    ?>
</div>

<div id="quick-view-modal" class="modal" style="display:none">
    <span class="close">&times;</span>
    <div class="modal-content product-lightbox lightbox-content">
        <p id="modal-content-product"></p>
    </div>
</div>

<style>
    .icon-arrow-right:before {
        content: " ";
        padding: 10px;
        background: url(<?php echo ABFINDER_PLUGIN_URL . 'assets/images/arrow-right.svg' ?>) center center no-repeat;
        width: 10px;
    }
</style>

<script>
    jQuery(".accordion-title").each(function() {
        jQuery(this)
            .off("click.accordion")
            .on("click.accordion", function(t) {
                if (jQuery(this).next().is(":hidden")) {
                    jQuery(this)
                        .toggleClass("active")
                        .next()
                        .slideDown(200, function() {
                            /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(
                                    navigator.userAgent
                                ) &&
                                jQuery.scrollTo(jQuery(this).prev(), {
                                    duration: 300,
                                    offset: -100
                                });
                        });
                } else {
                    jQuery(this)
                        .parent()
                        .parent()
                        .find(".accordion-title")
                        .addClass("active")
                        .next()
                        .slideUp(200)
                }
            });
    });
</script>