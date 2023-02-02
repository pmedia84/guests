<?php

//find the referring page to redirect to once logged in

include("connect.php");
include("inc/settings.php");


//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    if ($wedding->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if ($wedding_user->num_rows < 2) {
        header('Location: setup.php?action=check_users_wedding');
    }
}

?>
<?php include("./inc/head.inc.php"); ?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Wedding Website - Guest Area">
<meta name="title" content="Manage your wedding invitation">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Guest Area | RSVP</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->



    <main class="main login-main">
        <div class="header">

            <div class="header-actions login-header">
                <h1 class="">The Wedding Of <?= $wedding_result['wedding_name']; ?> </h1>
            </div>
        </div>
<?php if($guest_area_status == "On") : 
        if (isset($_GET['action'])  && $_GET['action'] == "setup") :
            $rsvp_code_input = $_GET['rsvp_code'];
            //look for guest details
            if ($guest = $db->prepare('SELECT guest_id, guest_fname, guest_sname, guest_email FROM guest_list WHERE guest_rsvp_code = ?')) {
                $guest->bind_param('s', $rsvp_code_input);
                $guest->execute();
                $guest->bind_result($guest_id, $guest_fname, $guest_sname, $guest_email);
                $guest->fetch();
                $guest->close();
            }
        ?>

            <div class="std-card rsvp-card">
                <h1>Set Up Your Guest Area</h1>
                <p>Once you have set up your guest area you will have full access.</p>
                <p><strong><?= $guest_fname . ' ' . $guest_sname; ?></strong></p>
                <h2>Set up a password:</h2>
                <form class="form-card" id="pw_setup" action="scripts/rsvp.script.php" method="post">
                    <p><strong>Please provide an email address, you will use this to login</strong></p>
                    <div class="form-input-wrapper">

                        <label for="guest_email"><strong>Email Address</strong></label>
                        <!-- input -->
                        <input type="email" name="guest_email" id="guest_email" placeholder="Enter email address..." required="" maxlength="45" value="">
                    </div>
                    <div class="form-input-wrapper">

                        <label for="pw1"><strong>Password</strong></label>
                        <!-- input -->
                        <input type="password" name="pw1" id="pw1" placeholder="Create a password..." required="" maxlength="45">
                    </div>
                    <div class="form-input-wrapper">
                        <label for="pw2"><strong>Password</strong></label>
                        <!-- input -->
                        <input type="password" name="pw2" id="pw2" placeholder="Re Enter Password..." required="" maxlength="45">
                    </div>
                    <div class="button-section my-3">
                        <button class="btn-primary form-controls-btn loading-btn" type="submit">Set Password <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                    </div>
                </form>
                <div id="response" class="d-none">
                </div>
            </div>
        <?php else : ?>
            <div class="login-wrapper">
                <h1>Respond To Your Invitation</h1>
                <form class="form-card" id="rsvp_search" action="scripts/rsvp.script.php" method="post">
                    <div class="form-input-wrapper">
                        <p>You will need your RSVP code that was sent with your invite. <br> If you do not have this, contact us and we will let you know what your code is.</p>
                        <label for="rsvp_code"><strong>RSVP Code:</strong></label>
                        <!-- input -->
                        <input type="text" name="rsvp_code" id="rsvp_code" placeholder="Enter Your RSVP Code..." required="" maxlength="45">
                    </div>
                    <div class="button-section my-3">
                        <button class="btn-primary form-controls-btn loading-btn" type="submit">Find Code <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                    </div>

                </form>
                <div id="response" class="d-none">
                </div>
            </div>


        <?php endif; ?>
        <?php else:?>
            <div class="login-wrapper">
                <h2>Our RSVP Section is not available yet</h2>
                <p>We are not quite ready to process your responses. If you would rather let us know by email then please contact us <a href="../contact">Here</a></p>
            </div>
            <?php endif;?>
    </main>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("#pw_setup").submit(function(event) {
            event.preventDefault();
            var guest_id = '<?php echo $guest_id; ?>';
            var guest_name = '<?php echo $guest_fname . ' ' . $guest_sname; ?>';
            var formData = new FormData($("#pw_setup").get(0));
            formData.append("action", "pw_setup");
            formData.append("guest_id", guest_id);
            formData.append("guest_name", guest_name);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/rsvp.script.php",
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
                    if (data === 'success') {
                       window.location.replace('index');

                    }

                }
            });
        })
    </script>
    <script>
        $("#rsvp_search").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#rsvp_search").get(0));
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/rsvp.script.php",
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
        })
    </script>


</body>

</html>