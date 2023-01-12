<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
session_start();

include("../connect.php");
include("../inc/settings.php");
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    echo "hello";
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
if(empty($_POST['business_id'])){
    $response = '<div class="form-response error"><p>Form Error, please try again!</p></div>';
    
}else{
    //declare variables
    $business_id = $_POST['business_id'];
    $business_name = mysqli_real_escape_string($db, $_POST['business_name']);
    $address_id = mysqli_real_escape_string($db, $_POST['address_id']);
    $business_email = mysqli_real_escape_string($db, $_POST['business_email']);
    $business_phone = mysqli_real_escape_string($db, $_POST['business_phone']);
    $business_contact_name = mysqli_real_escape_string($db, $_POST['business_contact_name']);
    //Update business record
    $update_business = $db->prepare('UPDATE business SET  business_name=?, address_id=?, business_phone=?, business_email=?, business_contact_name=?  WHERE business_id ='.$business_id);
    $update_business->bind_param('sssss', $business_name, $address_id, $business_phone, $business_email, $business_contact_name );
    $update_business->execute();
    $update_business->close();



    $response = '<div class="form-response"><p>'.$business_name.' Successfully updated</p></div>';
    
}
//set up variable for response message


echo $response;
