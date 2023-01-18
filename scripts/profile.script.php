<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
$response ="";
if(array_key_exists('action', $_POST) && $_POST['action']=="edit"){
    include("../connect.php");
    //edit the guest
    //set up variables
    $guest_fname=mysqli_real_escape_string($db, $_POST['guest_fname']);
    $guest_sname=mysqli_real_escape_string($db, $_POST['guest_sname']);
    $guest_email=mysqli_real_escape_string($db, $_POST['guest_email']);
    $guest_address=mysqli_real_escape_string($db, $_POST['guest_address']);
    $guest_postcode=mysqli_real_escape_string($db, $_POST['guest_postcode']);
    
    $guest_id = $_POST['guest_id'];
    $user_id = $_POST['user_id'];
    //detect if the user has changed their email address, look up users table
    $email_verify = $db->query('SELECT user_email FROM users  WHERE user_id='.$user_id.' AND user_type="wedding_guest"');
    $email_verify_result =$email_verify->fetch_assoc();
    $email_verify->close();
    $user_email = $email_verify_result['user_email'];
    
    if($guest_email <> $user_email){
        $user_pw_status = "TEMP";
        //update users table and set password as TEMP
        $update_user = $db->prepare('UPDATE users SET user_email=?, user_pw_status=? WHERE user_id=?');
        $update_user->bind_param('si',$guest_email, $user_pw_status, $guest_id);
        $update_user->execute();
        $update_user->close();

    }

    //update guest list
    $update_guest = $db->prepare('UPDATE guest_list SET guest_fname=?, guest_sname=?, guest_email=?, guest_address=?, guest_postcode=? WHERE guest_id=?');
    $update_guest->bind_param('sssssi',$guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_id);
    $update_guest->execute();
    $update_guest->close();

     /////////////////////Send email with confirmation/////////////////////////
    //load wedding details for email
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
     include("../inc/settings.php");
     //email subject
     $subject = 'Your Guest Profile';
     //body of email to send to client as an auto reply
     $body = '
         <div style="padding:16px;font-family:sans-serif;">
             <h1 style="text-align:center;">'.$wedding_result['wedding_name'].'\'s Wedding</h1>
             <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                 <h2>Your Profile Update</h2>
                 <p>Dear ' . $guest_fname . ', thank you for updating your contact details with us.</p>
                 <p>The changes you have requested have been updated. If you did not request this, contact us to rectify.</p>
                 <br><hr style="color:#496e62;">
                 <p>Kind regards</p>
                 <p><strong>'.$wedding_result['wedding_name'].'</strong></p>
                 <p>You can contact us with any questions about our big day via our email: <strong>'.$wedding_result['wedding_email'].'</strong></p>
                 
             </div>
         </div>';
     //configure email to send to users
     //stored in separate file
     //From Server
     $fromserver = $username;
     $email_to = $guest_email;
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
}
echo $response;
?>