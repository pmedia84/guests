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
    //verify that passwords match
    $pw1 = mysqli_real_escape_string($db, $_POST['pw1']);
    $pw2 = mysqli_real_escape_string($db, $_POST['pw2']);
    if ($pw1 == $pw2) {
        //set password
        $password = password_hash($pw1, PASSWORD_DEFAULT);

        //Update guest table with email address
        $guest = $db->prepare('UPDATE guest_list SET guest_email=? WHERE guest_id =?');
        $guest->bind_param('ss', $guest_email, $guest_id);
        $guest->execute();
        $guest->close();

        $user_email = $guest_email;
        $user_pw = $password;
        $user_type = "wedding_guest";
        $user_pw_status = "SET";
        //create a user in users table and set user type as wedding_guest
        $new_user = $db->prepare('INSERT INTO users (user_email, user_name, user_pw, user_type, user_pw_status) VALUES (?,?,?,?,?)');
        $new_user->bind_param('sssss', $user_email, $user_name, $user_pw, $user_type, $user_pw_status);
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
                <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                    <h2>You are now set up in our guest area</h2>
                    <p>Dear ' . $user_name . ', thank you for setting up your guest area. You can now respond to your invitation!</p>
                    
                    <br><hr style="color:#496e62;">
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
