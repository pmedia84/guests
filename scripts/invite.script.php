<?php
/////Include PHP Mailer\\\\
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
//response variable
$response = "";
include("../connect.php");

////////////////////////\\\\\\\\\\\\\\\\\\\\\\Response Script \\\\\\\\\\\\\\\//////////////////////////////////////////////
//if action type is response then update guest table and invites table
if (isset($_POST['action']) && $_POST['action'] == "response") {
    //set up variables
    $guest_id = $_POST['guest_id'];// guest ID of lead guest
    $guest_group_id = $_POST['guest_group_id'];
    $event_id = $_POST['event_rsvp'][0]['event_id']; // will only ever be one event
    //\\ if the guest has responded and stated that they will not be attending:://\\
    if ($_POST['event_rsvp'][0]['rsvp'] === "Not Attending") {
        //no need to create guest group etc
        //loop through the event information
        
        $event_ar = $_POST['event_rsvp'];
        $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?, guest_group_id=?  WHERE event_id =? AND guest_id=?');
        foreach ($event_ar as $rsvp) {
            $update_rsvp->bind_param('siii', $rsvp['rsvp'], $guest_group_id, $rsvp['event_id'], $guest_id);
            $update_rsvp->execute();
        }
        $update_rsvp->close();
        $response = "Success";
        echo $response;
        
        //update the guest list for the main guest
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
        $update_guest_list->bind_param('ssi',$invite_rsvp_status, $guest_dietery, $guest_id);
        $update_guest_list->execute();
        $update_guest_list->close();
        exit();
    }

    //////////////////\\\if the guest has stated they are attending then carry on\\\////////////////////////
    ////make sure that if the guest has extra invites they have defined them
    if (!isset($_POST['guest']) && $_POST['guest_extra_invites'] > 0) {
        //\\If the guest has said they are attending but have not added guests then stop the script and send back an error//\\
        $response = '<div class="form-response error"><p>You have not told us who you are bringing with you. Please add your group members.</p></div>';
        echo $response;
        exit();
    } else {
        ///////////If the guest has set guests, add guests to guest list etc 
        ////Update the main guests information
        $invite_rsvp_status = "Attending";
        $event_ar = $_POST['event_rsvp'];
        $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?, guest_group_id=?  WHERE event_id =? AND guest_id=?');
        foreach ($event_ar as $rsvp) {
            $update_rsvp->bind_param('siii', $rsvp['rsvp'], $guest_group_id, $rsvp['event_id'], $guest_id);
            $update_rsvp->execute();
        }
        $update_rsvp->close();
        echo var_dump($event_ar);

        //\\Add all guests to the guest group//\\
        $guest_group = $_POST['guest'];
        $invite_rsvp_status = "Attending";
        //////Update guest group status
        $guest_group_status = "Assigned"; //Set as assigned to prevent admin removing invites, but more can be added
        $group_status = $db->prepare('UPDATE guest_groups SET guest_group_status=?  WHERE guest_group_id =?');
        $group_status->bind_param('si', $guest_group_status, $guest_group_id);
        $group_status->execute();
        $group_status->close();

        /////insert each guest into the guest list from the POST request
        //guest array for all new added guests
        $guest_array=array();
        $guest_type = "Member"; //only set as a member, these guests are a group member
        $new_guest = $db->prepare('INSERT INTO guest_list (guest_fname, guest_sname, guest_rsvp_status, guest_type, guest_group_id, guest_dietery) VALUES (?,?,?,?,?,?)');
        foreach ($guest_group as $group_member) {
            $new_guest->bind_param('ssssis', $group_member['guest_fname'], $group_member['guest_sname'], $invite_rsvp_status, $guest_type, $guest_group_id, $group_member['guest_dietary']);
            $new_guest->execute();
            //insert into an array for adding to the invites table
            $new_guest_id = $db->insert_id;
            array_push($guest_array, $new_guest_id);
        }
        $new_guest->close();

        $invite_rsvp_status = "Attending"; // this is set to attending.
        /////Add to invites table for each guest 
        $set_invites = $db->prepare('INSERT INTO invitations (guest_id, event_id, invite_rsvp_status, guest_group_id) VALUES (?,?,?,?)');
        foreach($guest_array as $guest){
            $set_invites->bind_param('iisi', $guest, $event_id, $invite_rsvp_status, $guest_group_id );
            $set_invites->execute();
           
        }
        
        $set_invites->close();
    }







    $guest_rsvp_note = mysqli_real_escape_string($db, $_POST['rsvp_note']);
    
   


    // /////////////////////Send email with confirmation/////////////////////////
    // //load guest details
    // $guest_query = ('SELECT guest_fname, guest_sname FROM guest_list WHERE guest_id='.$guest_id);
    // $guest = $db->query($guest_query);
    // $guest_result = $guest->fetch_assoc();
    // //load wedding details
    // $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    // $wedding = $db->query($wedding_query);
    // $wedding_result = $wedding->fetch_assoc();
    // //load event name details
    // $event_query = ('SELECT event_name FROM wedding_events WHERE event_id='.$event_id);
    // $event = $db->query($event_query);
    // $event_result = $event->fetch_assoc();
    // include("../inc/settings.php");
    // //email subject
    // $subject = $guest_result['guest_fname'].' '.$guest_result['guest_sname'].' '.'has responded to their invitation!';
    // //body of email to send to client as an auto reply
    // $body = '
    //     <div style="padding:16px;font-family:sans-serif;">
    //         <h1 style="text-align:center;">Your Guest</h1>
    //         <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
    //             <h2>'.$guest_result['guest_fname'].' '.'has responded to their invitation!'.'</h2>
    //             <p>Dear ' .$wedding_result['wedding_name'].', here are the details of their response:</p>
    //             <p><strong>Event: </strong>'.$event_result['event_name'].'</p>    
    //             <p><strong>Their Response: </strong>'.$invite_rsvp_status.'</p>
    //             <p><strong>Any Dietary Requirements: </strong>'.$guest_dietery.'</p>
    //             <p><strong>Their Message:</strong><br>'.$guest_rsvp_note.'</p>
    //             <br><hr style="color:#496e62;">
    //             <p>Kind regards</p>
    //         </div>
    //     </div>';
    // //configure email to send to users
    // //stored in separate file
    // //From Server
    // $fromserver = $username;
    // $email_to = $wedding_result['wedding_email'];
    // $mail = new PHPMailer(true);
    // $mail->IsSMTP();
    // $mail->Host = $host; // Enter your host here
    // $mail->SMTPAuth = true;
    // $mail->Username = $username; // Enter your email here
    // $mail->Password = $pass; //Enter your password here
    // $mail->Port = 25;
    // $mail->From = $from;
    // $mail->FromName = $fromname;
    // $mail->Sender = $fromserver; // indicates ReturnPath header
    // $mail->Subject = $subject;
    // $mail->Body = $body;
    // $mail->IsHTML(true);
    // $mail->AddAddress($email_to);
    // if (!$mail->Send()) {
    //      echo "Mailer Error: " . $mail->ErrorInfo;
    //  }
    //response success
    $response = "success";
}

