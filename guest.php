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
//guest variable, only required for edit and view actions
if ($_GET['action'] == "edit" || $_GET['action'] == "view" ||$_GET['action']=="delete") {
    $guest_id = $_GET['guest_id'];
    //find guest details

    $guest = $db->prepare('SELECT * FROM guest_list WHERE guest_id =' . $guest_id);

    $guest->execute();
    $guest->store_result();
} else {
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
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <div class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="guest_list.php" class="breadcrumb">Guest List</a>
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
                    / Add Guest
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
                    <h1>Add Guest</h1>
                <?php endif; ?>


                <?php if ($user_type == "Admin" || $user_type == "Developer") : //detect if user is an admin or developer 
                ?>
                    <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                    ?>
                        <?php if ($_GET['confirm'] == "yes") : //if yes then delete the guest
                        ?>
                        <?php if (($guest->num_rows) > 0) :
                            //load guest information
                            $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                            $guest->fetch();
                            //load any invites the guest may have and delete them also;
                            $remove_invites = "DELETE FROM invitations WHERE guest_id=$guest_id";
                            

                            if(mysqli_query($db, $remove_invites)){
                                echo mysqli_error($db);
                            }
                            

                            
                                // connect to db and delete the guest
                                $remove_guest = "DELETE FROM guest_list WHERE guest_id=$guest_id";
                                if (mysqli_query($db, $remove_guest)) {
                                    
                                    echo '<div class="std-card"><div class="form-response error"><p>' . $guest_fname.' '.$guest_sname . ' Has been removed from your guest list</p></div></div>';
                                    
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
                                    <h2 class="text-alert">Remove: <?= $guest_fname.' '.$guest_sname;?> From your guest list?</h2>
                                    <p>Are you sure you want to remove this guest from your guest list?</p>
                                    <p><strong>This Cannot Be Reversed</strong></p>
                                    <p><strong>Note:</strong> This will also remove any assignments they have to your events.</p>
                                    <?php if($guest_type =="Group Organiser"):?>
                                    <p><strong><?=$guest_fname;?> is a group organiser and cannot be removed!</strong></p>
                                    <p><strong>Remove their extra invites and try again.</strong></p>
                                    <div class="card-actions">
                                        <a class="my-2" href="guest.php?action=edit&guest_id=<?= $guest_id ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Guest </a><br>
                                    </div>
                                    <?php else:?>
                                        <div class="button-section">
                                        <a class="btn-primary btn-delete my-2" href="guest.php?action=delete&confirm=yes&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-trash"></i>Remove Guest</a>
                                        <a class="btn-primary btn-secondary my-2" href="guest.php?action=view&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-ban"></i>Cancel</a>
                                    </div>
                                    <?php endif;?>

                                </div>
                            <?php endif; ?>
                        <?php endif; ?>



                    <?php endif; ?>
                    
                    <?php if ($_GET['action'] == "create") : ?>
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

                                <div class="form-input-wrapper my-2">
                                    <label for="guest_email"><strong>Email Address</strong></label>
                                    <input class="text-input input" type="text" id="guest_email" name="guest_email" placeholder="Email Address">
                                </div>

                                <div class="form-input-wrapper my-2">
                                    <label for="guest_address"><strong>Address</strong></label>
                                    <textarea name="guest_address" id="guest_address"></textarea>
                                </div>

                                <div class="form-input-wrapper my-2">
                                    <label for="guest_postcode"><strong>Postcode</strong></label>
                                    <input class="text-input input" type="text" id="guest_postcode" name="guest_postcode" placeholder="Postcode">
                                </div>

                                <div class="form-input-wrapper my-2">
                                    <label for="guest_extra_invites"><strong>Extra Invites</strong></label>
                                    <p class="form-hint-small my-2">Assign up to 10 additional invites for this guest, they will then add their own details of the additional guests they can bring.</p>
                                    <input class="text-input input" type="number" id="guest_extra_invites" name="guest_extra_invites" placeholder="Extra Invites" min="0" max="10">


                                </div>
                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Add Guest </button>
                                </div>

                                <div id="response" class="d-none">
                                    <p>Article Saved <img src="./assets/img/icons/check.svg" alt=""></p>
                                </div>
                            </form>
                        </div>

                    <?php endif; ?>



                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($guest->num_rows) > 0) :
                        //load guest information
                            $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email, $guest_address, $guest_postcode, $guest_rsvp_code, $guest_rsvp_status, $guest_extra_invites, $guest_type, $guest_group_id, $guest_events, $guest_dietery);
                            $guest->fetch();

                        //load guest group information if they are an organiser
                        if($guest_type=="Group Organiser"){
                            $group_details = $db->prepare('SELECT guest_group_status FROM guest_groups  WHERE guest_group_id=' . $guest_group_id);

                            $group_details->execute();
                            $group_details->bind_result($guest_group_status);
                            $group_details->fetch();
                            //$group_details->close();
                        }else{
                            $guest_group_status="";
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
                                        <input class="text-input input" type="text" name="guest_sname" id="guest_sname" placeholder="Guest Surname" required="" maxlength="45" value="<?= $guest_sname; ?>">
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_email"><strong>Email Address</strong></label>
                                        <input class="text-input input" type="text" id="guest_email" name="guest_email" placeholder="Email Address" value="<?= $guest_email; ?>">
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_address"><strong>Address</strong></label>
                                        <textarea name="guest_address" id="guest_address"><?= $guest_address; ?></textarea>
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_postcode"><strong>Postcode</strong></label>
                                        <input class="text-input input" type="text" id="guest_postcode" name="guest_postcode" placeholder="Postcode" value="<?= $guest_postcode; ?>">
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_extra_invites"><strong>Extra Invites</strong></label>
                                        <?php if($guest_group_status =="Assigned"):?>
                                        <p class="form-hint-small my-2"><?=$guest_fname;?> has already assigned guests to their group, so you can only increase the number of invites available.</p>
                                        <input class="text-input input" type="number" id="guest_extra_invites" name="guest_extra_invites" placeholder="Extra Invites" value="<?= $guest_extra_invites; ?>" min="<?= $guest_extra_invites; ?>" max="10">
                                        <?php endif;?>
                                        <?php if($guest_group_status =="Unassigned"):?>
                                        <p class="form-hint-small my-2">Assign up to 10 additional invites for this guest, they will then add their own details of the additional guests they can bring.</p>
                                        <input class="text-input input" type="number" id="guest_extra_invites" name="guest_extra_invites" placeholder="Extra Invites" value="<?= $guest_extra_invites; ?>" min="0" max="10">
                                        <?php endif;?>
                                        <?php if($guest_type=="Sole"):?>
                                        <p class="form-hint-small my-2">Assign up to 10 additional invites for this guest, they will then add their own details of the additional guests they can bring.</p>
                                        <input class="text-input input" type="number" id="guest_extra_invites" name="guest_extra_invites" placeholder="Extra Invites" value="<?= $guest_extra_invites; ?>" min="0" max="10">
                                        <?php endif;?>
                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
                                    </div>

                                    <div id="response" class="d-none">
                                        <p>Article Saved <img src="./assets/img/icons/check.svg" alt=""></p>
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
                            WHERE guest_list.guest_id='.$guest_id);
                            if($guest_invites ->num_rows>1){
                                $guest_invites ->fetch_array();
                            }


                        ?>
                            <h2><?= $guest_fname . ' ' . $guest_sname; ?></h2>
                            <div class="std-card">
                                <h3>Contact Details</h3>
                                <p><strong>eMail: </strong><a href="mailto:<?= $guest_email; ?>"><?= $guest_email; ?></a></p>
                                <h4><strong>Address</strong></h4>
                                <address>
                                    <?= $guest_address; ?>,
                                    <?= $guest_postcode; ?>
                                </address>
                                <h3>RSVP Code</h3>
                                <p><?= $guest_rsvp_code; ?></p>
                                <h3>Extra Invites</h3>
                                <p><?= $guest_extra_invites; ?></p>
                                <h3>Events</h3>
                                <?php if ($guest_invites ->num_rows>=1):?>
                                <?php foreach($guest_invites as $invite):?>
                                 <p><a href="event.php?action=view&event_id=<?=$invite['event_id'];?>"><?=$invite['event_name'];?></a></p>
                                 <?php endforeach;?> 
                                 <?php else:?>
                                 <p><?=$guest_fname;?> Has not been assigned to any events yet. You can do that in your event manager <a href="events.php">Click Here</a></p>     
                                 <?php endif;?>       
                            <h3>Dietary Requirements </h3>
                            <p><?= $guest_dietery; ?></p>
                            <div class="card-actions">
                                <a class="my-2" href="guest.php?action=edit&guest_id=<?= $guest_id ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Guest </a><br>
                                <a class="my-2" href="guest.php?action=delete&confirm=no&guest_id=<?= $guest_id; ?>"><i class="fa-solid fa-trash"></i> Remove Guest </a>
                            </div>
                            </div>

                            <?php if ($guest_type == "Group Organiser") :
                                $guest_group_query = ('SELECT guest_id, guest_fname, guest_sname, guest_rsvp_status, guest_events FROM guest_list  WHERE guest_group_id=' . $guest_group_id . ' AND guest_type ="Member" ORDER BY guest_sname');
                                $guest_group = $db->query($guest_group_query);
                                $guest_group_result = $guest_group->fetch_assoc();

                            ?>
                                <div class="std-card">
                                    <h3>Group</h3>
                                    <p>The guest group that <?= $guest_fname; ?> is organising.</p>

                                    <table class="std-table">

                                        <tr>
                                            <th>Name</th>
                                            <th>Attending</th>
                                            <th>RSVP Status</th>

                                        </tr>
                                        <?php foreach ($guest_group as $guest):?>
                                            <tr>
                                                <td><a href="guest.php?action=view&guest_id=<?= $guest['guest_id']; ?>"><?= $guest['guest_fname'] . " " . $guest['guest_sname']; ?></a></td>
                                                <td><?= $guest['guest_events']; ?></td>
                                                <td><?= $guest['guest_rsvp_status']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </table>
                                </div>
                            <?php endif; ?>
                            <?php if ($guest_type == "Member") :
                                $group_details_query = ('SELECT guest_id, guest_fname, guest_sname, guest_group_id FROM guest_list WHERE guest_group_id=' . $guest_group_id . ' AND guest_type = "Group Organiser"');
                                $group_details = $db->query($group_details_query);
                                $group_details_result = $group_details->fetch_assoc();

                            ?>
                                <?php if ($group_details->num_rows >= 1):?>
                                    <div class="std-card">
                                        <h3>Guest Group</h3>
                                        <p><?= $guest_fname; ?> is a member of a guest group that is managed by <a href="guest.php?action=view&guest_id=<?= $group_details_result['guest_id']; ?>"><?= $group_details_result['guest_fname'] . " " . $group_details_result['guest_sname']; ?></a></p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                                <?php if ($guest_type == "Sole") :?>
                                    <div class="std-card">
                                        <h3>Guest Group</h3>
                                        <p><?= $guest_fname; ?> is not associated with a group, they are a sole invite. If you want them to bring guests, you can assign them invites by editing their details. </p>
                                    </div>
                            <?php endif;?>
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

</div>


    </main>

    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
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
                    window.location.replace('guest.php?action=view&guest_id=' + guest_id);
                }
            });
        });
    </script>
    <script>
        //script for adding a guest
        $("#add_guest").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#add_guest").get(0));
            formData.append("action", "create");
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/guest.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    window.location.replace('guest_list.php');
                }
            });

        });
    </script>

</body>

</html>