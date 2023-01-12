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
    //find user details for this business
    $wedding_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $wedding_users->execute();
    $wedding_users->bind_result($user_id, $user_name, $wedding_id, $user_type);
    $wedding_users->fetch();
    $wedding_users->close();

    //find wedding guest list
    $guest_list_query = ('SELECT * FROM guest_list ORDER BY guest_sname');
    $guest_list = $db->query($guest_list_query);
    $guest_list_result = $guest_list->fetch_assoc();
}
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//Guest list method, loads the list from a remote script so that it can be searched with ajax


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Guest List</title>
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
                                                                                                echo "Guest List";
                                                                                            } ?></div>
            <div class="main-cards">


                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>


                    <?php if ($cms_type == "Wedding") : ?>
                        <h2>Your Guest List</h2>
                        <p>Keep this information up to date as you plan for big day. Your invites will be sent out from this information.</p>
                        <a href="guest.php?action=create" class="btn-primary">Add Guest <i class="fa-solid fa-user-plus"></i></a>
                        <div class="search-controls">
                            <form id="guest_search" action="./scripts/guest_list.script.php" method="POST">
                                <div class="form-input-wrapper">
                                    <label for="search">Search by guest name</label>
                                    <div class="search-input">

                                        <input type="text" id="search" name="search" placeholder="Search For A Guest ...">
                                        <button class="btn-primary form-controls-btn loading-btn" type="submit"><i class="fa-solid fa-magnifying-glass" id="search-icon"></i></button>
                                    </div>
                                </div>
                            </form>

                        </div>

                        <div class="std-card d-none" id="guest_list">

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
        url = "scripts/guest_list.script.php?action=load_guest_list";
        $.ajax({ //load image gallery
            type: "GET",
            url: url,
            encode: true,
            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);


            }
        });
    })
</script>
<script>
    //script for searching for guests
    $("#guest_search").submit(function(event) {
        event.preventDefault();
        var formData = new FormData($("#guest_search").get(0));
        formData.append("action", "guest_search");

        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/guest_list.script.php",
            data: formData,
            contentType: false,
            processData: false,

            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);
            }
        });

    });
    //script for searching for guests
    $("#guest_search").on('keyup', function(event) {
        event.preventDefault();
        var formData = new FormData($("#guest_search").get(0));
        formData.append("action", "guest_search");

        $.ajax({ //start ajax post
            type: "POST",
            url: "scripts/guest_list.script.php",
            data: formData,
            contentType: false,
            processData: false,

            success: function(data, responseText) {
                $("#guest_list").html(data);
                $("#guest_list").fadeIn(500);
            }
        });

    });
</script>

</html>