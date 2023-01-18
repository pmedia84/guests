<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
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
    $guest_rsvp_note = mysqli_real_escape_string($db, $_POST['rsvp_note']);
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

    /////////////////////Send email with confirmation/////////////////////////
    //load guest details
    $guest_query = ('SELECT guest_fname, guest_sname FROM guest_list WHERE guest_id='.$guest_id);
    $guest = $db->query($guest_query);
    $guest_result = $guest->fetch_assoc();
    //load wedding details
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    //load event name details
    $event_query = ('SELECT event_name FROM wedding_events WHERE event_id='.$event_id);
    $event = $db->query($event_query);
    $event_result = $event->fetch_assoc();
    include("../inc/settings.php");
    //email subject
    $subject = $guest_result['guest_fname'].' '.$guest_result['guest_sname'].' '.'has responded to their invitation!';
    //body of email to send to client as an auto reply
    $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                <h2>'.$guest_result['guest_fname'].' '.'has responded to their invitation!'.'</h2>
                <p>Dear ' .$wedding_result['wedding_name'].', here are the details of their response:</p>
                <p><strong>Event: </strong>'.$event_result['event_name'].'</p>    
                <p><strong>Their Response: </strong>'.$invite_rsvp_status.'</p>
                <p><strong>Any Dietary Requirements: </strong>'.$guest_dietery.'</p>
                <p><strong>Their Message:</strong><br>'.$guest_rsvp_note.'</p>
                <br><hr style="color:#496e62;">
                <p>Kind regards</p>
            </div>
        </div>';
    //configure email to send to users
    //stored in separate file
    //From Server
    $fromserver = $username;
    $email_to = $wedding_result['wedding_email'];
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = $host; // Enter your host here
    $mail->SMTPAuth = true;
    $mail->Username = $username; // Enter your email here
    $mail->Password = $pass; //Enter your password here
    $mail->Port = 25;
    $mail->From = $from;
    $mail->FromName = $fromname;
    $mail->Sender = $fromserver; // indicates ReturnPath header
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->IsHTML(true);
    $mail->AddAddress($email_to);
    if (!$mail->Send()) {
         echo "Mailer Error: " . $mail->ErrorInfo;
     }
    //response success
    $response = "success";
}

//if action type is update then update guest table and invites table
if(isset($_POST['action']) && $_POST['action']=="update"){
    //set up variables
    $guest_id = $_POST['guest_id'];
    $event_id = $_POST['event_id'];
    $invite_rsvp_status = $_POST['invite_rsvp_status'];
    $guest_dietery = $_POST['guest_dietery'];
    $guest_rsvp_note = mysqli_real_escape_string($db, $_POST['rsvp_note']);
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

    /////////////////////Send email with confirmation/////////////////////////
    //load guest details
    $guest_query = ('SELECT guest_fname, guest_sname FROM guest_list WHERE guest_id='.$guest_id);
    $guest = $db->query($guest_query);
    $guest_result = $guest->fetch_assoc();
    //load wedding details
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    //load event name details
    $event_query = ('SELECT event_name FROM wedding_events WHERE event_id='.$event_id);
    $event = $db->query($event_query);
    $event_result = $event->fetch_assoc();
    include("../inc/settings.php");
    //email subject
    $subject = $guest_result['guest_fname'].' '.$guest_result['guest_sname'].' '.'has updated their response to your invitation!';
    //body of email to send to client as an auto reply
    $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                <h2>'.$guest_result['guest_fname'].' '.'has changed their response to your invitation!'.'</h2>
                <p>Dear ' .$wedding_result['wedding_name'].', here are the details of their response:</p>
                <p><strong>Event: </strong>'.$event_result['event_name'].'</p>    
                <p><strong>Their Response: </strong>'.$invite_rsvp_status.'</p>
                <p><strong>Any Dietary Requirements: </strong>'.$guest_dietery.'</p>
                <p><strong>Their Message:</strong><br>'.$guest_rsvp_note.'</p>
                <br><hr style="color:#496e62;">
                <p>Kind regards</p>
            </div>
        </div>';
    //configure email to send to users
    //stored in separate file
    //From Server
    $fromserver = $username;
    $email_to = $wedding_result['wedding_email'];
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = $host; // Enter your host here
    $mail->SMTPAuth = true;
    $mail->Username = $username; // Enter your email here
    $mail->Password = $pass; //Enter your password here
    $mail->Port = 25;
    $mail->From = $from;
    $mail->FromName = $fromname;
    $mail->Sender = $fromserver; // indicates ReturnPath header
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->IsHTML(true);
    $mail->AddAddress($email_to);
    
    if (!$mail->Send()) {
         $response= "Mailer Error: " . $mail->ErrorInfo;
     }
    //response success
    $response = "success";
}
//echo out variable
echo $response;
