<?php

/**
 * Admin End Templates
 *
 * @package Auto Bulb Finder
 * @since 1.0.0
 */

namespace ABFinder\Helper;

use ABFinder\Helper\ABFinder_Adaptions;
use ABFinder\Helper\ABFinder_Vehicles;

defined('ABSPATH') || exit;

class ABFinder_Database
{
	/**
	 * DB global variable
	 *
	 * @var Object
	 */
	protected $wpdb;
	/**
	 * The table name
	 *
	 * @var Object
	 */
	protected $table_name;

	protected $base_url = 'https://auto.mtoolstec.com/';

	public function query_local_vehicle_years()
	{
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix . 'abfinder_vehicles';
		$sql = "SELECT DISTINCT year FROM {$this->table_name} WHERE status = 0 ORDER BY year DESC";
		$result = $this->wpdb->get_results($sql, ARRAY_A);
		return $result;
	}

	public function query_local_vehicles($query)
	{
		$this->wpdb = $GLOBALS['wpdb'];
		$store_query_result = array();
		$this->table_name = $this->wpdb->prefix . 'abfinder_vehicles';
		switch ($query['key']) {
			case 'yearSelect':
				$sql = "SELECT DISTINCT year FROM {$this->table_name} WHERE year != '' AND status = 0 ORDER BY year ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result['key'] = 'makeSelect';
					$store_query_result['defaultText'] = 'Year';
					$store_query_result['value'] = $result;
					$store_query_result['select'] = 'year';
					break;
				} else {
					$query['key'] = 'makeSelect';
					return $this->query_local_vehicles($query);
				}
			case 'makeSelect':
				$sql = "SELECT DISTINCT make FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make != '' AND status = 0 ORDER BY make ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result['key'] = 'modelSelect';
					$store_query_result['defaultText'] = 'Make';
					$store_query_result['value'] = $result;
					$store_query_result['select'] = 'make';
					break;
				} else {
					$query['key'] = 'modelSelect';
					return $this->query_local_vehicles($query);
				}
				$query['make'] = '';
			case 'modelSelect':
				$sql = "SELECT DISTINCT model FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make LIKE '%{$query['make']}%' AND model != '' AND status = 0 ORDER BY model ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result['key'] = 'submodelSelect';
					$store_query_result['defaultText'] = 'Model';
					$store_query_result['value'] = $result;
					$store_query_result['select'] = 'model';
					break;
				} else {
					$query['key'] = 'submodelSelect';
					return $this->query_local_vehicles($query);
				}
				$query['model'] = '';
			case 'submodelSelect':
				$sql = "SELECT DISTINCT submodel FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make LIKE '%{$query['make']}%' AND model LIKE '%{$query['model']}%' AND submodel != '' ORDER BY submodel ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result['key'] = 'bodytypeSelect';
					$store_query_result['defaultText'] = 'Submodel';
					$store_query_result['value'] = $result;
					$store_query_result['select'] = 'submodel';
					break;
				} else {
					$query['key'] = 'bodytypeSelect';
					return $this->query_local_vehicles($query);
				}
				$query['submodel'] = '';
			case 'bodytypeSelect':
				$sql = "SELECT DISTINCT bodytype FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make LIKE '%{$query['make']}%' AND model LIKE '%{$query['model']}%' AND submodel LIKE '%{$query['submodel']}%' AND bodytype != ''  ORDER BY bodytype ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result = array();
					$store_query_result['key'] = 'qualifierSelect';
					$store_query_result['defaultText'] = 'Bodytype';
					$store_query_result['value'] = $result;
					$store_query_result['select'] = 'bodytype';
					break;
				} else {
					$query['key'] = 'qualifierSelect';
					return $this->query_local_vehicles($query);
				}

				$query['bodytype'] = '';
			case 'qualifierSelect':
				$sql = "SELECT DISTINCT qualifier FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make LIKE '%{$query['make']}%' AND model LIKE '%{$query['model']}%' AND submodel LIKE '%{$query['submodel']}%' AND qualifier LIKE '%{$query['bodytype']}%' AND qualifier != '' AND status = 0 ORDER BY qualifier ASC";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				$store_query_result['value'] = $result;
				if (!empty($result)) {
					$store_query_result['defaultText'] = 'Qualifier';
					$store_query_result['key'] = 'selectVehicle';
					$store_query_result['select'] = 'qualifier';
					break;
				} else {
					$query['key'] = 'selectVehicle';
					return $this->query_local_vehicles($query);
				}
			case 'selectVehicle':
				$store_query_result['defaultText'] = 'Vehicle';
				$sql = "SELECT * FROM {$this->table_name} WHERE year LIKE '%{$query['year']}%' AND make LIKE '%{$query['make']}%' AND model LIKE '%{$query['model']}%' AND submodel LIKE '%{$query['submodel']}%' AND bodytype LIKE '%{$query['bodytype']}%' AND qualifier LIKE '%{$query['qualifier']}%'";
				$result = $this->wpdb->get_results($sql, ARRAY_A);
				if (!empty($result)) {
					$store_query_result['value'] = $result;
					$vehicle = $result[0];
					$store_query_result['year'] = $vehicle['year'];
					$store_query_result['make'] = $vehicle['make'];
					$store_query_result['model'] = $vehicle['model'];
					$store_query_result['submodel'] = $vehicle['submodel'];
					$store_query_result['bodytype'] = $vehicle['bodytype'];
					$store_query_result['qualifier'] = $vehicle['qualifier'];
					$store_query_result['vid'] = $vehicle['id'];
					$store_query_result['id'] = 'vehicle';

					$bulb_size = $vehicle['bulb_size'];
					$bulb_size = explode(';', $bulb_size);
					$items = array();
					$helper = new ABFinder_Adaptions();
					foreach ($bulb_size as $bulb) {
						$bulb = explode(':', $bulb);
						$bulb_position = str_replace(' ', '_', strtolower(trim($bulb[0])));
						if (empty($bulb_position)) continue;
						$bulb_model = $bulb[1];
						$productIds = $helper->abfinder_get_adaption_by_size($bulb_model);

						$items[$bulb_position] = [
							'size' => $bulb_model,
							'products' => $productIds,
							'html' => do_shortcode('[products columns="5" ids="' . implode(",", $productIds) . '"]')
						];
					}

					$store_query_result['items'] = $items;
					$store_query_result['select'] = 'vehicle';
					return $store_query_result;
				}
				break;
			default:
				break;
		}
		$store_query_result['id'] = $store_query_result['key'];
		$items = array();
		foreach ($store_query_result['value'] as $item) {
			$items[$item[$store_query_result['select']]] = [$store_query_result['key'], $item[$store_query_result['select']], $store_query_result['select']];
		}
		$store_query_result['items'] = $items;
		return $store_query_result;
	}


	public function get_local_exclude_vehicles($query, $select)
	{
		$limit = '';
		switch ($select) {
			case 'year':
				$limit = " AND year = '' AND model = '' AND submodel = '' AND bodytype = '' AND qualifier = ''";
			case 'make':
				$limit = " AND model = '' AND submodel = '' AND bodytype = '' AND qualifier = ''";
				break;
			case 'model':
				$limit = " AND submodel = '' AND bodytype = '' AND qualifier = ''";
				break;
			case 'submodel':
				$limit = " AND bodytype = '' AND qualifier = ''";
				break;
			case 'bodytype':
				$limit = " AND qualifier = ''";
				break;
			case 'qualifier':
				$limit = "";
				break;
			default:
				break;
		}

		$condition = '';
		$condition .= array_key_exists('year', $query) ? "AND year LIKE '%{$query['year']}%'" : '';
		$condition .= array_key_exists('make', $query) ? "AND make LIKE '%{$query['make']}%'" : '';
		$condition .= array_key_exists('model', $query) ? "AND model LIKE '%{$query['model']}%'" : '';
		$condition .= array_key_exists('submodel', $query) ? "AND submodel LIKE '%{$query['submodel']}%'" : '';
		$condition .= array_key_exists('bodytype', $query) ? "AND bodytype LIKE '%{$query['bodytype']}%'" : '';
		$condition .= array_key_exists('qualifier', $query) ? "AND qualifier LIKE '%{$query['qualifier']}%'" : '';

		$sql = "SELECT " . $select . " FROM {$this->table_name} WHERE status = 1 " . $condition . $limit;

		$vehicles = $this->wpdb->get_results($sql, ARRAY_A);

		return $vehicles;
	}
	public function query_vehicles($query, $ip_address)
	{
		$ip_address = isset($ip_address) ? $ip_address : "";
		$store_result = $this->query_local_vehicles($query);

		$country = $query['country'];
		$platform = 'wp';
		$key = $query['key'];
		$year = $query['year'];
		$make = array_key_exists("make", $query) ? $query['make'] : null;
		$model = array_key_exists("model", $query) ? $query['model'] : null;
		$submodel = array_key_exists("submodel", $query) ? $query['submodel'] : null;
		$bodytype = array_key_exists("bodytype", $query) ? $query['bodytype'] : null;
		$qualifier = array_key_exists("qualifier", $query) ? $query['qualifier'] : null;

		$query_result = array();
		$query_result['query'] = $query;
		$query_result['store'] = $store_result;

		$abf_search_result_priority = get_option("abf_search_result_priority", 0);

		if ($abf_search_result_priority == 0) {
			$query_result['id'] = $store_result['id'];
			$query_result['key'] =  $store_result['key'];
			$query_result['select'] =  $store_result['select'];
			$query_result['defaultText'] =  $store_result['defaultText'];
			$query_result['items'] =  $store_result['items'];
		} else if ($abf_search_result_priority == 1) {
			$abf_token = get_option('abf_token', site_url());

			$url = $this->base_url . 'checkVehicle?';
			$url .= "plugin=v3&";
			$url .= "platform=" . $platform . "&";
			$url .= "country=" . $country . "&";
			$url .= "key=" . $key . "&";
			$url .= "year=" . $year . "&";
			$url .= "token=" . $abf_token . "&";
			$url .= isset($make) ? "make=" . $make . "&" : "";
			$url .= isset($model) ? "model=" . $model . "&" : "";
			$url .= isset($submodel) ? "submodel=" . $submodel . "&" : "";
			$url .= isset($bodytype) ? "bodytype=" . $bodytype . "&" : "";
			$url .= isset($qualifier) ? "qualifier=" . $qualifier : "";
			$url = str_replace(' ', '%20', $url);

			$remote_response = wp_remote_get($url);
			$remote_code = wp_remote_retrieve_response_code($remote_response);

			if ($remote_code != 200) {
				$query_result['id'] = $store_result['id'];
				$query_result['key'] =  $store_result['key'];
				$query_result['select'] =  $store_result['select'];
				$query_result['defaultText'] =  $store_result['defaultText'];
				$query_result['items'] =  $store_result['items'];
				return $query_result;
			}

			try {
				$remote_body = wp_remote_retrieve_body($remote_response);
				$json = json_decode($remote_body, true);

				if($json['id'] == 'none'){
					$query_result['id'] = $store_result['id'];
					$query_result['key'] =  $store_result['key'];
					$query_result['select'] =  $store_result['select'];
					$query_result['defaultText'] =  $store_result['defaultText'];
					$query_result['items'] =  $store_result['items'];
					return $query_result;
				}

				if ($json['id'] == 'vehicle') {
					if (array_key_exists('bulb', $json)) {
						unset($json['bulb']);
					}

					//save vehicle to query history with function abfinder_add_vehicle_query_history
					try {
						$vid = $json['vid'];
						$year = $json['year'];
						$make = $json['make'];
						$model = $json['model'];
						$submodel = $json['submodel'];
						$bodytype = $json['bodytype'];
						$qualifier = $json['qualifier'];
						$this->insert_vehicle_query_history($vid, $year, $make, $model, $submodel, $bodytype, $qualifier, $ip_address);
					} catch (\Throwable $th) {
					}

					$bulbs = $json['items'];

					//if bulbs is not empty
					if (is_array($bulbs) && !empty($bulbs)) {
						$helper = new ABFinder_Adaptions();
						foreach ($bulbs as $key => $bulb) {
							$bulbSize = $bulb[0];
							unset($bulbs[$key]);
							$productIds = $helper->abfinder_get_adaption_by_size($bulbSize);

							$bulbs[$key] = [
								'size' => $bulbSize,
								'products' => $productIds,
								'html' => do_shortcode('[abf_products ids="' . implode(",", $productIds) . '"]')
							];
						}
						$json['items'] = $bulbs;
					}else{
						$json['items'] = ['bulb' => ['size' => 'No Informations', 'products' => [], 'html' => '']];
					}
				} else {
					$excludeVehicles = $this->get_local_exclude_vehicles($query, $json['select']);
					foreach ($excludeVehicles as $excludeVehicle) {
						$excludeSelectValue = $excludeVehicle[$json['select']];
						if (array_key_exists($excludeSelectValue, $json['items'])) {
							unset($json['items'][$excludeSelectValue]);
						}
					}
					$query_result['exclude'] = $excludeVehicles;
				}

				if ($query_result['select'] == "vehicle") {
					if (!empty($store_result['items'])) {
						$store_result_items = $store_result['items'];
						$remote_result_items = $json['items'];
						foreach ($store_result_items as $store_key => $store_item) {
							if (!array_key_exists($store_key, $remote_result_items)) {
								$remote_result_items[$store_key] = $store_item;
							}
						}
						ksort($remote_result_items);
						$query_result['items'] = $remote_result_items;
					}
				} else {
					if (!empty($store_result['items'])) {
						$store_result_items = $store_result['items'];
						$remote_result_items = $json['items'];
						foreach ($store_result_items as $store_key => $store_item) {
							if (array_key_exists($store_key, $remote_result_items)) {
								$remote_result_items[$store_key] = array_merge($remote_result_items[$store_key], $store_item);
							} else {
								$remote_result_items[$store_key] = $store_item;
							}
						}
						ksort($remote_result_items);
						$query_result['items'] = $remote_result_items;
					} else {
						$query_result['items'] = $json['items'];
					}
				}
			} catch (\Throwable $th) {
				$json = ['error' => $th->getMessage() , 'items' => []];
			}

			$query_result['remote'] = $json;
			$query_result['id'] = empty($json['items']) ? $store_result['id'] : (array_key_exists('id', $json) ? $json['id'] : $store_result['id']);
			$query_result['key'] = empty($json['items']) ? $store_result['key'] : (array_key_exists('key', $json) ? $json['key'] : $store_result['key']);
			$query_result['select'] = empty($json['items']) ? $store_result['select'] : (array_key_exists('select', $json) ? $json['select'] : $store_result['select']);
			$query_result['defaultText'] = empty($json['items']) ? $store_result['defaultText'] : (array_key_exists('defaultText', $json) ? $json['defaultText'] : $store_result['defaultText']);
			if (empty($json['items'])) {
				if (isset($json['defaultText'])) {
					$query_result['defaultText'] = $json['defaultText'];
				}
			}
		}

		return $query_result;
	}

	public function query_similar_bulbs($search = "")
	{
		$url = $this->base_url . 'queryBulbSimilar?';

		$url .= "search=" . $search;

		$result = wp_remote_post($url);

		$result_code = wp_remote_retrieve_response_code($result);
		if ($result_code != 200) {
			return ['data' => []];
		}
		$json = json_decode($result['body'], true);
		return $json;
	}

	public function get_token($code)
	{
		$url = $this->base_url . 'getToken?';

		$url .= "plugin=wp&";
		$url .= "site=" . site_url() . "&";
		$url .= "code=" . $code;

		$url = str_replace(' ', '%20', $url);

		$result = wp_remote_get($url);

		$result_code = wp_remote_retrieve_response_code($result);
		if ($result_code != 200) {
			return ['status' => -1];
		}

		$json = json_decode($result['body'], true);

		try {
			if ($json['status'] == 1) {
				update_option("abf_code_expired", $json['expired']);
				update_option("abf_code_status", $json['status']);
				update_option("abf_token", $json['token']);
			} else {
				delete_option("abf_code_expired");
			}
		} catch (\Throwable $th) {
		}

		return $json;
	}

	public function revoke_token()
	{
		delete_option('abf_code_status');
		delete_option('abf_code_expired');
		delete_option('abf_token');
		return ['msg' => 'Done'];
	}

	public function save_settings($names = [], $values = [])
	{
		foreach ($names as $key => $name) {
			$sanitize_name = sanitize_text_field($name);
			$sanitize_value = sanitize_text_field(wp_unslash($values[$key]));
			update_option($sanitize_name, $sanitize_value);
		}
		return array('msg' => 'Saved', 'names' => $names, 'values' => $values);
	}

	public function import_adaptions($file)
	{
		$response = ['success' => false, 'file' => $file, 'data' => []];
		$fileContent = file_get_contents($file);
		$fileName = sanitize_file_name($file);

		if (!$this->checkIfHeaderMatches($fileContent, 'adaptions-template.csv')) {
			$response['msg'] = esc_html__('Template does not match.', 'auto-bulb-finder');
			$response['fileName'] = $fileName;
			$response['header'] = $this->getCsvHeader($fileContent);
			return $response;
		}
		$adaptions = $this->getAdaptionCsvContent($fileContent);
		$helper = new ABFinder_Adaptions();
		$update = 0;
		$save = 0;
		$error = 0;
		foreach ($adaptions as $adaption) {
			try {
				$existAdaption = $helper->get_adaption_id($adaption['size']);
				if (!empty($existAdaption)) {
					$helper->abfinder_save_adaption($adaption, $existAdaption[0]['id']);
					$update++;
				} else {
					$helper->abfinder_save_adaption($adaption);
					$save++;
				}
			} catch (\Throwable $th) {
				$error++;
			}
		}
		$response['success'] = true;
		$response['data'] = [
			'update' => $update,
			'save' => $save,
			'error' => $error,
			'fileName' => $fileName,
			'fileContent' => $fileContent,
		];

		//show imported result with update, save, error count
		$response['msg'] = "Imported Done. " . $update . " updated, " . $save . " saved, " . $error . " error.";
		return $response;
	}

	public function import_vehicles($file)
	{
		$response = ['success' => false, 'file' => $file, 'data' => []];
		$fileContent = file_get_contents($file);
		$fileName = sanitize_file_name($file);

		if (!$this->checkIfHeaderMatches($fileContent)) {
			$response['msg'] = esc_html__('Template does not match.', 'auto-bulb-finder');
			$response['fileName'] = $fileName;
			$response['header'] = $this->getCsvHeader($fileContent);
			return $response;
		}
		$vehicles = $this->getVehicleCsvContent($fileContent);
		$helper = new ABFinder_Vehicles();
		$update = 0;
		$save = 0;
		$error = 0;
		foreach ($vehicles as $vehicle) {
			try {
				$existVehicle = $helper->get_vehicle_id($vehicle['year'], $vehicle['make'], $vehicle['model'], $vehicle['submodel'], $vehicle['bodytype'], $vehicle['qualifier']);
				if ($existVehicle) {
					$update++;
					$helper->abfinder_save_vehicle($vehicle, $existVehicle->id);
				} else {
					$save++;
					$helper->abfinder_save_vehicle($vehicle);
				}
			} catch (\Throwable $th) {
				$error++;
			}
		}
		$response['data'] = $vehicles;
		$response['success'] = true;
		$response['msg'] = "Imported $save vehicle" . ($save > 1 ? 's' : '') . ", updated $update vehicle" . ($update > 1 ? 's' : '') . ", $error error" . ($error > 1 ? 's' : '') . ".";
		return $response;
	}

	public function export_adaptions()
	{
		$export = ['success' => false, 'data' => []];
		try {
			$adaptions = new ABFinder_Adaptions();
			$adaptions = $adaptions->abfinder_get_adaptions();
			if ($adaptions) {
				$export = [
					'success' => true,
					'name' => 'abf_adaption_export_' . date('Y-m-d') . '_' . time() . '.csv',
					'content' => $this->exportAdaptionContent($adaptions)
				];
			}
		} catch (\Throwable $th) {
			$export['msg'] = $th->getMessage();
		}

		return $export;
	}

	public function export_vehicles($include)
	{
		$export = ['success' => false, 'data' => []];
		$vehicles = new ABFinder_Vehicles();
		$vehicles = $vehicles->abfinder_get_vehicles($include);
		if ($vehicles) {
			$export = [
				'success' => true,
				'name' => 'abf_vehicle_export_' . date('Y-m-d') . '_' . time() . '.csv',
				'content' => $this->exportVehicleContent($vehicles)
			];
		}

		return $export;
	}

	private function exportAdaptionContent($adaptions)
	{
		$csv = $this->getExpectedHeader('adaptions-template.csv') . "\n";
		foreach ($adaptions as $adaption) {
			$csv .= $adaption['name'] . ',"' . $adaption['size'] . '","' . $adaption['products'] . '","' . $adaption['fits_on'] . '",' . ($adaption['status'] == 0 ? 'include' : 'exclude') . "\n";
		}
		return $csv;
	}
	private function exportVehicleContent($vehicles)
	{
		$csv = $this->getExpectedHeader() . "\n";
		foreach ($vehicles as $vehicle) {
			$csv .= $vehicle['year'] . ',' . $vehicle['make'] . ',' . $vehicle['model'] . ',' . $vehicle['submodel'] . ',' . $vehicle['bodytype'] . ',' . $vehicle['qualifier'] . ',' . $vehicle['bulb_size'] . ',' . ($vehicle['status'] == 0 ? 'include' : 'exclude') . "\n";
		}
		return $csv;
	}

	private function getCsvHeader($content, $split = PHP_EOL)
	{
		$rows = explode($split, $content);
		return trim($rows[0]);
	}

	private function getAdaptionCsvContent($content, $split = PHP_EOL)
	{
		$rows = explode($split, $content);
		unset($rows[0]);
		$adaptions = [];
		foreach ($rows as $row) {
			//get adaption_columns, explode by comma, but notice that do not explode if comma is inside double quotes
			$adaption_columns = preg_split('/,(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/', trim($row));
			if (count($adaption_columns) < 5) continue;
			$adaption_columns = array_map(function ($item) {
				return str_replace('"', '', $item);
			}, $adaption_columns);

			$adaptions[] = [
				'name' => $adaption_columns[0],
				'size' => $adaption_columns[1],
				'products' => $adaption_columns[2],
				'fits_on' => $adaption_columns[3],
				'status' => strtolower($adaption_columns[4]) == 'include' ? 0 : 1
			];
		}
		return $adaptions;
	}
	private function getVehicleCsvContent($content, $split = PHP_EOL)
	{
		$rows = explode($split, $content);
		unset($rows[0]);
		$vehicles = [];
		foreach ($rows as $row) {
			$vehicle_columns = explode(',', trim($row));
			if (count($vehicle_columns) < 8) continue;
			$vehicles[] = [
				'year' => $vehicle_columns[0],
				'make' => $vehicle_columns[1],
				'model' => $vehicle_columns[2],
				'submodel' => $vehicle_columns[3],
				'bodytype' => $vehicle_columns[4],
				'qualifier' => $vehicle_columns[5],
				'bulb_size' => $vehicle_columns[6],
				'status' => strtolower($vehicle_columns[7]) == 'include' ? 0 : 1,
			];
		}
		return $vehicles;
	}

	private function checkIfHeaderMatches($content, $template_name = 'vehicles-template.csv')
	{
		$header = $this->getCsvHeader($content);
		$expectedHeader = $this->getExpectedHeader($template_name);
		return $header == $expectedHeader;
	}

	private function getExpectedHeader($template_name = 'vehicles-template.csv')
	{
		$templateContent = file_get_contents(ABFINDER_PLUGIN_FILE . 'assets/templates/' . $template_name);
		return $this->getCsvHeader($templateContent);
	}

	public function abfinder_add_vehicle_query_history($vid, $year, $make, $model, $submodel, $bodytype, $qualifier, $bulb_size)
	{
		// $vehicle = $this->abfinder_get_vehicle_query_history($vid);
		// if ($vehicle) {
		// 	$this->abfinder_update_vehicle_query_history($vehicle, $vid, $bulb_size);
		// } else {
		// 	$this->insert_vehicle_query_history($vid, $year, $make, $model, $submodel, $bodytype, $qualifier, $bulb_size);
		// }

		return true;
	}

	public function insert_vehicle_query_history($vid, $year, $make, $model, $submodel, $bodytype, $qualifier, $ip_address)
	{
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . 'abfinder_vehicle_query_history',
			[
				'vid' => $vid,
				'year' => $year,
				'make' => $make,
				'model' => $model,
				'submodel' => $submodel,
				'bodytype' => $bodytype,
				'qualifier' => $qualifier,
				'ip_address' => $ip_address,
				'time' => current_time('mysql')
			]
		);
	}

	private function abfinder_update_vehicle_query_history($vehicle, $vid)
	{
		//update updated_at, count = count + 1
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'abfinder_vehicle_query_history',
			[
				'updated_at' => current_time('mysql'),
				'count' => $vehicle['count'] + 1,
			],
			['vid' => $vid]
		);
	}

	private function abfinder_get_vehicle_query_history($vid)
	{
		global $wpdb;
		$vehicle = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}abfinder_vehicle_query_history WHERE vid = %s",
				$vid
			),
			ARRAY_A
		);
		return $vehicle;
	}

	//get top 10 vehicles group by vid and vid is not null and show count(*)
	public function get_top_vehicles()
	{
		global $wpdb;
		$vehicles = $wpdb->get_results(
			"SELECT *, count(*) as count FROM {$wpdb->prefix}abfinder_vehicle_query_history WHERE vid IS NOT NULL GROUP BY vid ORDER BY count DESC LIMIT 10",
			ARRAY_A
		);
		return $vehicles;
	}

	//get vehicle count queried everyday in the last 7 days
	public function get_vehicle_count_by_day($limit = 7)
	{
		global $wpdb;
		$vehicles = $wpdb->get_results(
			"SELECT DATE_FORMAT(time, '%Y-%m-%d') as date, count(*) as count FROM {$wpdb->prefix}abfinder_vehicle_query_history GROUP BY date ORDER BY date DESC LIMIT " . $limit,
			ARRAY_A
		);
		return $vehicles;
	}
}
