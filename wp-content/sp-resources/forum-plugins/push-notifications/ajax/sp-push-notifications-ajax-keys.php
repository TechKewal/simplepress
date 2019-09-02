<?php
/*
Simple:Press
Topic Push Notifications plugin ajax routine for management functions
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/


if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();
$userId = SP()->filters->integer($_GET['user_id'])?:get_current_user_id();
$deviceId = $_GET['device_id'];

if(isset($deviceId)){

     $onesignal_keys = explode(', ', get_user_meta($userId, 'onesignal_key')[0])
                     ?: $deviceId;

     foreach($onesignal_keys as $key => $onesignal_key){
          if($onesignal_key == $deviceId && $_GET['sub_action'] == 'remove'){
               $is_old = true;
               unset($onesignal_keys[$key]);
          }
          if($onesignal_key == $deviceId && $_GET['sub_action'] == 'add'){
               $is_old = true;
          }
     }
     if(!$is_old){
          $onesignal_keys[count($onesignal_keys)] = $deviceId;
     }

     $onesignal_keys = array_diff($onesignal_keys, array('', NULL, false));
     $onesignal_keys = implode(', ', $onesignal_keys);

     update_user_meta($userId, 'onesignal_key', $onesignal_keys);

} else {
     foreach($_GET['keys'] as $key=>$value){
          update_user_meta($userId, $key, $value);
     }
}