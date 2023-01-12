<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./connect.php");
include("inc/head.inc.php");
include("inc/settings.php");

////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name

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


                <?php if ($user_type == "Admin" ||$user_type=="Developer") : ?>


                    <?php if ($cms_type == "Wedding") : ?>
                        <h2>Your Wedding Day Events</h2>
                        <p>Keep this information up to date as you plan for big day. Information from this page will be displayed on your website.</p>
                       
                        <a class="btn-primary" href="event.php?action=create">Create An Event <i class="fa-solid fa-plus"></i></a>
                        <?php foreach ($wedding_events as $event) :
                            $event_time = strtotime($event['event_time']);
                            $time = date('H:ia', $event_time);
                            $event_date = strtotime($event['event_date']);
                            $date = date('D d M Y', $event_date);
                        ?>

                            <div class="event-card">
                                <h3 class="event-card-title mb-3"> <?= $event['event_name']; ?> <span class="event-card-title-time"><?= $time ?></span></h3>
                                <div class="event-card-details my-3">
                                    <div class="event-card-item">
                                        <h4>Location</h4>
                                        <p><?= $event['event_location'];?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h4>Date</h4>
                                        <p><?= $date;?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h4>Time</h4>
                                        <p><?= $time;?></p>
                                    </div>
                                </div>
                                <h4>Address</h4>
                                <address class="my-2"><?= $event['event_address']; ?></address>
                            <div class="card-actions">
                                <a class="my-2" href="event.php?action=view&event_id=<?= $event['event_id']; ?>"><i class="fa-solid fa-eye"></i> View Event</a>
                                <a class="my-2" href="event.php?action=edit&event_id=<?= $event['event_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Event </a>
                            </div>
                            </div>
                        <?php endforeach; ?>

                        
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