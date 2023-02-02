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

//guest variable, only required for edit and view actions
    // load this users details
    $user_details_query = $db->query('SELECT users.user_id, users.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_email, guest_list.guest_address, guest_list.guest_postcode, guest_list.guest_dietery  FROM users LEFT JOIN guest_list ON guest_list.guest_id=users.guest_id WHERE users.user_id =' . $user_id);
    $user_details_result = $user_details_query->fetch_assoc();
    //define guest group id
    //find Wedding details.
    $wedding = $db->prepare('SELECT * FROM wedding LIMIT 1');
    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name, $wedding_date, $wedding_email, $wedding_phone, $wedding_contact_name);
    $wedding->fetch();
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Wedding Guest Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | My User Profile</title>
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
                <a href="profile" class="breadcrumb">My Profile</a>
                <?php if (array_key_exists('action', $_GET) && $_GET['action'] == "edit") : ?>
                    / Edit My Profile
                <?php endif;?>



            </div>
            <div class="main-cards">
                <?php if (array_key_exists('action', $_GET) && $_GET['action'] == "edit") :?>
                    <h1>Edit My Profile</h1>
                <?php endif; ?>
                <?php if(!array_key_exists('action', $_GET)) : ?>
                    <h1><i class="fa-solid fa-address-book"></i> My Profile</h1>
                    <div class="std-card">
                        <h2>Contact Details</h2>
                        <p><strong>First Name: </strong><?=$user_details_result['guest_fname'];?></p>
                        <p><strong>Last Name: </strong><?=$user_details_result['guest_sname'];?></p>
                        <p><strong>eMail: </strong><?=$user_details_result['guest_email'];?></p>
                        <h2>Address:</h2>
                        <address>
                            <?=$user_details_result['guest_address'];?><br>
                            <?=$user_details_result['guest_postcode'];?><br>
                        </address>
                        <h2>Dietary Requirements</h2>
                        <?=$user_details_result['guest_dietery'];?><br>
                        <div class="card-actions">
                            <a href="profile?action=edit" class="btn-primary">Edit My Profile <i class="fa-solid fa-pen-to-square"></i></a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (array_key_exists('action', $_GET) && $_GET['action'] == "edit") : ?>
                    <?php if (($user_details_query->num_rows) > 0):?>
                        
                        <div class="std-card">
                        <h2>Contact Details</h2>
                            <form class="form-card" id="edit_profile" action="scripts/guest.script.php" method="POST" enctype="multipart/form-data">
                                <div class="form-input-wrapper">
                                    <label for="guest_fname"><strong>First Name</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_fname" id="guest_fname" placeholder="Guest First Name" required="" maxlength="45" value="<?= $user_details_result['guest_fname'];?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="guest_sname"><strong>Surname</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="guest_sname" id="guest_sname" placeholder="Guest Surname" required="" maxlength="45" value="<?= $user_details_result['guest_sname'];?>">
                                </div>
                                <div class="form-input-wrapper my-2">
                                        <label for="guest_email"><strong>Email Address</strong></label>
                                        <p class="form-hint-small">Changing this will also change the email address you use to login to this guest area.</p>
                                        <p class="form-hint-small">You will also be required to change your password next time you login.</p>
                                        <input class="text-input input" type="text" id="guest_email" name="guest_email" placeholder="Email Address" value="<?= $user_details_result['guest_email'];?>">
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_address"><strong>Address</strong></label>
                                        <textarea name="guest_address" id="guest_address"><?= $user_details_result['guest_address']; ?></textarea>
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_postcode"><strong>Postcode</strong></label>
                                        <input class="text-input input" type="text" id="guest_postcode" name="guest_postcode" placeholder="Postcode" value="<?= $user_details_result['guest_postcode'];?>">
                                    </div>
                                    <div class="form-input-wrapper my-2">
                                        <label for="guest_dietery"><strong>Dietary Requirements</strong></label>
                                        <input class="text-input input" type="text" id="guest_dietery" name="guest_dietery" placeholder="Your dietary requirements..." value="<?= $user_details_result['guest_dietery'];?>">
                                    </div>

                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit">Save Changes <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
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
            </div>
        </div>
    </main>

    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

    <script>
        //script for editing a guest
        $("#edit_profile").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            guest_id = '<?php echo $user_details_result['guest_id'];?>';
            user_id = '<?php echo $user_details_result['user_id'];?>';
            var formData = new FormData($("#edit_profile").get(0));
            formData.append("action", "edit");
            formData.append("guest_id", guest_id);
            formData.append("user_id", user_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/profile.script.php",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() { //animate button
                    $("#loading-icon").show(400);
                },
                complete: function() {
                    $("#loading-icon").hide(400);
                },
                success: function(data, responseText) {
                    window.location.replace('profile');
                }
            });
        });
    </script>
   

</body>

</html>