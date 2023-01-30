<?php
session_start();
$location=$_SERVER['REQUEST_URI'];
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:
    
    header("Location: login.php?location=".$location);
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
$user_id = $_SESSION['user_id'];



//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name,$wedding_date, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();

    $guest = $db->prepare('SELECT guest_id FROM users WHERE user_id =' . $_SESSION['user_id']);
    $guest->execute();
    $guest->bind_result($guest_id);
    $guest->fetch();
    $guest->close();
    // find the guest group that this user manages
    $guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
    $group_id_result = $guest_group_id_query->fetch_assoc();
    //define guest group id
    $guest_group_id = $group_id_result['guest_group_id'];
    //load details of the group that this user manages
    $group_details = $db->prepare('SELECT guest_group_name FROM guest_groups WHERE guest_group_organiser ='.$guest_id);
    $group_details->execute();
    $group_details->bind_result($group_name);
    $group_details->fetch();
    $group_details->close();
}
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//loads guest group list
$group_query = $db->query('SELECT guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_id, guest_list.guest_group_id, guest_list.guest_type, guest_groups.guest_group_id, guest_groups.guest_group_name FROM guest_list LEFT JOIN guest_groups ON guest_groups.guest_group_id=guest_list.guest_group_id  WHERE guest_groups.guest_group_id=' . $guest_group_id . ' AND guest_list.guest_type = "Member"');
$group_result = $group_query->fetch_assoc();

//loads group capacity
$group_cap_query = $db->query('SELECT guest_id, guest_extra_invites FROM guest_list WHERE guest_id=' . $guest_id);
$group_cap_result = $group_cap_query->fetch_assoc();
$group_capacity = $group_cap_result['guest_extra_invites'];
//calculate the remaining amount of invites available
$group_size_query = $db->query('SELECT guest_group_id FROM guest_list WHERE guest_type="Member" AND guest_group_id=' . $guest_group_id);
$group_size = $group_size_query->num_rows;
$remaining_inv = $group_capacity - $group_size ;