//if action type is update then update guest table and invites table
if (isset($_POST['action']) && $_POST['action'] == "update") {
    //set up variables
    $guest_id = $_POST['guest_id'];
    $event_id = $_POST['event_id'];
    $guest_group_id = $_POST['guest_group_id'];
    $invite_rsvp_status = $_POST['invite_rsvp_status'];
    $guest_dietery = $_POST['guest_dietery'];
    $guest_rsvp_note = mysqli_real_escape_string($db, $_POST['rsvp_note']);
    //update the invitations table
    $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?  WHERE event_id =? AND guest_id=?');
    $update_rsvp->bind_param('sii', $invite_rsvp_status, $event_id, $guest_id);
    $update_rsvp->execute();
    $update_rsvp->close();
    //update the whole group rsvp status
    $update_group_list = $db->prepare('UPDATE invitations SET invite_rsvp_status=?  WHERE  guest_group_id=?');
    $update_group_list->bind_param('si', $invite_rsvp_status, $guest_group_id);
    $update_group_list->execute();
    $update_group_list->close();

    // remove all guests from guest list of status set as not attending
    if ($invite_rsvp_status == "Not Attending") {
        $update_guest_list = $db->prepare('DELETE FROM guest_list WHERE  guest_group_id=? AND guest_type="Member"');
        $update_guest_list->bind_param('i', $guest_group_id);
        $update_guest_list->execute();
        $update_guest_list->close();
    }
    /////////////////////Send email with confirmation/////////////////////////
    //load guest details
    $guest_query = ('SELECT guest_fname, guest_sname FROM guest_list WHERE guest_id=' . $guest_id);
    $guest = $db->query($guest_query);
    $guest_result = $guest->fetch_assoc();
    //load wedding details
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    //load event name details
    $event_query = ('SELECT event_name FROM wedding_events WHERE event_id=' . $event_id);
    $event = $db->query($event_query);
    $event_result = $event->fetch_assoc();
    include("../inc/settings.php");
    //email subject
    $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has updated their response to your invitation!';
    //body of email to send to client as an auto reply
    $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #496e62; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has changed their response to your invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $invite_rsvp_status . '</p>
                <p><strong>Any Dietary Requirements: </strong>' . $guest_dietery . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
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
        $response = "Mailer Error: " . $mail->ErrorInfo;
    }
    //response success
    $response = "success";
}
//echo out variable
echo $response;
