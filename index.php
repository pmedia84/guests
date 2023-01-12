<?php
session_start();
if (!$_SESSION['loggedin'] == TRUE) {
    // Redirect to the login page:
    header('Location: login.php');
}
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
//determine what type of cms is running

//run checks to make sure a business has been set up
if ($cms_type == "Business") {
    //look for a business setup in the db, if not then direct to the setup page
    $business_query = ('SELECT business_id, business_name FROM business');
    $business = $db->query($business_query);
    $business = $db->prepare('SELECT * FROM business');
    $business->execute();
    $business->store_result();
    $business->bind_result($business_id, $business_name, $address_id, $business_phone, $business_email, $business_contact_name);
    $business->fetch();


    if ($business->num_rows == 0) {
        header('Location: setup.php?action=setup_business');
    }
    //check that there are users set up 
    $business_user_query = ('SELECT * FROM business_users');
    $business_user = $db->query($business_user_query);
    if ($business_user->num_rows == 0) {
        header('Location: setup.php?action=check_users_business');
    }
    $business->close();
    //find business details

}

//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {

    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding LIMIT 1');
    $wedding_result = $db->query($wedding_query);

    if ($wedding_result->num_rows == 0) {
        header('Location: setup.php?action=setup_wedding');
    }
    $wedding = $db->prepare($wedding_query);
    $wedding->execute();
    $wedding->store_result();
    $wedding->bind_result($wedding_id, $wedding_name);
    $wedding->fetch();
    $wedding->close();
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




//connect to user db to check admin rights etc
//find username and email address to display on screen.
$user = $db->prepare('SELECT user_id,  user_type FROM users WHERE user_id = ?');
$user->bind_param('s', $_SESSION['user_id']);
$user->execute();
$user->store_result();
$user->bind_result($user_id, $user_type);
$user->fetch();
$user->close();
//find news articles
$news_query = ('SELECT * FROM news_articles WHERE news_articles_status="Published" ORDER BY news_articles_date LIMIT 3 ');
$news = $db->query($news_query);
$num_articles = $news->num_rows;
//find the amount of articles listed
$article_num = ('SELECT news_articles_id FROM news_articles  ');
$article_num = $db->query($article_num);
$article_amt = $article_num->num_rows;
//find the amount of images listed
$image_num = ('SELECT image_id FROM images  ');
$image_num = $db->query($image_num);
$image_amt = $image_num->num_rows;
//find the amount of users listed
$user_num = ('SELECT user_id FROM users');
$user_num = $db->query($user_num);
$user_amt = $user_num->num_rows;
//find the amount of guests
$guest_num = ('SELECT guest_id FROM guest_list');
$guest_num = $db->query($guest_num);
$guest_amt = $guest_num->num_rows;
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Mi-Admin | Dashboard</title>
<!-- /Page Title -->
</head>

<body>


    <!-- Main Body Of Page -->
    <main class="main">

        <!-- Header Section -->
        <?php include("inc/header.inc.php"); ?>
        <!-- Nav Bar -->
        <?php include("./inc/nav.inc.php"); ?>
        <!-- /nav bar -->
        <section class="body">
            <div class="breadcrumbs"><span>Home / </span></div>
            <div class="main-dashboard">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <span><?= $article_amt; ?></span>
                        <img src="assets/img/icons/newspaper.svg" alt="">
                    </div>
                    <h2>News Posts</h2>
                    <a href="news.php">Manage</a>
                </div>
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <span><?= $image_amt; ?></span>
                        <img src="assets/img/icons/image.svg" alt="">
                    </div>
                    <h2>Photo Gallery</h2>
                    <a href="gallery.php">Manage</a>
                </div>
                    <?php if ($user_type == "Admin") : ?>
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <span><?= $user_amt; ?></span>
                            <img src="assets/img/icons/users.svg" alt="">
                        </div>
                        <h2>Users</h2>
                        <a href="users.php">Manage</a>
                    </div>
                <?php endif; ?>
                <?php if ($cms_type == "Wedding") : ?>
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <span><?= $guest_amt; ?></span>
                            <img src="assets/img/icons/users.svg" alt="">
                        </div>
                        <h2>Guest List</h2>
                        <a href="guest_list.php">Manage</a>
                    </div>
                <?php endif; ?>
            </div>


        </section>

        <div class="main-cards">
            <h2>Published Posts</h2>
            <?php foreach ($news as $article) :
                $news_article_body = html_entity_decode($article['news_articles_body']);
                $news_articles_date = strtotime($article['news_articles_date']);

                if ($article['news_articles_status'] == "Published") {
                    $news_articles_status = "<p class='news-item-status published'>Published <i class='fa-solid fa-check'></i></p>";
                }
                if ($article['news_articles_status'] == "Draft") {
                    $news_articles_status = "<p class='news-item-status draft'>Draft <i class='fa-solid fa-flag'></i></p>";
                }
            ?>
                <div class="news-card news-card-dashboard">
                    <?php if ($article['news_articles_img'] == null) : ?>
                        <img src="./assets/img/news/news-item.jpg" alt="">
                    <?php else : ?>
                        <img src="./assets/img/news/<?= $article['news_articles_img']; ?>" alt="">
                    <?php endif; ?>
                    <p class="news-create-date my-2"><?= date('d-M-y', $news_articles_date); ?></p>
                    <h3><?= $article['news_articles_title']; ?></h3>
                    <div class="news-card-body">
                        <p><?= $news_article_body; ?></p>
                    </div>
                    <div class="card-actions"><a href="news_article.php?action=view&news_articles_id=<?= $article['news_articles_id']; ?>"><i class="fa-solid fa-eye"></i> View Article</a></div>
                </div>

            <?php endforeach; ?>



        </div>


    </main>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->

</body>

</html>