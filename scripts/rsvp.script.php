<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
$response = "";
include("../connect.php");

if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    echo "hello";
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//check if the post has been transferred from rsvp form
if (isset($_POST['rsvp_code'])) {
    // if set then look up the rsvp code and match to a guest on the guest list table
    //set up variables
    $rsvp_code_input = mysqli_real_escape_string($db, $_POST['rsvp_code']);
    //check that this code has not been used already 
    if ($rsvp_status = $db->prepare('SELECT guest_rsvp_code, guest_rsvp_status FROM guest_list WHERE guest_rsvp_code = ? AND guest_rsvp_status=""')) {
        $rsvp_status->bind_param('s', $rsvp_code_input);
        $rsvp_status->execute();
        $rsvp_status->store_result();
    }
    if ($rsvp_status->num_rows == 0) {
        $response = '<div class="form-response error"><p>RSVP Code has already been used. Try logging in with your email address you registered with.</p>
                    <a href="login">LOGIN NOW<a/>
                    </div>';
        echo $response;
        exit();
    }
    if ($rsvp = $db->prepare('SELECT guest_fname, guest_sname, guest_extra_invites,guest_rsvp_code FROM guest_list WHERE guest_rsvp_code = ?')) {
        $rsvp->bind_param('s', $rsvp_code_input);
        $rsvp->execute();
        $rsvp->store_result();
    }
    if ($rsvp->num_rows > 0) {
        //check the rsvp code exists on the guest list
        $rsvp->bind_result($guest_fname, $guest_sname, $guest_extra_invites, $guest_rsvp_code);
        $rsvp->fetch();
        $rsvp->close();
        $extra_inv = "";
        //define if the guest has extra invites, if they do then show this in the information.
        if ($guest_extra_invites > 0) {
            $extra_inv = '<p><strong>No. of extra invites: </strong>' . $guest_extra_invites . '</p>';
        }
        //if found then echo out the invitation details 
        $response =
            '<div class="form-response ">
            <h2>RSVP Code Found.</h2>
            <p><strong>Here are the details we have for you:</strong></p>
            <p><strong>Name: </strong>' . $guest_fname . ' ' . $guest_sname . '</p>
            ' . $extra_inv . '
            <p>You can now proceed to our Guest Area to manage your invitation.</p>
            <a class="btn-primary my-3" href="rsvp.php?action=setup&rsvp_code=' . $guest_rsvp_code . '">Enter Guest Area</a>
        </div>';
    } else {
        //if there is no match then echo out an error
        $response = '<div class="form-response "><p>RSVP Code not found, please check and try again.</p></div>';
    }
}

//process the password setup
if (isset($_POST['action']) && $_POST['action'] == "pw_setup") {
    //define variables to setup user account
    $guest_id = $_POST['guest_id'];
    $user_name = $_POST['guest_name'];
    $guest_email = mysqli_real_escape_string($db, $_POST['guest_email']);
    //rsvp status is set to setup tp prevent the code being used again
    $guest_rsvp_status = "setup";
    //verify that the email is not already used
    $user_email = $db->prepare('SELECT user_email FROM users WHERE user_email =? AND user_type="wedding_guest"');
    $user_email ->bind_param('s',$_POST['guest_email']);
    $user_email->execute();
    $user_email->store_result();
    //if there is already a user with this email then send back a response and stop the script.
    if($user_email ->num_rows >0){
        $response ='<div class="form-response error"><p>A user with that email address already exists. Please provide another email address.</p></div>';
        
        echo $response;
        exit();
    }

    //verify that passwords match
    $pw1 = mysqli_real_escape_string($db, $_POST['pw1']);
    $pw2 = mysqli_real_escape_string($db, $_POST['pw2']);
    if ($pw1 == $pw2) {
        //set password
        $password = password_hash($pw1, PASSWORD_DEFAULT);

        //Update guest table with email address and rsvp status to prevent code being used more than once
        $guest = $db->prepare('UPDATE guest_list SET guest_email=?, guest_rsvp_status=? WHERE guest_id =?');
        $guest->bind_param('ssi', $guest_email, $guest_rsvp_status, $guest_id);
        $guest->execute();
        $guest->close();

        $user_email = $guest_email;
        $user_pw = $password;
        $user_type = "wedding_guest";
        $user_pw_status = "SET";
        //create a user in users table and set user type as wedding_guest, add the guest id also
        $new_user = $db->prepare('INSERT INTO users (user_email, user_name, user_pw, user_type, guest_id, user_pw_status) VALUES (?,?,?,?,?,?)');
        $new_user->bind_param('ssssis', $user_email, $user_name, $user_pw, $user_type, $guest_id, $user_pw_status);
        $new_user->execute();
        $new_user->close();

        //once successful send an email to confirm set up of guest area and provide link to login
        //load wedding details for email
        $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
        $wedding = $db->query($wedding_query);
        $wedding_result = $wedding->fetch_assoc();
        /////////////////////Send email with confirmation/////////////////////////
        include("../inc/settings.php");
        //email subject
        $subject = 'Your Guest Area Account';
        //body of email to send to client as an auto reply
        $body = '
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">'.$wedding_result['wedding_name'].'\'s Wedding</h1>
                <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                    <h2>You are now set up in our guest area</h2>
                    <p>Dear ' . $user_name . ', thank you for setting up your guest area. You can now respond to your invitation!</p>
                    
                    <br><hr style="color:#7f688d;">
                    <p>Kind regards</p>
                    <p><strong>'.$wedding_result['wedding_name'].'</strong></p>
                    <p>You can contact us with any questions about our big day via our email:<strong>'.$wedding_result['wedding_email'].'</strong></p>
                    
                </div>
            </div>';
        //configure email to send to users
        //stored in separate file
        //From Server
        $fromserver = $username;
        $email_to = $user_email;
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
        //Set CC address
        $mail->addCC($wedding_result['wedding_email']);
        if (!$mail->Send()) {
             echo "Mailer Error: " . $mail->ErrorInfo;
         }
        $response = "success";
       
    } else {
        //if passwords don't match then send back an error and exit the script
        $response = '<div class="form-response error"><p>Passwords Don\'t Match. Please Try Again.</p></div>';
        echo $response;
        exit();
    }
}





echo $response;
