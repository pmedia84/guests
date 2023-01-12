<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
if (isset($_POST['action'])) {
    include("../connect.php");
    //check that post action has been sent through, if it has perform create business and users from setup page
    if ($_POST['action'] == "create_business") {

        //declare variables
        //address first
        $add_1 = mysqli_real_escape_string($db, $_POST['address_line_1']);
        $add_2 = mysqli_real_escape_string($db, $_POST['address_line_2']);
        $add_3 = mysqli_real_escape_string($db, $_POST['address_line_3']);
        $add_county = mysqli_real_escape_string($db, $_POST['address_county']);
        $add_pc = mysqli_real_escape_string($db, $_POST['address_pc']);

        //new business variables
        $business_name = mysqli_real_escape_string($db, $_POST['business_name']);
        $business_email = mysqli_real_escape_string($db, $_POST['business_email']);
        $business_phone = mysqli_real_escape_string($db, $_POST['business_phone']);
        $business_contact_name = mysqli_real_escape_string($db, $_POST['business_contact_name']);

        //set up address first
        $address = $db->prepare('INSERT INTO addresses (address_line_1, address_line_2, address_line_3, address_county, address_pc)VALUES(?,?,?,?,?)');
        $address->bind_param('sssss', $add_1, $add_2, $add_3, $add_county, $add_pc);
        $address->execute();
        $address->close();
        $address_id = mysqli_insert_id($db);

        //insert new business into table
        $new_business = $db->prepare('INSERT INTO business (business_name, address_id, business_phone, business_email, business_contact_name)VALUES(?,?,?,?,?)');
        $new_business->bind_param('sisss', $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
        $new_business->execute();
        $new_business->close();
    }
    if ($_POST['action'] == "create_user_business") {
        include("../inc/settings.php");
        //declare variables
        $user_email = mysqli_real_escape_string($db, $_POST['user_email']);
        $user_name = mysqli_real_escape_string($db, $_POST['username']);
        $business_id = $_POST['business_id'];
        $user_type = $_POST['user_type'];
        $user_cms_type = $cms_type;
        // Generate Random Password
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
        $password = substr(str_shuffle($chars), 0, 8);
        $user_pw = $password;
        $password = password_hash($password, PASSWORD_DEFAULT);
        //set a pw status of temp
        $user_pw_status = "TEMP";
        //check user does not exist with email provided
        $user = $db->prepare('SELECT user_email FROM users WHERE user_email = ?');
        $user->bind_param('s', $user_email);
        $user->execute();
        $user->store_result();
        //if user exists, echo an error
        if ($user->num_rows > 0) {
            echo "User already exists with that email address";
            exit();
        } else {
            //if not, then insert new user
            //find business name
            $business_name = $db->prepare('SELECT business_name FROM business WHERE business_id = ?');
            $business_name->bind_param('i', $business_id);
            $business_name->execute();
            $business_name->bind_result($business_name);
            $business_name->fetch();

            //insert new user into table
            $new_user = $db->prepare('INSERT INTO users (user_email, user_name, user_pw, user_type, user_pw_status, user_cms_type)VALUES(?,?,?,?,?,?)');
            $new_user->bind_param('ssssss', $user_email, $user_name, $password, $user_type, $user_pw_status, $user_cms_type);
            $new_user->execute();
            $new_user->close();
            //find user_id of last user created
            $new_user_id = $db->insert_id;

            //insert into business users table
            $new_user = $db->prepare('INSERT INTO business_users (users_user_id, business_id, user_type)VALUES(?,?,?)');
            $new_user->bind_param('iis', $new_user_id, $business_id, $user_type);
            $new_user->execute();
            $new_user->close();
            // /////////////////////Send email to confirm Setup and password/////////////////////////

            //email subject
            $subject = "You have been set up as a new user for " . $business_name . "";
            //body of email to send to client as an auto reply
            $body = '
            <img src="' . $emailheaderlogo . '">
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">' . $user_name . ' Your new user account</h1>
                <div style="padding:16px; border: 10px solid #5ca38f; border-radius: 10px;">
                <h2>Your user account has now been set up:</h2>
                    <p>Dear ' . $user_name . ', we are pleased to let you know that ' . $business_name . ' has added you as a user for their admin area.</p>
                    <p><strong>User Name:</strong>' . $user_name . '</p>
                    <p><strong>User Email:</strong>' . $user_email . '</p>
                    <p><strong>User Access Level:</strong>' . $user_type . '</p>
                    <p><strong>Password:</strong>' . $user_pw . '</p>
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
        }
    }

    //setup script for creating a wedding
    if ($_POST['action'] == "create_wedding") {
        //new wedding variables
        $wedding_name = mysqli_real_escape_string($db, $_POST['wedding_name']);
        $wedding_email = mysqli_real_escape_string($db, $_POST['wedding_email']);
        $wedding_phone = mysqli_real_escape_string($db, $_POST['wedding_phone']);
        $wedding_contact_name = mysqli_real_escape_string($db, $_POST['wedding_contact_name']);

        //insert new wedding into table
        $new_wedding = $db->prepare('INSERT INTO wedding (wedding_name, wedding_phone, wedding_email, wedding_contact_name)VALUES(?,?,?,?)');
        $new_wedding->bind_param('ssss', $wedding_name,  $wedding_phone, $wedding_email, $wedding_contact_name);
        $new_wedding->execute();
        $new_wedding->close();

    }

//set up users for wedding cms

    if ($_POST['action'] == "create_user_wedding") {
        include("../inc/settings.php");
        //declare variables
        $user_email = mysqli_real_escape_string($db, $_POST['user_email']);
        $user_name = mysqli_real_escape_string($db, $_POST['username']);
        $wedding_id = $_POST['wedding_id'];
        $user_type = $_POST['user_type'];
        $user_cms_type = $cms_type;
        // Generate Random Password
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
        $password = substr(str_shuffle($chars), 0, 8);
        $user_pw = $password;
        $password = password_hash($password, PASSWORD_DEFAULT);
        //set a pw status of temp
        $user_pw_status = "TEMP";
        //check user does not exist with email provided
        $user = $db->prepare('SELECT user_email FROM users WHERE user_email = ?');
        $user->bind_param('s', $user_email);
        $user->execute();
        $user->store_result();
        //if user exists, echo an error
        if ($user->num_rows > 0) {
            echo "User already exists with that email address";
            exit();
        } else {
            //if not, then insert new user
            //find Wedding name
            $wedding_name = $db->prepare('SELECT wedding_name FROM wedding WHERE wedding_id = ?');
            $wedding_name->bind_param('i', $wedding_id);
            $wedding_name->execute();
            $wedding_name->bind_result($wedding_name);
            $wedding_name->fetch();

            //insert new user into table
            $new_user = $db->prepare('INSERT INTO users (user_email, user_name, user_pw, user_type, user_pw_status, user_cms_type)VALUES(?,?,?,?,?,?)');
            $new_user->bind_param('ssssss', $user_email, $user_name, $password, $user_type, $user_pw_status, $user_cms_type);
            $new_user->execute();
            $new_user->close();
            //find user_id of last user created
            $new_user_id = $db->insert_id;

            //insert into wedding users table
            $new_user_wedding = $db->prepare('INSERT INTO wedding_users (users_user_id, wedding_id, user_type)VALUES(?,?,?)');
            $new_user_wedding->bind_param('iis', $new_user_id, $wedding_id, $user_type);
            $new_user_wedding->execute();
            $new_user_wedding->close();
            // /////////////////////Send email to confirm Setup and password/////////////////////////

            //email subject
            $subject = "You have been set up as a new user for " . $wedding_name . "";
            //body of email to send to client as an auto reply
            $body = '
            <img src="' . $emailheaderlogo . '">
            <div style="padding:16px;font-family:sans-serif;">
                <h1 style="text-align:center;">' . $user_name . ' Your new user account</h1>
                <div style="padding:16px; border: 10px solid #5ca38f; border-radius: 10px;">
                <h2>Your user account has now been set up:</h2>
                    <p>Dear ' . $user_name . ', we are pleased to let you know that ' . $wedding_name . ' has added you as a user for their admin area.</p>
                    <p><strong>User Name:</strong>' . $user_name . '</p>
                    <p><strong>User Email:</strong>' . $user_email . '</p>
                    <p><strong>User Access Level:</strong>' . $user_type . '</p>
                    <p><strong>Password:</strong>' . $user_pw . '</p>
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
        }
    }
}
