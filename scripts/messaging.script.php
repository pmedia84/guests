<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
$response ="";
if(isset($_POST) && $_POST['action']=="send"){
    include("../connect.php");
    //sending messages to wedding organisers
    //set up variables
    $guest_fname=mysqli_real_escape_string($db, $_POST['guest_fname']);
    $guest_sname=mysqli_real_escape_string($db, $_POST['guest_sname']);
    $guest_email=mysqli_real_escape_string($db, $_POST['guest_email']);
    
    $guest_id = $_POST['guest_id'];
    $message_body = mysqli_real_escape_string($db, $_POST['message_body']);
    $message_subject = mysqli_real_escape_string($db, $_POST['message_subject']);


    //update guest list
    $save_message = $db->prepare('INSERT INTO guest_messages (message_subject, message_body, guest_id, guest_email) VALUES (?,?,?,?)');
    $save_message->bind_param('ssis',$message_subject, $message_body, $guest_id, $guest_email );
    $save_message->execute();
    $save_message->close();

     /////////////////////Send email with confirmation/////////////////////////
    //load wedding details for email
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
     include("../inc/settings.php");
     //email subject
     $subject = $guest_fname.' '.$guest_sname.' has messaged you.';
     //body of email to send to client as an auto reply
     $body = '
         <div style="padding:16px;font-family:sans-serif;">
             <h1 style="text-align:center;">You have received a message in your guest area. </h1>
             <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                 <h2>Guest Message</h2>
                 <p><strong>Subject: </strong>'.$message_subject.'</p>
                 <p><strong>Message: </strong><br>'.$message_body.'</p>
                 <p><strong>Their eMail: </strong><br>'.$guest_email.'</p>
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
     //Set CC address
     $mail->addCC($wedding_result['wedding_email']);
     if (!$mail->Send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
      }
      $response = '<div class="form-response"><p>Thank you for your message. We will respond to you as soon as we can.</p></div>';
}
echo $response;
?>