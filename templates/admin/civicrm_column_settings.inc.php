<?php defined('ABSPATH') or die('Access denied.'); ?>
<div class="row hidden" id="wpcivicrm-datatable-columnsetting">
    <div class="col-sm-6">
      <h4 class="c-title-color m-b-2"><?php _e('Content Replacement', 'wpcivicrm-datatable'); ?></h4>
      <div class="form-group">
        <div class="toggle-switch">
          <input id="wdt-column-civicrm_replace_content_enable" type="checkbox" />
          <label for="wdt-column-civicrm_replace_content_enable" class="ts-label"><?php _e('Enable content replacement', 'wpcivicrm-datatable'); ?></label>
        </div>
      </div>
      <div id="civicrm_replace_content_div" class="hidden">
        <h4 class="c-title-color m-b-2"><?php _e('Content Replacement', 'wpcivicrm-datatable'); ?></h4>
        <div class="form-group">
            <div class="fg-line">
                <input type="text" class="form-control input-sm" id="wdt-column-civicrm_replace_content" value="">
            </div>
        </div>
      <div class="fg-line">
          <h4><?php _e('Content Replacement Tokens', 'wpcivicrm-datatable'); ?></h4>
          <p id="wdt-column-civicrm_replace_content_tokens"></p>
      </div>
      </div>
    </div>
</div>

<script type="text/javascript">
(function ($) {
    $(function () {
      var column_config = {
        "civicrm_content_replacement_enable": 0,
        "civicrm_content_replacement": ""
      };

      // When the #wd-table-id content is changed we know the table config has been load
      // and now it is time to apply our changes which is basically copying the civicrm_columns.
      $('body').on('DOMSubtreeModified', '#wdt-table-id', function() {
        if (typeof(wpdatatable_config.civicrm_columns) === 'undefined') {
          if (typeof (wpdatatable_init_config) === 'undefined' || typeof (wpdatatable_init_config.civicrm_columns) === 'undefined') {
            wpdatatable_config.civicrm_columns = [];
          } else {
            wpdatatable_config.civicrm_columns = wpdatatable_init_config.civicrm_columns;
          }
        }
      });

      $('body').on('DOMSubtreeModified', 'span.wdtColumnOrigHeader', function(){
        for(var i=0; i<wpdatatable_config.civicrm_columns.length; i++) {
          if (wpdatatable_config.civicrm_columns[i].orig_header == wpdatatable_config.currentOpenColumn.orig_header) {
            column_config.civicrm_content_replacement_enable = wpdatatable_config.civicrm_columns[i].civicrm_content_replacement_enable;
            column_config.civicrm_content_replacement = wpdatatable_config.civicrm_columns[i].civicrm_content_replacement;
            break;
          }
        }

        var tokens = '';
        for(var i=0; i < wpdatatable_config.columns.length; i++) {
          if (tokens.length > 0) {
            tokens = tokens + '<br />';
          }
          tokens = tokens + '['+wpdatatable_config.columns[i].orig_header+'] = '+wpdatatable_config.columns[i].display_header;
        }
        $('#wdt-column-civicrm_replace_content_tokens').html(tokens);
        $('#wdt-column-civicrm_replace_content_enable').prop('checked', column_config.civicrm_content_replacement_enable);
        $('#wdt-column-civicrm_replace_content_enable').trigger('change');
        $('#wdt-column-civicrm_replace_content').val(column_config.civicrm_content_replacement);
      });

      $('#wdt-column-civicrm_replace_content_enable').on('change', function() {
        if ($('#wdt-column-civicrm_replace_content_enable').prop('checked')) {
            $('#civicrm_replace_content_div').removeClass('hidden');
        } else {
            $('#civicrm_replace_content_div').addClass('hidden');
        }
        updateConfig();
      });

      $('#wdt-column-civicrm_replace_content').on('change', function() {
        updateConfig();
      });

      function updateConfig() {
        for(var i=0; i<wpdatatable_config.civicrm_columns.length; i++) {
          if (wpdatatable_config.civicrm_columns[i].orig_header == wpdatatable_config.currentOpenColumn.orig_header) {
            wpdatatable_config.civicrm_columns[i].civicrm_content_replacement_enable = $('#wdt-column-civicrm_replace_content_enable').prop('checked');
            wpdatatable_config.civicrm_columns[i].civicrm_content_replacement = $('#wdt-column-civicrm_replace_content').val();
            break;
          }
        }
      }

      $('.wdt-cancel-column-settings').on('click', function() {
        for(var i=0; i<wpdatatable_config.civicrm_columns.length; i++) {
          if (wpdatatable_config.civicrm_columns[i].orig_header == wpdatatable_config.currentOpenColumn.orig_header) {
            wpdatatable_config.civicrm_columns[i].civicrm_content_replacement_enable = column_config.civicrm_content_replacement_enable;
            wpdatatable_config.civicrm_columns[i].civicrm_content_replacement = column_config.civicrm_content_replacement;
            break;
          }
        }
      });

      if ($('#column-display-settings')) {
        $('#wpcivicrm-datatable-columnsetting').appendTo('#column-display-settings');
        $('#wpcivicrm-datatable-columnsetting').removeClass('hidden');
      }
    });
})(jQuery);
</script>
