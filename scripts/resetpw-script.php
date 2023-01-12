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

//set up variable for response message
$response = "";

//Action type = requestpwreset
if ($_POST['action'] == 'requestreset') {
    if ($user = $db->prepare('SELECT user_id, user_email, user_name FROM users WHERE user_email = ?')) {


        $user->bind_param('s', $_POST['user_email']);
        $user->execute();
        $user->store_result();

        if ($user->num_rows > 0) {

            //check the user exists
            $user->bind_result($user_id, $email, $name);
            $user->fetch();
            $user->close();
            //verify email
            $verify = $email;
            if ($verify == $_POST['user_email']) {
                //create a random key code
                $key = substr(md5(rand()), 0, 7);
                $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
                $key = $key . $addKey;
                //set date with expiry in one day
                date_default_timezone_set('Europe/London');
                $curdate = date('Y-m-d');
                $date = date_create($curdate);
                date_add($date, date_interval_create_from_date_string("1 days"));
                $expdate = date_format($date, "Y-m-d");
                //insert into temp reset table
                $reset = $db->prepare('INSERT INTO pwreset (user_id, user_email, pwreset_code,pwreset_expdate )VALUES(?,?,?,?)');
                $reset->bind_param('ssss', $user_id, $email, $key, $expdate);
                $reset->execute();
                $reset->close();
                /////////////////////Send email with reset code/////////////////////////

                //email subject
                $subject = 'Your password reset link';
                //body of email to send to client as an auto reply
                $body = '
            <img src="' . $emailheaderlogo . '">
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">' . $name . ' You have requested a password reset</h1>
                <div style="padding:16px; border: 10px solid #3b685c; border-radius: 10px;">
                    <h2>Follow the instructions below:</h2>
                    <p>Dear ' . $name . ', please click on the link below to reset your password</p>
                    <strong><a href="http://localhost/admin/resetpw.php?key=' . $key . '&user_id=' . $user_id . '&action=reset">Click Here</a></strong>
                    <p><strong>Please Note:</strong>This email link will only last for 24 hours, after that time you will need to request another password reset.</p>
                    <br><hr style="color:#3b685c;">
                    <p>Kind regards</p>
                    <p><strong>Parrot Media</strong></p>
                </div>
            </div>';
                //configure email to send to users
                //stored in separate file
                //From Server
                $fromserver = $username;
                $email_to = $email;
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
                echo '<div class="form-response"><p>User found, please check your emails for further instructions.</p></div>';
            }
        } else {
            echo '<div class="form-response error"><p>User not found with that email address, please try again.</p></div>';
        }
    }
}


///////Action type = reset/////

