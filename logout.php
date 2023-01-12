<?php
session_start();
//alter session status to logged out
include("connect.php");
$update = "UPDATE user_sessions SET session_status = 'Logged Out' WHERE session_id =".$_SESSION['db_session_id'];
$submit = $db->query($update);
session_destroy();
// Redirect to the login page:
header('Location: login.php');
?>