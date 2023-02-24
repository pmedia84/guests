<?php
session_start();
$location=urlencode($_SERVER['REQUEST_URI']);
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:
    
    header("Location: login.php?location=".$location);
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
$cms_name = "";
$user_id = $_SESSION['user_id'];
if (empty($_GET)) {
    header('Location: guest_group');
}
$user_type->close();
//guest variable, only required for edit and view actions
if ($_GET['action'] == "edit" || $_GET['action'] == "view" || $_GET['action'] == "delete") {
    $guest_id = $_GET['guest_id'];
    // find the guest group that this user manages
    $guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
    $group_id_result = $guest_group_id_query->fetch_assoc();
    //define guest group id
    $guest_group_id = $group_id_result['guest_group_id'];
    //find guest details that match this group
    $guest = $db->prepare('SELECT * FROM guest_list WHERE guest_id=' . $guest_id . ' AND guest_group_id=' . $guest_group_id);
    $guest->execute();
    $guest->store_result();
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding LIMIT 1');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_time, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
} else {
    //for create get request, load guest group information and find events that this organiser is invited to
    // find the guest group that this user manages
    $guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
    $group_id_result = $guest_group_id_query->fetch_assoc();
    //define guest group id
    $guest_group_id = $group_id_result['guest_group_id'];
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding LIMIT 1');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_time, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    $guest_id = "";
}







?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Manage Guest</title>
<!-- /Page Title -->
</head>


