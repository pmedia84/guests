
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
            <a href="#" class="nav-btn" id="nav-btn"><i class="fa-solid fa-bars"></i></a>
            </div>

            <a class="header-actions-btn-user" href="profile">
            <i class="fa-solid fa-user"></i>
            </a>
            <div class="header-actions-business-name">
                <h1><?php  { echo $wedding_name . '\'s Wedding';} ?></h1>
            </div>
            <a class="header-actions-btn-logout" href="logout.php"><span>Logout</span><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>
</div>