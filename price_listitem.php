<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("inc/head.inc.php");
include("inc/settings.php");
include("connect.php");
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
}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_details = mysqli_fetch_assoc($wedding);
    if ($wedding->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if ($wedding_user->num_rows == 0) {
        header('Location: setup.php?action=check_users_wedding');
    }

    if (!$_SESSION['loggedin'] == true) {
        // Redirect to the login page:
        header('Location: login.php');
    }
}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//service variable
if ($_GET['action'] == "edit") {
    $service_id = $_GET['service_id'];
    //find service details

    $service = $db->prepare('SELECT * FROM services WHERE service_id =' . $service_id);

    $service->execute();
    $service->store_result();
}
if ($_GET['action'] == "delete") {
    $service_id = $_GET['service_id'];
    //find service details

    $service = $db->prepare('SELECT * FROM services WHERE service_id =' . $service_id);

    $service->execute();
    $service->store_result();
}



?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Manage Price List</title>
<!-- /Page Title -->
</head>


<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("./inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="price_list.php" class="breadcrumb">Price List</a>
                <?php if ($_GET['action'] == "edit") : ?>
                    / Edit Service
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    / Delete Service
                <?php endif; ?>
                <?php if ($_GET['action'] == "add") : ?>
                    / Create Service
                <?php endif; ?>

            </div>
            <div class="main-cards">
                <?php if ($_GET['action'] == "edit") : ?>
                    <h1>Edit Service</h1>
                <?php endif; ?>
                <?php if ($_GET['action'] == "delete") : ?>
                    <h1>Delete Service</h1>
                <?php endif; ?>

                <?php if ($_GET['action'] == "edit") : ?>
                    <p class="font-emphasis">This page is best viewed on a large screen</p>

                <?php endif; ?>
                <?php if ($user_type == "Admin") : //detect if user is an admin or not 
                ?>

                    <?php if ($_GET['action'] == "add") : ?>

                        <div class="std-card">
                            <form class="form-card" id="add_service" action="scripts/price_list.script.php" method="POST" enctype="multipart/form-data">
                                <div class="form-input-wrapper">

                                    <h2>Create New Service</h2>
                                    <label for="service_name"><strong>Service Name</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="service_name" id="service_name" placeholder="Service Name" required="" maxlength="45">
                                </div>

                                <div class="form-input-wrapper my-2">
                                    <label for="service_description"><strong>Service Description</strong></label>
                                    <p class="form-hint-small">This is not essential, but can be useful.</p>
                                    <input class="text-input input" type="text" id="service_description" name="service_description" placeholder="Service Description">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="service_price"><strong>Price</strong></label>
                                    <!-- input -->
                                    <input class="text-input input" type="text" name="service_price" id="service_price" placeholder="Price" required="" maxlength="45">
                                </div>
                                <div class="form-input-wrapper">
                                    <label for="user_email">Category</label>
                                    <!-- input -->
                                    Load categories here
                                    <select class="form-select" aria-label="Message regarding" name="service_category" id="service_category">
                                                    
                                                    <option value="Admin" selected>Admin</option>
                                                    <option value="Editor" selected>Editor</option>
                                                </select>
                                </div>
                                <div class="button-section my-3">
                                    <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Add Service </button>
                                </div>
                                <div id="response" class="d-none">
                                </div>
                            </form>
                        </div>

                    <?php endif; ?>



                    <?php if ($_GET['action'] == "delete") : //if action is delete, detect if the confirm is yes or no
                    ?>
                        <?php if ($_GET['confirm'] == "yes") : //if yes then delete the article
                        ?>
                            <?php if (($service->num_rows) > 0) :
                                $service->bind_result($service_id, $service_name, $service_description, $service_category, $service_price, $service_promo);
                                $service->fetch();
                                // connect to db and delete the record
                                $delete_service = "DELETE FROM services WHERE service_id=" . $service_id;
                                if (mysqli_query($db, $delete_service)) {
                                    echo '<div class="std-card"><div class="form-response error"><p>' . $service_name . ' Has Been Deleted</p></div></div>';
                                } else {
                                    echo '<div class="form-response error"><p>Error deleting service, please try again.</p></div>';
                                }
                            ?>

                            <?php endif; ?>
                        <?php else : //if not then display the message to confirm the user wants to delete the news article
                        ?>
                            <?php if (($service->num_rows) > 0) :
                                $service->bind_result($service_id, $service_name, $service_description, $service_category, $service_price, $service_promo);
                                $service->fetch();



                            ?>
                                <div class="std-card">
                                    <h2 class="text-alert">Delete: <?= $service_name; ?></h2>
                                    <p><?= $service_description; ?></p>
                                    <p>Are you sure you want to delete this service?</p>
                                    <p><strong>This Cannot Be Reversed</strong></p>
                                    <div class="button-section">
                                        <a class="btn-primary btn-delete my-2" href="price_listitem.php?action=delete&confirm=yes&service_id=<?= $service_id; ?>"><i class="fa-solid fa-trash"></i>Delete Service</a>
                                        <a class="btn-primary btn-secondary my-2" href="price_list.php"><i class="fa-solid fa-ban"></i>Cancel</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>



                    <?php endif; ?>

                    <?php if ($_GET['action'] == "edit") : ?>
                        <?php if (($service->num_rows) > 0) :
                            $service->bind_result($service_id, $service_name, $service_description, $service_category, $service_price, $service_promo);
                            $service->fetch();

                        ?>
                            <div class="std-card">
                                <form class="form-card" id="edit_service" action="scripts/price_list.script.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-input-wrapper">

                                        <h2><?= $service_name; ?></h2>
                                        <label for="service_name"><strong>Service Name</strong></label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="service_name" id="service_name" placeholder="Service Name" required="" maxlength="45" value="<?= $service_name; ?>">
                                    </div>

                                    <div class="form-input-wrapper my-2">
                                        <label for="service_description"><strong>Service Description</strong></label>
                                        <p class="form-hint-small">This is not essential, but can be useful.</p>
                                        <input class="text-input input" type="text" id="service_description" name="service_description" placeholder="Service Description" value="<?= $service_description; ?>">
                                    </div>
                                    <div class="form-input-wrapper">
                                        <label for="service_price"><strong>Price</strong></label>
                                        <!-- input -->
                                        <input class="text-input input" type="text" name="service_price" id="service_price" placeholder="Price" required="" maxlength="45" value="<?= $service_price; ?>">
                                    </div>
                                    <div class="button-section my-3">
                                        <button class="btn-primary form-controls-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Changes </button>
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




                <?php else : ?>
                    <p class="font-emphasis">You do not have the necessary Administrator rights to view this page.</p>
                <?php endif; ?>
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
        //script for editing a news article
        $("#edit_service").submit(function(event) {
            event.preventDefault();
            //declare form variables and collect GET request information
            service_id = '<?php echo $service_id; ?>';
            var formData = new FormData($("#edit_service").get(0));
            formData.append("action", "edit");
            formData.append("service_id", service_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/price_list.script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === "Done") {
                        console.log(data);
                        window.location.replace('price_list.php');
                    }

                }
            });

        });
    </script>
</body>

</html>