//check that the guest has responded to their invite first
$invite_status = $db->prepare('SELECT invite_rsvp_status FROM invitations WHERE guest_id =' . $guest_id);
$invite_status->execute();
$invite_status->bind_result($invite_rsvp_status);
$invite_status->fetch();
$invite_status->close();
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media Wedding Admin - Guest Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Guest Area | My Guest Group</title>
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
            <div class="breadcrumbs mb-2">
                <a href="index" class="breadcrumb">Home</a> /
                My Guest Group
                <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
                    / Edit My Guest Group
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if (isset($_GET['action']) && $_GET['action'] == "respond") : ?>
                    <h1>Respond To Invitation</h1>
                <?php endif; ?>

                <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
                    <h1>Update My Guest Group</h1>
                <?php else : ?>
                    <h1><?= $group_name; ?>'s Guest Group</h1>
                <?php endif; ?>
            <?php if($invite_rsvp_status=="Attending")://check if the guest has responded?>
                <div class="search-controls">
                    <a href="guest.php?action=create" class="btn-primary">Add Guest <i class="fa-solid fa-user-plus"></i></a>
                </div>
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
                <p>When you add a guest, they will automatically be added to your invitations</p>
                <?php if (($group_query->num_rows) > 0) : ?>
                    <div class="std-card">
                        <h2>My Group</h2>

                        <table class="std-table guest_group">
                            <tr>
                                <th>Name</th>
                                <th>Manage</th>
                            </tr>
                            <?php foreach ($group_query as $member) : ?>
                                <tr>
                                    <td><a href="guest?action=view&guest_id=<?=$member['guest_id'];?>"><?= $member['guest_fname'] . ' ' . $member['guest_sname']; ?></a></td>
                                    <td>
                                        <div class="guest-list-actions">
                                            <a href="guest.php?guest_id=<?= $member['guest_id']; ?>&action=view"><i class="fa-solid fa-eye"></i></a>
                                            <a href="guest.php?guest_id=<?= $member['guest_id']; ?>&action=edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                        </table>
                    </div>

                <?php else : ?>
                    <div class="std-card">
                        <h2>My Guest Group</h2>
                        <p>You have not set up your guest group yet.</p>
                        <p>Click the button above to add your first guest.</p>
                    </div>
                <?php endif; ?>
                <?php else : ?>
                    <div class="std-card">
                        <h2>My Guest Group</h2>
                        <p class="text-alert">You need to respond to your invitation before you can set up your guest group.</p>
                        <a href="invite?action=respond" class="btn-primary my-3 alert">Respond Now <i class="fa-solid fa-reply"></i></a>
                    </div>
        <?php endif;?>

                <?php if (isset($_GET['action']) && $_GET['action'] == "respond") :
                    $event_id = $_GET['event_id'];
                    //load event details
                    $event = $db->query('SELECT wedding_events.event_name, wedding_events.event_id, wedding_events.event_location, wedding_events.event_date, wedding_events.event_time,  invitations.event_id, invitations.guest_id, guest_list.guest_id, guest_list.guest_dietery FROM wedding_events
                    LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id
                    LEFT JOIN guest_list ON guest_list.guest_id=invitations.guest_id WHERE guest_list.guest_id=' . $guest_id . ' AND wedding_events.event_id=' . $event_id);
                    $event_result = $event->fetch_array();
                    $event->close();
                    $event_date = strtotime($event_result['event_date']);
                    $event_time = strtotime($event_result['event_time']);

                ?>
                    <div class="std-card">
                        <form class="form-card" id="invite_response" action="scripts/invite.script.php" method="POST" enctype="multipart/form-data">
                            <div class="form-input-wrapper">
                                <label for="invite_rsvp_status"><strong><?= $event_result['event_name']; ?></strong></label>
                                <p><strong>Date:</strong> <?php echo date('D d M Y', $event_date); ?></p>
                                <p><strong>Time:</strong> <?php echo date('H:ia', $event_time); ?></p>
                                <p><strong>Select Your Response Below:</strong></p>
                                <!-- input -->
                                <select name="invite_rsvp_status" id="invite_rsvp_status">
                                    <option value="Attending" selected>Attending</option>
                                    <option value="Not Attending">Not Attending</option>
                                </select>
                            </div>

                            <div class="form-input-wrapper">
                                <label for="guest_dietery"><strong>Any Dietary Requirements?</strong></label>
                                <textarea name="guest_dietery" id="guest_dietery" cols="30" placeholder="Tell us about any dietary requirements you have..."></textarea>

                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit"> Save Response <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">

                            </div>
                        </form>
                    </div>

                <?php endif; ?>
                <?php if (isset($_GET['action']) && $_GET['action'] == "edit") :
                    $event_id = $_GET['event_id'];
                    //load event details
                    $event = $db->query('SELECT wedding_events.event_name, wedding_events.event_id, wedding_events.event_location, wedding_events.event_date, wedding_events.event_time,  invitations.event_id, invitations.guest_id, invitations.invite_rsvp_status, guest_list.guest_id ,guest_list.guest_dietery, guest_list.guest_rsvp_status FROM wedding_events
                    LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id
                    LEFT JOIN guest_list ON guest_list.guest_id=invitations.guest_id WHERE guest_list.guest_id=' . $guest_id . ' AND wedding_events.event_id=' . $event_id);
                    $event_result = $event->fetch_array();
                    $event->close();
                    $event_date = strtotime($event_result['event_date']);
                    $event_time = strtotime($event_result['event_time']);

                ?>
                    <div class="std-card">
                        <form class="form-card" id="edit_response" action="scripts/invite.script.php" method="POST" enctype="multipart/form-data">
                            <div class="form-input-wrapper">
                                <label for="invite_rsvp_status"><strong><?= $event_result['event_name']; ?></strong></label>
                                <p><strong>Date:</strong> <?php echo date('D d M Y', $event_date); ?></p>
                                <p><strong>Time:</strong> <?php echo date('H:ia', $event_time); ?></p>
                                <p><strong>Update Your Response Below:</strong></p>
                                <!-- input -->
                                <select name="invite_rsvp_status" id="invite_rsvp_status">
                                    <?php if ($event_result['invite_rsvp_status'] == "Attending") : ?>
                                        <option value="<?= $event_result['invite_rsvp_status']; ?>" selected>Attending</option>
                                        <option value="Not Attending">Not Attending</option>
                                    <?php endif; ?>
                                    <?php if ($event_result['invite_rsvp_status'] == "Not Attending") : ?>
                                        <option value="<?= $event_result['invite_rsvp_status']; ?>" selected>Not Attending</option>
                                        <option value="Attending">Attending</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-input-wrapper">
                                <label for="guest_dietery"><strong>Any Dietary Requirements?</strong></label>
                                <textarea name="guest_dietery" id="guest_dietery" cols="30" placeholder="Tell us about any dietary requirements you have..."><?= $event_result['guest_dietery']; ?></textarea>
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit"> Save Response <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">

                            </div>
                        </form>
                    </div>

                <?php endif; ?>
            </div>



        </div>





    </main>
    <!-- /Main Body Of Page -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
</body>

</html>