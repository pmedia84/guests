<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="btn btn-close" id="nav-btn-close"></button>
        </div>
        <ul class="nav-links">
            <?php if ($cms_type == "Business") : ?>
                <li><a class="nav-link" href="index.php">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($price_list_status == "On") : ?>
                    <li><a class="nav-link" href="price_list.php">Price List <i class="fa-solid fa-tags"></i></a></li>
                <?php endif; ?>
                <?php if ($image_gallery_status == "On") : ?>
                    <li><a class="nav-link" href="gallery.php">Image Gallery <i class="fa-solid fa-image"></i></a></li>
                <?php endif; ?>
                <?php if ($news_status == "On") : ?>
                    <li><a class="nav-link" href="news.php">News <i class="fa-solid fa-newspaper"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin") : ?>
                    <li><a class="nav-link" href="users.php">Users <i class="fa-solid fa-users"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type=="Developer") : ?>
                    <li><a class="nav-link" href="settings.php">Settings <i class="fa-solid fa-gear"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin") : ?>
                    <li><a class="nav-link" href="reviews.php">Reviews <i class="fa-solid fa-comment-dots"></i></a></li>
                <?php endif; ?>


            <?php endif; ?>
            <?php if ($cms_type == "Wedding") : ?>
                <li><a class="nav-link" href="index.php">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($guest_list_status == "On") : ?>
                    <li><a class="nav-link" href="guest_list.php">Guest List <i class="fa-solid fa-people-group"></i></a></li>
                <?php endif; ?>
                <?php if ($invite_manager_status == "On") : ?>
                    <li><a class="nav-link" href="invitations.php">Invitations <i class="fa-solid fa-champagne-glasses"></i></a></li>
                <?php endif; ?>
                <?php if ($guest_messaging_status == "On") : ?>
                    <li><a class="nav-link" href="messaging.php">Guest Messages <i class="fa-solid fa-message"></i></a></li>
                <?php endif; ?>
                <?php if ($gift_list_status == "On") : ?>
                    <li><a class="nav-link" href="gift_list.php">Gift List <i class="fa-solid fa-gifts"></i></a></li>
                <?php endif; ?>
                <?php if ($image_gallery_status == "On") : ?>
                    <li><a class="nav-link" href="gallery.php">Image Gallery <i class="fa-solid fa-image"></i></a></li>
                <?php endif; ?>
                <?php if ($news_status == "On") : ?>
                    <li><a class="nav-link" href="news.php">News <i class="fa-solid fa-newspaper"></i></a></li>
                <?php endif; ?>
                <?php if ($user_type == "Admin" || $user_type=="Developer") : ?>
                    <li><a class="nav-link" href="events.php">Events <i class="fa-solid fa-calendar-day"></i></a></li>
                <?php endif; ?>

            <?php endif; ?>
            <?php if ($user_type == "Developer") : ?>
                    <li><a class="nav-link" href="cms_settings.php">CMS Settings <i class="fa-solid fa-gear"></i></a></li>
            <?php endif; ?>
        </ul>

    </div>
</nav>