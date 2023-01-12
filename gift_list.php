<?php
session_start();
if (!$_SESSION['loggedin'] == true) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("./connect.php");
include("inc/head.inc.php");
include("inc/settings.php");

////////////////Find details of the cms being used, on every page\\\\\\\\\\\\\\\
//Variable for name of CMS
//wedding is the name of people
//business name
$cms_name = "";
$user_id = $_SESSION['user_id'];
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
    $wedding_users = $db->prepare('SELECT users.user_id, users.user_name, wedding_users.wedding_id, wedding_users.user_type FROM users NATURAL LEFT JOIN wedding_users WHERE users.user_id=' . $user_id);

    $wedding_users->execute();
    $wedding_users->bind_result($user_id, $user_name, $wedding_id, $user_type);
    $wedding_users->fetch();
    $wedding_users->close();

    //find wedding guest list
    $gift_list_query = ('SELECT * FROM gift_list');
    $gift_list = $db->query($gift_list_query);
    $gift_list_result = $gift_list->fetch_assoc();
}
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//Gift list method, loads the list from a remote script so that it can be searched with ajax


?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Guest List</title>
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


            <div class="breadcrumbs mb-2"><a href="index.php" class="breadcrumb">Home</a> / <?php if ($cms_type == "Business") {
                                                                                                echo "Settings";
                                                                                            } else {
                                                                                                echo "Guest List";
                                                                                            } ?></div>
            <div class="main-cards">
                <?php if ($user_type == "Admin" || $user_type == "Developer") : ?>
                    <?php if ($cms_type == "Wedding") : ?>
                        <h1>Your Gift List</h1>
                        <p>Keep this information up to date as you plan for big day. Your invites will be sent out from this information.</p>
                        <a href="gift_item.php?action=create" class="btn-primary">Add Item <i class="fa-solid fa-gift"></i></a>

                        <?php if ($gift_list->num_rows > 0) : ?>
                            <?php foreach ($gift_list as $gift_item) : ?>
                                <div class="std-card">
                                    <?php if ($gift_item['gift_item_name'] == "") : ?>
                                        <h1>Gift List Message</h1>
                                        <p><?= $gift_item['gift_item_desc']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($gift_item['gift_item_name'] > "") : ?>
                                        <h1><?= $gift_item['gift_item_name']; ?></h1>
                                        <p><?= $gift_item['gift_item_desc']; ?></p>
                                        <p><strong>URL: </strong><a href="http://<?= $gift_item['gift_item_url']; ?>" target="_blank"><?= $gift_item['gift_item_url']; ?></a></p>
                                    <?php endif; ?>
                                    <div class="card-actions">
                                        <a class="my-2" href="gift_item.php?action=edit&gift_item_id=<?= $gift_item['gift_item_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit Item </a><br>
                                        <a class="my-2" href="gift_item.php?action=delete&confirm=no&gift_item_id=<?= $gift_item['gift_item_id']; ?>"><i class="fa-solid fa-trash"></i> Remove Item </a>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>




                    <?php endif; ?>
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

</body>



</html>