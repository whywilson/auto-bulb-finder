<?php
function abfinder_shortcode_abf($atts = [], $content = null, $tag = '')
{
    wp_register_style('chosen-css',  ABFINDER_PLUGIN_URL . 'assets/css/chosen.min.css');
    wp_register_script('chosen-js', ABFINDER_PLUGIN_URL . 'assets/js/chosen.jquery.min.js');
    wp_register_style('abf-app-css', ABFINDER_PLUGIN_URL . 'assets/css/app.css');

    wp_enqueue_style('abf-app-css');
    wp_enqueue_script('chosen-js');
    wp_enqueue_style('chosen-css');
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    ob_start();
    include ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-dynamic.php';
    $html = ob_get_clean();
    return $html;
}

function getSizeAndProducIdByVid($vid = '', $year = '', $make = '', $model = '', $submodel = '')
{
    $url = 'https://auto.mtoolstec.com/queryVehicleByVid?platform=web&vid=' . $vid . '&year=' . $year . '&make=' . $make . '&model=' . $model . '&submodel=' . $submodel;
    $response = wp_remote_get($url);
    $result = wp_remote_retrieve_body($response);
    return $result;
}

add_shortcode('abf', 'abfinder_shortcode_abf');

function abfinder_shortcode_abf_vehicle($atts = [], $content = null, $tag = '')
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

add_shortcode('abf_vehicle', 'abfinder_shortcode_abf_vehicle');

add_shortcode('abf_products', 'abfinder_products_shortcode');
function abfinder_products_shortcode($atts)
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
            if (is_woocommerce_activated()) {
                foreach ($ids as $id) {
                    $product = wc_get_product($id);
                    if (!$product) {
                        continue;
                    }
            ?> <div class="product-small col has-hover product type-product">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" target="_blank">
                            <?php echo $product->get_image(); ?>
                            <p class="name product-title woocommerce-loop-product__title"><?php echo esc_html($product->get_name()); ?></p>
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

function abfinder_ux_builder_element()
{
    add_ux_builder_shortcode('abfinder_ux_shortcode', array(
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
add_action('ux_builder_setup', 'abfinder_ux_builder_element');

function abfinder_ux_shortcode_func($atts)
{
    extract(shortcode_atts(array(
        'title'       => 'Find My Vehicle'
    ), $atts));

    wp_enqueue_script('abf-app-js');
    echo do_shortcode(force_balance_tags('[abf]' . $title . '[/abf]'));
}
add_shortcode('abfinder_ux_shortcode', 'abfinder_ux_shortcode_func');

if (!function_exists('is_woocommerce_activated')) {
    function is_woocommerce_activated()
    {
        if (class_exists('woocommerce')) {
            return true;
        } else {
            return false;
        }
    }
}

//check options of enable-my-vehicles, if 1, add My Vehicle Tab to my-account page, load templates/front/abfinder-my-vehicles.php on my-account page
if (get_option('abf_enable_my_vehicles') == 1) {
    add_action('init', 'abfinder_register_my_vehicles_endpoint');
    function abfinder_register_my_vehicles_endpoint()
    {
        add_rewrite_endpoint('my-vehicles', EP_ROOT | EP_PAGES);
    }
    add_filter('woocommerce_account_menu_items', 'abfinder_add_my_vehicles_tab', 40);
    function abfinder_add_my_vehicles_tab($items)
    {
        $items['my-vehicles'] = __('My Vehicles', 'auto-bulb-finder');
        return $items;
    }

    function abfinder_my_vehicles_endpoint_content()
    {
        include ABFINDER_PLUGIN_FILE . 'templates/front/abfinder-my-vehicles.php';
    }

    add_action('woocommerce_account_my-vehicles_endpoint', 'abfinder_my_vehicles_endpoint_content');
}
