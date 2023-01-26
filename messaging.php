<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
$cms_name = "";
$user_id = $_SESSION['user_id'];

//guest variable, only required for edit and view actions
// load this users details
$user_details_query = $db->query('SELECT users.user_id, users.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_email  FROM users LEFT JOIN guest_list ON guest_list.guest_id=users.guest_id WHERE users.user_id =' . $user_id);
$user_details_result = $user_details_query->fetch_assoc();
//define guest group id
//find Wedding details.
$wedding = $db->prepare('SELECT * FROM wedding LIMIT 1');
$wedding->execute();
$wedding->store_result();
$wedding->bind_result($wedding_id, $wedding_name, $wedding_email, $wedding_phone, $wedding_contact_name);
$wedding->fetch();

//load any previous messages that have been sent by this guest

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
                Message Us
            </div>
            <div class="main-cards">

                <h1>Message Us</h1>
                <div class="std-card">
                    <form class="form-card" id="guest_message" action="scripts/guest.script.php" method="POST" enctype="multipart/form-data">
                        <div class="form-input-wrapper">
                            <label for="message_subject"><strong>Subject</strong></label>
                            <!-- input -->
                            <input class="text-input input" type="text" name="message_subject" id="message_subject" placeholder="Message Subject" required="" maxlength="45">
                        </div>
                        <div class="form-input-wrapper my-2">
                            <label for="message_body"><strong>Message</strong></label>
                            <textarea name="message_body" id="message_body" rows="5" placeholder="Enter your message here..."></textarea>
                        </div>
                        <div class="button-section my-3">
                            <button class="btn-primary form-controls-btn loading-btn" type="submit">Send Message <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                        </div>

                        <div id="response" class="d-none">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

    <script>
        //script for submitting a message
        $("#guest_message").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            guest_id = '<?php echo $user_details_result['guest_id'];?>';
            guest_fname = '<?php echo $user_details_result['guest_fname'];?>';
            guest_sname = '<?php echo $user_details_result['guest_sname'];?>';
            guest_email = '<?php echo $user_details_result['guest_email'];?>';
            var formData = new FormData($("#guest_message").get(0));
            formData.append("action", "send");
            formData.append("guest_id", guest_id);
            formData.append("guest_fname", guest_fname);
            formData.append("guest_sname", guest_sname);
            formData.append("guest_email", guest_email);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/messaging.script.php",
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
                    $("#response").html(data);
                    $("#response").slideDown(400);
                }
            });
        });
    </script>


</body>

</html>