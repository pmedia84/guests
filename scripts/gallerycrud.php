<?php
session_start();
require("functions.php");
$user = new User();
//* Response Codes
//200: Success
//400: Error
//? Uploading new images
if ($_POST['action'] == "upload") {

    $code = "";
    //Total amt of images uploaded
    $img_total = "";
    //Amount of successful Images
    $success_amt = "";
    //Amount of images that did not upload
    $img_error_amt = "";
    //Response Message
    $msg = "";
    //Load the current user information.

    $img = new Guest_img;
    $img->guest_name($user->user_name());
    $img->guest_id($user->guest_id());
    //set the image description
    $img->desc();
    //call the create image function from with the guest img class
    $img->create();
    //Total amount of images uploaded
    $img_total = $img->img_total();
    //return the message from img function
    $msg = $img->msg();
    //return the response code
    $code = $img->response_code();
    //return the amount of images with errors
    $img_error_amt = $img->img_error_amt();
    //return the amount of images that were successful
    $success_amt = $img->img_success_amt();
    //Response Array
    $status = array("img_total" => $img_total, "img_success_amt" => $success_amt, "img_error_count" => $img_error_amt, "response_code" => $code, "message" => $msg);
    //echo out the response in a JSON file
    echo json_encode($status);
}
//? Deleting images from guest gallery
if ($_POST['action'] == "delete") {
    //check the post method has been sent
    if ($_SERVER['REQUEST_METHOD'] !== "POST") {

        $status = array("img_total" => $img_total, "img_success_amt" => $success_amt, "img_error_count" => $img_error_amt, "response_code" => $code, "message" => $msg);
        //echo out the response in a JSON file
        echo json_encode($status);
        exit();
    }
    
    $img = new Guest_img;
    $img->guest_id($user->guest_id());

    $img->delete();
    //total amount of images requested for delete
    $img_total = $img->img_total();
    //return the message from img function
    $msg = $img->msg();
    //return the response code
    $code = $img->response_code();
    //return the amount of images with errors
    $img_error_amt = $img->img_error_amt();
    //return the amount of images that were successfully deleted
    $success_amt = $img->img_success_amt();
    $status = array("img_total" => $img_total, "img_success_amt" => $success_amt, "img_error_count" => $img_error_amt, "response_code" => $code, "message" => $msg);
    echo json_encode($status);
}
