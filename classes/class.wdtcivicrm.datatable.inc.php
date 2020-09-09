<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

class WPCivicrm_Datatable {

  public static function init() {
    add_action('plugins_loaded', function() {
      WPDataTable::$allowedTableTypes[] = 'civicrm';
    });
    add_action('wpdatatables_generate_civicrm', ['WPCivicrm_Datatable', 'getData'], 10, 3);
    if (is_admin()) {
      add_filter('wpdatatables_before_save_table', ['WPCivicrm_Datatable', 'beforeSaveTable'], 10, 1);
      add_action('wdt_add_table_type_option', ['WPCivicrm_Datatable', 'tableTypeOption']);
      add_action('wdt_add_data_source_elements', ['WPCivicrm_Datatable', 'adminConfigurationTemplate']);
    }
  }

  /**
   * Addd CiviCRM to the data source dropdown.
   */
  public static function tableTypeOption() {
    echo "<option value=\"civicrm\">".__('CiviCRM', 'wpcivicrm-datatable')."</option>";
  }

  /**
   * Include the civicrm configuration template.
   */
  public static function adminConfigurationTemplate() {
    include WPCIVICRM_DATATABLES_ROOT_PATH.'templates/admin/civicrm_datasource.inc.php';
  }

  /**
   * Adjust the civicrm configuration before saving to database.
   *
   * @param $table
   * @return mixed
   */
  public static function beforeSaveTable($table) {
    if ($table->table_type != 'civicrm') {
      return $table;
    }

    $config = json_decode($table->content);
    // Save the correct uppercase/lowercase varion of the action.
    // This way the user can enter the action like Get, gEt and it will all transformed to the right
    // spelling of get. This is done to check the action against the getactions of the entity.
    $result = wpcivicrm_datatable_api($config->entity, 'getactions', [], [], $config->profile);
    foreach($result['values'] as $action) {
      if (strtolower($action) == strtolower(($config->action))) {
        $config->action = $action;
        break;
      }
    }
    $config = self::updateConfigWithFieldInformation($config);
    $table->content = json_encode($config);
    return $table;
  }


  /**
   * Retrieve data
   * @param \WPDataTable $data_table
   * @param $content
   * @param $params
   */
  public static function getData(WPDataTable $data_table, $content, $datatable_params) {
    $defaults = array();
    $config = json_decode($content);
    $api_params = ['sequential' => 1];
    if (!isset($config->filters)) {
      $config->filters = array();
    }
    foreach($config->filters as $filter) {
      if (isset($_GET[$filter['name']])) {
        $api_params[$filter] = $_GET[$filter['name']];
      }
    }
    if (!isset($config->columns)) {
      $config->columns = array();
    }
    foreach($config->columns as $fieldName => $title) {
      if (!isset($params['columnTitles'][$fieldName])) {
        $datatable_params['columnTitles'][$fieldName] = $title;
        $defaults[$fieldName] = '';
      }
    }
    if (!isset($config->columnTypes)) {
      $config->columnTypes = array();
    }
    foreach($config->columnTypes as $fieldName => $type) {
      if (!isset($params['data_types'][$fieldName])) {
        $datatable_params['data_types'][$fieldName] = $type;
      }
    }

    $options = ['limit' => 0];
    if (isset($params['limit'])) {
      $options['limit'] = $params['limit'];
    }

    do_action('wpcivicrm_datatable_api_params', [$api_params, $config, $data_table, $datatable_params]);
    $reply = wpcivicrm_datatable_api($config->entity, $config->action, $api_params, $options, $config->profile);
    $data = array();
    if (isset($reply['values']) && is_array($reply['values'])) {
      foreach ($reply['values'] as $row) {
        $row = array_merge($defaults, $row);
        $data[] = $row;
      }
    }
    $data = apply_filters('wpcivicrm_datatable_alter_data', $data, $data_table);
    $data_table->arrayBasedConstruct($data, $datatable_params);
    // Set no data to false to prevent error popping up when civicrm returns no data.
    // This caused be caused by required filters.
    if (empty($data)) {
      $data_table->setNoData(FALSE);
    }
  }

  /**
   * Update config object with the fields returned from the api.
   *
   * @param $config
   * @return mixed
   */
  protected static function updateConfigWithFieldInformation($config) {
    $fields = wpcivicrm_datatable_api($config->entity, 'getfields', ['api_action' => $config->action], [], $config->profile);
    $fields = $fields['values'];

    if (!isset($params['columnTitles'])) {
      $params['columnTitles'] = [];
    }
    $config->columns = array();
    $config->columnTypes = array();
    $config->filters = array();
    foreach($fields as $field) {
      if (isset($field['api.filter']) && $field['api.filter']) {
        $config->filters[] = $field['name'];
      }

      if (!isset($field['api.return']) || $field['api.return']) {
        // if api.return is not defined we assume it is a return field.
        // if api.return is defined and set to false then it is only a filter field
        $config->columns[$field['name']] = $field['title'];
        $config->columnTypes[$field['name']] = self::determineType($field['type']);
      }
    }
    return $config;
  }

  protected static function determineType($civicrm_type) {
    switch ($civicrm_type) {
      case 1:
      case 16:
        return 'int';
        break;
      case 4:
        return 'date';
        break;
      case 8 :
        return 'time';
        break;
      case 256:
        return 'datetime';
        break;
      case 512:
      case 1024:
        return 'float';
        break;
      case 2048:
        return 'email';
        break;
      case 4096:
        return 'link';
        break;
    }
    return 'string';
  }

}