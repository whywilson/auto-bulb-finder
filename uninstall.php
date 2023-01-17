<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}


function abf_delete_plugin()
{
	global $wpdb;
	$vehicles_table = $wpdb->prefix . 'abfinder_vehicles';
	$wpdb->query("DROP TABLE IF EXISTS $vehicles_table");
	$adaptions_table = $wpdb->prefix . 'abfinder_adaptions';
	$wpdb->query("DROP TABLE IF EXISTS $adaptions_table");

	//remove all options
	delete_option('enable_vehicle_post');
	delete_option('abf_code_status');
	delete_option('abf_code_expired');
	delete_option('abf_token');
	delete_option('app_promotion_html');
	unregister_post_type('vehicle');
}

if (!defined('ABFINDER')) {
	abf_delete_plugin();
}
