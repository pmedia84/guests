<?php 
session_start();
include("../connect.php");

if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
    echo"hello";
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    
}

//check if the post has been transferred from login form

if(!isset($_POST['user_email'], $_POST['password'])){
    exit('Error');
    echo"no post";
}


if($user = $db->prepare('SELECT user_id, user_pw, user_name FROM users WHERE user_email = ?')){
    
    
    $user ->bind_param('s',$_POST['user_email']);
    $user->execute();
    $user->store_result();

    if($user->num_rows >0){
        
        //check the user exists
        $user->bind_result($user_id, $password, $username);
        $user->fetch();
        //verify password
     
       if(password_verify($_POST['password'], $password)){
        //check if the password is a temp one from new user setup
        $pw_check = $db->prepare('SELECT user_id, user_pw_status FROM users WHERE user_email = ?');
        $pw_check ->bind_param('s',$_POST['user_email']);
        $pw_check->execute();
        $pw_check->bind_result($user_id, $user_pw_status);
        $pw_check->fetch();
        $pw_check->close();
        if($user_pw_status =="TEMP"){
            echo "TEMP";
            exit();
        }
        ////create session in db user sessions:////
        //declare time and date variables
        date_default_timezone_set('Europe/London');
        $session_date = date('d-m-y');
        $session_time = date('h:i:s');
        $session_status = "Active";
        $session = $db->prepare('INSERT INTO user_sessions (user_id, session_date, session_time, session_status)VALUES(?,?,?,?)');
        $session ->bind_param('ssss',$user_id, $session_date, $session_time, $session_status);
        $session ->execute();
        //remove old failed login attempts and only leave the current active session.
        $remove = "DELETE FROM user_sessions WHERE session_status = 'Failed' AND user_id=".$user_id;
        $submit = $db->query($remove);

        $session->close();

        //set up php session variables and pass information to browser
        $status = "Failed";
        $session_id_query ="SELECT session_id FROM user_sessions WHERE user_id=".$user_id." ORDER BY session_id DESC LIMIT 1";
        $session_id_result= $db->query($session_id_query);
        $session_id = $session_id_result->fetch_assoc();

        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['user_email'] = $_POST['user_email'];
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $username;
        $_SESSION['db_session_id']=$session_id['session_id'];
        $db->close();
        echo"correct";
       }else{
        //look up how many failed login attempts have been made and return an error message to reset password if there are more than 2 failed attempts.
        $status = "Failed";
        $failed =$db->prepare('SELECT session_id, user_id, session_status FROM user_sessions WHERE user_id = ? AND session_status = ?');
        $failed ->bind_param('ss',$user_id,$status );
        $failed->execute();
        $failed->store_result();
        if($failed->num_rows > 2){
            $loginerrmsg='<div class="form-response error"><p>You have had more than 2 unsuccessful login attempts!</p>
                            <p>Consider resetting your password</p><a href="resetpw.php">Reset Password</a>            
            </div>';
            
            echo$loginerrmsg;
        }else{//if there is less than two, send back a message that the password is wrong
            $loginerrmsg='<div class="form-response error"><p>Username and/or Password incorrect, please try again</p></div>';
            echo $loginerrmsg;
        }
        $failed->close();
        //enter into session table the failed attempt
        date_default_timezone_set('Europe/London');
        $session_date = date('d-m-y');
        $session_time = date('h:i:s');
        $session_status = "Failed";
        $session = $db->prepare('INSERT INTO user_sessions (user_id, session_date, session_time, session_status)VALUES(?,?,?,?)');
        $session ->bind_param('ssss',$user_id, $session_date, $session_time, $session_status);
        $session ->execute();
        $session->close();
       }

    }else{
        echo"User Not Found";
    }

    $user->close();
}
