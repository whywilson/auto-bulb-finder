<?php
/*
Plugin Name: Auto Bulb Finder for WP & WC - Year/Make/Model
Plugin URI:  https://auto.mtoolstec.com
Description: Year/Make/Model/BodyType/Qualifer automoive bulb size querying system for vehicles from 1960 to 2022. Online database or custom vehicle list. Add to any page or content by a shortcode <code>[abf]</code>.
Version:     2.6.0
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
{ 
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    return ABFINDER::instance();
}

add_action(
    'plugins_loaded',
    function () {
        $GLOBALS['ABFINDER'] = ABFINDER();
    }
);


register_activation_hook(__FILE__, 'abfinder_plugin_install');

register_deactivation_hook(__FILE__, 'abfinder_plugin_deactive');

function abfinder_plugin_install()
{
    add_option("enable_vehicle_post", "false");
    add_option("app_promotion_html", abfinder_get_default_app_promotion_html());
}

function abfinder_plugin_deactive()
{
    delete_option('enable_vehicle_post');
    delete_option('app_promotion_html');
    unregister_post_type('vehicle');
}

function abfinder_get_default_app_promotion_html()
{
    return ' <hr class="solid" style="margin-bottom: 12px"> ';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'abfinder_add_settings_link');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'abfinder_add_adaptions_link');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'abfinder_add_vehicles_link');

function abfinder_add_settings_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder-for-wp-wc') . '">Settings</a>',));
}

function abfinder_add_adaptions_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder-adaption') . '">Adaptions</a>',));
}

function abfinder_add_vehicles_link($links)
{
    return array_merge($links, array('<a href="' . admin_url('admin.php?page=auto-bulb-finder-vehicle') . '">Vehicles</a>',));
}

function abfinder_add_jquery()
{
    if (!is_admin()) {
        wp_enqueue_script('jquery');
    }

    if (is_admin()) {
        wp_register_script('abf-settings-js', plugins_url('assets/js/settings.js', __FILE__));
        wp_register_style('abf-settings-style', plugins_url('assets/css/style-setting.css', __FILE__));
    }
}
add_action('init', 'abfinder_add_jquery');

add_filter('autoptimize_filter_js_exclude', 'abfinder_jquery_toggle');

function abfinder_jquery_toggle($in)
{
    if (is_front_page() || strpos($_SERVER['REQUEST_URI'], 'test-page') !== false) {
        return $in . ', js/jquery/jquery.js';
    } else {
        return $in;
    }
}

function abfinder_locate_template($template_name, $template_path = '', $default_path = '')
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

function abfinder_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
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

function abfinder_vehicle_post_type()
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
    add_action('init', 'abfinder_vehicle_post_type');
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
        require_once ABFINDER_PLUGIN_FILE . 'includes/blocks/custom-block.php';

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

function abfinder_add_abfinder_ajax_actions()
{
    add_action('wp_ajax_' . 'auto_bulb_finder', 'abfinder_ajax_function');
    add_action('wp_ajax_nopriv_' . 'auto_bulb_finder', 'abfinder_ajax_function');
}

add_action('init', 'abfinder_add_abfinder_ajax_actions');

function abfinder_ajax_function()
{
    try {
        $abfinderDb = new ABFinder_Database();

        switch ($_REQUEST['fn']) {
            case 'get_token':
                $output = $abfinderDb->get_token(sanitize_text_field($_REQUEST['code']));
                break;
            case 'query_similar_bulbs':
                $output = $abfinderDb->query_similar_bulbs(sanitize_text_field($_REQUEST['search']));
                break;
            case 'import_adaptions':
                $output = $abfinderDb->import_adaptions($_FILES['upload']['tmp_name']);
                break;
            case 'export_adaptions':
                $output = $abfinderDb->export_adaptions();
                break;
            case 'import_vehicles':
                $output = $abfinderDb->import_vehicles($_FILES['upload']['tmp_name'], sanitize_text_field($_REQUEST['overwrite']));
                break;
            case 'export_vehicles':
                $output = $abfinderDb->export_vehicles(sanitize_text_field($_REQUEST['all']));
                break;
            case 'save_settings':
                $output = $abfinderDb->save_settings($_REQUEST['names'], $_REQUEST['values']);
                break;
            case 'query_vehicle':
                $output = $abfinderDb->query_vehicles($_REQUEST['query']);
                break;
            case 'revoke_token':
                $output = $abfinderDb->revoke_token();
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

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function abfinder_plugin_meta_links( $links, $file ) {

	$plugin_file = 'auto-bulb-finder-for-wp-wc/auto-bulb-finder.php';
	if ( $file == $plugin_file ) {
		return array_merge(
			$links,
			array(
				'<a target="_blank" href="https://shop.mtoolstec.com/product/auto-bulb-finder-plugin-for-woocommerce" style="color: red">' . __( 'License', 'auto-bulb-finder') . '</a>',
                '<a target="_blank" href="https://shop.mtoolstec.com/auto-bulb-finder-plugin" style="color: green">' . __( 'Demo', 'auto-bulb-finder') . '</a>',
			)
		);
	}

	return $links;

}
add_filter( 'plugin_row_meta', 'abfinder_plugin_meta_links', 10, 2 );