<?php
    if ($_POST['action'] == "edit") { //check if the action type of edit has been set in the post request
        include("../connect.php");
        //determine variables
        $guest_id = $_POST['guest_id'];
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_email= mysqli_real_escape_string($db, $_POST['guest_email']);
        $guest_address= mysqli_real_escape_string($db, $_POST['guest_address']);
        $guest_postcode= mysqli_real_escape_string($db, $_POST['guest_postcode']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        $guest_group_id = $_POST['guest_group_id'];
        
        //Update guest
        $guest = $db->prepare('UPDATE guest_list SET guest_fname=?, guest_sname=?, guest_email=?, guest_address=?, guest_postcode=?,guest_extra_invites=?  WHERE guest_id =?');
        $guest->bind_param('sssssii',$guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_extra_invites, $guest_id);
        $guest->execute();
        $guest->close();

        //if the guest now has extra invites associated with their record then add a group for them, but only if they are not a member of a group already
        
        if($_POST['guest_extra_invites']>0){
            if($guest_group_id==NULL){
            //create a guest group if the guest being changed has one or more extra invites
            //set up a group name using first and last name of primary guest
            $group_name = $guest_fname.' '.$guest_sname;
            //Set the group status as Unassigned. This is changed once guests log in and add their members
            $guest_group_status = "Unassigned";
            //insert into guest group tables
            $group = $db->prepare('INSERT INTO guest_groups (guest_group_name, guest_group_organiser, guest_group_status) VALUES (?,?,?)');
            $group->bind_param('sis',$group_name, $guest_id, $guest_group_status);
            $group->execute();
            $group->close();
            $new_group_id = $db->insert_id;
            //update guest list with the guest group id and change guest type from sole to group organiser
            $guest_type = "Group Organiser";
            $guest = $db->prepare('UPDATE guest_list SET guest_group_id=?, guest_type=?  WHERE guest_id =?');
            $guest->bind_param('isi',$new_group_id, $guest_type, $guest_id);
            $guest->execute();
            $guest->close();
            }



        }

        //if the number of invites have been reduced to zero, then remove the guest group and change guest type to Sole
        if($_POST['guest_extra_invites']==0){
            
            //remove the guest group and update the guest list
            
            //delete the guest group
            $delete_guest_group = "DELETE FROM guest_groups WHERE guest_group_id=$guest_group_id";
            if(mysqli_query($db, $delete_guest_group)){
                echo"Success";
                
            }
            //set guest_group_id to null
            $guest_group_id=NULL;
            //update guest list with the guest group id and change guest type from group organiser to sole
            $guest_type = "Sole";
            $guest = $db->prepare('UPDATE guest_list SET guest_group_id=?, guest_type=?  WHERE guest_id=?');
            $guest->bind_param('isi', $guest_group_id, $guest_type, $guest_id);
            $guest->execute();
            $guest->close();


        }
    }
    if ($_POST['action'] == "create") { //check if the action type of create has been set in the post request
        include("../connect.php");
        //determine variables
        $guest_fname= mysqli_real_escape_string($db, $_POST['guest_fname']);
        $guest_sname= mysqli_real_escape_string($db, $_POST['guest_sname']);
        $guest_email= mysqli_real_escape_string($db, $_POST['guest_email']);
        $guest_address= mysqli_real_escape_string($db, $_POST['guest_address']);
        $guest_postcode= mysqli_real_escape_string($db, $_POST['guest_postcode']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        $guest_extra_invites= mysqli_real_escape_string($db, $_POST['guest_extra_invites']);
        //create and RSVP CODE
        $code = rand(1000,20000);
        $code_name = mb_substr($_POST['guest_sname'],0,3);
        $code_name = strtoupper($code_name);
        $guest_rsvp_code = $code_name . $code; // Generate random RSVP Code
        if($_POST['guest_extra_invites']>=1){
            //if the guest has 1 or more extra invites then ad them as a group organiser
            $guest_type= "Group Organiser";
            

        }else{
            $guest_type="Sole";
        }
        
        //insert guest
        $guest = $db->prepare('INSERT INTO guest_list (guest_fname, guest_sname, guest_email, guest_address, guest_postcode, guest_rsvp_code, guest_extra_invites, guest_type) VALUES (?,?,?,?,?,?,?,?)');
        $guest->bind_param('ssssssis',$guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_extra_invites, $guest_type);
        $guest->execute();
        $guest->close();

        //if the guest has extra invites associated with their record then add a group for them
        if($_POST['guest_extra_invites']>=1){
            $new_guest_id = $db->insert_id;//last id entered
            //create a guest group if the guest being added has one or more extra invites
            //set up a group name using first and last name of primary guest
            $group_name = $guest_fname.' '.$guest_sname;
            //insert into guest group tables
            $group = $db->prepare('INSERT INTO guest_groups (guest_group_name, guest_group_organiser) VALUES (?,?)');
            $group->bind_param('si',$group_name, $new_guest_id);
            $group->execute();
            $group->close();
            $new_group_id = $db->insert_id;
            //update guest list with the guest group id
            $guest = $db->prepare('UPDATE guest_list SET guest_group_id=?  WHERE guest_id =?');
            $guest->bind_param('ii',$new_group_id, $new_guest_id);
            $guest->execute();
            $guest->close();


        }
       
    }
