<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("inc/head.inc.php");
include("inc/settings.php");
include("./connect.php");
//find if this module is on or off

////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name ="";
$user_id = $_SESSION['user_id'];
if ($cms_type == "Business") {
    //look for the business set up and load information
    //find business details.
    $business_query = ('SELECT business_id, business_name FROM business');
    $business = $db->query($business_query);
    $business_details = mysqli_fetch_assoc($business);
    $business = $db->prepare('SELECT * FROM business');

    $business->execute();
    $business->store_result();
    $business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
    $business->fetch();
    $business->close();
    //set cms name
    $cms_name = $business_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //set cms name
    $cms_name = $wedding_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//find users and display on screen.
$user_query = "SELECT user_id, user_name,user_type, user_email FROM users";
$users = mysqli_query($db, $user_query);
//find username and email address to display on screen.
$user = $db->prepare('SELECT user_id,  user_type FROM users WHERE user_id = ?');
$user->bind_param('s', $_SESSION['user_id']);
$user->execute();
$user->store_result();
$user->bind_result($user_id, $user_type);
$user->fetch();
$user->close();
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Users</title>
<!-- /Page Title -->
</head>

<body>

   
    <!-- Main Body Of Page -->
    <main class="main col-2">


 <!-- Header Section -->
 <?php include("./inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
<section class="body">
    
    
            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / Users</div>
            <div class="main-cards">
    
                <h1>Manage Users</h1>
                <?php
                if($user_type =="Admin"):
                foreach ($users as $user):?>
                <div class="std-card">
                    <h2><?=$user['user_name'];?></h2>
                    <p><strong>Email Address:</strong> <?=$user['user_email'];?></p>
                    <p><strong>Access Level: </strong><?=$user['user_type'];?></p>
                    <a href="edit_user.php?user_id=<?=$user['user_id'];?>">Edit User</a>
                </div>
                <?php endforeach;?>
                <?php else:?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                    <?php endif;?>
            </div>
</section>



    </main>

    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

</body>

</html>