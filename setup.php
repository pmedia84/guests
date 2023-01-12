<?php
session_start();
$business_id = "1";
$wedding_id = "1";
if (empty($_GET)) {
    //if user arrives at this page without a get request, redirect to the index page
    header('location: index.php');
}

include("connect.php");
include("inc/settings.php");
//determine what type of cms is running
//run checks to make sure a business has been set up
if ($cms_type == "Business") {
    //look for a business setup in the db, if not then direct to the setup page
    $business_query = ('SELECT business_id FROM business ORDER BY business_id LIMIT 1');
    $business = $db->query($business_query);
    //check if a business exists, if it does fetch the ID, if not set the business ID to NULL
    if ($business->num_rows > 0) {
        $ar = mysqli_fetch_assoc($business);
        $business_id = $ar['business_id'];
        
    } else {
        $business_id = "1";
    }
    //check if there are users for the business
    $users_query = ('SELECT user_id, business_id FROM business_users WHERE business_id=' . $business_id);
    $users = $db->query($users_query);
    
}

//run checks to make sure a wedding has been set up
if ($cms_type == "Wedding") {
    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id FROM wedding');
    $wedding = $db->query($wedding_query);
    if ($wedding->num_rows > 0) {
        $ar = mysqli_fetch_assoc($wedding);
        $wedding_id = $ar['wedding_id'];
    }else {
        $wedding_id = "1";
    }


    //check if there are users for the wedding
    $users_query = ('SELECT wedding_user_id FROM wedding_users WHERE wedding_id=' . $wedding_id);
    $users = $db->query($users_query);
    $users_result = $users->num_rows;
}





