<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./connect.php");
include("./inc/head.inc.php");
include("./inc/settings.php");

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
    //find business address details.
    $business = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $business->execute();
    $business->store_result();
    $business->bind_result($address_id, $address_house, $address_road, $address_town, $address_county, $address_pc);
    $business->fetch();



    //find social media info
    $socials_query = ('SELECT business_socials.business_socials_id, business_socials.socials_type_id, business_socials.business_socials_url, business_socials.business_id, business_socials_types.socials_type_id, business_socials_types.socials_type_name   FROM business_socials  NATURAL LEFT JOIN business_socials_types WHERE  business_socials.business_id =' . $business_id);
    $socials = $db->query($socials_query);
    $social_result = $socials->fetch_assoc();
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


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Settings</title>
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


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / CMS Settings</div>
            <div class="main-cards cms-settings-cards">


                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>



                    <h1>CMS Settings</h1>
                    <p>Manage modules and features that are available to the end user.</p>
                    <?php
                    //connect to db and load module settings
                    $modules_query = ('SELECT * FROM modules');
                    $modules = $db->query($modules_query);
                    //connect to db and load cms settings
                    $settings = $db->prepare('SELECT setting_id,cms_type FROM settings');

                    $settings->execute();
                    $settings->store_result();
                    $settings->bind_result($setting_id,$cms_type);
                    $settings->fetch();
                    $settings->close();
                    ?>
                    <h2>CMS Type</h2>
                    <p>The system will work for a business website as well as a wedding website. This can be changed here.</p>
                    <div class="settings-card">
                        <div class="settings-card-text">
                            <h3>Business Or Wedding Website</h3>
                            <form action="cms_settings.script.php" method="POST" enctype="multipart/form-data" id="cms_settings">
                                <select name="cms_type" id="cms_type" required="">
                                    <option value="<?= $cms_type; ?>" selected><?= $cms_type; ?></option>
                                    <?php
                                    if ($cms_type == "Wedding") :
                                    ?>

                                        <option value="Business">Business</option>
                                    <?php else : ?>
                                        <option value="Wedding">Wedding</option>
                                    <?php endif; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <h2>Modules</h2>
                    <form action="cms_settings.script.php" method="POST" enctype="multipart/form-data" id="cms_modules">
                        <?php foreach ($modules as $module) : ?>
                            <div class="settings-card">
                                <div class="settings-card-text">
                                    <h3><?= $module['module_name']; ?></h3>
                                    <p><?= $module['module_desc']; ?></p>
                                </div>
                                <label class="switch">
                                    <input class="switch-check" type="checkbox" value="<?= $module['module_id']; ?>" <?php if ($module['module_status'] == "On") : ?>checked<?php endif; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
            </div>
            </form>






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
        //script for updating module status
        $(".switch-check").on('click', function(event) {

            var module_id = $(this).attr("value");
            var module_status = "Off";
            if ($(this).is(":checked")) {
                module_status = "On";
            }
            //collect form data and GET request information to pass to back end script
            var formData = new FormData();
            formData.append("module_id", module_id);
            formData.append("module_status", module_status);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/cms_settings.script.php",
                data: formData,
                contentType: false,
                processData: false,

                success: function(data, responseText) {


                    $("#response").html(data);
                    $("#response").slideDown(400);


                }
            });
        });
    </script>

<script>
        //script for setting CMS Type
        $("#cms_type").on('change', function(event) {

            var cms_type= $("#cms_type").val();
            var formData = new FormData($("#cms_settings").get(0));
            var setting_id = <?php echo $setting_id; ?>;
            formData.append("setting_id", setting_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/cms_settings.script.php",
                data: formData,
                contentType: false,
                processData: false,

                success: function(data, responseText) {


                    $("#response").html(data);
                    $("#response").slideDown(400);


                }
            });
        });
    </script>
</body>

</html>