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
if(empty($_POST['user_id'])){
    $response = '<div class="form-response error"><p>Form Error, please try again!</p></div>';
    
}else{
    //declare variables
    $business_id = $_POST['business_id'];
    $user_name = mysqli_real_escape_string($db, $_POST['user_name']);
    $user_email = mysqli_real_escape_string($db, $_POST['user_email']);
    $user = $db->prepare('UPDATE users SET  user_email=?, user_name=?  WHERE user_id ='.$user_id);
    $user->bind_param('ss', $user_email, $user_name);
    $user->execute();

                /////////////////////Send email to confirm changes/////////////////////////

                //email subject
                $subject = 'Your user profile has been updated';
                //body of email to send to client as an auto reply
                $body = '
            <img src="' . $emailheaderlogo . '">
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">' . $user_name . ' Your user details have been updated</h1>
                <div style="padding:16px; border: 10px solid #03b0fa; border-radius: 10px;">
                <h2>Your user account has been updated:</h2>
                    <p>Dear ' . $user_name . ', we are pleased to confirm that the changes you requested have been completed.</p>
                    <p><strong>Please Note:</strong>If you did not request these changes, please contact us.</p>
                    <br><hr style="color:#3b685c;">
                    <p>Kind regards</p>
                    <p><strong>Parrot Media</strong></p>
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


    $response = 'success';
    
}
//set up variable for response message


echo $response;
