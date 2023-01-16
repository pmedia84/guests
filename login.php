<?php
session_start();
//find the referring page to redirect to once logged in
if(empty($_SERVER['HTTP_REFERER'])){
    $redirect = "";
   
}else{
    $redirect = $_SERVER['HTTP_REFERER'];
}
include("connect.php");
include("inc/settings.php");


//run checks to make sure a wedding has been set up correctly
if($cms_type =="Wedding"){
    
    //look for a wedding setup in the db, if not then direct to the setup page
    $wedding_query = ('SELECT wedding_id, wedding_name FROM wedding');
    $wedding = $db->query($wedding_query);
    if($wedding -> num_rows ==0){
        header('Location: setup.php?action=setup_wedding');
    }
    $wedding_result = $wedding->fetch_assoc();
    //check that there are users set up 
    $wedding_user_query = ('SELECT wedding_user_id FROM wedding_users');
    $wedding_user = $db->query($wedding_user_query);
    if($wedding_user -> num_rows <2){
        header('Location: setup.php?action=check_users_wedding');
    }
    



    }

?>
<?php include("./inc/head.inc.php"); ?>
<!-- Meta Tags For Each Page -->
<meta name="description" content="Parrot Media - Client Admin Area">
<meta name="title" content="Manage your website content">
<!-- /Meta Tags -->

<!-- / -->
<!-- Page Title -->
<title>Guest Area | Login</title>
<!-- /Page Title -->
</head>

<body>
    <!-- Main Body Of Page -->
    <main class="main login-main">
    <div class="header">

<div class="header-actions login-header">
    <h1><?=$wedding_result['wedding_name'];?>'s Guest Area</h1>
</div>
</div>
        <div class="login-wrapper">
            <h1>Login</h1>
            <form class="form-card" id="login" action="scripts/auth.php" method="post">
                <div class="form-input-wrapper">
                    <label for="user_email">eMail Address:</label>
                    <!-- input -->
                    <input  type="text" name="user_email" id="user_email" placeholder="Enter Email Address" autocomplete="email" required="" maxlength="45">
                </div>

                <div class="form-input-wrapper">
                    <label for="password">Password:</label>
                    <!-- input -->
                    <input class="text-input input" type="password" name="password" id="password" placeholder="Your Password*" autocomplete="current-password" required="" maxlength="45">
                </div>

                <div class="button-section my-3">
                    <button class="btn-primary" type="submit">Login</button>
                </div>
                <div id="response" class="d-none">
                </div>
            </form>
        </div>







    </main>
    <!-- /Main Body Of Page -->

    <!-- Footer -->
    <?php include("./inc/footer.inc.php"); ?>
    <!-- /Footer -->
    <script>
        $("#login").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($("#login").get(0));
            var user_email = $("#user_email").val();
            $.ajax({ //start ajax post
                type: "POST",
                url: "scripts/auth.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data, responseText) {
                    $("#response").html(data);
                    $("#response").slideDown(400);
                    if (data === 'correct') {
                        window.location.replace('index.php');
                    }
                    if (data === 'TEMP') {
                        window.location.replace('resetpw.php?action=temp&user_email='+user_email);
                    }

                }
            });
        })
    </script>
</body>

</html>