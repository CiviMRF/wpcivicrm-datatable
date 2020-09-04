<?php defined('ABSPATH') or die('Access denied.'); ?>

<div class="col-sm-6 civicrm-block hidden">
  <h4 class="c-title-color m-b-2">
    <?php _e('Connection Profile', 'wpcivicrm-datatable'); ?>
  </h4>
  <div class="form-group">
    <div class="fg-line">
        <select id="wdt-civicrm-profile" class="selectpicker">
            <?php foreach(wpcivicrm_datatable_get_profiles() as $profile_id => $profile) {
                echo '<option value="'.$profile_id.'">'.$profile['title'].'</option>';
            } ?>
        </select>
    </div>
    <div class="col-sm-6 p-0 p-r-5">
        <h4 class="c-title-color m-b-2">
          <?php _e('API Entity', 'wpcivicrm-datatable'); ?>
        </h4>
      <input type="text" id="wdt-civicrm-entity" class="form-control input-sm"
             placeholder="<?php _e('CiviCRM API Entity', 'wpcivicrm-datatable'); ?>">
    </div>
    <div class="col-sm-6 p-0">
        <h4 class="c-title-color m-b-2">
          <?php _e('API Action', 'wpcivicrm-datatable'); ?>
        </h4>
      <input type="text" id="wdt-civicrm-action" class="form-control input-sm"
             placeholder="<?php _e('CiviCRM API Action', 'wpcivicrm-datatable'); ?>">
    </div>
  </div>
  <!-- /input URL or path -->
</div>

<script type="text/javascript">
(function ($) {
  $(function () {
    var config = {
      "entity": "",
      "action": "",
      "profile": ""
    };

    $('#wdt-table-type').on('change', function() {
      var type = $('#wdt-table-type').val();
      if (!type) {
        if (typeof(wpdatatable_init_config) === 'undefined') {
          return;
        }
        type = wpdatatable_init_config.table_type;
      }
      if (type == 'civicrm') {
        $('.civicrm-block').removeClass('hidden');
        setFromConfig();
      } else {
        $('.civicrm-block').addClass('hidden');
        clearContent();
      }
    });

    $('#wdt-civicrm-profile').on('change', updateContent);
    $('#wdt-civicrm-entity').on('change', updateContent);
    $('#wdt-civicrm-action').on('change', updateContent);

    function updateContent() {
      config.profile = $('#wdt-civicrm-profile').val();
      if (config.profile == null) {
        config.profile = $('#wdt-civicrm-profile option:first-child').val();
      }
      config.entity = $('#wdt-civicrm-entity').val();
      config.action = $('#wdt-civicrm-action').val();
      if (typeof(wpdatatable_config) === 'undefined') {
        return;
      }
      wpdatatable_config.setContent(JSON.stringify(config));
    }

    function clearContent() {
      wpdatatable_config.content = "";
    }

    function setFromConfig() {
      $('#wdt-civicrm-profile').val(config.profile);
      $('#wdt-civicrm-entity').val(config.entity);
      $('#wdt-civicrm-action').val(config.action);
    }

    function loadConfig() {
      var content;
      if (typeof(wpdatatable_init_config) === 'undefined') {
        return;
      }
      if (wpdatatable_init_config) {
        content = wpdatatable_init_config.content;
      }
      if (!content) {
        return;
      }
      var contentObj = JSON.parse(content);
      if (contentObj.profile && contentObj.entity && contentObj.action) {
        config.profile = contentObj.profile;
        config.entity = contentObj.entity;
        config.action = contentObj.action;
        setFromConfig();
      }
    }

    $('#wdt-table-type').change();
    loadConfig();
  });
})(jQuery);
</script>