<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php");?>
        <!-- /nav bar -->
        
        <div class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="guest_group" class="breadcrumb">Guest Group</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Guest
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Guest
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    / View Guest
                <?php endif; ?>
                <?php if ($_GET['action'] == "create") : ?>
                    / Add Guest To Group
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1>Edit Guest</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1>View Guest</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Remove Guest</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "create") : ?>
                    <h1>Add A Guest To My Group</h1>
                <?php endif; ?>




                

                <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                ?>
                <?php if($guest_add_remove =="On"):?>
                    <?php if ($_GET['confirm'] == "yes") : //if yes then delete the guest
                    ?>
                        <?php if (($guest->num_rows) > 0) :
                            //load guest information
                            $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                            $guest->fetch();
                            //load any invites the guest may have and delete them also;
                            $remove_invites = "DELETE FROM invitations WHERE guest_id=$guest_id";


                            if (mysqli_query($db, $remove_invites)) {
                                echo mysqli_error($db);
                            }



                            // connect to db and delete the guest
                            $remove_guest = "DELETE FROM guest_list WHERE guest_id=$guest_id";
                            if (mysqli_query($db, $remove_guest)) {

                                echo '<div class="std-card"><div class="form-response error"><p>' . $guest_fname . ' ' . $guest_sname . ' Has been removed from your guest list</p></div></div>';
                            } else {
                                echo '<div class="form-response error"><p>Error removing guest, please try again.</p></div>';
                                //echo mysqli_error($db);
                            }
                        ?>
                        <?php else : ?>
                            <div class="std-card">
                                <h2>Error</h2>
                                <p>There has been an error, please return to the last page and try again.</p>
                            </div>
                        <?php endif; ?>
                    <?php else : //if not then display the message to confirm the user wants to delete the news article
                    ?>
                        <?php if (($guest->num_rows) > 0) :
                            //load guest information
                            $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                            $guest->fetch();
                        ?>
                            <div class="std-card">
                                <h2 class="text-alert">Remove: <?= $guest_fname . ' ' . $guest_sname; ?> From your guest group?</h2>
                                <p>Are you sure you want to remove this guest from your guest group?</p>
                                <p><strong>This Cannot Be Reversed</strong></p>
                                <?php if ($guest_type == "Group Organiser") : ?>
                                    <p><strong><?= $guest_fname; ?> is a group organiser and cannot be removed!</strong></p>
                                    <p><strong>Remove their extra invites and try again.</strong></p>
                                    <div class="card-actions">
                                        <a class="my-2" href="guest.php?action=edit&guest_id=<?= $guest_id ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Guest </a><br>
                                    </div>
                                <?php else : ?>
                                    <div class="button-section">
                                        <a class="btn-primary btn-delete my-2" href="guest.php?action=delete&confirm=yes&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-trash"></i>Remove Guest</a>
                                        <a class="btn-primary btn-secondary my-2" href="guest.php?action=view&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php else:?>
                    <div class="std-card">
                        <h2>Remove Guest</h2>
                        <p>This feature is not possible. You can't remove guests from your group. Please contact us if you need to make any changes.</p>
                    </div>
                <?php endif;?>
                <?php endif; ?>

                <?php if ($_GET['action'] == "create") : ?>
                    <?php
                    //find how many guests have been added to the group already
                    $guest = $db->prepare('SELECT guest_id FROM users WHERE user_id =' . $_SESSION['user_id']);
                    $guest->execute();
                    $guest->bind_result($guest_id);
                    $guest->fetch();
                    $guest->close();
                    //loads group capacity
                    $group_cap_query = $db->query('SELECT guest_id, guest_extra_invites FROM guest_list WHERE guest_id=' . $guest_id);
                    $group_cap_result = $group_cap_query->fetch_assoc();
                    $group_capacity = $group_cap_result['guest_extra_invites'];
                    //calculate the remaining amount of invites available
                    $group_size_query = $db->query('SELECT guest_group_id FROM guest_list WHERE guest_type="Member" AND guest_group_id=' . $guest_group_id);
                    $group_size = $group_size_query->num_rows;

                    $remaining_inv = $group_capacity - $group_size;

                    ?>
                    <div class="guest-group-stats-container">
                        <div class="guest-group-stats">
                            <span class="guest-group-stats-title">Invites Available: </span>
                            <span class="guest-group-stat"><?= $group_capacity; ?></span>
                        </div>
                        <div class="guest-group-stats">
                            <span class="guest-group-stats-title">Invites Allocated: </span>
                            <span class="guest-group-stat"><?= $group_size; ?></span>
                        </div>
                        <div class="guest-group-stats">
                            <span class="guest-group-stats-title">Invites Remaining: </span>
                            <span class="guest-group-stat"><?= $remaining_inv; ?></span>
                        </div>
                    </div>
                    <?php if ($remaining_inv > 0) : ?>
                        <div class="std-card">
                            <form class="form-card" id="add_guest" action="scripts/guest.script.php" method="POST" enctype="multipart/form-data">
                                <div class="form-input-wrapper">
                                    <label for="guest_fname"><strong>First Name</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_fname" id="guest_fname" placeholder="Guest First Name" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="guest_sname"><strong>Surname</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_sname" id="guest_sname" placeholder="Guest Surname" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="guest_dietery"><strong>Any Dietary Requirements?</strong></label>
                                    <textarea name="guest_dietery" id="guest_dietery" cols="30" placeholder="Tell us about any dietary requirements you have..."></textarea>

                                </div>

                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit"> Add Guest <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                                </div>


                            </form>
                            <div id="response" class="d-none">

                            </div>
                        </div>
                    <?php else : ?>
                        <div class="std-card">
                            <h2>Guest group capacity reached</h2>
                            <p>You will need to remove some guests from your group before you can add any more guests.</p>
                            <p>Contact us via our messaging area if you need help.</p>
                        </div>
                    <?php endif; ?>


                <?php endif; ?>



                <?php if ($_GET['action'] == "edit") : ?>
                    <?php if (($guest->num_rows) > 0) :
                        //load guest information
                        $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                        $guest->fetch();

                        //load guest group information if they are an organiser
                        if ($guest_type == "Group Organiser") {
                            $group_details = $db->prepare('SELECT guest_group_status FROM guest_groups  WHERE guest_group_id=' . $guest_group_id);

                            $group_details->execute();
                            $group_details->bind_result($guest_group_status);
                            $group_details->fetch();
                            //$group_details->close();
                        } else {
                            $guest_group_status = "";
                        }
                    ?>
                        <h2><?= $guest_fname . ' ' . $guest_sname; ?></h2>
                        <div class="std-card">
                            <form class="form-card" id="edit_guest" action="scripts/guest.script.php" method="POST" enctype="multipart/form-data">
                                <div class="form-input-wrapper">
                                    <label for="guest_fname"><strong>First Name</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_fname" id="guest_fname" placeholder="Guest First Name" required="" maxlength="45" value="<?= $guest_fname; ?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="guest_sname"><strong>Surname</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_sname" id="guest_sname" placeholder="Guest Surname" maxlength="45" value="<?= $guest_sname; ?>">
                                </div>

                                <div class="form-input-wrapper">
                                    <label for="guest_dietery"><strong>Any Dietary Requirements?</strong></label>
                                    <textarea name="guest_dietery" id="guest_dietery" cols="30" placeholder="Tell us about any dietary requirements you have..."></textarea>

                                </div>


                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
                                </div>

                                <div id="response" class="d-none">

                                </div>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="std-card">
                            <h2>Error</h2>
                            <p>There has been an error, please return to the last page and try again.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($_GET['action'] == "view") : ?>
                    <?php if (($guest->num_rows) > 0) :
                        $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                        $guest->fetch();
                        //search for any events the guest is associated with
                        $guest_invites = $db->query('SELECT wedding_events.event_id, wedding_events.event_name, invitations.guest_id, invitations.event_id, guest_list.guest_id, guest_list.guest_fname FROM wedding_events
                            LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id
                            LEFT JOIN guest_list ON guest_list.guest_id=invitations.guest_id
                            WHERE guest_list.guest_id=' . $guest_id);
                        if ($guest_invites->num_rows > 1) {
                            $guest_invites->fetch_array();
                        }


                    ?>
                        <h2><?= $guest_fname . ' ' . $guest_sname; ?></h2>
                        <div class="std-card">
                            <h3>Dietary Requirements </h3>
                            <p><?= $guest_dietery; ?></p>
                            <div class="card-actions">
                                <a class="my-2" href="guest.php?action=edit&guest_id=<?= $guest_id ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Guest </a><br>
                                <?php if($guest_add_remove=="On"):?>
                                <a class="my-2" href="guest.php?action=delete&confirm=no&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-trash"></i> Remove Guest </a>
                                <?php endif;?>    
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

    <script>
        //script for editing a guest
        $("#edit_guest").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            guest_id = '<?php echo $guest_id; ?>';
            guest_group_id = '<?php echo $guest_group_id; ?>';
            var formData = new FormData($("#edit_guest").get(0));
            formData.append("action", "edit");
            formData.append("guest_id", guest_id);
            formData.append("guest_group_id", guest_group_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/guest.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    window.location.replace('guest_group');
                }
            });
        });
    </script>
    <script>
        //script for adding a guest
        $("#add_guest").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#add_guest").get(0));
            var guest_group_id = '<?php echo $guest_group_id; ?>';
            formData.append("action", "create");
            formData.append("guest_group_id", guest_group_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/guest.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    window.location.replace('guest_group.php');
                }
            });

        });
    </script>

</body>

</html>