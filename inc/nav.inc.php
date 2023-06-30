<nav class="nav-bar">
    <div class="nav-container">
        <div class="close">
            <button class="btn btn-close" id="nav-btn-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <ul class="nav-links">
            <?php if ($cms_type == "Wedding"):?>
                <li><a class="nav-link" href="index">Home <i class="fa-solid fa-house"></i></a></li>
                <?php if ($guest_list_m->status() == "On" && $guest_type=="Group Organiser"):?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "guest_group")){echo"link-active";}?>" href="guest_group">My Guest Group <i class="fa-solid fa-people-group"></i></a></li>
                <?php endif;?>
                <?php if($invite_manager->status() == "On"):?>
                    <li><a class="nav-link invite <?php if(str_contains($_SERVER['REQUEST_URI'], "invite")){echo"link-active";}?>" href="invite">My Invitation <i class="fa-solid fa-champagne-glasses"></i></a><?php if($user_invite_rsvp_status == NULL || $user_invite_rsvp_status =="Not Replied"):?><div class="alert-icon"><i class="fa-solid fa-circle"></i></div><?php endif;?></li>
                <?php endif; ?>
                <?php if($meal_choices_m->status() == "On" && $user->meal_choices()==1):?>
                    <li><a class="nav-link invite <?php if(str_contains($_SERVER['REQUEST_URI'], "meal")){echo"link-active";}?>" href="meal_choices">My Meal Choices <i class="fa-solid fa-utensils"></i></a></li>
                <?php endif; ?>
                <!-- <?php if($guest_messaging->status() == "On"):?>
                    <li><a class="nav-link" href="messaging">Message Us <i class="fa-solid fa-message"></i></a></li>
                <?php endif;?> -->
                <?php if($guest_image_gallery->status() == "On" ):?>
                    <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "gallery")){echo"link-active";}?>" href="gallery">Photo Gallery <i class="fa-solid fa-images"></i></a></li>
                <?php endif;?>
                <li><a class="nav-link <?php if(str_contains($_SERVER['REQUEST_URI'], "profile")){echo"link-active";}?> " href="profile">My Profile <i class="fa-solid fa-address-book"></i></a></li>
                <li><a class="nav-link" href="logout.php">Return to Website <i class="fa-solid fa-laptop"></i></a></li>
            <?php endif;?>
        </ul>
    </div>
</nav>