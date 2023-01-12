<?php
include("../connect.php");
if (isset($_GET['action'])) {
    
    if($_GET['action']=="load_guest_list"){
        //load guest list from the db and send back to the front page
        include("../connect.php");
        //find wedding guest list
        $guest_list_query = ('SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname,guest_list.guest_extra_invites, invitations.guest_id, invitations.event_id, invitations.invite_rsvp_status, wedding_events.event_id, wedding_events.event_name FROM guest_list  
                                LEFT JOIN invitations ON guest_list.guest_id = invitations.guest_id
                                LEFT JOIN wedding_events ON invitations.event_id = wedding_events.event_id
                                WHERE invitations.guest_id=guest_list.guest_id
                                ORDER BY wedding_events.event_name');
        $guest_list = $db->query($guest_list_query);
        $guest_list_result = $guest_list->fetch_assoc();
        $num_invites = $guest_list->num_rows;
        echo 
        '<p>Total Number Of Invites: <strong>'.$num_invites.'</strong></p>';
        echo
        '<table class="std-table">
            <tr>
                <th>Name</th>
                <th>Event</th>
                <th>RSVP Status</th>
            </tr>'; 
    foreach($guest_list as $guest){
        if($guest['guest_extra_invites']>=1){
            $plus= "+".$guest['guest_extra_invites'];
        }else{
            $plus="";
        }
        echo' <tr>
        <td><a href="guest.php?guest_id='.$guest['guest_id'].'&action=view">'.$guest['guest_fname'].' '.$guest['guest_sname'].' '.$plus.'</a></td>
        <td><a href="event.php?action=view&event_id='.$guest['event_id'].'">'.$guest['event_name'].'</a></td>
        <td>'.$guest['invite_rsvp_status'].'</td>
    </tr>                   
    ';}

    echo '</table>';
    }


        
}
if (isset($_POST['action'])) {
    //load guest list based on the search bar

    if($_POST['action']=="invite_search"){
        
        $search = mysqli_real_escape_string($db, $_POST['search']);
        $event_name = mysqli_real_escape_string($db, $_POST['event_name']);
        $rsvp_status = mysqli_real_escape_string($db, $_POST['rsvp_status']);
        //set the rsvp and event search
        $event_filter="";
        $rsvp_status_filter="";
        if(!$event_name ==""){
            $event_filter="AND wedding_events.event_name = '$event_name'";
        }
        if(!$rsvp_status==""){
            $rsvp_status_filter="AND invitations.invite_rsvp_status = '$rsvp_status'";
        }
               //load guest list from the db and send back to the front page
               //find wedding invite list
               $guest_list_query = (' SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname,guest_list.guest_extra_invites, invitations.guest_id, invitations.event_id, invitations.invite_rsvp_status, wedding_events.event_id, wedding_events.event_name FROM guest_list  
               LEFT JOIN invitations ON invitations.guest_id = guest_list.guest_id 
               LEFT JOIN wedding_events ON wedding_events.event_id = invitations.event_id  
               WHERE guest_list.guest_id = invitations.guest_id 
               AND guest_list.guest_fname LIKE ("'.$search.'%")
               '.$event_filter.'
               '.$rsvp_status_filter.'
               ORDER BY wedding_events.event_name');

               $guest_list = $db->query($guest_list_query);
               $guest_list_result = $guest_list->fetch_assoc();
               $num_guests = $guest_list->num_rows;
               $db ->close();
               if($num_guests == NULL){
                echo '<p>Sorry, no invites found with those details</p>';
               }
               if($num_guests >0){
                echo '<p>'.$num_guests.' Invites found matching '.$search.'</p>';
               }

               echo
               '<table class="std-table">
                   <tr>
                       <th>Name</th>
                       <th>Attending</th>
                       <th>RSVP Status</th>
                   </tr>'; 
           foreach($guest_list as $guest){
            if($guest['guest_extra_invites']>=1){
                $plus= "+".$guest['guest_extra_invites'];
            }else{
                $plus="";
            }
               echo' <tr>
               <td><a href="guest.php?guest_id='.$guest['guest_id'].'&action=view">'.$guest['guest_fname'].' '.$guest['guest_sname'].' '.$plus.'</a></td>
               <td><a href="event.php?action=view&event_id='.$guest['event_id'].'">'.$guest['event_name'].'</a></td>
               <td>'.$guest['invite_rsvp_status'].'</td>
           </tr>                   
           ';}
       
           echo '</table>';
    }


}