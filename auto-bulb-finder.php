<?php
/*
Plugin Name: Auto Bulb Finder for WP & WC
Plugin URI:  https://auto.mtoolstec.com
Description: Year/Make/Model/BodyType/Qualifer automoive bulb size querying system for vehicles from 1960 to 2022. Online database or custom vehicle list. Add to any page or content by a shortcode <code>[abf]</code>.
Version:     2.4.2
Author:      MTools Tec
Author URI:  https://shop.mtoolstec.com/about-us/
License:     GPL
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/


use ABFinder\Includes\Admin;
use ABFinder\Helper\ABFinder_Database;

defined('ABSPATH') || exit();

ob_start();
// Define Constants.
defined('ABFINDER_PLUGIN_FILE') || define('ABFINDER_PLUGIN_FILE', plugin_dir_path(__FILE__));
//defile plugin url
defined('ABFINDER_PLUGIN_URL') || define('ABFINDER_PLUGIN_URL', plugin_dir_url(__FILE__));


// Include the main ABFINDER class.
if (!class_exists('ABFINDER', false)) {
}

/**
 * Returns the main instance of ABFINDER.
 *
 * @since  1.0.0
 * @return ABFINDER
 */
function ABFINDER()
{ // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (function_exists('WC')) {
        return ABFINDER::instance();
    }
}

add_action(
    'plugins_loaded',
    function () {
        $GLOBALS['ABFINDER'] = ABFINDER();
    }
);


register_activation_hook(__FILE__, 'auto_bulb_finder_install');

register_deactivation_hook(__FILE__, 'auto_bulb_finder_deactive');

function auto_bulb_finder_install()
{
    add_option("enable_vehicle_post", "false");
    add_option("app_promotion_html", get_default_app_promotion_html());
}

function auto_bulb_finder_deactive()
{
    delete_option('enable_vehicle_post');
    delete_option('app_promotion_html');
    unregister_post_type('vehicle');
}

function get_default_app_promotion_html()
{
    return ' <hr class="solid" style="margin-bottom: 12px">  <p>Get Full Bulb Size on Auto Bulb Finder App.</p>  <p style="text-align: left;"> <a class="bullet-btn" style="background-image: linear-gradient(#3bc5ff, #5c8feb); color: white;" href="https://apps.apple.com/us/app/anyvalue/id1547269180" target="_blank" rel="noopener">App Store</a> <a class="bullet-btn" style="background-image: linear-gradient(#0FBEFC, #19E46C); color: white;" href="https://play.google.com/store/apps/details?id=com.automotive.mtools&hl=en_US&gl=US" target="_blank" rel="noopener">Play Store</a>  </p>  <style type="text/css"> .bullet-btn { border-radius: 20px; border-width: 2px; padding: 4px 12px; color: white; background-color: dodgerblue; text-decoration: none }  </style>';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'abf_add_settings_link');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_ab_finder_adaptions_link');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_ab_finder_vehicles_link');

function abf_add_settings_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder') . '">Settings</a>',));
}

function add_ab_finder_adaptions_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder-adaption') . '">Adaptions</a>',));
}

function add_ab_finder_vehicles_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder-vehicle') . '">Vehicles</a>',));
}


function auto_bulb_finder_config_html()
{
    require dirname(__FILE__) . '/templates/admin/settings/class-abfinder-setting.php';
}

function abf_add_jquery()
{
    if (!is_admin()) {
        wp_enqueue_script('jquery');
    }

    if (is_admin()) {
        wp_register_script('abf-settings-js', plugins_url('assets/js/settings.js', __FILE__));
        wp_register_style('abf-settings-style', plugins_url('assets/css/style-setting.css', __FILE__));
    }
}
add_action('init', 'abf_add_jquery');

add_filter('autoptimize_filter_js_exclude', 'abf_jquery_toggle');

function abf_jquery_toggle($in)
{
    if (is_front_page() || strpos($_SERVER['REQUEST_URI'], 'test-page') !== false) {
        return $in . ', js/jquery/jquery.js';
    } else {
        return $in;
    }
}