if ($_POST['action'] == 'reset') {
    //declare variables
    $password1 = mysqli_real_escape_string($db, $_POST['pw1']);
    $password2 = mysqli_real_escape_string($db, $_POST['pw2']);
    $user_id = $_POST['user_id'];
    $pwreset_key = $_POST['key'];
    //connect to database table
    $resetkey = $db->prepare('SELECT * FROM pwreset WHERE pwreset_code = ?');
    $resetkey->bind_param('s', $pwreset_key);
    $resetkey->execute();
    $resetkey->store_result();

    //check that the code is valid and returns a result
    if ($resetkey->num_rows >= 1) {
        $resetkey->bind_result($pwreset_id, $user_id, $user_email, $pwreset_code, $pwreset_expdate);
        $resetkey->fetch();
        $resetkey->close();
        //determine date and check if the link has expired or not
        $currentdate = date("Y-m-d");
        $expirydate = $pwreset_expdate;
        if ($currentdate <= $expirydate) {
        } else {
            $response = '<div class="form-response error"><p>Link has expired, please request a new reset link <a href="resetpw.php">HERE</a></p></div>';
            echo $response;
            exit();
        }
    } else {
        $response = '<div class="form-response error"><p>Link error, please request a new reset link <a href="resetpw.php">HERE</a></p></div>';
        echo $response;
        exit();
    }

    //check that both passwords match when posting as a reset POST
    if ($password1 == $password2) {
        //set password
        $password = password_hash($password1, PASSWORD_DEFAULT);
        //connect to database users table and update password
        $newpw = "UPDATE users SET user_pw = '$password' WHERE user_id = " . $user_id;
        $submit = $db->query($newpw);

        //delete temp reset code entry onto pwreset table
        $delete_pw_reset = "DELETE FROM pwreset WHERE pwreset_code = '$pwreset_code' ";
        $delete = $db->query($delete_pw_reset);
        $response = '<div class="form-response"><p>Password updated, you can now <a href="login.php">Login<a/></p></div>';

        //send a confirmation email to confirm password changed
        //locate user details
        $user = $db->prepare('SELECT user_id, user_email, user_name FROM users WHERE user_id = ?');
        $user->bind_param('s', $user_id);
        $user->execute();
        $user->store_result();
        $user->bind_result($user_id, $email, $name);
        $user->fetch();
        $user->close();
        /////////////////////Send email with reset code/////////////////////////
        //email subject
        $subject = 'Your password has been reset';
        //body of email to send to client as an auto reply
        $body = '
            <img src="' . $emailheaderlogo . '">
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">' . $name . ' You have successfully reset your password</h1>
                <div style="padding:16px; border: 10px solid #3b685c; border-radius: 10px;">
                    <h2>Your new password:</h2>
                    <p>Dear ' . $name . ', you password has now been reset.</p>
                    <p><strong>Please Note:</strong>If you did not request this, please contact us to advise you how to secure your account.</p>
                    <br><hr style="color:#3b685c;">
                    <p>Kind regards</p>
                    <p><strong>Parrot Media</strong></p>
                </div>
            </div>';
        //configure email to send to users
        //stored in separate file
        //From Server
        $fromserver = $username;
        $email_to = $email;
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
    } else {
        $response = '<div class="form-response error"><p>Passwords do not match! Please try again.</p></div>';
    }
}

///////Action Type = Reset Temp
//Temporary password reset feature
if ($_POST['action'] == "tempreset") {
    //declare variables
    $password1 = mysqli_real_escape_string($db, $_POST['new_pw']);
    $password2 = mysqli_real_escape_string($db, $_POST['new_pw2']);
    $user_id = $_POST['user_id'];


    //check that both passwords match when posting as a reset POST
    if ($password1 == $password2) {
        //set password
        $password = password_hash($password1, PASSWORD_DEFAULT);
        //connect to database users table and update password
        $newpw = "UPDATE users SET user_pw = '$password', user_pw_status ='SET' WHERE user_id = " . $user_id;
        $submit = $db->query($newpw);



        //send a confirmation email to confirm password changed
        //locate user details
        $user = $db->prepare('SELECT user_id, user_email, user_name FROM users WHERE user_id = ?');
        $user->bind_param('s', $user_id);
        $user->execute();
        $user->store_result();
        $user->bind_result($user_id, $email, $name);
        $user->fetch();
        $user->close();
        /////////////////////Send email with confirmation/////////////////////////
        //email subject
        $subject = 'Your password has now been reset';
        //body of email to send to client as an auto reply
        $body = '
                <img src="' . $emailheaderlogo . '">
                <div style="padding:16px;font-family:sans-serif;">
                    <h1 style="text-align:center;">' . $name . ' You have successfully reset your password</h1>
                    <div style="padding:16px; border: 10px solid #3b685c; border-radius: 10px;">
                        <h2>Follow the instructions below:</h2>
                        <p>Dear ' . $name . ', you password has now been reset.</p>
                        <p><strong>Please Note:</strong>If you did not request this, please contact us to advise you how to secure your account.</p>
                        <br><hr style="color:#3b685c;">
                        <p>Kind regards</p>
                        <p><strong>Parrot Media</strong></p>
                    </div>
                </div>';
        //configure email to send to users
        //stored in separate file
        //From Server
        $fromserver = $username;
        $email_to = $email;
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

        $response = '<div class="form-response"><p>Password reset, you can now login. <br> <a href="index.php">Login Now<a></p></div>'; 
    } else {
        $response = '<div class="form-response error"><p>Passwords do not match! Please try again.</p></div>';
    }
}
//echo out the response message
echo $response;
$db->close();
