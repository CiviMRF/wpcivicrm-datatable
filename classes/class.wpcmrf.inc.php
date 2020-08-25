<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

class WPCivicrm_Datatable_Wpcmrf {

  public static function profiles($profiles) {
    if (function_exists('wpcmrf_get_core')) {
      $core = wpcmrf_get_core();
      $wpcmrf_profiles = $core->getConnectionProfiles();
      foreach($wpcmrf_profiles as $profile) {
        $profile_name = 'wpcmrf_profile_'.$profile['id'];
        $profiles[$profile_name] = [
          'title' => $profile['label'],
          'function' => ['WPCivicrm_Datatable_Wpcmrf', 'api'],
          'profile_id' => $profile['id'],
        ];
      }
    }
    return $profiles;
  }

  public static function api($entity, $action, $params, $options, $profile) {
    $call = wpcmrf_api($entity, $action, $params, $options, $profile);
    if ($call->getStatus() == \CMRF\Core\Call::STATUS_FAILED) {
      return ['error' => 'Could not retrieve data', 'is_error' => '1'];
    }
    return $call->getReply();
  }

}