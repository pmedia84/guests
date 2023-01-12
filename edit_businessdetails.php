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


//find business details.
$business = $db->prepare('SELECT * FROM business WHERE business_id =' . $business_id);
$business->execute();
$business->store_result();
$business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
$business->fetch();
$business->close();

//Find Addresses
$address_query = "SELECT address_id, address_house, address_road FROM addresses";
$address_list = mysqli_query($db, $address_query);

//List current address
$curaddress = $db->prepare('SELECT address_id, address_line_1, address_line_2 FROM addresses WHERE address_id=' . $address_id);
$curaddress->execute();
$curaddress->store_result();
$curaddress->bind_result($curaddress_id, $address_house, $address_road);
$curaddress->fetch();
$curaddress->close();
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
            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <a href="settings.php">Settings</a> / Edit Business Details</div>
            <div class="grid-col">
                <?php
                if ($user_type == "Admin") : ?>
                    <h1 class="text-center my-2">Edit Business Details: <?= $business_name; ?></h1>
                    <div class="std-card user-card">
                        <div class="form-controls my-2">
                            <button class="btn-primary form-controls-btn" id="form-edit" title="Edit">Edit <img src="./assets/img/icons/pen.svg" alt=""></button>
                        </div>
                        <form class="form-card" id="edit_business_details" action="scripts/edit_businessdetails-script.php" method="post">
                            <div class="form-input-wrapper">
                                <label for="business_name">Business Name:</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="business_name" id="business_name" placeholder="Business Name" required="" maxlength="45" value="<?= $business_name; ?>">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_email">Business eMail Address:</label>
                                <p class="form-hint-small">This is the primary email address you want your clients to use. This will appear on your contact page and on your footer.</p>
                                <!-- input -->
                                <input type="text" name="business_email" id="business_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45" value="<?= $business_email; ?>">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_phone">Business Primary Phone No.:</label>
                                <p class="form-hint-small">This is the primary contact number you want your clients to use.</p>
                                <!-- input -->
                                <input type="text" name="business_phone" id="business_phone" placeholder="Business Phone No." autocomplete="tel" required="" maxlength="45" value="<?= $business_phone; ?>">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_contact_name">Primary Contact Name:</label>
                                <p class="form-hint-small">Contact name shown on your contact pages.</p>
                                <!-- input -->
                                <input type="text" name="business_contact_name" id="business_contact_name" placeholder="Business Contact Name" autocomplete="given-name" required="" maxlength="45" value="<?= $business_contact_name; ?>">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="user_email">Business Address</label>
                                <!-- input -->
                                <select class="form-select" aria-label="Message regarding" name="address_id" id="address_id">
                                    <option value="<?= $address_id; ?>" selected>Current Address: <?= $address_house . ' ' . $address_road; ?></option>
                                    <?php foreach ($address_list as $addresses) : ?>

                                        <option value="<?= $addresses['address_id']; ?>"><?= $addresses['address_house'] . ', ' . $addresses['address_road']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <p class="font-emphasis">Need to edit your addresses?</p>
                            <p class="font-emphasis">You can do that here: <a href="edit_address.php">Edit Address</a></p>
                                        
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn" type="submit">Save Changes <img src="./assets/img/icons/floppy-disk.svg" alt=""></button>
                                <a href="settings.php" class="btn-primary btn-secondary">Cancel Changes</a>
                            </div>
                            <div id="response" class="d-none">
                            </div>
                        </form>
                    </div>

                <?php else : ?>
                    <h1 class="text-center">Edit Business Details </h1>
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
        $("#edit_business_details").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            business_id = '<?php echo $business_id; ?>';
            //collect form data and GET request information to pass to back end script
            var formdata = {
                business_id,
                business_name: $("#business_name").val(),
                business_email: $("#business_email").val(),
                business_phone: $("#business_phone").val(),
                business_contact_name: $("#business_contact_name").val(),
                address_id: $("#address_id").val(),
            }
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/edit_businessdetails-script.php",
                data: formdata,
                encode: true,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'success') {
                        window.location.replace('users.php');
                    }
                    $("#edit_business_details *").prop("disabled", true);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#edit_business_details *").prop("disabled", true);

        })

        $("#form-edit").click(function() {
            $("#edit_business_details *").prop("disabled", false);
        })
    </script>
</body>

</html>