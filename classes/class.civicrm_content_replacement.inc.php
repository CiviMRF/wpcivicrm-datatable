<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

class WPCivicrm_Datatable_ContentReplacement {

  protected static $metadata = [];

  protected static $columnsToSave = [];

  public static function init() {
    if (is_admin()) {
      add_filter('wpdatatables_filter_table_metadata', ['WPCivicrm_Datatable_ContentReplacement', 'filterTableData'], 10, 2);
      add_filter('wpdatatables_before_save_table', ['WPCivicrm_Datatable_ContentReplacement', 'updateTableData'], 10, 1);
      add_action('wdt_add_data_source_elements', ['WPCivicrm_Datatable_ContentReplacement', 'adminConfigurationTemplate']);
      add_filter('wpdatatables_filter_update_column_array', ['WPCivicrm_Datatable_ContentReplacement', 'updateColumn'], 10, 2);
      add_filter('wpdatatables_filter_insert_column_array', ['WPCivicrm_Datatable_ContentReplacement', 'updateColumn'], 10, 2);
    }
    add_filter('wpdatatables_filter_columns_metadata', ['WPCivicrm_Datatable_ContentReplacement', 'columnMetaData'], 10, 2);
    add_filter('wpcivicrm_datatable_alter_data', ['WPCivicrm_Datatable_ContentReplacement', 'replaceContent'], 10, 2);
  }

  public static function adminConfigurationTemplate() {
    include WPCIVICRM_DATATABLES_ROOT_PATH . "templates/admin/civicrm_column_settings.inc.php";
  }

  /**
   * Set the civicrm content replacement options on the $metadata variable
   * so we can look it up later. We retrieve this from the database in the column
   * advanced settings.
   *
   * @param $columns
   * @param $tableId
   * @return mixed
   */
  public static function columnMetaData($columns, $tableId) {
    self::$metadata[$tableId] = array();
    foreach($columns as $column) {
      self::$metadata[$tableId][$column->id] = json_decode($column->advanced_settings);
    }
    return $columns;
  }

  /**
   * Set the civicrm content replacement options on the table data object
   * on the variable civicrm_columns.
   * Retrieve the data from the $metadata
   *
   * @param $tableData
   * @param $tableId
   * @return mixed
   */
  public static function filterTableData($tableData, $tableId) {
    $tableData->civicrm_columns = array();
    foreach($tableData->columns as $idx => $column) {
      $metadata = self::$metadata[$tableId][$column->id];
      $civi_column = new stdClass();
      $civi_column->orig_header = $column->orig_header;
      $civi_column->civicrm_content_replacement_enable = isset($metadata->civicrm_content_replacement_enable) ? $metadata->civicrm_content_replacement_enable : false;
      $civi_column->civicrm_content_replacement = isset($metadata->civicrm_content_replacement) ? $metadata->civicrm_content_replacement : "";
      $tableData->civicrm_columns[] = $civi_column;
    }
    return $tableData;
  }

  /**
   * Store the civicrm content replacement options in a temporary $columnsToSave
   *
   * @param $tableData
   * @return mixed
   */
  public static function updateTableData($tableData) {
    self::$columnsToSave[$tableData->id] = array();
    foreach($tableData->civicrm_columns as $civicrm_column) {
      self::$columnsToSave[$tableData->id][$civicrm_column->orig_header] = new stdClass();
      self::$columnsToSave[$tableData->id][$civicrm_column->orig_header]->civicrm_content_replacement_enable = $civicrm_column->civicrm_content_replacement_enable;
      self::$columnsToSave[$tableData->id][$civicrm_column->orig_header]->civicrm_content_replacement = $civicrm_column->civicrm_content_replacement;
    }
    return $tableData;
  }

  /**
   * Add the civicrm content replacement options to the columns advanced settings.
   * We retrieve the settings from the temporary variable $columnsToSave
   *
   * @param $column
   * @param $tableId
   *
   * @return mixed
   */
  public static function updateColumn($column, $tableId) {
    if (isset(self::$columnsToSave[$tableId][$column['orig_header']])) {
      $settings = json_decode($column['advanced_settings']);
      $settings->civicrm_content_replacement_enable = self::$columnsToSave[$tableId][$column['orig_header']]->civicrm_content_replacement_enable;
      $settings->civicrm_content_replacement = self::$columnsToSave[$tableId][$column['orig_header']]->civicrm_content_replacement;
      $column['advanced_settings'] = json_encode($settings);
    }
    return $column;
  }

  public static function replaceContent($data, WPDataTable $data_table) {
    $tableData = WDTConfigController::loadTableFromDB($data_table->getWpId());
    $search = array();
    foreach($tableData->columns as $column) {
      $search[] = '['.$column->orig_header.']';
    }
    foreach($data as $idx => $row) {
      foreach($tableData->columns as $column) {
        $metadata = self::$metadata[$data_table->getWpId()][$column->id];
        if (isset($metadata->civicrm_content_replacement_enable) && $metadata->civicrm_content_replacement_enable) {
          $data[$idx][$column->orig_header] = str_replace($search, $row, $metadata->civicrm_content_replacement);
        }
      }
    }
    return $data;
  }

}