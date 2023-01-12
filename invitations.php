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
$cms_name = "";
$user_id = $_SESSION['user_id'];
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
    //find user details for this wedding
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
<title>Mi-Admin | Invitation List</title>
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
        <section class="body">


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <?php if ($cms_type == "Business") {
                                                                                                echo "Settings";
                                                                                            } else {
                                                                                                echo "Invitations";
                                                                                            } ?></div>
            <div class="main-cards">


                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>


                    <?php if ($cms_type == "Wedding") : ?>
                        <h2>Your Invitations</h2>
                        <p>This is your invite list, this list is automatically populated when you assign guests to events.</p>
                        <a class="btn-primary" href="invitations_dl.php">Download Invitations <i class="fa-solid fa-download"></i></a>
                            <form id="invite_search" action="./scripts/guest_list.script.php" method="POST">
                            <div class="search-controls">

                                <div class="form-input-wrapper">
                                    <label for="search">Search by guest name</label>
                                    <div class="search-input">
                                        <input type="text" id="search" name="search" placeholder="Search For A Guest ...">
                                        <button class="btn-primary form-controls-btn loading-btn" type="submit"><i class="fa-solid fa-magnifying-glass" id="search-icon"></i></button>
                                    </div>
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Filter By Event</label>
                                    <select class="form-select" name="event_name" id="search_filter">
                                        <option value="">Show All Events</option>
                                        <?php foreach ($wedding_events as $event) : ?>
                                            <option value="<?= $event['event_name']; ?>"><?= $event['event_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Filter By RSVP Status</label>
                                    <select class="form-select" name="rsvp_status" id="rsvp_status">
                                            <option value="">Show All Status</option>
                                            <option value="Not Replied">Not Replied</option>
                                            <option value="Attending">Attending</option>
                                            <option value="Unsure">Unsure</option>
                                            <option value="Not Attending">Not Attending</option>
                                    </select>
                                </div>

                        
                            </div>
                            </form>
                        <div class="std-card d-none" id="invite_list">

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
<script>
    $(document).ready(function() {
        url = "scripts/invitations.script.php?action=load_guest_list";
        $.ajax({ //load image gallery
            type: "GET",
            url: url,
            encode: true,
            success: function(data, responseText) {
                $("#invite_list").html(data);
                $("#invite_list").fadeIn(500);


            }
        });
    })
</script>
<script>
    //script for searching for guests
    $("#invite_search").on('submit keyup change',function(event) {
        event.preventDefault();
        var formData = new FormData($("#invite_search").get(0));
        formData.append("action", "invite_search");

        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/invitations.script.php",
            data: formData,
            contentType: false,
            processData: false,

            success: function(data, responseText) {
                $("#invite_list").html(data);
                $("#invite_list").fadeIn(500);
            }
        });

    });
    // //script for searching for guests
    // $("#guest_search").on('keyup', function(event) {
    //     event.preventDefault();
    //     var formData = new FormData($("#guest_search").get(0));
    //     formData.append("action", "guest_search");

    //     $.ajax({ //start ajax post
    //         type: "POST",
    //         url: "scripts/invitations.script.php",
    //         data: formData,
    //         contentType: false,
    //         processData: false,

    //         success: function(data, responseText) {
    //             $("#invite_list").html(data);
    //             $("#invite_list").fadeIn(500);
    //         }
    //     });

    // });
    // //script for searching for guests
    // $("#search_filter").on('change', function(event) {
    //     event.preventDefault();
    //     var formData = new FormData($("#eventsearch_filter").get(0));
    //     formData.append("action", "event_search_filter");

    //     $.ajax({ //start ajax post
    //         type: "POST",
    //         url: "scripts/invitations.script.php",
    //         data: formData,
    //         contentType: false,
    //         processData: false,

    //         success: function(data, responseText) {
    //             $("#invite_list").html(data);
    //             $("#invite_list").fadeIn(500);
    //         }
    //     });

    // });
</script>

</html>