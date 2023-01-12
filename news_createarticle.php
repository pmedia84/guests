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
<title>Mi-Admin | Create News Article</title>
<!-- /Page Title -->
<!-- Tiny MCE -->
<script src="https://cdn.tiny.cloud/1/7h48z80zyia9jc41kx9pqhh00e1e2f4pw9kdcmhisk0cm35w/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<script>
    tinymce.init({
        selector: 'textarea#news_article_body',
        height: 500,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | ',
        tinycomments_mode: 'embedded',

        tinycomments_author: 'Author name',
        mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
        ]
    });
</script>

<body>
    <!-- Main Body Of Page -->
    <main class="main col-2">
        <!-- Header Section -->
        <?php include("inc/header.inc.php");?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs mb-2">
                <a href="index.php" class="breadcrumb">Home</a> /
                <a href="news.php" class="breadcrumb">News</a>
                / Create News Articles
            </div>
            <div class="main-cards">

                <h1>Create News Article</h1>
                <p class="font-emphasis">This page is best viewed on a large screen</p>
                <?php if ($user_type == "Admin" || $user_type=="Developer") : ?>


                    <div class="news-create">

                        <form class="form-card" id="create_news_article" action="scripts/news_createarticle.php" method="post" enctype="multipart/form-data">
                            <div class="form-input-wrapper">
                                <label for="news_articles_title">Title</label>
                                <!-- input -->
                                <input class="text-input input" type="text" name="news_articles_title" id="news_articles_title" placeholder="Article Title" required="" maxlength="45">
                            </div>
                            <div class="form-input-wrapper my-2">
                                <label for="news_articles_img">Header Image</label>
                                <p class="form-hint-small">This can be in a JPG, JPEG or PNG format. And no larger than 1MB.</p>
                                <!-- input -->
                                <input type="file" name="news_articles_img" id="news_articles_img" accept="image/*">
                            </div>
                            <div class="form-input-wrapper my-2">
                                <label for="news_article_body">Article Body</label>
                                <textarea id="news_article_body" name="news_article_body" required="">

                                </textarea>
                            </div>

                            <div class="form-input-wrapper my-2">
                                <label for="news_articles_status">Status</label>
                                <p class="form-hint-small">Set as a draft to come back and finish, or set as published to publish to your website straight away.</p>
                                <select name="news_articles_status" id="news_articles_status" required="">
                                    <option value="" selected>Select Article Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Published">Published</option>
                                </select>
                            </div>
                            <div class="button-section my-3">
                                <button class="btn-primary form-controls-btn" type="submit">Save Article<i class="fa-solid fa-floppy-disk"></i></button>

                            </div>

                            <div id="response" class="d-none">
                                <p>Article Saved </p>
                            </div>
                        </form>
                    </div>
            </div>

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


        $("#form-reset").click(function() {
            $("#create_news_article *").prop("disabled", false);
        });
    </script>
    <script>
        //script for creating a news article
        $("#create_news_article").submit(function(event) {
            tinyMCE.triggerSave();
            event.preventDefault();
            //declare form variables and collect GET request information
            user_id = '<?php echo $user_id; ?>';
            var formData = new FormData($("#create_news_article").get(0));
            formData.append("action", "addnew");
            formData.append("user_id", user_id);
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/news_createarticle-script.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    $("#create_news_article *").prop("disabled", true);
                }
            });

        });
    </script>
</body>

</html>