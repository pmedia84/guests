<?php
session_start();
//alter session status to logged out
include("connect.php");
$update = "UPDATE user_sessions SET session_status = 'Logged Out' WHERE session_id =".$_SESSION['db_session_id'];
$submit = $db->query($update);
//remove sessions older than 5 days
$delete = 'DELETE FROM user_sessions WHERE session_date < (NOW() - INTERVAL 5 DAY)';
$run_delete =$db->query($delete);
session_destroy();
// Redirect to the login page:
header('Location: login.php');
?>