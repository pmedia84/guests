<?php
session_start();
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
include("../inc/settings.php");
require("functions.php");
//script is divided into different sections and is relevant to the requests

//1. Response, will skip over 2 and 3 if the requirements have not been met, such as not attending and they don't have a guest group.
//2. If they are not attending, updates tables and removes any guest group 
//3. If the guest responding is a Sole member it will update just the guest list and invitations list, no guest group information is altered.
//4. If the guest has told us they will be attending alone even though extra invites have been assigned, then run a separate script to update guest table.

////////////////////////\\\\\\\\\\\\\\\\\\\\\\Response Script \\\\\\\\\\\\\\\//////////////////////////////////////////////
//if action type is response then update guest table and invites table
//1.
if (isset($_POST['action']) && $_POST['action'] == "response") {
    //set up variables
    $guest_dietery = mysqli_real_escape_string($db, $_POST['guest_dietery']);
    $guest_rsvp_note = mysqli_real_escape_string($db, $_POST['rsvp_note']);
    $guest_id = $_POST['guest_id']; // guest ID of lead guest
    $guest_group_id = $_POST['guest_group_id'];
    $event_id = $_POST['event_rsvp'][0]['event_id']; // will only ever be one event


    //\\ if the guest has responded and stated that they will not be attending:://\\
    //2.    
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


        //update the guest list for the main guest
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
        $update_guest_list->bind_param('ssi', $rsvp['rsvp'], $guest_dietery, $guest_id);
        $update_guest_list->execute();
        $update_guest_list->close();

        //remove any group members if any had been added

        $remove_group = $db->query("DELETE FROM guest_list WHERE guest_group_id=$guest_group_id AND guest_type='Member'");

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
        $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has responded to their invitation!';
        //body of email to send to client as an auto reply
        $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has responded to their invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $rsvp['rsvp'] . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
                <br><hr style="color:#7f688d;">
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
        $response = "success";
        echo $response;
        exit();
    }

    //3.
    /////////////If the guest is a sole invite then process rsvp and don't set up a group etc
    ////////////If the guest has not added any extra guests and just stated they are attending, then send response to server.
    ///////////////////finsish here//////////////////////////////////////////
    //////////////////
    ///////////
    ////////

    if ($_POST['guest_type'] == "Sole" && $_POST['event_rsvp'][0]['rsvp'] === "Attending") {
        //stop the script here

        //loop through the event information

        $event_ar = $_POST['event_rsvp'];
        $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?, guest_group_id=?  WHERE event_id =? AND guest_id=?');
        foreach ($event_ar as $rsvp) {
            $update_rsvp->bind_param('siii', $rsvp['rsvp'], $guest_group_id, $rsvp['event_id'], $guest_id);
            $update_rsvp->execute();
        }
        $update_rsvp->close();
        //update the guest list for the main guest
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
        $update_guest_list->bind_param('ssi', $rsvp['rsvp'], $guest_dietery, $guest_id);
        $update_guest_list->execute();
        $update_guest_list->close();
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
        $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has responded to their invitation!';
        //body of email to send to client as an auto reply
        $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has responded to their invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $rsvp['rsvp'] . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
                <br><hr style="color:#7f688d;">
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
        $response = "success";
        echo $response;
        exit();
    }
    //1. Continued, this carries on if sections 2 and 3 have not been met
    //////////////////\\\if the guest has stated they are attending then carry on\\\////////////////////////
    ////make sure that if the guest has extra invites they have defined them
    //if they do have extra invites but have not ticked the checkbox, suggest that they do and try again.
    if (isset($_POST['sole_invite']) && $_POST['sole_invite'] == "Sole") {
        //stop the script here

        //loop through the event information

        $event_ar = $_POST['event_rsvp'];
        $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?, guest_group_id=?  WHERE event_id =? AND guest_id=?');
        foreach ($event_ar as $rsvp) {
            $update_rsvp->bind_param('siii', $rsvp['rsvp'], $guest_group_id, $rsvp['event_id'], $guest_id);
            $update_rsvp->execute();
        }
        $update_rsvp->close();
        //update the guest list for the main guest
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
        $update_guest_list->bind_param('ssi', $rsvp['rsvp'], $guest_dietery, $guest_id);
        $update_guest_list->execute();
        $update_guest_list->close();
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
        $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has responded to their invitation!';
        //body of email to send to client as an auto reply
        $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has responded to their invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $rsvp['rsvp'] . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
                <br><hr style="color:#7f688d;">
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
        $response = "success";
        echo $response;
        exit();
    }
    if (!isset($_POST['guest'])) {
        //arrives here if none of the above have been met and the guest has simply stated that they are coming

        //loop through the event information

        $event_ar = $_POST['event_rsvp'];
        $update_rsvp = $db->prepare('UPDATE invitations SET invite_rsvp_status=?, guest_group_id=?  WHERE event_id =? AND guest_id=?');
        foreach ($event_ar as $rsvp) {
            $update_rsvp->bind_param('siii', $rsvp['rsvp'], $guest_group_id, $rsvp['event_id'], $guest_id);
            $update_rsvp->execute();
        }
        $update_rsvp->close();
        //update the guest list for the main guest
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?, guest_dietery=?  WHERE  guest_id=?');
        $update_guest_list->bind_param('ssi', $rsvp['rsvp'], $guest_dietery, $guest_id);
        $update_guest_list->execute();
        $update_guest_list->close();
        //update the guest list for group members
        $update_guest_list = $db->prepare('UPDATE guest_list SET guest_rsvp_status=?  WHERE  guest_group_id=?');
        $update_guest_list->bind_param('si', $rsvp['rsvp'], $guest_group_id);
        $update_guest_list->execute();
        $update_guest_list->close();
        $update_group_invites = $db->prepare('UPDATE invitations SET invite_rsvp_status=?  WHERE  guest_group_id=?');
        $update_group_invites->bind_param('si', $rsvp['rsvp'], $guest_group_id);
        $update_group_invites->execute();
        $update_group_invites->close();
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
        $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has responded to their invitation!';
        //body of email to send to client as an auto reply
        $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has responded to their invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $rsvp['rsvp'] . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
                <br><hr style="color:#7f688d;">
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
        $response = "success";
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
        $guest_array = array();
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
        foreach ($guest_array as $guest) {
            $set_invites->bind_param('iisi', $guest, $event_id, $invite_rsvp_status, $guest_group_id);
            $set_invites->execute();
        }

        $set_invites->close();
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
        $subject = $guest_result['guest_fname'] . ' ' . $guest_result['guest_sname'] . ' ' . 'has responded to their invitation!';
        //body of email to send to client as an auto reply
        $body = '
        <div style="padding:16px;font-family:sans-serif;">
            <h1 style="text-align:center;">Your Guest</h1>
            <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">
                <h2>' . $guest_result['guest_fname'] . ' ' . 'has responded to their invitation!' . '</h2>
                <p>Dear ' . $wedding_result['wedding_name'] . ', here are the details of their response:</p>
                <p><strong>Event: </strong>' . $event_result['event_name'] . '</p>    
                <p><strong>Their Response: </strong>' . $rsvp['rsvp'] . '</p>
                <p><strong>Their Message:</strong><br>' . $guest_rsvp_note . '</p>
                <br><hr style="color:#7f688d;">
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
        $response="success";
        echo $response;
        exit();
    }
} ?>
<?php if (isset($_GET['action']) && $_GET['action'] == "load_group") :
    $user_id = $_SESSION['user_id'];
    // find the guest group that this user manages
    $guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
    $group_id_result = $guest_group_id_query->fetch_assoc();
    //define guest group id
    $guest_group_id = $group_id_result['guest_group_id'];
    //loads guest group list
    $group_query = $db->query('SELECT guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_id, guest_list.guest_group_id, guest_list.guest_type, guest_list.guest_extra_invites, guest_groups.guest_group_id, guest_groups.guest_group_name FROM guest_list LEFT JOIN guest_groups ON guest_groups.guest_group_id=guest_list.guest_group_id  WHERE guest_groups.guest_group_id=' . $guest_group_id . ' AND guest_list.guest_type = "Member"');

    $guest = $db->prepare('SELECT guest_id FROM users WHERE user_id =' . $_SESSION['user_id']);
    $guest->execute();
    $guest->bind_result($guest_id);
    $guest->fetch();
    $guest->close();
    //load extra invites this guest has available
    $guest_extra_inv = $db->query('SELECT guest_extra_invites FROM guest_list WHERE guest_id=' . $guest_id);
    $extra_inv_result = $guest_extra_inv->fetch_assoc();
    $group_capacity = $extra_inv_result['guest_extra_invites'];
    $available_inv = $group_capacity - $group_query->num_rows;



    if (($group_query->num_rows) > 0) : ?>

        <h2>My Group</h2>
        <p>This is your guest group that we have made for you. If you have dietary information to pass onto us, please let us know below.</p>
        <table class="std-table guest_group">
            <tr>
                <th>Name</th>
                <?php if($guest_add_remove->status() =="On"):?>
                <th>Remove</th>
                <?php endif;?>
            </tr>
            <?php foreach ($group_query as $member) : ?>
                <tr>
                    <td><a href="guest.php?guest_id=<?= $member['guest_id']; ?>&action=view"><?= $member['guest_fname'] . ' ' . $member['guest_sname']; ?></a></td>
                    <?php if($guest_add_remove =="On"):?>
                    <td>
                        <div class="guest-list-actions">
                            <button class="btn-primary btn-secondary remove_guest" data-guest_id="<?= $member['guest_id']; ?>" type="button"><i class="fa-solid fa-user-minus"></i></button>
                        </div>
                    </td>
                    <?php endif;?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="invite-guest-group invite-card" id="">

        <?php if ($available_inv > 0) : ?>
            <h2>Your Additional Invites</h2>
            <p><strong>You also have <?= $extra_inv_result['guest_extra_invites']; ?> additional invites .</strong></p>
            <p>Please tell us who you will be bringing with you.</p>
            <div class="guest-group-stats my-3">
                <span class="guest-group-stats-title">Invites Available: </span>
                <span class="guest-group-stat"><?= $available_inv; ?></span>
            </div>
            <div id="guest-group-row"></div>
            <div class="btn-wrapper"><button class="btn-primary" id="add-member" type="button">Add Guest <i class="fa-solid fa-user-plus"></i></button></div>
        <?php endif; ?>
        <p class="my-2">If you are not planning on bringing anyone with you, please tick here:</p>
        <label class="checkbox-form-control" for="sole_invite">
            <input type="checkbox" id="sole_invite" name="sole_invite" value="Sole" />
            <strong>I will be attending on my own</strong>
        </label>

    </div>
<?php endif; ?>

<?php
if (isset($_POST['action']) && $_POST['action'] == "remove_guest") {
    //remove guest from post request
    $guest_id = $_POST['guest_id'];
    $remove_guest = $db->prepare('DELETE FROM guest_list  WHERE  guest_id=?');
    $remove_guest->bind_param('i', $guest_id);
    $remove_guest->execute();
    $remove_guest->close();
}
?>