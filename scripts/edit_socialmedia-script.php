<?php
include("../connect.php");
if(isset($_GET['action'])){//check if the get request is set
    if($_GET['action']=="load"){//if action == load then return the current list of social media items
        $business_id = $_GET['business_id'];
        //find social media information
//find social media info
$socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
$socials = $db->query($socials_query);
$social_result = $socials->fetch_assoc();
foreach($socials as $social){
echo                         '<div class="std-card mb-3">
<p ><strong>Profile: </strong>'.$social['socials_type_name'].'</p>
<p class="wrap-words"><strong>URL: </strong><a href="'.$social['business_socials_url'].'" target="_blank">'.$social['business_socials_url'].'</a></p>
<a href="edit_socialmedia_profile.php?action=edit&business_socials_id='.$social['business_socials_id'].'" class="btn-primary my-2">Edit</a>
</div>';
}
    }
}
//add a new profile
if(isset($_POST['socials_type_id'])){
    if($_POST['action']=="addnew"){
    //declare variables
    $socials_type_id = mysqli_real_escape_string($db, $_POST['socials_type_id']);
    $business_socials_url = mysqli_real_escape_string($db, $_POST['business_socials_url']);
    $business_id = mysqli_real_escape_string($db, $_POST['business_id']);
    //insert New Social Media Profile
    $new_profile = $db->prepare('INSERT INTO business_socials (socials_type_id, business_socials_url, business_id)VALUES(?,?,?)');
    $new_profile ->bind_param('isi',$socials_type_id, $business_socials_url, $business_id);
    $new_profile ->execute();
    $new_profile->close();

    //reload the current info
    //find social media info
$socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
$socials = $db->query($socials_query);
$social_result = $socials->fetch_assoc();
foreach($socials as $social){
echo                         '<div class="std-card mb-3">
<p><strong>Name:</strong>'.$social['socials_type_name'].'</p>
<p><strong>URL:</strong>'.$social['business_socials_url'].'</p>
<a href="edit_socialmedia_item.php?action=edit&business_socials_id='.$social['business_socials_id'].'" class="btn-primary">Edit</a>
</div>';
}
    }
}
//update a  profile
if(isset($_POST['socials_type_id'])){
    if($_POST['action']=="update"){
    //declare variables
    $socials_type_id = mysqli_real_escape_string($db, $_POST['socials_type_id']);
    $business_socials_url = mysqli_real_escape_string($db, $_POST['business_socials_url']);
    $business_id = mysqli_real_escape_string($db, $_POST['business_id']);
    $business_socials_id = mysqli_real_escape_string($db, $_POST['business_socials_id']);
    //Update Social Media Profile
    $update_profile = $db->prepare('UPDATE business_socials SET socials_type_id=?, business_socials_url=?, business_id=?  WHERE business_socials_id =?');
    $update_profile ->bind_param('isii',$socials_type_id, $business_socials_url,$business_id,  $business_socials_id);
    $update_profile ->execute();
    $update_profile->close();
    echo'<div class="form-response"><p>Profile Updated</p></div>';

    }else{
        echo'<div class="form-response error"><p>Error, profile not updated!</p></div>';
    }

}

if(isset($_POST['business_socials_id'])){
    //check that the action is delete
    if($_POST['action']=="delete"){
        $business_socials_id = $_POST['business_socials_id'];
        // connect to db and delete the record
        $delete_profile = "DELETE FROM business_socials WHERE business_socials_id=".$business_socials_id;
        if(mysqli_query($db, $delete_profile)){
            echo'<div class="form-response error"><p>Profile Deleted</p></div>';
        }else{
            echo'<div class="form-response error"><p>Error deleting profile, please try again.</p></div>';
        }
        
    }
}

?>

