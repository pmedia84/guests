<?php
    //set user id
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $response = "";
    if ($_POST['action'] == "edit") { //check if the action type of edit has been set in the post request
        include("../connect.php");
        //determine variables
        $guest_id = $_POST['guest_id'];
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_dietery = mysqli_real_escape_string($db, $_POST['guest_dietery']);
        $guest_group_id = $_POST['guest_group_id'];
        
        //Update guest
        $guest = $db->prepare('UPDATE guest_list SET guest_fname=?, guest_sname=?, guest_dietery=?  WHERE guest_id =?');
        $guest->bind_param('sssi',$guest_fname, $guest_sname, $guest_dietery, $guest_id);
        $guest->execute();
        $guest->close();



    }
    if ($_POST['action'] == "create") { //check if the action type of create has been set in the post request and add a guest to the active users group
        include("../connect.php");
        //determine variables
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_dietery = mysqli_real_escape_string($db, $_POST['guest_dietery']);
        $guest_group_id = $_POST['guest_group_id'];//comes from the guest group result on guest.php page
        $guest_type="Member"; //only set as a member
        $guest_rsvp_status = "Attending";
        //Update guest group status
        $guest_group_status = "Assigned"; //Set as assigned to prevent admin removing invites, but more can be added
        $guest = $db->prepare('UPDATE guest_groups SET guest_group_status=?  WHERE guest_group_id =?');
        $guest->bind_param('si',$guest_group_status, $guest_group_id);
        $guest->execute();
        $guest->close();
                
        //insert guest
        //set rsvp status on guest list table as well
        $guest = $db->prepare('INSERT INTO guest_list (guest_fname, guest_sname, guest_rsvp_status, guest_type, guest_group_id, guest_dietery) VALUES (?,?,?,?,?,?)');
        $guest->bind_param('ssssis',$guest_fname, $guest_sname, $guest_rsvp_status, $guest_type, $guest_group_id, $guest_dietery );
        $guest->execute();
        $guest->close();
        $new_guest_id = $db->insert_id;
        
        //find invites associated to this user
        $user_invites=$db->query('SELECT users.user_id, users.guest_id, invitations.guest_id, invitations.event_id, invitations.invite_rsvp_status FROM users LEFT JOIN invitations ON invitations.guest_id=users.guest_id WHERE users.user_id = '.$user_id.' AND invitations.invite_rsvp_status="Attending"');
        $user_invites_result = mysqli_fetch_array($user_invites, MYSQLI_ASSOC);
        $invite_rsvp_status = "Attending";
        $invite = $db->prepare('INSERT INTO invitations (guest_id, event_id, invite_rsvp_status, guest_group_id) VALUES (?,?,?,?)');
        foreach($user_invites as $event_id){
            $invite->bind_param('iisi', $new_guest_id, $event_id['event_id'], $invite_rsvp_status, $guest_group_id);
            $invite->execute();
        }
        $invite->close();
        
    }
echo $response;
?>