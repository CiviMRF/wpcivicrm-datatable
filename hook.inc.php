<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

/**
 * Example implementation of actions and filters
 */

add_action('wpcivicrm_datatable_api_params', function($api_params, $config, WPDataTable $data_table, $datatable_params) {
  // $api_params is an array containing the civicrm api parameters
  // $config is an object containing the CiviCRM datasource configuration.
  // $config->entity
  // $config->action
  // $config->columns
  // $config->filters
  // $config->profile_id
  // $data_table is the WPDataTable object
  // $datatable_params is the params for the WPDataTable object which contains for example column titles.

  // Add your custom logic to the api params.
  // For example filter on a certain contact id
  $user = wp_get_current_user();
  $api_params['contact_id'] = $user->ID;
}, 10, 4);

add_filter('cf_civicrm_formprocessor_get_profiles', function($profiles) {
  $profiles['my_profile_type'] = [
    'function' => 'my_profile_type_api_function', // See below for an example
    'profile_id' => false, // Fill in an additional profile id if needed
    'title' => 'My Profile',
  ];
  return $profiles;
}, 10, 1);

function my_profile_type_api_function($entity, $action, $params, $options, $profile_id) {
  // Call the civicrm api your way.
}

add_filter('wpcivicrm_datatable_alter_data', function($data, WPDataTable $data_table) {
  // Do whatever you want with the data.
  // This filter is called just before the data is passed to the WPDataTable
  return $data;
}, 10, 2);