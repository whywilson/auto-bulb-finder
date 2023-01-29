<?php
function shortcode_auto_bulb_finder($atts = [], $content = null, $tag = '')
{
    wp_register_style('chosen-css',  ABFINDER_PLUGIN_URL . 'assets/css/chosen.min.css');
    wp_register_script('chosen-js', ABFINDER_PLUGIN_URL . 'assets/js/chosen.jquery.min.js');
    wp_register_script('abf-app-js', ABFINDER_PLUGIN_URL . 'assets/js/app.js');
    wp_register_style('abf-app-css', ABFINDER_PLUGIN_URL . 'assets/css/app.css');

    wp_enqueue_style('abf-app-css');
    wp_enqueue_script('chosen-js');
    wp_enqueue_style('chosen-css');
    wp_enqueue_script('abf-app-js');
    wp_enqueue_script('wc-add-to-cart-variation');

    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $bs_atts = shortcode_atts(
        ['vid' => '', 'year' => '', 'make' => '', 'model' => '', 'submodel' => '', 'show' => 'true'],
        $atts,
        $tag
    );

    $preloader = file_get_contents(ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-preloader.php');

    if ($bs_atts['vid'] == '' && $bs_atts['make'] == '') {
        ob_start();
        include ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-dynamic.php';
        $html = ob_get_clean();
        return $html;
    } else {
        $html = file_get_contents(ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-detail.php');
        $sizeAndProductId = json_decode(getSizeAndProducIdByVid($bs_atts['vid'], $bs_atts['year'], $bs_atts['make'], $bs_atts['model'], $bs_atts['submodel']), true);
        $bulbResultHtml = '';
        $vehicleName = $sizeAndProductId['year'] . ' ' . $sizeAndProductId['make'] . ' ' . $sizeAndProductId['model'];
        $html = str_replace("{year_make_model}", $vehicleName, $html);
        foreach ($sizeAndProductId['bulb'] as $bulb) {
            $bulbResultHtml .= '<div id="v-' . $bs_atts['vid'] . '-' . str_replace(" ", "-", $bulb['location']) . '">';
            $bulbResultHtml .= '<div style="display: -webkit-box;"><h2 style="text-transform: capitalize; width: auto;display: inline-block; ">' . $bulb['location'] . '</h2>';
            $bulbResultHtml .= '<a style="text-transform: capitalize; color: #334862" > &nbsp;&nbsp; - Bulb Size: ' . $bulb['size'] . '</a></div>';
            $bulbResultHtml .= '<div id="v-' . $bs_atts['vid'] . '-' . str_replace(" ", "-", $bulb['location']) . '-products" class="slide-box" style="transform: translateX(0px);"> ';

            if (sizeof($bulb['product']) > 0) {
                foreach ($bulb['product'] as $fitProduct) {
                    $bulbResultHtml .= '<div class="slide-item woo-product-' . $fitProduct['variation_id'] . ' medium-3 small-5 large-3 has-equal-box-heights equalize-box"> ';
                    $bulbResultHtml .= '<div style="padding: 10px">';
                    $product = wc_get_product($fitProduct['variation_id']);
                    if ($product) {
                        $bulbResultHtml .= abf_woo_block_product_grid_item_html($product);
                    }
                    $bulbResultHtml .= '  </div>';
                    $bulbResultHtml .= '</div>';
                }
            } else {
                $bulbResultHtml .= '<p><a style="color: gray;">&nbsp;&nbsp;LED for this bulb is coming soon.</a></p>';
            }


            $bulbResultHtml .= '</div>';
            $bulbResultHtml .= '</div>';
        }

        $html = str_replace("{bulb_result}", $bulbResultHtml, $html);
        $html = str_replace("{introduction}", $sizeAndProductId['seo'], $html);
        $reviews = json_decode($sizeAndProductId['review']);
        if (sizeof($reviews) > 0) {
            $reviewsHtml = '<div class="yt-slide-box">';
            $reviewIndex = 0;
            foreach ($reviews as $review) {
                $reviewsHtml .= '<div class="yt-slide-item" ';
                if ($reviewIndex == 0) {
                    $reviewsHtml .= 'style="padding-left:20px;transform:translateX(-10px)"';
                }
                $reviewsHtml .= '>';
                if (strlen($review->testimonial) > 0) {
                    $reviewsHtml .= '<blockquote>' . $review->testimonial . '   ---' . $review->youtuber . '</blockquote>';
                }
                $reviewsHtml .= '<div class="yt-video-container">';
                $reviewsHtml .= '<iframe width="1280" height="720" src="https://www.youtube.com/embed/' . $review->vid . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                $reviewsHtml .= '</div>';
                $reviewsHtml .= '</div>';
                $reviewIndex++;
            }
            $reviewsHtml .= '</div>';
            $html = str_replace("{videos}", $reviewsHtml, $html);
        }

        $html = str_replace("{reviews}", '[cusrev_all_reviews sort="DESC" sort_by="date" per_page="10" number="-1" show_summary_bar="false" show_pictures="false" show_products="true" categories="" product_tags="" products="" shop_reviews="true" number_shop_reviews="-1" inactive_products="false" show_replies="false" show_more="1" min_chars="0"]', $html);
    }

    if (strlen(trim($content)) > 0) {
        if (strpos($content, 'h1') !== false  || strpos($content, 'h2') !== false) {
        } else {
            $content = '<h2 style="text-align: center;margin-top: 6px;">' . $content . '</h2>';
        }
    }

    $html = str_replace("{preloader}", $preloader, $html);
    $html = str_replace("{content}", $content, $html);
    $html = str_replace("{promotion}", get_option("app_promotion_html", get_default_app_promotion_html()), $html);
    return $html;
}

add_shortcode('abf', 'shortcode_auto_bulb_finder');

function shortcode_auto_bulb_finder_vehicle($atts = [], $content = null, $tag = '')
{
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $bs_atts = shortcode_atts(
        [
            'return' => 'json',
            'year' => '',
            'make' => '',
            'model' => '',
            'submodel' => '',
            'bodytype' => '',
            'qualifier' => ''
        ],
        $atts,
        $tag
    );

    return $bs_atts['year'] . ' <> ' . $bs_atts['make'] . ' <> ' . $bs_atts['model'];
}

add_shortcode('abf_vehicle', 'shortcode_auto_bulb_finder_vehicle');

add_shortcode('abf_products', 'abf_products_shortcode');
function abf_products_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'ids' => '',
    ), $atts, 'abf_products');

    $ids = explode(',', $atts['ids']);
    ob_start();
