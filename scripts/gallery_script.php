<?php
session_start(); ?>
<?php

if ($_GET['action'] == "load_doc") :
    include("../connect.php");
    $total_img = $db->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $num_results_on_page = 9;
    //load the gallery but only images that are set as gallery and that have been approved
    if ($gallery = $db->prepare('SELECT * FROM images WHERE image_placement="Gallery" AND status="Approved" ORDER BY image_id LIMIT ?,?')) {
        // Calculate the page to get the results we need from our table.
        $calc_page = ($page - 1) * $num_results_on_page;
        $gallery->bind_param('ii', $calc_page, $num_results_on_page);
        $gallery->execute();
        // Get the results...
        $gallery_r = $gallery->get_result();
        //find the number of pages
        $num_pages = ceil($total_img / $num_results_on_page);
    }
    $count = 1; ?>
    <div class="grid-row-3col">
        <?php if ($gallery_r->num_rows > 0) :
            foreach ($gallery_r as $image) :
        ?>
                <div class="img-card">
                    <img class="gallery-img" src="assets/img/gallery/<?= $image['image_filename']; ?>" alt="" onclick="openModal();currentSlide(<?= $count; ?>)" class="hover-shadow">
                    <p class="img-card-caption"><?= $image['image_description']; ?></p>
                </div>
        <?php $count++;
            endforeach;
        endif; ?>

    </div>

    <div id="gallery-modal" class="gallery-modal">
        <button class="btn-close modal-close cursor" type="button" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-content">
            <?php $slide_count = 1; ?>
            <?php foreach ($gallery_r as $slides) : ?>
                <div class="gallery-modal-slides">
                    <div class="numbertext">
                        <?= $slide_count; ?> / <?= $gallery_r->num_rows; ?>
                    </div>
                    <div class="slide-body">
                        <div class="slide-body-img">
                            <img src="assets/img/gallery/<?= $slides['image_filename']; ?>">
                        </div>
                        <!-- Caption text -->
                        <p class="slide-body-caption text-center"><?= $slides['image_description']; ?></p>
                    </div>
                </div>
            <?php $slide_count++;
            endforeach; ?>
            <!-- Next/previous controls -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>

        <div class="thumbnails">
            <?php $thumb_count = 1; ?>
            <?php foreach ($gallery_r as $image) :
            ?>
                <!-- Thumbnail image controls -->
                <img class="thumbnail-img" src="../../assets/img/gallery/<?= $image['image_filename']; ?>" onclick="currentSlide(<?= $thumb_count; ?>)">
            <?php $thumb_count++;
            endforeach; ?>

        </div>
    </div>
    <script>
        var slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("gallery-modal-slides");
            var thumb = document.getElementsByClassName("thumbnail-img");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < thumb.length; i++) {
                thumb[i].classList.remove("active");
            }
            //slides[slideIndex - 1].style.display = "block";
            thumb[slideIndex - 1].classList.add("active");
        }
    </script>
<?php endif; ?>

<?php if ($_GET['action'] == "load_page") :
    include("../connect.php");
    $total_pages = $db->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $num_results_on_page = 9;
    if ($gallery = $db->prepare('SELECT * FROM images WHERE image_placement="Gallery" AND status="Approved" ORDER BY image_id LIMIT ?,?')) {
        // Calculate the page to get the results we need from our table.
        $calc_page = ($page - 1) * $num_results_on_page;
        $gallery->bind_param('ii', $calc_page, $num_results_on_page);
        $gallery->execute();
        // Get the results...
        $gallery_r = $gallery->get_result();
    }
    $count = 1; ?>
    <div class="grid-row-3col">
        <?php if ($gallery_r->num_rows > 0) :
            foreach ($gallery_r as $image) :
        ?>
                <div class="img-card">
                    <img class="gallery-img" src="assets/img/gallery/<?= $image['image_filename']; ?>" alt="" onclick="openModal();currentSlide(<?= $count; ?>)" class="hover-shadow">
                    <p class="img-card-caption">Fun</p>
                </div>
        <?php $count++;
            endforeach;
        endif; ?>

    </div>

    <div id="gallery-modal" class="gallery-modal">
        <button class="btn-close modal-close cursor" type="button" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-content">
            <?php $slide_count = 1; ?>
            <?php foreach ($gallery_r as $slides) : ?>
                <div class="gallery-modal-slides">
                    <div class="numbertext">
                        <?= $slide_count; ?> / <?= $gallery_r->num_rows; ?>
                    </div>
                    <div class="slide-body">
                        <div class="slide-body-img">
                            <img src="../../assets/img/gallery/<?= $slides['image_filename']; ?>">
                        </div>
                        <!-- Caption text -->
                        <p class="slide-body-caption text-center"><?= $slides['image_description']; ?></p>
                    </div>
                </div>
            <?php $slide_count++;
            endforeach; ?>
            <!-- Next/previous controls -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>

        <div class="thumbnails">
            <?php $thumb_count = 1; ?>
            <?php foreach ($gallery_r as $image) :
            ?>
                <!-- Thumbnail image controls -->
                <img class="thumbnail-img" src="assets/img/gallery/<?= $image['image_filename']; ?>" onclick="currentSlide(<?= $thumb_count; ?>)">
            <?php $thumb_count++;
            endforeach; ?>

        </div>
    </div>
    <script>
        var slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("gallery-modal-slides");
            var thumb = document.getElementsByClassName("thumbnail-img");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < thumb.length; i++) {
                thumb[i].classList.remove("active");
            }
            slides[slideIndex - 1].style.display = "block";
            thumb[slideIndex - 1].classList.add("active");
        }
        console.log(slideIndex - 1);
    </script>
<?php endif; ?>
<?php if (isset($_GET) && $_GET['action'] == "load_guest_images") :
    require("functions.php");
    require("../connect.php");
    $user = new User();
    $guest_gallery = $db->query('SELECT * FROM images WHERE guest_id=' . $user->guest_id());
    //find out how many images are in the guest gallery
    $total_g_img = $guest_gallery->num_rows; ?>
    <h2 class="my-2 notification-header">My Photos <span class="notification"><?= $total_g_img ?></span></h2>
    <p class="my-2">These are the images that you have contributed to our gallery, we will review your submissions and select our favorites. Don't worry if you can't see all of your images in our gallery. We will keep all submissions.</p>
    <div class="form-controls my-2 actions-bar">
        <button class="btn-primary btn-secondary" id="upload-img"><i class="fa-solid fa-upload"></i> Upload</button>
        <button class="btn-primary  btn-secondary" id="delete-img" data-action="delete"><i class="fa-solid fa-trash"></i> Delete</button>
        <button class="btn-primary btn-secondary" id="check_all"><i class="fa-solid fa-check-double"></i> Select</button>
    </div>
    <form action="upload" id="guest-gallery">
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
<?php endif; ?>