<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");


//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding');

    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
    $wedding->close();
    //load the guest ID for this logged in user
    $guest = $db->prepare('SELECT guest_id FROM users WHERE user_id =' . $_SESSION['user_id']);
    $guest->execute();
    $guest->bind_result($guest_id);
    $guest->fetch();
    $guest->close();
}
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//load details of any invitations that this guest has
$invite_query = $db->query('SELECT wedding_events.event_name, wedding_events.event_id, wedding_events.event_location, wedding_events.event_date, wedding_events.event_time, wedding_events.event_address, wedding_events.event_notes, invitations.event_id, invitations.guest_id, invitations.invite_rsvp_status, guest_list.guest_id, guest_list.guest_extra_invites, guest_list.guest_type FROM wedding_events
LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id
LEFT JOIN guest_list ON guest_list.guest_id=invitations.guest_id WHERE guest_list.guest_id=' . $guest_id . '
  ');
  $invite_query_res = $invite_query->fetch_assoc();
$guest_invites = $invite_query_res['guest_extra_invites'];
$guest_type = $invite_query_res['guest_type'];
// find the guest group that this user manages
$guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id ='.$user_id);
$group_id_result = $guest_group_id_query->fetch_assoc();
//define guest group id
$guest_group_id = $group_id_result['guest_group_id'];
//loads guest group list
$group_query = $db->query('SELECT guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_id, guest_list.guest_group_id, guest_list.guest_type, guest_groups.guest_group_id, guest_groups.guest_group_name FROM guest_list LEFT JOIN guest_groups ON guest_groups.guest_group_id=guest_list.guest_group_id  WHERE guest_groups.guest_group_id=' . $guest_group_id . ' AND guest_list.guest_type = "Member"');
//$group_result = $group_query->fetch_assoc();
$event_id = "";



?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media Wedding Admin - Guest Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Guest Area | My Invitation</title>
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
                <a href="invite" class="breadcrumb">My Invitation</a>
                <?php if (isset($_GET['action']) && $_GET['action'] == "respond") : ?>
                    / Respond To Invitation
                <?php endif; ?>
                <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
                    / Update Invitation Response
                <?php endif; ?>



            </div>
            <div class="main-cards">
                <?php if (isset($_GET['action']) && $_GET['action'] == "respond") : ?>
                    <h1>Respond To Invitation</h1>
                <?php endif; ?>

                <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
                    <h1>Manage My Invitation</h1>
                <?php endif; ?>

                <?php if (($invite_query->num_rows) > 0) : ?>
                    <?php if (empty($_GET)) :?>
                        <?php foreach ($invite_query as $invite) :
                            $event_date = strtotime($invite['event_date']);
                            $event_time = strtotime($invite['event_time']);
                        ?>

                            <div class="std-card">
                                <h2>Our <?= $invite['event_name']; ?></h2>
                            </div>
                            <div class="std-card">
                                <h2><?= $invite['event_location']; ?></h2>
                                <p><?= $invite['event_notes']; ?></p>
                                <p><strong>Date:</strong> <?php echo date('D d M Y', $event_date); ?></p>
                                <p><strong>Time:</strong> <?php echo date('H:ia', $event_time); ?></p>
                                <div class="invite-card-address">
                                    <h3>Address:</h3>
                                    <div class="invite-card-address-body">
                                        <address class="mb-3"><?= $invite['event_address']; ?></address>
                                        <?php echo '<iframe frameborder="0" width="100%" height="250px" src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=' . str_replace(",", "", str_replace(" ", "+", $invite['event_address'])) . '&z=14&output=embed"></iframe>'; ?>
                                    </div>
                                </div>
                                <h2>RSVP Status</h2>
                                <?php if ($invite['invite_rsvp_status'] == NULL) : ?>
                                    <p class="text-alert"><strong>Please respond to your invitation: <i class="fa-solid fa-flag"></i></strong></p>
                                    <div class="card-actions error">
                                        <a class="my-2 btn-primary alert" href="invite?action=respond&event_id=<?= $invite['event_id']; ?>">Respond To Invitation <i class="fa-solid fa-reply"></i></a>
                                    </div>
                                <?php else : ?>
                                    <p>You have told us that you are <strong><?= $invite['invite_rsvp_status']; ?></strong> our <?= $invite['event_name']; ?></p>
                                    <div class="card-actions">
                                        <a class="my-2 btn-primary" href="invite?action=respond&event_id=<?= $invite['event_id']; ?>">Change Response <i class="fa-solid fa-reply"></i></a>
                                    </div>
                                <?php endif;?>

                                <?php if ($invite['guest_extra_invites'] > 0) : ?>
                                    <h2>Additional Invites</h2>
                                    <p>We are delighted to tell you that you have the following additional invites that you can bring with you to share our day:</p>
                                    <div class="guest-group-stats my-3">
                                        <span class="guest-group-stats-title">Invites Available: </span>
                                        <span class="guest-group-stat"><?= $invite['guest_extra_invites']; ?></span>
                                    </div>

                                    <p>You can manage your additional invites under the <a href="guest_group">My Guest Group</a> Tab</p>

                                    <?php if (($group_query->num_rows) > 0) : ?>

                                        <h2>My Group</h2>

                                        <table class="std-table guest_group">
                                            <tr>
                                                <th>Name</th>
                                                <th>Manage</th>
                                            </tr>
                                            <?php foreach ($group_query as $member) : ?>
                                                <tr>
                                                    <td><a href="guest.php?guest_id=<?= $member['guest_id']; ?>&action=view"><?= $member['guest_fname'] . ' ' . $member['guest_sname']; ?></a></td>
                                                    <td>
                                                        <div class="guest-list-actions">
                                                            <a href="guest.php?guest_id=<?= $member['guest_id']; ?>&action=view"><i class="fa-solid fa-eye"></i></a>
                                                            <a href="guest.php?guest_id='.$guest['guest_id'].'&action=edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php endif; ?>
                                <?php endif; ?>
                                

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>
                <?php endif; ?>


                <?php if (isset($_GET['action']) && $_GET['action'] == "respond") :
                    //load event details
                    $event = $db->query('SELECT wedding_events.event_name, wedding_events.event_id, wedding_events.event_location, wedding_events.event_date, wedding_events.event_time, wedding_events.event_address,  invitations.event_id, invitations.guest_id, invitations.invite_rsvp_status, guest_list.guest_id, guest_list.guest_extra_invites FROM wedding_events
                    LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id
                    LEFT JOIN guest_list ON guest_list.guest_id=invitations.guest_id WHERE invitations.guest_id=' . $guest_id);
                    $event_result = $event->fetch_array();
                    $guest_extra_invites = $event_result['guest_extra_invites'];
                    $event_date = strtotime($event_result['event_date']);
                    $event_time = strtotime($event_result['event_time']);
                    $count = 0;
                    //load all events this guest is linked to
                ?>
                    <div class="std-card">
                        <form class="form-card" id="invite_response" action="scripts/invite.script.php" method="POST" enctype="multipart/form-data">
                            
                            <?php foreach ($event as $event_details) :
                                $event_date = strtotime($event_details['event_date']);
                                $event_time = strtotime($event_details['event_time']);

                            ?>
                                <div class="form-input-wrapper invite-card">
                                <p><strong><?=$_SESSION['user_name'];?></strong> You are invited to our:</p>

                                    <h3><?= $event_details['event_name']; ?></h3>
                                    <input type="hidden" name="event_rsvp[<?php echo $count; ?>][event_id]" value="<?= $event_details['event_id']; ?>">
                                    
                                    <p><strong>Date:</strong> <?php echo date('D d M Y', $event_date); ?></p>
                                    <p><strong>Time:</strong> <?php echo date('H:ia', $event_time); ?></p>
                                    <p><strong>Location:</strong> <?=$event_result['event_location']?></p>
                                    <address> <?=$event_result['event_address']?></address>
                                    <label for="event_rsvp"><strong>Please Select Your Response Below:</strong></label>
                                    <!-- input -->
                                    <select name="event_rsvp[<?php echo $count; ?>][rsvp]" required class="rsvp">
                                        <?php if ($event_details['invite_rsvp_status']==""):?>  
                                        <option value="">Select</option>
                                        <option value="Attending">Attending</option>
                                        <option value="Not Attending">Not Attending</option>
                                        <?php endif;?>
                                        <?php if ($event_details['invite_rsvp_status']=="Attending"):?>  
                                        <option value="<?= $event_details['invite_rsvp_status']; ?>"><?= $event_details['invite_rsvp_status']; ?></option>
                                        <option value="Not Attending">Not Attending</option>
                                        <?php endif;?>
                                        <?php if ($event_details['invite_rsvp_status']=="Not Attending"):?>  
                                        <option value="<?= $event_details['invite_rsvp_status']; ?>"><?= $event_details['invite_rsvp_status']; ?></option>
                                        <option value="Attending">Attending</option>
                                        <?php endif;?>
                                    </select>


                                </div>
                            <?php
                                $count++;
                            endforeach;
                            $event->close();
                            ?>
                            
                            <div class="form-input-wrapper invite-card">
                            <h2>Your Dietary Requirements?</h2>
                                <label for="guest_dietery"><strong>Dietary Requirements?</strong></label>
                                <input type="text" name="guest_dietery" placeholder="Tell us about any dietary requirements you have...">

                            </div>
                            <?php if ($guest_extra_invites > 0): 
                                //load guest group table

                                ?>
                                <div id="guest_group" class="d-none"></div>
                            <?php endif; ?>
                            <div class="form-input-wrapper invite-card">
                                <h2>Your Message</h2>
                                <label for="rsvp_note"><strong>Message</strong></label>
                                <p class="form-hint-small">Any message you would like to pass on in response to your invitation:</p>
                                <textarea name="rsvp_note" id="rsvp_note" cols="30" placeholder="Any message to pass onto us..."></textarea>
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
    <script>//load guest group
    $(document).ready(function() {
        url = "scripts/invite.script.php?action=load_group";
        $.ajax({ //load guest group
            type: "GET",
            url: url,
            encode: true,
            success: function(data, responseText) {
                $("#guest_group").html(data);
                $("#guest_group").fadeIn(400);


            }
        });
    })
</script>
    <script>
        //script for  submitting rsvp
        $("#invite_response").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            var guest_extra_invites ='<?php echo $guest_invites;?>';
            var guest_id = '<?php echo $guest_id; ?>';
            var guest_group_id = '<?php echo $guest_group_id; ?>';
            var guest_type = '<?php echo $guest_type; ?>';
            var formData = new FormData($("#invite_response").get(0));
            formData.append("action", "response");
            formData.append("guest_id", guest_id);
            formData.append("guest_group_id", guest_group_id);
            formData.append("guest_extra_invites", guest_extra_invites);
            formData.append("guest_type", guest_type);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/invite.script.php",
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'text',
                beforeSend: function() { //animate button
                    $("#loading-icon").show(400);
                },
                success: function(data, status, xhr) {
                    if (data === "success") {
                        window.location.replace('invite');
                    }
                    $("#response").html(data);
                    $("#response").slideDown(400);
                },
                complete: function() {
                    $("#loading-icon").hide(400);
                },
            });
        });
    </script>
</body>
</html>