<?php
/*
Plugin Name: Data source CiviCRM api for wpDataTable
Description: Provides a CiviCRM api data source for wpDataTable plugin. You can use this plugin with Connector to CiviCRM with CiviMcRestFace (https://wordpress.org/plugins/connector-civicrm-mcrestface/)
Version:     20180716
Author:      Jaap Jansma
License:     AGPL3
License URI: https://www.gnu.org/licenses/agpl-3.0.html
Text Domain: wpcivicrm-datatable
*/

defined('ABSPATH') or die("Cannot access pages directly.");

define('WPCIVICRM_DATATABLES_ROOT_PATH', plugin_dir_path(__FILE__)); // full path to the wpDataTables root directory

function wpcivicrm_datatable_init() {
  require_once WPCIVICRM_DATATABLES_ROOT_PATH . "classes/class.wdtcivicrm.datatable.inc.php";
  require_once WPCIVICRM_DATATABLES_ROOT_PATH . "classes/class.civicrm_content_replacement.inc.php";
  require_once WPCIVICRM_DATATABLES_ROOT_PATH . "classes/class.local.inc.php";
  require_once WPCIVICRM_DATATABLES_ROOT_PATH . "classes/class.wpcmrf.inc.php";
  WPCivicrm_Datatable::init();
  WPCivicrm_Datatable_ContentReplacement::init();;
}

/**
 * Returns a list of possible connection profiles.
 * @return array
 */
function wpcivicrm_datatable_get_profiles() {
  static $profiles = null;
  if (is_array($profiles)) {
    return $profiles;
  }

  $profiles = array();
  $profiles = WPCivicrm_Datatable_Local::profiles($profiles);
  $profiles = WPCivicrm_Datatable_Wpcmrf::profiles($profiles);

  $profiles = apply_filters('cf_civicrm_formprocessor_get_profiles', $profiles);
  return $profiles;
}

function wpcivicrm_datatable_api($entity, $action, $params, $options, $profile_id) {
  $profiles = wpcivicrm_datatable_get_profiles();
  if (!isset($profiles[$profile_id])) {
    return ['error' => 'Invalid connection', 'is_error' => '1'];
  }
  $func = $profiles[$profile_id]['function'];
  return call_user_func($func, $entity, $action, $params, $options, $profiles[$profile_id]['profile_id']);
}

wpcivicrm_datatable_init();