function abf_woo_block_product_grid_item_html($_product)
{
    global $product, $post;
    $product = $_product;
    $post    = get_post($_product->get_id());
    $html    = abf_wc_get_template_part_str('content-product-simple');
    $html = str_replace('a href=', 'a target="_blank" href=', $html);
    return $html;
}

function abf_wc_get_template_part_str($template_name, $part_name = null)
{
    ob_start();
    wc_get_template_part($template_name, $part_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

function abf_get_template_html()
{
    ob_start();
    abf_get_template('product-item');
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

function abf_locate_template($template_name, $template_path = '', $default_path = '')
{
    // Set variable to search in woocommerce-plugin-templates folder of theme.
    if (!$template_path) :
        $template_path = 'auto-bulb-finder/';
    endif;

    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = plugin_dir_path(__FILE__) . 'templates/woocommerce/';
    endif;

    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));

    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;

    return apply_filters('wcpt_locate_template', $template, $template_name, $template_path, $default_path);
}

function abf_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
{
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;
    $template_file = abf_locate_template($template_name, $tempate_path, $default_path);
    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
        return;
    endif;
    include $template_file;
}

function abf_vehicle_post_type()
{
    register_post_type(
        'vehicle',
        [
            'labels'      => [
                'name'          => __('Vehicles'),
                'singular_name' => __('vehicles'),
            ],
            'public'      => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'comments', 'revisions'),
            'rewrite' => [
                'slug'       => 'vehicle', // string (默认为文章类型名称)
                'with_front' => false, // bool (默认为 TRUE)

                // 是否允许文章类型中的文章通过 <!--nextpage--> 快捷标签实现分页
                'pages'      => true, // bool (默认为 TRUE)

                // 是否为订阅源创建漂亮的固定链接feeds.
                'feeds'      => true, // bool (默认为 'has_archive' 的值)

                // 为固定链接设置设置 endpoint 遮罩
                'ep_mask'    => EP_PERMALINK,
            ],
        ]
    );
}

if (get_option('enable_vehicle_post', 'false') == "true") {
    add_action('init', 'abf_vehicle_post_type');
}

final class ABFINDER
{

    /**
     * Group wpdb
     *
     * @var wpdb
     */
    protected $wpdb;
    /**
     * The single instance of the class.
     *
     * @var WooCommerce
     * @since 2.1
     */
    protected static $_instance = null;

    /**
     * Main WooCommerce Instance.
     *
     * Ensures only one instance of WooCommerce is loaded or can be loaded.
     *
     * @since 2.1
     * @static
     * @see WC()
     * @return ABFinder - Main instance.
     */

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * ABFinder Constructor.
     */
    public function __construct()
    {
        $this->abfinder_includes();
        global $wpdb;
        $this->wpdb = $wpdb;
    }


    public function abfinder_includes()
    {

        /**
         * Class autoloader.
         */
        require_once ABFINDER_PLUGIN_FILE . 'inc/autoload.php';
        require_once ABFINDER_PLUGIN_FILE . 'includes/admin/class-abfinder-admin-hooks.php';
        require_once ABFINDER_PLUGIN_FILE . 'includes/admin/class-abfinder-admin-functions.php';
        require_once ABFINDER_PLUGIN_FILE . 'templates/admin/class-abfinder-admin-templates.php';
        require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-database.php';
        require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-adaptions.php';
        require_once ABFINDER_PLUGIN_FILE . 'helper/class-abfinder-vehicles.php';

        if ($this->abfinder_is_request('frontend')) {
            wp_enqueue_script('jquery');
        } else {
            new Admin\ABFINDER_Admin_Hooks();
        }
    }

    private function abfinder_is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
}

if (!function_exists('abfinder_create_table')) {
    require_once ABFINDER_PLUGIN_FILE . 'includes/class-abfinder-install.php';
    $schema_handler = new ABFinder_Install();
    register_activation_hook(__FILE__, array($schema_handler, 'init'));
}

require_once ABFINDER_PLUGIN_FILE . 'includes/blocks/custom-block.php';

function abf_add_abfinder_ajax_actions()
{
    add_action('wp_ajax_' . 'auto_bulb_finder', 'abfinder_ajax_function');
    add_action('wp_ajax_nopriv_' . 'auto_bulb_finder', 'abfinder_ajax_function');
}

