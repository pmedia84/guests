<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("inc/head.inc.php");
include("inc/settings.php");
include("./connect.php");
//find if this module is on or off

////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name ="";
$user_id = $_SESSION['user_id'];
if ($cms_type == "Business") {
    //look for the business set up and load information
    //find business details.
    $business_query = ('SELECT business_id, business_name FROM business');
    $business = $db->query($business_query);
    $business_details = mysqli_fetch_assoc($business);
    $business = $db->prepare('SELECT * FROM business');

    $business->execute();
    $business->store_result();
    $business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
    $business->fetch();
    $business->close();
    //set cms name
    $cms_name = $business_name;
    //find user details for this business
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Users</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs"><a href="index.php" class="breadcrumb">Home</a> / <a href="users.php">Users</a> / Edit User</div>
            <div class="grid-col">
                <?php
                if ($user_type == "Admin") :
                    if (empty($_GET['user_id'])) : ?>
                        <h1 class="text-center">Edit User </h1>
                        <div class="std-card user-card">
                            <p class="font-emphasis">No user found, please return to the users page and try again</p>
                            <a href="users.php">Users</a>
                        </div>
                    <?php else :
                        //find users and display on screen.
                        $user = $db->prepare('SELECT user_id, user_email, user_name, user_type FROM users WHERE user_id = ?');
                        $user->bind_param('s', $_GET['user_id']);
                        $user->execute();
                        $user->store_result();
                        $user->bind_result($user_id, $email, $name, $user_type);
                        $user->fetch();
                        $user->close();
                    ?>
                        <h1 class="text-center">Edit User: <?= $name; ?></h1>
                        <div class="std-card user-card">
                            <form class="form-card" id="edit_user" action="scripts/auth.php" method="post">
                                <div class="form-input-wrapper">
                                    <label for="password">Name:</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="username" id="username" placeholder="Username" required="" maxlength="45" value="<?= $name; ?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">eMail Address:</label>
                                    <!-- input -->
                                    <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45" value="<?= $email; ?>">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Access Level</label>
                                    <!-- input -->
                                    <select class="form-select" aria-label="Message regarding" name="msgtype" id="msgtype">
                                                    <option value="<?=$user_type;?>" selected><?=$user_type;?></option>
                                                    <option value="Admin" selected>Admin</option>
                                                    <option value="Editor" selected>Editor</option>
                                                </select>
                                </div>
                                <p>Need to change your password?</p>
                                <p>You can do that here: <a href="resetpw.php">Reset Password</a></p>
                                <div class="button-section my-3">
                                    <button class="btn-primary" type="submit">Save Changes</button>
                                    <a href="users.php" class="btn-primary btn-secondary">Cancel Changes</a>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php else:?>
                    <h1 class="text-center">Edit User </h1>
                    <p class="font-emphasis text-center">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif;
                $db->close(); ?>
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
        $(".nav-btn").click(function() {
            $(".nav-bar").fadeToggle(500);
        });

        $(".btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
    <script>
        //script for requesting password reset
        $("#edit_user").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            user_id = '<?php echo $user_id; ?>';
            //collect form data and GET request information to pass to back end script
            var formdata = {
                user_email: $("#user_email").val(),
                user_id,
                user_name: $("#username").val(),
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_user-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'success') {
                        window.location.replace('users.php');
                    }

                }
            });
        });
    </script>
</body>

</html>