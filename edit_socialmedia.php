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
    $business_users = $db->prepare('SELECT users.user_id, users.user_name, business_users.business_id, business_users.user_type FROM users NATURAL LEFT JOIN business_users WHERE users.user_id='.$user_id);

    $business_users->execute();
    $business_users->bind_result($user_id, $user_name,$business_id, $user_type);
    $business_users->fetch();
    $business_users->close();
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

//Find Social Media Types
$social_types_query = "SELECT * FROM business_socials_types";
$social_types = mysqli_query($db, $social_types_query);

?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Social Media Settings</title>
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


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <a href="settings.php" class="breadcrumb">Settings</a> / Social Media Profiles</div>
            <div class="main-cards">

                <h1>Social Media Profiles</h1>
                <p>This information will be displayed on your contact page and on your footer.</p>
                <?php
                if ($user_type == "Admin") :

                ?>


                    <div id="socials">

                    </div>



                    <button class="btn-primary" id="add_social">Add A Social Media Profile</button>
                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
            </div>

        </section>

        <div class="modal">
            <div class="modal-body">
                <div class="modal-close">
                    <button type="button" class="btn-close" id="modal-btn-close" aria-label="Close"></button>
                </div>
                <h2>Add Social Media Profile</h2>
                <form class="form-card" id="add_social_media_profile" action="scripts/edit_socialmedia-script.php" method="POST">
                    <div class="form-input-wrapper my-2">
                        <label for="user_email">Social Media Platform</label>
                        <!-- input -->
                        <select class="form-select" aria-label="Social Platform" name="socials_type_id" id="socials_type_id" required>
                            <option value="" selected>Select a Platform</option>
                            <?php foreach ($social_types as $types) : ?>

                                <option value="<?= $types['socials_type_id']; ?>"><?= $types['socials_type_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-input-wrapper">
                        <label for="business_socials_url">URL</label>
                        <!-- input -->
                        <input class="text-input input" type="text" name="business_socials_url" id="business_socials_url" placeholder="URL" required>
                    </div>


                    <div class="button-section my-3">
                        <button class="btn-primary form-controls-btn" type="submit">Add Profile <img src="./assets/img/icons/floppy-disk.svg" alt=""></button>

                    </div>
                    <div id="response" class="d-none">
                    </div>
                </form>
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
        $(".nav-btn").click(function() {
            $(".nav-bar").fadeToggle(500);
        });
    </script>
    <script>
        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadein(500);
        });

        $("#nav-btn-close").click(function() {
            $(".nav-bar").fadeOut(500);
        })
    </script>
    <script>
        $("#add_social").click(function(event) {
            event.preventDefault();
            $(".modal").addClass("modal-active");
        })

        //close modal when close button is clicked
        $("#modal-btn-close").click(function() {
            $(".modal").removeClass("modal-active");

        })
        //close modal when confirm button is clicked
        $(".btn-confirm").on("click", function() {
            $(".modal").removeClass("modal-active");
        })
    </script>

    <script>
        $(document).ready(function() {
            business_id = '<?php echo $business_id; ?>';
            url = "scripts/edit_socialmedia-script.php?action=load&business_id=" + business_id;
            $.ajax({ //load current address
                type: "GET",
                url: url,
                encode: true,
                success: function(data, responseText) {
                    $("#socials").html(data);

                }
            });
        })
        //script for adding a new social profile reset
        $("#add_social_media_profile").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            business_id = '<?php echo $business_id; ?>';
            var formdata = {
                business_id,
                socials_type_id: $("#socials_type_id").val(),
                business_socials_url: $("#business_socials_url").val(),
                action: "addnew",

            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_socialmedia-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $(".modal").removeClass("modal-active");
                    $("#add_social_media_profile")[0].reset();
                    url = "scripts/edit_socialmedia-script.php?action=load&business_id=" + business_id;
                    $.ajax({ //load current address
                        type: "GET",
                        url: url,
                        encode: true,
                        success: function(data, responseText) {
                            $("#socials").html(data);

                        }
                    });
                }
            });

        });
    </script>

</body>

</html>