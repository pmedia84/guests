<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="btn btn-close" id="nav-btn-close"></button>
        </div>
        <ul class="nav-links">

            <?php if ($cms_type == "Wedding"):?>
                <li><a class="nav-link" href="index">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($guest_list_status == "On" && $guest_type=="Group Organiser"):?>
                    <li><a class="nav-link" href="guest_group">My Guest Group <i class="fa-solid fa-people-group"></i></a></li>
                <?php endif;?>
                <?php if ($invite_manager_status == "On"):?>
                    <li><a class="nav-link invite" href="invite">My Invitation <i class="fa-solid fa-champagne-glasses"></i></a><?php if($user_invite_rsvp_status == NULL):?><div class="alert-icon"><i class="fa-solid fa-circle"></i></div><?php endif;?></li>
                <?php endif; ?>
                <?php if ($guest_messaging_status == "On"):?>
                    <li><a class="nav-link" href="messaging">Message Us <i class="fa-solid fa-message"></i></a></li>
                <?php endif; ?>

                <?php if ($image_gallery_status == "On"):?>
                    <li><a class="nav-link" href="gallery">Image Gallery <i class="fa-solid fa-image"></i></a></li>
                <?php endif; ?>
                <li><a class="nav-link" href="profile">My Profile <i class="fa-solid fa-address-book"></i></a></li>    


            <?php endif; ?>

        </ul>

    </div>
</nav>