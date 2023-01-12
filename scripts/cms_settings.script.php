<?php
//update module status
if (isset($_POST['module_id'])){
   $response="";
   $module_id = $_POST['module_id'];
   $module_status = $_POST['module_status'];
   //update module setting
   include("../connect.php");
   $update_module = $db->prepare('UPDATE modules SET module_status=? WHERE module_id =?');
   $update_module->bind_param('si', $module_status, $module_id);
   $update_module->execute();
   $update_module->close();
}
//update settings from dropdown
if (isset($_POST['setting_id'])){
    $response="";
    $setting_id = $_POST['setting_id'];
    $cms_type = $_POST['cms_type'];
    //update module setting
    include("../connect.php");
    $update_settings = $db->prepare('UPDATE settings SET cms_type=? WHERE setting_id =?');
    $update_settings->bind_param('si', $cms_type, $setting_id);
    $update_settings->execute();
    $update_settings->close();
 }
?>