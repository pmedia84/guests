
<div class="header">
    <h1 class="header-name"><?php date_default_timezone_set("Europe/London");
                            $h = date('G');
                            if ($h >= 5 && $h <= 11) {
                                echo "Good morning";
                            } else if ($h >= 12 && $h <= 15) {
                                echo "Good Afternoon";
                            } else {
                                echo "Good Evening";
                            }
                            ?> <?php if(!empty($_SESSION['user_name'])){
                                    echo $_SESSION['user_name'];
                            } ; ?></h1>
    <div class="header-actions">
        <div class="header-actions-btn-section">
            <div class="header-actions-navbtn">
                <a href="#" class="nav-btn" id="nav-btn"><img src="assets/img/icons/menu-bars.svg" alt=""></a>
            </div>

            <a class="header-actions-btn-user" href="">
                <img src="assets/img/icons/user.svg" alt="">
                <img src="assets/img/icons/down.svg" alt="">
            </a>
            <div class="header-actions-business-name">
                <h1><?php if ($cms_type == "Business") {
                        echo $business_name;
                    } else {
                        echo $wedding_name . '\'s Wedding';
                    } ?></h1>
            </div>
            <a class="header-actions-btn-logout" href="logout.php"><span>Logout</span><img src="assets/img/icons/logout.svg" alt=""></a>
        </div>
    </div>
</div>