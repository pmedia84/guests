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
// Business
// Wedding
// 
$cms_type = $cms_type;

//email settings for contact forms
//Settings for all form scripts

/// Define who the emails get sent to from forms filled out
$email_to = "";

$host = "admin.parrotmedia.co.uk"; /// Hostname
$username = "admin@admin.parrotmedia.co.uk"; ///Username
$pass = "Krb833908"; /// Password
$from = $username; ///Email address

$fromname = "Parrot Media"; /// Username and how you want your name to be displayed on emails
$emailheaderlogo = "https://www.parrotmedia.co.uk/img/pmedia-logo-new.png"; //logo url for inserting into the top of email bodies

////////////////Modules Available\\\\\\\\\\\\\\\\\\\\
//connect to modules table and load available modules
$modules_query = ('SELECT module_name, module_status FROM modules');
$modules = $db->query($modules_query);
$modules_result = $modules->fetch_assoc();
//Reviews
$module_reviews = "On";
$api_key = ""; //api key from google source
$place_id = ""; //Found from google places api
//Image Gallery


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
