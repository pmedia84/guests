<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./inc/head.inc.php");
include("./inc/settings.php");
include("./connect.php");
////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name = "";
$user_id = $_SESSION['user_id'];
if ($cms_type == "Business") {
    //look for the business set up and load information
    //find business details.
    $business = $db->prepare('SELECT * FROM business');

    $business->execute();
    $business->store_result();
    $business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
    $business->fetch();
    $business->close();
    //set cms name
    $cms_name = $business_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id=' . $user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name, $business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
    //find business address details.
    $business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $business->execute();
    $business->store_result();
    $business->bind_result($address_id, $address_house, $address_road, $address_town, $address_county, $address_pc);
    $business->fetch();
    $business->close();


    //find social media info
    $socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
    $socials = $db->query($socials_query);
    $social_result = $socials->fetch_assoc();
    $db->close();
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
    $wedding_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $wedding_users->execute();
    $wedding_users->bind_result($user_id, $user_name, $wedding_id, $user_type);
    $wedding_users->fetch();
    $wedding_users->close();

    //find wedding events details
    $wedding_events_query = ('SELECT * FROM wedding_events ORDER BY event_time');
    $wedding_events = $db->query($wedding_events_query);
    $wedding_events_result = $wedding_events->fetch_assoc();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Settings</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main col-2">


        <!-- Header Section -->
        <?php include("inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <?php if ($cms_type == "Business") {
                                                                                                echo "Settings";
                                                                                            } else {
                                                                                                echo "Wedding Details";
                                                                                            } ?></div>
            <div class="main-cards">


                <?php if ($user_type == "Admin") : ?>
                    <?php if ($cms_type == "Business") : ?>
                        <h2>Settings</h2>
                        <div class="std-card">
                            <h3>Business Details</h3>
                            <p><strong>Business Name:</strong> <?= $business_name; ?></p>
                            <p><strong>Email Address:</strong> <?= $business_email; ?></p>
                            <p><strong>Primary Contact No.:</strong> <?= $business_phone; ?></p>
                            <p><strong>Business Contact Name.:</strong> <?= $business_contact_name; ?></p>

                            <a href="edit_businessdetails.php" class="my-2">Edit Business Details</a>
                        </div>
                        <div class="std-card">
                            <h3>Social Media Details</h3>
                            <p>These are your social media details, make sure these links are correct, clients will follow these links from your website to your social media pages.</p>
                            <?php

                            foreach ($socials as $social) : ?>
                                <p><strong>Name:</strong> <?= $social['socials_type_name']; ?></p>
                                <p><strong>URL:</strong> <?= $social['business_socials_url']; ?></p>

                            <?php endforeach; ?>
                            <a class="my-2" href="edit_socialmedia.php">Edit Social Media Details</a>

                        </div>
                        <div class="std-card">
                            <h3>Primary Business Address</h3>
                            <p>Make sure this is up to date, this address is displayed on your contact page.</p>
                            <p><?= $address_house ?></p>
                            <p><?= $address_road ?></p>
                            <p><?= $address_town ?></p>
                            <p><?= $address_county ?></p>
                            <p><?= $address_pc ?></p>
                            <a class="my-2" href="edit_address.php">Edit Address</a>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
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