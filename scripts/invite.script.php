<?php
//response variable
$response ="";
include("../connect.php");
//if action type is response then update guest table and invites table
if(isset($_POST['action']) && $_POST['action']=="response"){
    //set up variables
    $guest_id = $_POST['guest_id'];
    $event_id = $_POST['event_id'];
    $invite_rsvp_status = $_POST['invite_rsvp_status'];
    $guest_dietery=$_POST['guest_dietery'];
    //update the invitations table
    $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?  WHERE event_id =? AND guest_id=?');
    $update_rsvp->bind_param('sii',$invite_rsvp_status, $event_id, $guest_id);
    $update_rsvp->execute();
    $update_rsvp->close();
    //update the guest list
    $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
    $update_guest_list->bind_param('ssi',$invite_rsvp_status, $guest_dietery, $guest_id);
    $update_guest_list->execute();
    $update_guest_list->close();

    //response success
    $response = "success";
}else{
    $response = '<div class="form-response error"><p>Post Error, TRY AGAIN</p></div>';
}

//if action type is update then update guest table and invites table
if(isset($_POST['action']) && $_POST['action']=="update"){
    //set up variables
    $guest_id = $_POST['guest_id'];
    $event_id = $_POST['event_id'];
    $invite_rsvp_status = $_POST['invite_rsvp_status'];
    $guest_dietery=$_POST['guest_dietery'];
    //update the invitations table
    $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?  WHERE event_id =? AND guest_id=?');
    $update_rsvp->bind_param('sii',$invite_rsvp_status, $event_id, $guest_id);
    $update_rsvp->execute();
    $update_rsvp->close();
    //update the guest list
    $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
    $update_guest_list->bind_param('ssi',$invite_rsvp_status, $guest_dietery, $guest_id);
    $update_guest_list->execute();
    $update_guest_list->close();

    //response success
    $response = "success";
}else{
    $response = '<div class="form-response error"><p>Post Error, TRY AGAIN</p></div>';
}
//echo out variable
echo $response;