?>
<?php include("./inc/head.inc.php"); ?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Setup Admin</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main login-main">
        <div class="header">

            <div class="header-actions login-header">
                <img src="assets/img/logo.png" alt="">
            </div>
        </div>
        <div class="login-wrapper">
            <?php if($cms_type == "Business"):?>
                <?php if ($_GET['action'] == "setup_business") : ?>
                    <?php if ($business->num_rows == 0) : ?>
                        <h1>Setup Business</h1>
                        <p>You need to set up a business first.</p>
                        <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p><br>
                        <form class="form-card" id="setup-business" action="scripts/setup.php" method="post">
                            <div class="form-input-wrapper">
                                <h2>Business Name</h2>
                                <label for="business_name">Business Name</label>
                                <!-- input -->
                                <input type="text" name="business_name" id="business_name" placeholder="Enter Business Name" required="" maxlength="45">
                            </div>
                            <h2>Address</h2>
                            <div class="form-input-wrapper">
                                <label for="address_line_1">Address Line 1</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="address_line_1" id="address_line_1" placeholder="Address Line1" required="" autocomplete="address-line1">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_line_2">Address Line 2</label>
                                <!-- input -->
                                <input type="text" name="address_line_2" id="address_line_2" placeholder="Address Line 2" autocomplete="address-line2">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_line_3">Town or City</label>
                                <!-- input -->
                                <input type="text" name="address_line_3" id="address_line_3" placeholder="Town or City" autocomplete="address-line3" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_county">County</label>
                                <!-- input -->
                                <input type="text" name="address_county" id="address_county" placeholder="County" autocomplete="address-level1" required="">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="address_pc">Postal Code</label>
                                <!-- input -->
                                <input type="text" name="address_pc" id="address_pc" placeholder="Postal Code" autocomplete="postal-code" required="">
                            </div>
                            <h2>Business Contact Details</h2>
                            <div class="form-input-wrapper">
                                <label for="business_email">Business eMail Address:</label>

                                <!-- input -->
                                <input type="email" name="business_email" id="business_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_phone">Business Primary Phone No.:</label>
                                <!-- input -->
                                <input type="text" name="business_phone" id="business_phone" placeholder="Business Phone No." autocomplete="tel" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="business_contact_name">Primary Contact Name:</label>
                                <!-- input -->
                                <input type="text" name="business_contact_name" id="business_contact_name" placeholder="Business Contact Name" autocomplete="given-name" required="" maxlength="45">
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit">Set Up Business <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">
                            </div>
                        </form>
                    <?php else : ?>
                        <h1>Setup Business</h1>
                        <p><strong>Business already setup!</strong></p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($_GET['action'] == "check_users_business") : ?>
                    <?php if ($users == 0) : ?>
                        <?php
                        //display business name
                        $business_query = ('SELECT business_id, business_name FROM business ORDER BY business_id LIMIT 1');
                        $business = $db->query($business_query);
                        $business_result = $business->fetch_array();
                        ?>
                        <?php
                        //find an admin for this business
                        $admin_user_query = ('SELECT user_type, business_id FROM business_users WHERE business_id=' . $business_id . ' AND user_type = "Admin" ');
                        
                        if($admin_user = $db->query($admin_user_query)){
                            $admin_user_result = $admin_user->fetch_assoc();
                        }else{
                            $admin_user_result="";
                        }
                        

                        //find a Developer for this business
                        $dev_user_query = ('SELECT user_type, business_id FROM business_users WHERE business_id=' . $business_id . ' AND user_type = "Developer" ');
                        
                        if($dev_user = $db->query($dev_user_query)){
                            $dev_user_result = $dev_user->fetch_assoc();
                        }else{
                            $dev_user_result="";
                        }
                        
                        ?>
                        <?php if ($admin_user_result == "") : ?>
                            <h1><?= $business_result['business_name']; ?></h1>
                            <p><strong>Admin User Required</strong></p>
                            <p>You need to set up an admin user for this business.</p>
                            <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                            <p>Two user types are required: Admin and Developer</p><br>
                            <h2>Setup Admin User</h2>
                            <form class="form-card" id="add_user" action="scripts/setup.script.php" method="post">
                                <div class="form-input-wrapper">
                                    <label for="user_name">Name:</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">eMail Address:</label>
                                    <!-- input -->
                                    <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Access Level</label>
                                    <p><strong>Admin:</strong> For Clients login</p>
                                    <!-- input -->
                                    <select class="form-select" name="user_type" id="user_type">
                                        <option value="Admin">Admin</option>
                                    </select>
                                    <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                                </div>
                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>
                        <?php else : ?>
                            <?php if ($dev_user_result == null) : ?>
                                <h1><?= $business_result['business_name']; ?></h1>
                                <p><strong>Developer User Required</strong></p>
                                <p>You need to set up a Developer user for this business.</p>
                                <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                                <p>Two user types are required: Admin and Developer</p><br>
                                <h2>Setup Developer User</h2>
                                <form class="form-card" id="add_user" action="scripts/setup.script.php" method="post">
                                    <div class="form-input-wrapper">
                                        <label for="user_name">Name:</label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="user_email">eMail Address:</label>
                                        <!-- input -->
                                        <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="user_email">Access Level</label>
                                        <p><strong>Developer:</strong> For Developer to setup business and provide tech support.</p>
                                        <!-- input -->
                                        <select class="form-select" name="user_type" id="user_type">
                                            <option value="Developer">Developer</option>
                                        </select>
                                        <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon d-none" class="loading-icon" src="./assets/img/icons/loading.svg" alt=""></button>
                                    </div>
                                    <div id="response" class="d-none">
                                    </div>
                                </form>

                            <?php else : ?>
                                <?php header('location: login.php'); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif;?>


            <?php if($cms_type =="Wedding"):?>
                <?php if ($_GET['action'] == "setup_wedding") : ?>
                    <?php if ($wedding->num_rows == 0) : ?>
                        <h1>Setup Wedding</h1>
                        <p>You need to set up your Wedding First.</p>
                        <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p><br>
                        <form class="form-card" id="setup-wedding" action="scripts/setup.php" method="post">
                            <div class="form-input-wrapper">
                                <h2>Wedding Name</h2>
                                <label for="wedding_name">Wedding Name</label>
                                <!-- input -->
                                <input type="text" name="wedding_name" id="wedding_name" placeholder="The Wedding Of:" required="" maxlength="45">
                            </div>
                            
                            <h2>Wedding Primary Contact Details</h2>
                            <div class="form-input-wrapper">
                                <label for="wedding_email">eMail Address:</label>

                                <!-- input -->
                                <input type="email" name="wedding_email" id="wedding_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="wedding_phone">Primary Phone No.:</label>
                                <!-- input -->
                                <input type="text" name="wedding_phone" id="wedding_phone" placeholder="Primary Phone No." autocomplete="tel" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="wedding_contact_name">Primary Contact Name:</label>
                                <!-- input -->
                                <input type="text" name="wedding_contact_name" id="wedding_contact_name" placeholder="Contact Name" autocomplete="given-name" required="" maxlength="45">
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit">Set Up Wedding <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">
                            </div>
                        </form>
                    <?php else : ?>
                        <h1>Setup Wedding</h1>
                        <p><strong>Wedding already setup!</strong></p>
                    <?php endif; ?>

                    <?php if ($_GET['action'] == "check_users_wedding") : ?>
                    
                    <?php
                    //display wedding name
                    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding ORDER BY wedding_id LIMIT 1');
                    $wedding = $db->query($wedding_query);
                    $wedding_result = $wedding->fetch_array();
                    ?>
                    <?php
                    //find an admin for this wedding
                    $admin_user_query = ('SELECT user_type, wedding_id FROM wedding_users WHERE wedding_id=' . $wedding_id . ' AND user_type = "Admin" ');
                    $admin_user = $db->query($admin_user_query);
                    $admin_user_result = $admin_user->fetch_assoc();
                    //find a Developer for this wedding
                    $dev_user_query = ('SELECT user_type, wedding_id FROM wedding_users WHERE wedding_id=' . $wedding_id . ' AND user_type = "Developer" ');
                    $dev_user = $db->query($dev_user_query);
                    $dev_user_result = $dev_user->fetch_assoc();
                    ?>
                    <?php if ($admin_user_result == null) : ?>
                        <h1>The Wedding of <?= $wedding_result['wedding_name']; ?></h1>
                        <p><strong>Admin User Required</strong></p>
                        <p>You need to set up an admin user for your wedding website.</p>
                        <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                        <p>Two user types are required: Admin and Developer</p><br>
                        <h2>Setup Admin User</h2>
                        <form class="form-card" id="add_user_wedding" action="scripts/setup.script.php" method="post">
                            <div class="form-input-wrapper">
                                <label for="user_name">Name:</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="user_email">eMail Address:</label>
                                <!-- input -->
                                <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper">
                                <label for="user_email">Access Level</label>
                                <p><strong>Admin:</strong> For Clients login</p>
                                <!-- input -->
                                <select class="form-select" name="user_type" id="user_type">
                                    <option value="Admin">Admin</option>
                                </select>
                                <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                            <div id="response" class="d-none">
                            </div>
                        </form>
                    <?php else : ?>
                        <?php if ($dev_user_result == null) : ?>
                            <h1><?= $wedding_result['wedding_name']; ?></h1>
                            <p><strong>Developer User Required</strong></p>
                            <p>You need to set up a Developer user for this business.</p>
                            <p><strong>Note:</strong> This can only be done by a developer. Contact us if you are not a developer.</p>
                            <p>Two user types are required: Admin and Developer</p><br>
                            <h2>Setup Developer User</h2>
                            <form class="form-card" id="add_user_wedding" action="scripts/setup.script.php" method="post">
                                <div class="form-input-wrapper">
                                    <label for="user_name">Name:</label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="username" id="username" placeholder="Name" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">eMail Address:</label>
                                    <!-- input -->
                                    <input type="text" name="user_email" id="user_email" placeholder="Email Address" autocomplete="email" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Access Level</label>
                                    <p><strong>Developer:</strong> For Developer to setup business and provide tech support.</p>
                                    <!-- input -->
                                    <select class="form-select" name="user_type" id="user_type">
                                        <option value="Developer">Developer</option>
                                    </select>
                                    <p>A password will be randomly generated and emailed to the user to make their own password.</p>
                                </div>
                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn loading-btn" type="submit">Add User <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>

                        <?php else : ?>
                            <?php header('location: login.php'); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                
            <?php endif; ?>
                <?php endif; ?>
            <?php endif;?>

            



        </div>







    </main>
    <!-- /Main Body Of Page -->
    <!-- Quote request form script -->

    <!-- /Quote request form script -->
    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        //script for saving new business details then redirects to set up users
        $("#setup-business").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#setup-business").get(0));
            var url = "setup.php?action=check_users_business"
            formData.append("action", "create_business");

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/setup.script.php",
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

                    window.location.replace(url);



                }
            });

        });
    </script>
        <script>
        //script for saving new wedding details then redirects to set up users
        $("#setup-wedding").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#setup-wedding").get(0));
            var url = "setup.php?action=check_users_wedding"
            formData.append("action", "create_wedding");

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/setup.script.php",
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

                    window.location.replace(url);



                }
            });

        });
    </script>
    <script>
        //script for adding new users
        $("#add_user").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#add_user").get(0));
            var business_id = <?php echo $business_id; ?>;
            var url = "setup.php?action=check_users_business"
            formData.append("action", "create_user_business");
            formData.append("business_id", business_id);

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/setup.script.php",
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
                    if (data == "User already exists with that email address") {
                        $("#response").addClass("form-response error");
                        $("#response").html(data);
                        $("#response").slideDown(400);

                    }
                    window.location.replace(url);



                }
            });

        });
    </script>
        <script>
        //script for adding new users
        $("#add_user_wedding").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#add_user_wedding").get(0));
            var wedding_id = <?php echo $wedding_id; ?>;
            var url = "setup.php?action=check_users_wedding";
            formData.append("action", "create_user_wedding");
            formData.append("wedding_id", wedding_id);

            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/setup.script.php",
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
                    if (data == "User already exists with that email address") {
                        $("#response").addClass("form-response error");
                        $("#response").html(data);
                        $("#response").slideDown(400);

                    }
                    window.location.replace(url);



                }
            });

        });
    </script>
</body>

</html>