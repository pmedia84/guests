<?php
session_start();
require("scripts/functions.php");
check_login();
$user = new User();
$wedding = new Wedding();
include("connect.php");
include("inc/head.inc.php");
include("inc/settings.php");
if ($meal_choices_m->status() == "Off") {
    header("Location: index");
}
//run checks to make sure a wedding has been set up correctly
if ($cms_type == "Wedding") {
    //look for the Wedding set up and load information


    //check to see if a meal choices order has been set up for this guest.
    $meal_order_q = $db->query('SELECT choice_order_id FROM meal_choice_order WHERE guest_id=' . $user->guest_id());
    // find the guest group that this user manages
    $guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
    $group_id_result = $guest_group_id_query->fetch_assoc();
    //define guest group id
    $guest_group_id = $group_id_result['guest_group_id'];
    //loads guest group list
    $group_query = $db->query('SELECT guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_id, guest_list.guest_group_id, guest_list.guest_type, guest_groups.guest_group_id, guest_groups.guest_group_name FROM guest_list LEFT JOIN guest_groups ON guest_groups.guest_group_id=guest_list.guest_group_id  WHERE guest_groups.guest_group_id=' . $guest_group_id . ' AND guest_list.guest_type = "Member"');

}

//////////////////////////////////////////////////////////////////Everything above this applies to each page\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//load details of meal options and the menu
//find the event this guest is invited to
$event_query = $db->query('SELECT event_id FROM invitations WHERE guest_id=' . $user->guest_id());
$event_result = mysqli_fetch_assoc($event_query);
$event_id = $event_result['event_id'];
$menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id WHERE menu.event_id=' . $event_id);
$menu_result = mysqli_fetch_assoc($menu_query);
$menu_id = $menu_result['menu_id'];
$guest_type_q = $db->query('SELECT guest_type FROM guest_list WHERE guest_id=' . $user->guest_id());
$guest_type_r = mysqli_fetch_assoc($guest_type_q);
?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media Wedding Admin - Guest Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Guest Area | My Meal Choices</title>
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
                <a href="index" class="breadcrumb">Home</a> / Provide Meal Choices

            </div>
            <div class="main-cards">
                <h1><i class="fa-solid fa-utensils"></i> Provide Meal Choices</h1>
                <?php if (empty($_GET)) : ?>
                    <div class="std-card">
                        <?php if ($menu_query->num_rows > 0) : ?>
                            <?php foreach ($menu_query as $menu) :
                                $menu_query = $db->query('SELECT menu.menu_name, menu.menu_id, menu.event_id, wedding_events.event_id, wedding_events.event_name FROM menu LEFT JOIN wedding_events ON wedding_events.event_id=menu.event_id WHERE menu.menu_id=' . $menu['menu_id']);
                                $menu_result = mysqli_fetch_assoc($menu_query);
                                $menu_courses = $db->query('SELECT course_name, course_id FROM menu_courses');
                                $meal_choices_q = $db->query('SELECT menu_items.menu_item_id, menu_items.menu_item_name, menu_items.course_id, meal_choices.menu_item_id, meal_choices.choice_order_id, meal_choice_order.choice_order_id, meal_choice_order.guest_id, menu_courses.course_name, menu_courses.course_id  FROM menu_items LEFT JOIN meal_choices ON meal_choices.menu_item_id=menu_items.menu_item_id LEFT JOIN meal_choice_order ON meal_choices.choice_order_id=meal_choice_order.choice_order_id LEFT JOIN menu_courses ON menu_courses.course_id=menu_items.course_id WHERE meal_choice_order.guest_id=' . $user->guest_id());
                            ?>
                                <?php if ($menu_query->num_rows > 0) : ?>
                                    <div class="menu my-3" id="menus">
                                        <h2><?= $menu_result['menu_name']; ?> Menu</h2>
                                        <p>For Our</p>
                                        <p><?= $menu['event_name']; ?></p>
                                        <hr>
                                        <?php
                                        if ($menu_courses->num_rows > 0) :
                                            foreach ($menu_courses as $course) :
                                                $menu_item = $db->query('SELECT menu_item_id, menu_item_name, menu_item_desc, course_id, menu_id FROM menu_items WHERE course_id=' . $course['course_id'] . ' AND menu_id=' . $menu['menu_id']); ?>
                                                <h3><?= $course['course_name']; ?></h3>
                                                <?php if ($menu_item->num_rows > 0) :
                                                    foreach ($menu_item as $item) :  ?>
                                                        <div class="menu-item my-2">
                                                            <div class="menu-item-body">
                                                                <h4 class="menu-item-name"><?= $item['menu_item_name']; ?></h4>
                                                                <p class="menu-item-desc"><?= $item['menu_item_desc']; ?></p>
                                                            </div>
                                                        </div>
                                    <?php endforeach;
                                                endif;
                                                echo "<hr>";
                                            endforeach;
                                        endif;
                                    endif;
                                    ?>

                                    </div>
                                <?php endforeach; ?>
                            <?php 
                        endif; ?>
                    </div>
                    <div class="std-card">
                        <?php if ($meal_order_q->num_rows == 0) : ?>
                            <h2><?= $_SESSION['user_name']; ?>, please let us know your meal choices.</h2>
                            <div class="card-actions">
                                <a href="meal_choices?action=choose" class="btn-primary">Provide My Choices</a>
                            </div>
                        <?php else :
                            //load meal choices
                        ?>
                            <h2>Your meal choices</h2>
                            <p>If you want to change these, please <a href="../contact">Contact Us</a>.</p>
                            <?php if ($meal_choices_q->num_rows > 0) : ?>

                                <div class="menu my-3">
                                    <?php foreach ($meal_choices_q as $choice) : ?>
                                        <h3><?= $choice['course_name']; ?></h3>
                                        <p><?= $choice['menu_item_name']; ?></p>
                                        <hr>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($guest_type == "Group Organiser") : ?>
                        <div class="std-card">
                            <h2>Your Group Members Choices</h2>
                            <p>If any of your group wish to change their choices, please <a href="../contact">Contact Us</a></p>
                            <?php if($group_query->num_rows>0):
                                foreach($group_query as $member):
                               
                                    $meal_choices_g_q = $db->query('SELECT menu_items.menu_item_id, menu_items.menu_item_name, menu_items.course_id, meal_choices.menu_item_id, meal_choices.choice_order_id, meal_choice_order.choice_order_id, meal_choice_order.guest_id, menu_courses.course_name, menu_courses.course_id  FROM menu_items LEFT JOIN meal_choices ON meal_choices.menu_item_id=menu_items.menu_item_id LEFT JOIN meal_choice_order ON meal_choices.choice_order_id=meal_choice_order.choice_order_id LEFT JOIN menu_courses ON menu_courses.course_id=menu_items.course_id WHERE meal_choice_order.guest_id=' . $member['guest_id']);
                               ?>
                                <h2><?=$member['guest_fname'];?></h2>
                                <div class="menu my-3">
                                    <?php foreach ($meal_choices_g_q as $choice) : ?>
                                            <h3><?= $choice['course_name']; ?></h3>
                                            <p><?= $choice['menu_item_name']; ?></p>
                                            <hr>
                                        <?php endforeach; ?>
                                </div>
                                <?php endforeach;?>
                                <?php endif;?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($_GET['action']) && $_GET['action'] == "choose") :
                    //load menu courses  
                    $menu_courses = $db->query('SELECT course_name, course_id FROM menu_courses');

                ?>
                    <div class="std-card">
                        <h2>Your Choices</h2>
                        <p>Please let us know you meal choices, along with any additional guests you may be bringing</p>
                        <form action="POST" id="meal_choices" data-guest_name="<?=$_SESSION['user_name'];?>" data-guest_id="<?=$guest_id;?>">
                            
                            <div class="form-tab">
                            <h2><?= $_SESSION['user_name']; ?></h2>
                                <?php $count = 0;
                                foreach ($menu_courses as $course) :
                                    $menu_items = $db->query('SELECT menu_item_id, menu_item_desc, menu_item_name FROM menu_items WHERE menu_id=' . $menu_id . ' AND course_id=' . $course['course_id']); ?>
                                    <input type="hidden" name="guest_meal_choices[<?= $count; ?>][guest_id]" value="<?= $guest_id; ?>">
                                    <div class="input-form-wrapper meal-choice-card">
                                        <h2><?= $course['course_name']; ?></h2>
                                        <?php foreach ($menu_items as $menu_item) :
                                        ?>
                                            <label class="radio-label meal-choice"><?= $menu_item['menu_item_name']; ?>
                                                <p><?= $menu_item['menu_item_desc']; ?></p>
                                                <input type="radio" name="guest_meal_choices[<?= $count; ?>][menu_item_id]" value="<?= $menu_item['menu_item_id']; ?>" required>
                                                <span class="checkmark"></span>
                                            </label>
                                        <?php endforeach;
                                        $count++; ?>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($guest_type == "Group Organiser") :
                                //load group and provide more tabs

                            ?>
                                <?php foreach ($group_query as $member) : ?>
                                    <div class="form-tab">
                                        <h2><?=$member['guest_fname'];?></h2>
                                <?php 
                                foreach ($menu_courses as $course) :
                                    $menu_items = $db->query('SELECT menu_item_id, menu_item_desc, menu_item_name FROM menu_items WHERE menu_id=' . $menu_id . ' AND course_id=' . $course['course_id']); ?>
                                    <input type="hidden" name="guest_meal_choices[<?= $count; ?>][guest_id]" value="<?= $member['guest_id']; ?>">
                                    <div class="input-form-wrapper meal-choice-card">
                                        <h2><?= $course['course_name']; ?></h2>
                                        <?php foreach ($menu_items as $menu_item) :
                                        ?>
                                            <label class="radio-label meal-choice"><?= $menu_item['menu_item_name']; ?>
                                                <p><?= $menu_item['menu_item_desc']; ?></p>
                                                <input type="radio" name="guest_meal_choices[<?= $count; ?>][menu_item_id]" value="<?= $menu_item['menu_item_id']; ?>">
                                                <span class="checkmark"></span>
                                            </label>
                                        <?php endforeach;
                                        $count++; ?>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="form-progress" id="progress">
                                
                                
                            </div>
                            <div class="form-actions">
                                <button id="prev" type="button" class="btn-primary btn-secondary">Previous</button>
                                <button id="next" type="button" class="btn-primary form-controls-btn loading-btn"><span id="btn-text">Next</span> <img id="loading-icon" class="loading-icon d-none" src="./assets/img/icons/loading.svg" alt=""></button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class=" response-card-wrapper d-none" id="response-card-wrapper">
            <div class="response-card">
                <div class="response-card-icon">
                <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="response-card-body">
                    <p id="response-card-text"></p>
                </div>
            </div>
        </div>
    </main>
<?php $db->close();?>
    <!-- /Main Body Of Page -->


    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <?php if (isset($_GET['action']) && $_GET['action'] == "choose") : ?>
        <script src="assets/js/meal_choices.js"></script>
    <?php endif; ?>

</body>

</html>