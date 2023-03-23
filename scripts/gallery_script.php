<?php if ($_GET['action'] == "load_doc") :
    include("../connect.php");
    $total_img = $db->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $num_results_on_page = 9;
    if ($gallery = $db->prepare('SELECT * FROM images ORDER BY image_id LIMIT ?,?')) {
        // Calculate the page to get the results we need from our table.
        $calc_page = ($page - 1) * $num_results_on_page;
        $gallery->bind_param('ii', $calc_page, $num_results_on_page);
        $gallery->execute();
        // Get the results...
        $gallery_r = $gallery->get_result();
        //find the number of pages
        $num_pages = ceil($total_img / $num_results_on_page) ;
    }
    $count = 1; ?>
    <div class="grid-row-6col">
        <?php if ($gallery_r->num_rows > 0) :
            foreach ($gallery_r as $image) :
        ?>
                <div class="img-card">
                    <img class="gallery-img" src="../assets/img/gallery/<?= $image['image_filename']; ?>" alt="" onclick="openModal();currentSlide(<?= $count; ?>)" class="hover-shadow">
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
            slides[slideIndex - 1].style.display = "block";
            thumb[slideIndex - 1].classList.add("active");
        }
    </script>
<?php endif; ?>

<?php if ($_GET['action'] == "load_page") :
    include("../connect.php");
    $total_pages = $db->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $num_results_on_page = 9;
    if ($gallery = $db->prepare('SELECT * FROM images ORDER BY image_id LIMIT ?,?')) {
        // Calculate the page to get the results we need from our table.
        $calc_page = ($page - 1) * $num_results_on_page;
        $gallery->bind_param('ii', $calc_page, $num_results_on_page);
        $gallery->execute();
        // Get the results...
        $gallery_r = $gallery->get_result();
    }
    $count = 1; ?>
    <div class="grid-row-6col">
        <?php if ($gallery_r->num_rows > 0) :
            foreach ($gallery_r as $image) :
        ?>
                <div class="img-card">
                    <img class="gallery-img" src="../assets/img/gallery/<?= $image['image_filename']; ?>" alt="" onclick="openModal();currentSlide(<?= $count; ?>)" class="hover-shadow">
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
            slides[slideIndex - 1].style.display = "block";
            thumb[slideIndex - 1].classList.add("active");
        }
    </script>
<?php endif; ?>