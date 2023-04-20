<?php
session_start();
require("scripts/functions.php");
check_login();
$user = new User();
$wedding = new Wedding();
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
if($guest_image_gallery->status()=="Off"){
    header("Location: index");
}
//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//load images from gallery, shows all images

$guest_gallery = $db->query('SELECT * FROM images WHERE guest_id=' . $user->guest_id());
//find out how many images are in the guest gallery
$total_g_img = $guest_gallery->num_rows;
$total_img = $db->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
$num_results_on_page = 9;
$num_pages = ceil($total_img / $num_results_on_page);
$page_count = 0;
//set the current page number
$page = 1;
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media Wedding Admin - Guest Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- Page Title -->
<title>Guest Area | Photo Gallery</title>
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
            <div class="breadcrumbs mb-2">
                <a href="index" class="breadcrumb">Home</a> / Photo Gallery

            </div>
            <?php if ($guest_area_gallery->status() == "On") : ?>
                <div class="main-cards">
                    <h1><i class="fa-solid fa-images"></i> Our Photo Gallery</h1>
                    <p>A collection of some of our best photos from our big day. If you would like to contribute, please upload images below.</p>


                    <?php $count = 1; ?>
                    <div class="std-card" id="gallery-top">
                        <div class="gallery-wrapper">

                            <div class="loader">
                                <svg class="loader-spinner" width="75" height="75" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_11_2)">
                                        <path d="M10.874 1.56982C11.1182 2.39648 10.6494 3.2666 9.81934 3.51269C5.94727 4.66552 3.125 8.25683 3.125 12.4561C3.125 17.6758 7.28027 21.8311 12.5 21.8311C17.6758 21.8311 21.875 17.6758 21.875 12.4561C21.875 8.25683 19.0527 4.66552 15.1807 3.51269C14.3506 3.2666 13.8818 2.39648 14.126 1.56982C14.375 0.742672 15.2441 0.271578 16.0693 0.517574C21.2354 2.0542 25 6.79199 25 12.4561C25 19.4043 19.4043 24.9561 12.5 24.9561C5.5957 24.9561 0 19.4043 0 12.4561C0 6.79199 3.76709 2.0542 8.93066 0.517574C9.75586 0.271578 10.625 0.742672 10.874 1.56982Z" fill="red" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_11_2">
                                            <rect width="25" height="25" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>

                            </div>
                            <div class="d-none" id="gallery">
                            </div>
                        </div>
                        <nav aria-label="gallery navigation">
                            <ul class="pagination" role="list">
                                <li class="pagination-prev" id="prev"><a class="" href="gallery?page=1" aria-label="Previous" data-page="-1"><span aria-hidden="true">&#10094;</span></a></li>
                                <li class="pagination-next" id="next"><a class="" href="gallery?page=1" aria-label="Next" data-page="+1"><span aria-hidden="true">&#10095;</span></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="std-card" id="guest_images">
                        <h2 class="my-2 notification-header">My Photos <span class="notification"><?= $total_g_img; ?></span></h2>
                        <p class="my-2">These are the images that you have contributed to our gallery, we will review your submissions and select our favorites. Don't worry if you can't see all of your images in our gallery. We will keep all submissions.</p>
                        <div class="form-controls my-2 actions-bar">
                            <button class="btn-primary btn-secondary" id="upload-img"><i class="fa-solid fa-upload"></i> Upload</button>
                            <button class="btn-primary  btn-secondary" id="delete-img" data-action="delete"><i class="fa-solid fa-trash"></i> Delete</button>
                            <button class="btn-primary btn-secondary" id="check_all"><i class="fa-solid fa-check-double"></i> Select</button>
                        </div>
                        <form action="upload" id="guest-gallery" method="POST">
                            <div class="grid-row-3col">
                                <?php if ($guest_gallery->num_rows > 0) :
                                    $key = 0;
                                    foreach ($guest_gallery as $guest_image) :
                                ?>
                                        <div class="img-card guest-img" data-status="<?= $guest_image['status']; ?>">
                                            <span class="guest-img-status"> <?php if ($guest_image['status'] == "Approved") : echo $guest_image['status']; ?> <i class="fa-solid fa-check"></i><?php endif; ?></span>
                                            <input class="guest-img-select" data-select="false" data-image_filename="<?= $guest_image['image_filename']; ?>" type="checkbox" name="gallery_img[<?= $key; ?>][image_id]" value="<?= $guest_image['image_id']; ?>">
                                            <img class="gallery-img" src="assets/img/gallery/<?= $guest_image['image_filename']; ?>" alt="" data-img_id="<?= $guest_image['image_id']; ?>">
                                            <p class="img-card-caption"><?= $guest_image['image_description']; ?></p>
                                        </div>
                                <?php $key++;
                                    endforeach;

                                endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

        </div>
        </div>
    <?php endif; ?>
    </div>
    <div class=" response-card-wrapper d-none" id="response-card-wrapper">
        <div class="response-card">
            <div class="response-card-icon">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div class="response-card-body">
                <h2 id="response-card-title"></h2>
                <p id="response-card-text"></p>
            </div>
        </div>
    </div>
    <div class="modal upload-modal" id="upload-modal">
        <div class="modal-content">
            <div class="close"><button class="btn-close" type="button" id="close-upload"><i class="fa-solid fa-minus"></i></button></div>
            <form action="scripts/gallerycrud.php" id="upload" method="POST" enctype="multipart/form-data" data-action="upload">
                <div class="form-input-wrapper gallery-card">
                    <label for="gallery_img">Upload Images</label>
                    <p class="form-hint-small">These can be in a JPG, JPEG or PNG format</p>
                    <p>Once you have uploaded your photo's, we will add them to our gallery.</p>
                    <!-- input -->
                    <input type="file" name="gallery_img[]" id="gallery_img" accept="image/*" multiple>
                    <div class="button-section"><button class="btn-primary my-2 form-controls-btn loading-btn" type="submit" id="upload-btn" data-action="upload"><span id="loading-btn-text" class="loading-btn-text"><i class="fa-solid fa-upload"></i>Submit</span> <i id="loading-icon" class="fa-solid fa-spinner fa-spin-pulse spinner-icon d-none"></i></button></div>
                </div>
            </form>
        </div>
    </div>

    </main>
    <?php $db->close(); ?>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        const gallery_top = document.getElementById("gallery-top");
        let pos = gallery_top.offsetTop;
        //starting page
        var page = "<?php echo $page; ?>";
        let i = 0;
        let pages = "<?php echo $num_pages; ?>";
        let links = $(".page-link");

        for (i; i < pages; i++) {
            $("#next").before("<li><a class='page-link page-link-num' href='gallery?page=" + page + "' data-page='" + page + "'>" + page + "</a></li>");
            page++; //increment by one till all page links have been added.
        }

        //find the current page, always starts on page 1, alters when lins are clicked
        var cur_page = 1;
        //handle pagination
        if (cur_page == 1) {
            $("#prev").hide();
        }
        $(".page-link").on("click", function(e) {
            e.preventDefault();
            console.log("starting current page", cur_page);
            if (cur_page <= pages) {
                $("#next").show();
            }
            if (cur_page > 1) {
                $("#prev").show();
            }

            console.log("starting current page", cur_page);
            $(".gallery-wrapper .loader").show();
            let page = $(this).data("page");
            let action = "load_page";
            let gallery = "gallery";

            $.ajax({ //start ajax post
                beforeSend: function() {

                    $("#gallery").hide();
                },
                complete: function() {
                    $(".gallery-wrapper .loader").fadeOut(400);
                    $("#gallery").show();
                },
                type: "GET",
                url: "scripts/gallery_script.php?action=" + action + "&page=" + page + "&gallery=" + gallery,
                success: function(data, responseText) {
                    ///need script to catch errors
                    if (responseText === "success") {
                        $("#gallery").html(data);
                        window.scrollTo(0, pos);
                        cur_page = page;
                    }
                }
            });
            //toggle active class
            $(".page-link").removeClass("links-active");
            $(this).toggleClass("links-active");
            cur_page = page;
            console.log("current page", cur_page);
            if (cur_page >= pages) {
                $("#next").hide();
            }
            if (cur_page > 1) {
                $("#prev").show();
            } else {
                $("#prev").hide();
            };
        });
        $("#next").on("click", function(e) {
            e.preventDefault();
            //set the next page to transition to
            console.log("starting current page", cur_page);
            var page_num = cur_page + 1;
            if (page_num > pages) {
                //stop the script if the end has been reached
                return false
            }
            $(".gallery-wrapper .loader").show();
            let page = $(this).data("page");
            let action = "load_page";
            let gallery = "gallery";

            $.ajax({ //start ajax post
                beforeSend: function() {

                    $("#gallery").hide();
                },
                complete: function() {
                    $(".gallery-wrapper .loader").fadeOut(400);
                    $("#gallery").show();
                },
                type: "GET",
                url: "scripts/gallery_script.php?action=" + action + "&page=" + page_num + "&gallery=" + gallery,

                success: function(data, responseText) {
                    ///need script to catch errors
                    if (responseText === "success") {
                        $("#gallery").html(data);
                        window.scrollTo(0, pos);
                        cur_page++;
                        console.log("current page", cur_page);
                        if (cur_page >= pages) {
                            $("#next").hide();
                        }
                        if (cur_page <= pages) {
                            $("#prev").show();
                        }
                        //run check to assign the current page
                        $(".page-link").removeClass("links-active");
                        $(".page-link").each(function() {
                            if ($(this).data("page") == cur_page) {
                                $(this).toggleClass("links-active");
                            }
                        })
                    }
                }
            });

        })
        $("#prev").on("click", function(e) {
            e.preventDefault();
            console.log("starting current page", cur_page);
            //set the next page to transition to
            var page_num = cur_page - 1;
            console.log("next page", page_num);
            if (cur_page == 1) {
                //stop the script if the end has been reached
                return false

            }
            $(".gallery-wrapper .loader").show();
            let page = $(this).data("page");
            let action = "load_page";
            let gallery = "gallery";

            $.ajax({ //start ajax post
                beforeSend: function() {

                    $("#gallery").hide();
                },
                complete: function() {
                    $(".gallery-wrapper .loader").fadeOut(400);
                    $("#gallery").show();
                },
                type: "GET",
                url: "scripts/gallery_script.php?action=" + action + "&page=" + page_num + "&gallery=" + gallery,

                success: function(data, responseText) {
                    ///need script to catch errors
                    if (responseText === "success") {
                        $("#gallery").html(data);
                        window.scrollTo(0, pos);
                        cur_page--;
                        console.log("current page", cur_page);
                        if (cur_page <= pages) {
                            $("#next").show();
                        }
                        if (cur_page == 1) {
                            $("#prev").hide();
                        }
                        //run check to assign the current page
                        $(".page-link").removeClass("links-active");
                        $(".page-link").each(function() {
                            if ($(this).data("page") == cur_page) {
                                $(this).toggleClass("links-active");
                            }
                        })
                    }
                }
            });
        })
    </script>
    <script src="assets/js/gallery.js"></script>
</body>

</html>