add_action('init', 'abf_add_abfinder_ajax_actions');

function abfinder_ajax_function()
{
    try {
        $abfinderDb = new ABFinder_Database();

        switch ($_REQUEST['fn']) {
            case 'query_vehicle':
                $output = $abfinderDb->query_vehicles($_REQUEST['query']);
                break;
            case 'get_token':
                $output = $abfinderDb->get_token(sanitize_text_field($_REQUEST['code']));
                break;
            case 'revoke_token':
                $output = $abfinderDb->revoke_token();
                break;
            case 'query_vehicle_by_vid':
                $output = $abfinderDb->query_vehicle_by_vid(sanitize_text_field($_REQUEST['query']), 'woo');
                break;
            case 'query_similar_bulbs':
                $output = $abfinderDb->query_similar_bulbs(sanitize_text_field($_REQUEST['search']));
                break;
            case 'get_products_by_location_size':
                $output = get_abfinder_woo_shortcode_html(sanitize_text_field($_REQUEST['size']));
                break;
            case 'get_products_html':
                $output = get_abfinder_woo_shortcode_html(sanitize_text_field($_REQUEST['ids']));
                break;
            case 'save_settings':
                $output = $abfinderDb->save_settings($_REQUEST['names'], $_REQUEST['values']);
                break;
            case 'import_vehicles':
                $fileName = preg_replace('/\s+/', '-', $_FILES["upload"]["name"]);
                $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);

                $tmpFileName = $_FILES['upload']['tmp_name'];

                $output = $abfinderDb->import_vehicles($tmpFileName, sanitize_text_field($_REQUEST['overwrite']));
                break;
            case 'export_vehicles':
                $output = $abfinderDb->export_vehicles(sanitize_text_field($_REQUEST['all']));
                break;
            default:
                $output = 'No function specified.';
                break;
        }

        $output = json_encode($output);
      
        wp_send_json($output);
    } catch (\Throwable $th) {
        wp_send_json_error($th->getMessage());
    }

    wp_die();
}

function abfinder_save_settings($names = [], $values = [])
{
    foreach ($names as $key => $name) {
        update_option($name, str_replace("\\", "", $values[$key]));
    }
    return array('msg' => 'Saved');
}

function get_abfinder_woo_shortcode_html($ids)
{
    return abfinder_woo_shortcode_html($ids);
}

function abfinder_woo_shortcode_html($ids = [])
{
    echo do_shortcode('[woo ids="' . implode(",", $ids) . '"][/woo]');
    die;
}


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

//Get Product Button
function get_flatsome_lightbox_button()
{
    if (get_theme_mod('disable_quick_view', 0)) {
        return;
    }

    // Run Quick View Script.
    wp_enqueue_script('wc-add-to-cart-variation');

    global $product;
    return '  <a target="_blank" class="quick-view primary is-small mb-0 button product_type_simple add_to_cart_button ajax_add_to_cart is-flat text_replaceable" data-prod="' . $product->get_id() . '" href="' . get_permalink($product->get_id()) . '">' . __('Buy Now', 'flatsome') . '</a>';
}

//Return quick view button for viariable product
function flatsome_woocommerce_loop_add_to_cart_link_variable_quick_view($link, $product, $args)
{
    if (!doing_action('flatsome_product_box_actions') && !doing_action('flatsome_product_box_after')) {
        return $link;
    }

    switch (get_theme_mod('add_to_cart_icon', 'disabled')) {
        case 'show':
            $insert = '<div class="cart-icon tooltip is-small" title="' . esc_html($product->add_to_cart_text()) . '"><strong>+</strong></div>';
            $link   = preg_replace('/(<a.*?>).*?(<\/a>)/', '$1' . $insert . '$2', $link);
            break;
        case 'button':
            if ($product->product_type == 'variable') {
                $link = '<div class="add-to-cart-button"> ' . get_flatsome_lightbox_button() . '</div>';
            } else {
                $link = '<div class="add-to-cart-button">' . $link . '</div>';
            }
            break;
        default:
            return $link;
    }

    return $link;
}

add_filter('woocommerce_loop_add_to_cart_link', 'flatsome_woocommerce_loop_add_to_cart_link_variable_quick_view', 10, 3);

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
