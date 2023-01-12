<?php
session_start();
include("./connect.php");


$guestlist = fopen("scripts/guestlist.csv", "w") or die("Unable to open file!");

$query =("SELECT  guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname,guest_list.guest_extra_invites, guest_list.guest_rsvp_code,guest_list.guest_address, guest_list.guest_postcode, invitations.guest_id, invitations.event_id, wedding_events.event_id, wedding_events.event_name FROM guest_list  
   LEFT JOIN invitations ON guest_list.guest_id = invitations.guest_id
   LEFT JOIN wedding_events ON wedding_events.event_id=invitations.event_id
  WHERE invitations.guest_id=guest_list.guest_id
  ORDER BY wedding_events.event_id 
  ");

$fetch = $db->query($query);
$query_fetch = $fetch->fetch_array();

$note=array("NOTE: Save this file as an Excel workbook and remove this line. Guest ID and event ID are not required so these can be deleted. If you make changes to your guest list then make sure you download this again.");
fputcsv($guestlist, $note);
$headers = array('Guest ID', 'First Name', 'Surname','Additional Invites', 'RSVP Code', 'Address', 'Postcode', 'Event ID', 'Event Name');
fputcsv($guestlist, $headers);
foreach ($fetch as $line) {
  fputcsv($guestlist, $line);
}
fclose($guestlist);



if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}

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
                        <h2>Download Your Invitations</h2>
                        <p>Only do this once you are happy with your guest list and you have assigned all guests to the correct event.</p>
                        <p>Your guest list is now ready to download. Click the button below.</p>
                        <a class="btn-primary" href="scripts/guestlist.csv">Download  <i class="fa-solid fa-download"></i></a>
                        

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
        url = "scripts/invitations_dl.script.php?action=load_guest_list";
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

</script>

</html>