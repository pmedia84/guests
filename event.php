<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");

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
    echo $business_id;
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
//event variable
//only run if the get request is view or edit
if ($_GET['action'] == "edit" || $_GET['action'] == "view" || $_GET['action'] == "assign" || $_GET['action'] == "delete") {
    $event_id = $_GET['event_id'];
    //find event details

    $event = $db->prepare('SELECT * FROM wedding_events WHERE event_id =' . $event_id);

    $event->execute();
    $event->store_result();
}else{
    $event_id="";
}



?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Manage Your Event</title>
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
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="events.php" class="breadcrumb">Events</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Event
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Event
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    / View Event
                <?php endif; ?>
                <?php if ($_GET['action'] == "assign") : ?>
                    / Assign Guests
                <?php endif; ?>
            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1>Edit Event</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "view") : ?>
                    <h1>View Event</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Delete Event</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "assign") : ?>
                    <h1>Assign Guests To Your Event</h1>
                    <p>Add and remove guests from your event.</p>
                <?php endif; ?>

                <?php if ($user_type == "Admin" || $user_type == "Developer") : //detect if user is an admin or not 
                ?>
                    <?php if ($_GET['action'] == "assign") :?>
                        <?php if (($event->num_rows) > 0) :
                            $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                            $event->fetch();
                            $event_time = strtotime($event_time);
                            $time = date('H:ia', $event_time);
                            $event_date = strtotime($event_date);
                            $date = date('D d M Y', $event_date);
                        ?>

                            <div class="event-card">
                                <div class="card-actions">
                                    <a class="my-2" href="event.php?action=view&event_id=<?= $event_id; ?>"><i class="fa-solid fa-left-long"></i> Back To Event </a>
                                </div>
                                <h2 class="event-card-title mb-3"> <?= $event_name; ?></h2>
                                <div class="event-card-details my-3">
                                    <div class="event-card-item">
                                        <h3>Location</h3>
                                        <p><?= $event_location; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h3>Date</h3>
                                        <p><?= $date; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h3>Time</h3>
                                        <p><?= $time; ?></p>
                                    </div>
                                    <div class="event-card-item">
                                        <h3>Venue Capacity</h3>
                                        <p><?= $event_capacity; ?></p>
                                    </div>
                                </div>
                                <h3>Address</h3>
                                <address class="my-2"><?= $event_address; ?></address>

                                <div class="event-card-guestlist d-none" id="active_guest_list">
                                </div>
                                <div class="card-actions">
                                    <a class="my-2" href="event.php?action=edit&event_id=<?= $event_id; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Event </a>
                                    <a class="my-2" href="event.php?action=delete&confirm=no&event_id=<?= $event_id; ?>"><i class="fa-solid fa-trash"></i> Delete Event</a>
                                </div>
                            <?php else : ?>
                                <div class="std-card">
                                    <h2>Error</h2>
                                    <p>There has been an error, please return to the last page and try again.</p>
                                </div>
                            <?php endif; ?>








                        <?php endif; ?>








                        <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                        ?>
                            <?php if ($_GET['confirm'] == "yes") : //if yes then delete the article
                            ?>
                            <?php if (($event->num_rows) > 0) :
                                $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                                $event->fetch();

                            
                                    // connect to db and delete the event
                                    $delete_event = "DELETE FROM wedding_events WHERE event_id=" . $event_id;

                                    if (mysqli_query($db, $delete_event)) {
                                       
                                        //delete all invites relating to this event
                                        $delete_invites = "DELETE FROM invitations WHERE event_id=" . $event_id;
                                        if(mysqli_query($db,$delete_invites)){
                                            echo '<div class="std-card"><div class="form-response "><p>' . $event_name . ' Has Been Deleted</p></div></div>';
                                        }
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
                            <?php if (($event->num_rows) > 0) :
                                $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                                $event->fetch();

                            ?>
                                    <div class="std-card">
                                        <h2 class="text-alert">Delete Your <?= $event_name; ?></h2>
                                        
                                        
                                        <p>Are you sure you want to delete this image?</p>
                                        <p><strong>This Cannot Be Reversed</strong></p>
                                        <div class="button-section">
                                            <a class="btn-primary btn-delete my-2" href="event.php?action=delete&confirm=yes&event_id=<?= $event_id; ?>"><i class="fa-solid fa-trash"></i>Delete Event</a>
                                            <a class="btn-primary btn-secondary my-2" href="event.php?action=view&event_id=<?= $event_id; ?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>



                        <?php endif; ?>

                        <?php if ($_GET['action'] == "create") : ?>

                                <div class="std-card">

                                    <form class="form-card" id="add_event" action="scripts/event.script.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-input-wrapper">

                                            <h2>Add An Event</h2>
                                            <label for="event_name"><strong>Event Name</strong></label>
                                            <!-- input -->
                                            <input class="text-input input" type="text" name="event_name" id="event_name" placeholder="Wedding Ceremony..." required="" maxlength="45" >
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_capacity"><strong>Venue Capacity</strong></label>
                                            <p class="form-hint-small my-2">Make sure this number is up to date as it affects how many guests you can assign to this event.</p>
                                            <input class="text-input input" type="number" id="event_capacity" name="event_capacity" placeholder="Venue Capacity"  min="0">
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_date"><strong>Event Date</strong></label>
                                            <input class="text-input input" type="date" id="event_date" name="event_date" placeholder="Event Location..." >
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_time"><strong>Event Start Time</strong></label>
                                            <input class="text-input input" type="time" id="event_time" name="event_time" >
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_location"><strong>Event Location</strong></label>
                                            <p class="form-hint-small">I.e building name.</p>
                                            <input class="text-input input" type="text" id="event_location" name="event_location" placeholder="Event Location..." >
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_address"><strong>Event Address</strong></label>
                                            <textarea name="event_address" id="event_address"></textarea>
                                        </div>
                                        <div class="form-input-wrapper my-2">

                                            <label for="event_notes"><strong>Event Notes</strong></label>
                                            <p class="form-hint-small">Add as much information here as you wish, this will be displayed on your website. Information such as the location and type of venue can be useful for your guests.</p>
                                            <textarea name="event_notes" id="event_notes" placeholder="Add as much information here as you wish about your event."></textarea>
                                        </div>
                                        <div class="button-section my-3">
                                            <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-plus"></i> Add Event </button>
                                        </div>

                                    </form>
                                </div>

                         
                        <?php endif; ?>

                        <?php if ($_GET['action'] == "edit") : ?>
                            <?php if (($event->num_rows) > 0) :
                                $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                                $event->fetch();


                            ?>
                                <div class="std-card">
                                <div class="card-actions">
                                    <a class="my-2" href="event.php?action=view&event_id=<?= $event_id; ?>"><i class="fa-solid fa-left-long"></i> Back To Event </a>
                                </div>
                                    <form class="form-card" id="edit_event" action="scripts/event.script.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-input-wrapper">

                                            <h2><?= $event_name; ?></h2>
                                            <label for="event_name"><strong>Event Name</strong></label>
                                            <!-- input -->
                                            <input class="text-input input" type="text" name="event_name" id="event_name" placeholder="Wedding Ceremony..." required="" maxlength="45" value="<?= $event_name; ?>">
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_capacity"><strong>Venue Capacity</strong></label>


                                            <p class="form-hint-small my-2">Make sure this number is up to date as it affects how many guests you can assign to this event.</p>
                                            <input class="text-input input" type="number" id="event_capacity" name="event_capacity" placeholder="Venue Capacity" value="<?= $event_capacity; ?>" min="0">

                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_date"><strong>Event Date</strong></label>
                                            <input class="text-input input" type="date" id="event_date" name="event_date" placeholder="Event Location..." value="<?= $event_date; ?>">
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_time"><strong>Event Start Time</strong></label>
                                            <input class="text-input input" type="time" id="event_time" name="event_time" value="<?= $event_time; ?>">
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_location"><strong>Event Location</strong></label>
                                            <p class="form-hint-small">I.e building name.</p>
                                            <input class="text-input input" type="text" id="event_location" name="event_location" placeholder="Event Location..." value="<?= $event_location; ?>">
                                        </div>
                                        <div class="form-input-wrapper my-2">
                                            <label for="event_address"><strong>Event Address</strong></label>
                                            <textarea name="event_address" id="event_address"><?= $event_address; ?></textarea>
                                        </div>
                                        <div class="form-input-wrapper my-2">

                                            <label for="event_notes"><strong>Event Notes</strong></label>
                                            <p class="form-hint-small">Add as much information here as you wish, this will be displayed on your website. Information such as the location and type of venue can be useful for your guests.</p>
                                            <textarea name="event_notes" id="event_notes" placeholder="Add as much information here as you wish about your event."><?= $event_notes; ?></textarea>
                                        </div>
                                        <div class="button-section my-3">
                                            <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
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
                            <?php if (($event->num_rows) > 0) :
                                $event->bind_result($event_id, $event_name, $event_location, $event_address, $event_date, $event_time, $event_notes, $event_capacity);
                                $event->fetch();
                                $event_time = strtotime($event_time);
                                $time = date('H:ia', $event_time);
                                $event_date = strtotime($event_date);
                                $date = date('D d M Y', $event_date);
                            ?>
                                <div class="event-card">
                                    <h2 class="event-card-title mb-3"> <?= $event_name; ?></h2>
                                    <div class="event-card-details my-3">
                                        <div class="event-card-item">
                                            <h4>Location</h4>
                                            <p><?= $event_location; ?></p>
                                        </div>
                                        <div class="event-card-item">
                                            <h4>Date</h4>
                                            <p><?= $date; ?></p>
                                        </div>
                                        <div class="event-card-item">
                                            <h4>Time</h4>
                                            <p><?= $time; ?></p>
                                        </div>
                                        <div class="event-card-item">
                                            <h4>Venue Capacity</h4>
                                            <p><?= $event_capacity; ?></p>
                                        </div>
                                    </div>
                                    <h4>Address</h4>
                                    <address class="my-2"><?= $event_address; ?></address>
                                    <?php
                                    echo '<iframe frameborder="0" width="100%" height="250px" src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=' . str_replace(",", "", str_replace(" ", "+", $event_address)) . '&z=14&output=embed"></iframe>'; ?>

                                    <h4>Event Notes</h4>
                                    <p><?= $event_notes; ?></p>

                                    </p>

                                    <div class="event-card-guestlist">
                                        <?php
                                        //load all invites details
                                        $guest_allocated_query = ('SELECT invite_id FROM invitations  WHERE event_id=' . $event_id);
                                        $invites = $db->query($guest_allocated_query);
                                        $guests_allocated = $invites->num_rows;
                                        //find additional invites
                                        $extra_invites_query = ('SELECT guest_list.guest_id, SUM(guest_list.guest_extra_invites) AS extra_inv, invitations.guest_id FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_list.guest_id=invitations.guest_id AND invitations.event_id='.$event_id);
                                        $extra_invites = $db->query($extra_invites_query);
                                        $extra_inv = $extra_invites->fetch_array();
                                        $total_inv = $extra_inv['extra_inv'];
                                        //
                                        $invites_sent = ('SELECT invite_id FROM invitations  WHERE event_id=' . $event_id . ' AND invite_status="Sent"');
                                        $invites = $db->query($invites_sent);
                                        $invites_sent = $invites->num_rows;
                                        ?>
                                        <h4>Invite Details</h4>
                                        <p>Note that the figures below also include guests that can bring others with them.</p>
                                        <div class="event-card-invites">
                                            <div class="event-card-invites-textbox">
                                                <p>Invites Available </p><span><?= $event_capacity - $total_inv - $guests_allocated; ?></span>
                                            </div>
                                            <div class="event-card-invites-textbox">
                                                <?php
                                                ?>
                                                <p>Invites Sent </p><span><?= $invites_sent; ?></span>
                                            </div>

                                            <div class="event-card-invites-textbox">
                                                <p>Total Guests Allocated </p><span><?= $total_inv + $guests_allocated; ?></span>
                                            </div>
                                        </div>

                                        <h4>Guest List</h4>
                                        <table class="event-card-guestlist-table ">
                                            <?php
                                            $guest_list_query = ('SELECT guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_extra_invites, invitations.event_id, invitations.guest_id, invitations.invite_status, invitations.invite_rsvp_status FROM guest_list NATURAL LEFT JOIN invitations WHERE guest_list.guest_id = invitations.guest_id AND event_id=' . $event_id);
                                            $guest_list = $db->query($guest_list_query);
                                            ?>

                                            <tr>
                                                <th>Name</th>
                                                <th>Invited</th>
                                                <th>RSVP Status</th>
                                            </tr>
                                            <?php foreach ($guest_list as $guest) :
                                                if ($guest['guest_extra_invites'] >= 1) {
                                                    $plus = "+" . $guest['guest_extra_invites'];
                                                } else {
                                                    $plus = "";
                                                }
                                            ?>
                                                <tr>
                                                    <td><a href="guest.php?action=view&guest_id=<?= $guest['guest_id']; ?>"><?= $guest['guest_fname'] . " " . $guest['guest_sname'] . ' ' . $plus; ?></a></td>
                                                    <td><?= $guest['invite_status']; ?></td>
                                                    <td><?= $guest['invite_status']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>


                                        </table>
                                        <a class="btn-primary" href="event.php?action=assign&event_id=<?= $event_id; ?>">Assign Guests <i class="fa-solid fa-user-plus"></i></a>
                                    </div>
                                    <div class="card-actions">
                                        <a class="my-2" href="event.php?action=edit&event_id=<?= $event_id; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Event </a>
                                        <a class="my-2" href="event.php?action=delete&confirm=no&event_id=<?= $event_id; ?>"><i class="fa-solid fa-trash"></i> Delete Event</a>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="std-card">
                                    <h2>Error</h2>
                                    <p>There has been an error, please return to the last page and try again.</p>
                                </div>
                            <?php endif; ?>
                            </div>

            </div>



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
    <script>
        $(document).ready(function() {
            var event_id = <?php echo $event_id; ?>;
            url = "scripts/event.script.php?action=load&event_id=" + event_id;
            $.ajax({ //load active guest list
                type: "GET",
                url: url,
                encode: true,
                success: function(data, responseText) {
                    $("#active_guest_list").html(data);
                    $("#active_guest_list").fadeIn(500);
                }
            });
        })
    </script>
    <Script>
        //update event
        $("#edit_event").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#edit_event").get(0));
            var event_id = <?php echo $event_id; ?>;
            formData.append("action", "edit_event");
            formData.append("event_id", event_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/event.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                window.location.replace('event.php?action=view&event_id=' + event_id);

                }
            });
        })
    </script>
        <script>
        //add event
        $("#add_event").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#add_event").get(0));
            formData.append("action", "add_event");
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/event.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                //window.location.replace('events.php');

                }
            });
        })
    </script>


</body>

</html>