?>
    <div class="woocommerce columns-5 ">
        <div class="products row row-small large-columns-5 medium-columns-3 small-columns-2">
            <?php
            foreach ($ids as $id) {
                $product = wc_get_product($id);
                if (!$product) {
                    continue;
                }
            ?> <div class="product-small col has-hover product type-product">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" target="_blank">
                        <?php echo $product->get_image(); ?>
                        <p class="name product-title woocommerce-loop-product__title"><?php echo $product->get_name(); ?></p>
                    </a>
                    <?php echo $product->get_price_html(); ?>
                    <?php
                    $args = array(
                        'quantity' => 1,
                        'class' => 'button product_type_simple add_to_cart_button ajax_add_to_cart',
                    );

                    echo apply_filters(
                        'woocommerce_loop_add_to_cart_link',
                        sprintf(
                            '<p><a href="%s" target="_blank" data-quantity="%s" class="%s" %s>%s</a></p>',
                            esc_url($product->add_to_cart_url()),
                            esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
                            esc_attr(isset($args['class']) ? $args['class'] : 'button'),
                            isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
                            esc_html($product->add_to_cart_text())
                        ),
                        $product,
                        $args
                    );
                    ?>

                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
}

add_filter('woocommerce_loop_add_to_cart_link', function ($array, $product) {
    return sprintf(
        '<p><a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="%s product_type_%s"> %s </a></p>',
        esc_url($product->add_to_cart_url()),
        esc_attr($product->get_id()),
        esc_attr($product->get_sku()),
        esc_attr(isset($quantity) ? $quantity : 1),
        $product->is_purchasable() && $product->is_in_stock() ? 'button product_type_simple add_to_cart_button ajax_add_to_cart' : '',
        esc_attr($product->get_type()),
        esc_html($product->add_to_cart_text())
    );
}, 10, 2);

function abf_ux_builder_element()
{
    add_ux_builder_shortcode('abf_ux_shortcode', array(
        'name'      => __('Auto Bulb Finder', 'auto-bulb-finder'),
        'category'  => __('Content'),
        'options' => array(
            'title'    =>  array(
                'type' => 'textfield',
                'heading' => 'Title',
                'default' => '',
            )
        ),
    ));
}
add_action('ux_builder_setup', 'abf_ux_builder_element');

function abf_ux_shortcode_func($atts)
{
    extract(shortcode_atts(array(
        'title'       => 'Find My Vehicle'
    ), $atts));

    wp_enqueue_script('abf-app-js');
    echo do_shortcode('[abf]' . $title . '[/abf]');
}
add_shortcode('abf_ux_shortcode', 'abf_ux_shortcode_func');

function getSizeAndProducIdByVid($vid = '', $year = '', $make = '', $model = '', $submodel = '')
{
    $url = 'https://auto.mtoolstec.com/queryVehicleByVid?platform=web&vid=' . $vid . '&year=' . $year . '&make=' . $make . '&model=' . $model . '&submodel=' . $submodel;
    $response = wp_remote_get($url);
    $result = wp_remote_retrieve_body($response);
    return $result;
}
