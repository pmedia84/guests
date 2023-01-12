<?php
session_start();
if (isset($_GET['user_email'])) {
    $user_email = $_GET['user_email'];
}

?>
<?php 
include("./connect.php");
include("inc/head.inc.php");
include("inc/settings.php"); 

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
    //find business address details.
    $business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $business->execute();
    $business->store_result();
    $business->bind_result($address_id, $address_house, $address_road, $address_town, $address_county, $address_pc);
    $business->fetch();
    $business->close();


    //find social media info
    $socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
    $socials = $db->query($socials_query);
    $social_result = $socials->fetch_assoc();
    $db->close();
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


    //find wedding events details
    $wedding_events_query = ('SELECT * FROM wedding_events ORDER BY event_time');
    $wedding_events = $db->query($wedding_events_query);
    $wedding_events_result = $wedding_events->fetch_assoc();
   
}
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Reset Password</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->



    <main class="login-main">

        <div class="header">

            <div class="header-actions login-header">
                <img src="assets/img/logo.png" alt="">
            </div>
        </div>
        <div class="login-wrapper">
            <?php if (isset($_GET['action'])) :
                //scripts for changing temporary passwords for new users
                //find username and email address to display on screen.
                $user = $db->prepare('SELECT user_id, user_email, user_name, user_pw_status FROM users WHERE user_email = ?');
                $user->bind_param('s', $user_email);
                $user->execute();
                $user->bind_result($user_id, $email, $name, $user_pw_status);
                $user->fetch();
                $user->close(); 
                if($user_pw_status == "SET"){//check the user password status is temp and visitor has not arrived here from an old url. If the password has already been set then redirect to the index page.
                    header('location: index.php');
                    exit();
                }
                ?>
                <?php if ($_GET['action'] == "temp") : ?>

                    <h1>First Login</h1>
                    <p class="font-emphasis mb-3">As this is your first login, you need to set a password that you will remember.</p>
                    <p class="font-emphasis"><strong>Name: </strong><?= $name; ?></p>
                    <p class="font-emphasis mb-3"><strong>Email address: </strong><?= $email; ?></p>
                    <form class="form-card" id="tempreset" action="scripts/resetpw-script.php" method="post">
                        <div class="form-input-wrapper">
                            <label for="new_pw">New Password</label>
                            <!-- input -->
                            <input type="password" name="new_pw" id="new_pw" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                        </div>
                        <div class="form-input-wrapper">

                            <label for="new_pw2">Re Enter New Password</label>
                            <!-- input -->
                            <input type="password" name="new_pw2" id="new_pw2" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                        </div>
                        <div class="button-section my-3">
                            <button class="btn-primary form-controls-btn loading-btn" type="submit">Reset Password <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                        </div>
                        <div id="response" class="d-none">
                        </div>
                    </form>
                <?php endif; ?>
            <?php if ($_GET['action'] == "reset") :
                //find username and email address to display on screen.
                $user = $db->prepare('SELECT user_id, user_email, user_name FROM users WHERE user_id = ?');
                $user->bind_param('s', $_GET['user_id']);
                $user->execute();
                $user->store_result();
                $user->bind_result($user_id, $email, $name);
                $user->fetch();
                $user->close();?>
                <h1>Reset Password</h1>
                    <p class="font-emphasis">You can now change your password:</p>
                    <p class="font-emphasis"><strong>Name: </strong><?= $name; ?></p>
                    <p class="font-emphasis mb-3"><strong>Email address: </strong><?= $email; ?></p>
                    <form class="form-card" id="resetpw" action="scripts/resetpw-script.php" method="post">
                        <div class="form-input-wrapper">
                            <label for="new_pw">New Password</label>
                            <!-- input -->
                            <input type="password" name="new_pw" id="new_pw" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                        </div>
                        <div class="form-input-wrapper">

                            <label for="new_pw2">Re Enter New Password</label>
                            <!-- input -->
                            <input type="password" name="new_pw2" id="new_pw2" placeholder="Enter New Password" autocomplete="password" required="" maxlength="45">
                        </div>
                        <div class="button-section my-3">
                            <button class="btn-primary form-controls-btn loading-btn" type="submit">Reset Password <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                        </div>
                        <div id="response" class="d-none">
                        </div>
                    </form>
            <?php endif; ?>   

              
            <?php else : ?>
                <?php if (empty($_GET['user_id'])) :?>
                    <h1>Reset Password</h1>
                    <p class="font-emphasis mb-3">Need to reset your password? Enter your email address below and we will email you a password reset link.</p>
                    <form class="form-card" id="requestpwreset" action="scripts/resetpw-script.php" method="post">
                        <div class="form-input-wrapper">
                            <label for="user_email">eMail Address:</label>
                            <!-- input -->
                            <input type="text" name="user_email" id="user_email" placeholder="Enter Email Address" autocomplete="email" required="" maxlength="45">
                        </div>
                        <div class="button-section my-3">
                            <button class="btn-primary form-controls-btn loading-btn" type="submit">Request Link<img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                        </div>
                        <div id="response" class="d-none">
                        </div>
                    </form>

                                                                                                                                
                
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </main>
    <!-- /Main Body Of Page -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>

        //script for requesting password reset
        $("#requestpwreset").submit(function(event) {
            event.preventDefault();

            //collect form data and GET request information to pass to back end script
            var formData = new FormData($("#requestpwreset").get(0));
            formData.append("action", "requestreset");
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/resetpw-script.php",
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
                    if (data === 'correct') {
                        console.log("success");
                    }

                }
            });
        });
    </script>
    <script>
        //script for password reset
        $("#resetpw").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            key = '<?php echo $_GET['key']; ?>';
            user_id = '<?php echo $_GET['user_id']; ?>';
            action = '<?php echo $_GET['action']; ?>';
            //collect form data and GET request information to pass to back end script
            var formdata = {
                key,
                user_id,
                action,
                pw1: $("#new_pw").val(),
                pw2: $("#new_pw2").val(),
            }
            //send as an AJAX POST
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/resetpw-script.php",
                data: formdata,
                encode: true,
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
        <script>
        //script for requesting password reset
        $("#tempreset").submit(function(event) {
            event.preventDefault();
            //collect form data and GET request information to pass to back end script
            var formData = new FormData($("#tempreset").get(0));
            var user_id = <?php echo $user_id;?>;
            formData.append("action", "tempreset");
            formData.append("user_id", user_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/resetpw-script.php",
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