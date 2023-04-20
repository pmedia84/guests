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

