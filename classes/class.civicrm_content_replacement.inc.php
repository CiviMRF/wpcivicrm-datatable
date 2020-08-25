<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

class WPCivicrm_Datatable_ContentReplacement {

  public static function init() {
    if (is_admin()) {
      add_filter('wpdatatables_filter_table_metadata', ['WPCivicrm_Datatable_ContentReplacement', 'filterTableData'], 10, 2);
      add_filter('wpdt_filter_column_config_object', ['WPCivicrm_Datatable_ContentReplacement', 'filterConfigObject'], 10, 2);
      add_filter('wpdatatables_before_save_table', ['WPCivicrm_Datatable_ContentReplacement', 'updateTableData'], 10, 1);
      add_action('wdt_add_column_display_settings_element', function() {
        include WPCIVICRM_DATATABLES_ROOT_PATH . "templates/admin/civicrm_column_settings.inc.php";
      });

    }
    add_action('wdt_extend_wpdatatable_object', ['WPCivicrm_Datatable_ContentReplacement', 'replaceContent'], 10, 2);
    add_filter('wpdt_filter_column_description_object', ['WPCivicrm_Datatable_ContentReplacement', 'addContentReplacementToColumn'], 10, 3);
  }

  public static function addContentReplacementToColumn($feColumn, $dbColumn, $advancedSettings) {
    $feColumn->civicrm_content_replacement_enable = isset($advancedSettings->civicrm_content_replacement_enable) ? $advancedSettings->civicrm_content_replacement_enable : FALSE;
    $feColumn->civicrm_content_replacement        = isset($advancedSettings->civicrm_content_replacement) ? $advancedSettings->civicrm_content_replacement : '';
    return $feColumn;
  }

  public static function filterTableData($tableData, $tableId) {
    $tableData->civicrm_columns = array();
    foreach($tableData->columns as $idx => $column) {
      $civi_column = new stdClass();
      $civi_column->orig_header = $column->orig_header;
      $civi_column->civicrm_content_replacement_enable = isset($column->civicrm_content_replacement_enable) ? $column->civicrm_content_replacement_enable : false;
      $civi_column->civicrm_content_replacement = isset($column->civicrm_content_replacement) ? $column->civicrm_content_replacement : "";
      $tableData->civicrm_columns[] = $civi_column;
    }
    return $tableData;
  }

  public static function updateTableData($tableData) {
    foreach($tableData->civicrm_columns as $civicrm_column) {
      foreach($tableData->columns as $idx => $column) {
        if ($column->orig_header == $civicrm_column->orig_header) {
          $tableData->columns[$idx]->civicrm_content_replacement_enable = $civicrm_column->civicrm_content_replacement_enable;
          $tableData->columns[$idx]->civicrm_content_replacement = $civicrm_column->civicrm_content_replacement;
          break;
        }
      }
    }
    return $tableData;
  }

  public static function filterConfigObject($columnConfig, $feColumn) {
    $settings = json_decode($columnConfig['advanced_settings']);
    $settings->civicrm_content_replacement_enable = $feColumn->civicrm_content_replacement_enable;
    $settings->civicrm_content_replacement = $feColumn->civicrm_content_replacement;
    $columnConfig['advanced_settings'] = json_encode($settings);
    return $columnConfig;
  }

  public static function replaceContent(WPDataTable $data_table, $tableData) {
    $data = $data_table->getDataRows();
    $search = array();
    foreach($tableData->columns as $column) {
      $search[] = '['.$column->orig_header.']';
    }
    foreach($data as $idx => $row) {
      foreach($tableData->columns as $column) {
        if ($column->civicrm_content_replacement_enable) {
          $data[$idx][$column->orig_header] = str_replace($search, $row, $column->civicrm_content_replacement);
        }
      }
    }
    $data_table->setDataRows($data);
  }

}