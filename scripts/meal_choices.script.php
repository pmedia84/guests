<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
if (isset($_POST['action']) && $_POST['action'] == "set_choices") {

    include("../connect.php");

    $meal_choices = $db->prepare('INSERT INTO meal_choices (menu_item_id, choice_order_id)VALUES(?,?)');
    foreach ($_POST['guest_meal_choices'] as $guest_choice) {
        //check to see if a meal choices order has been set up for this guest.
        $meal_order_q = $db->query('SELECT choice_order_id FROM meal_choice_order WHERE guest_id=' . $guest_choice['guest_id']);
        if ($meal_order_q->num_rows > 0) {
            $meal_order_r = mysqli_fetch_assoc($meal_order_q);
            $choice_order_id = $meal_order_r['choice_order_id'];
        } else {
            //if not then set one up
            $new_choice_order = $db->query('INSERT INTO meal_choice_order (guest_id) VALUES(' . $guest_choice['guest_id'] . ')');
            $choice_order_id = $db->insert_id;
        }
        //next add the meal choices
        $meal_choices->bind_param("ii", $guest_choice['menu_item_id'], $choice_order_id);
        $meal_choices->execute();
    }
    $meal_choices->close();
    include("../inc/settings.php");
    //Send email to the wedding admins
    //load wedding details for email
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    //email subject
    $subject = $_POST['guest_name']." has provided their meal choices";
    //body of email to send to client as an auto reply
    $body = '
    <div style="padding:16px;font-family:sans-serif;">
    <h1 style="text-align:center;">Your Guest</h1>
    <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
        <h2>' . $_POST['guest_name'] . ' ' . 'has provided you with their meal choices' . '</h2>
        <p>' . $wedding_result['wedding_name'] . ', you can see what they have chosen on their guest profile.</p>
        <p>To view their profile <a href="https://' . $_SERVER['SERVER_NAME'] .'/admin/guest?guest_id='.$_POST['guest_id'].'&action=view">Click Here</a>
        <br><hr style="color:#7f688d;">
    </div>
</div>';
    //configure email to send to users
    //stored in separate file
    //From Server
    $fromserver = $username;
    $email_to = "besleykarl@gmail.com";
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
    exit("1");
}
