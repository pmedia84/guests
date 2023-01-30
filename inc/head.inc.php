
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/3318fdaaaf.js" crossorigin="anonymous"></script>
    <!-- Google Recaptcha -->
    <!-- <script src="https://www.google.com/recaptcha/api.js?render=6LevFFEiAAAAAPcel_AlRmOSMRgDSXCN5vT0lbmC" async="false"></script> -->
    <!-- Theme Color for safari and mobile browsers -->
    <meta name="theme-color" content="black" />
    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300&display=swap" rel="stylesheet">

    <?php
    if(isset($_SESSION)){
        $user_id = $_SESSION['user_id'];
        //load guest Type
        $user_type = $db->query('SELECT users.user_id, users.guest_id, guest_list.guest_id, guest_list.guest_type, invitations.invite_rsvp_status FROM users LEFT JOIN guest_list ON guest_list.guest_id=users.guest_id LEFT JOIN invitations ON guest_list.guest_id=invitations.guest_id WHERE users.user_id=' . $user_id);
        $user_type_result = $user_type->fetch_assoc();
        $guest_type = $user_type_result['guest_type'];
        $user_invite_rsvp_status = $user_type_result['invite_rsvp_status'];
        
        
    }

?>


    <!-- Everything above this is for the head element. And is displayed on every web page -->