<?php
session_start();
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");


//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding LIMIT 1');
    $wedding_result = $db->query($wedding_query);

    if ($wedding_result->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    $wedding = $db->prepare($wedding_query);
    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name);
    $wedding->fetch();
    $wedding->close();
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if ($wedding_user->num_rows == 0) {
        header('Location: setup.php?action=check_users_wedding');
    }

}



?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Dashboard</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main col-2">

        <!-- Header Section -->
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <div class="body">
            <div class="breadcrumbs"><span>Home / </span></div>
            
            <div class="std-card my-3">
                <h1 class="text-center">Welcome To Your Guest Area</h1>
                <p class="text-center">We are delighted to have you join us for our big day!</p>
                <p class="text-center">Here you can manage everything to do with your invitation to our wedding.</p>
            </div>
        <div class="main-cards">
            <div class="std-card grid-auto-sm">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                        <h2>My Invitation</h2>
                        <i class="fa-solid fa-champagne-glasses"></i>
                        </div>
                        <a href="invite">Manage</a>
                    </div>
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                        <h2>My Guest Group</h2>
                        <i class="fa-solid fa-people-group"></i>
                        </div>
                        
                        <a href="guest_group">Manage</a>
                    </div>
            </div>
            <div class="std-card index-img">
                <h2 class="text-center">Our Big Day</h2>
                <div id="clockdiv" class="countdown">
                    <div class="time">
                        <span class="days"></span>
                        <p class="countdown-subtitle">Days</p>
                    </div>
                    <div class="time">
                        <span class="hours"></span>
                        <p class="countdown-subtitle">Hours</p>
                    </div>
                    <div class="time">
                        <span class="minutes"></span>
                        <p class="countdown-subtitle">Minutes</p>
                    </div>
                    <div class="time">
                        <span class="seconds"></span>
                        <p class="countdown-subtitle">Seconds</p>
                    </div>
                   
                   
                </div>
                <img src="assets/img/index-img.jpg" alt="">
            </div>
        </div>

        </div>

        


    </main>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
<script src="assets/js/countdown.js"></script>
<script>
    const deadline = new Date(Date.parse(new Date('14 Oct 2023 13:00:00 GMT')));
    initializeClock('clockdiv', deadline);
</script>
</body>

</html>