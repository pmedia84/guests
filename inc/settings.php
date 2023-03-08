<?php
//////////////////////////////////////////////////////////settings script for all cms websites\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

//connect to settings table

$settings = $db->prepare('SELECT cms_type FROM settings');
$settings->execute();
$settings->store_result();
$settings->bind_result($cms_type);
$settings->fetch();
/// Define what type of website this is for \\\
//Business with services and reviews etc
//Or wedding site with rsvp features etc

include($_SERVER['DOCUMENT_ROOT'] . "/email_settings.php");
////////////////Modules Available\\\\\\\\\\\\\\\\\\\\
//connect to modules table and load available modules
$modules_query = ('SELECT module_name, module_status FROM modules');
$modules = $db->query($modules_query);
$modules_result = $modules->fetch_assoc();
//Reviews
$module_reviews = "On";
$api_key = ""; //api key from google source
$place_id = ""; //Found from google places api



foreach ($modules as $module) {
    //Guest List
    if ($module['module_name'] == "Guest List") {
        $guest_list_status = $module['module_status'];
    }
    //Reviews
    if ($module['module_name'] == "Reviews") {
        $reviews_status = $module['module_status'];
    }
    //Image Gallery
    if ($module['module_name'] == "Image Gallery") {
        $image_gallery_status = $module['module_status'];
    }
    //Price List
    if ($module['module_name'] == "Price List") {
        $price_list_status = $module['module_status'];
    }
    //News
    if ($module['module_name'] == "News") {
        $news_status = $module['module_status'];
    }
    //Invite Manager
    if ($module['module_name'] == "Invite Manager") {
        $invite_manager_status = $module['module_status'];
    }
    //Guest Messaging 
    if ($module['module_name'] == "Guest Messaging") {
        $guest_messaging_status = $module['module_status'];
    }
    //Gift List 
    if ($module['module_name'] == "Gift List") {
        $gift_list_status = $module['module_status'];
    }
}
//connect to modules table and load available modules for the wedding site
$wedding_modules_query = ('SELECT wedding_module_name, wedding_module_status FROM wedding_modules');
$wedding_modules = $db->query($wedding_modules_query);
$wedding_modules_result = $modules->fetch_assoc();

foreach ($wedding_modules as $wedding_module) {
    //RSVP
    if ($wedding_module['wedding_module_name'] == "Guest Area") {
        $guest_area_status = $wedding_module['wedding_module_status'];
    }
    //Add and remove guests
    if ($wedding_module['wedding_module_name'] == "Add & Remove Guests") {
        $guest_add_remove = $wedding_module['wedding_module_status'];
    }
    //Provide Meal Choices
    if ($wedding_module['wedding_module_name'] == "Meal Choices") {
        $meal_choices_status = $wedding_module['wedding_module_status'];
    }
}
//check if the guest has submitted meal choices if this feature is switched on.
if ($meal_choices_status == "On") {
    
}
