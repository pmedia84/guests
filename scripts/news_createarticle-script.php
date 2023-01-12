<?php
$response = "";
include("../connect.php");
if (isset($_POST['news_articles_title'])) {
    if ($_POST['action'] == "addnew") {
        if ($_POST['news_article_body'] == "") {
            $response = '<div class="form-response error"><p>Error, no article has been created. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
            echo $response;
            exit();
        }
        //check that an image has been uploaded
        if ($_FILES['news_articles_img']['name'] == null) {
            //header image name is blank if no file uploaded
            $news_articles_img = "";
        } else { //if there is an image uploaded then save it to the folder
            //////////////////////sort the image upload first////////////////////////////////////////
            $dir = "../assets/img/news/";
            $file = $dir . basename($_FILES['news_articles_img']['name']);
            $imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["news_articles_img"]["tmp_name"]);
            if ($check !== false) {
                $upload = 1;
            } else {
                $response = '<div class="form-response error"><p>File type not supported. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
            }
            // Check file size
            if ($_FILES["news_articles_img"]["size"] > 1048576) {
                $response = '<div class="form-response error"><p>Image size is too large.</p><a href=""news_createarticle.php>Try Again</a></div>';
                $upload = 0;
            }
            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"

            ) {
                $response = '<div class="form-response error"><p>Sorry, only JPG, JPEG, PNG images are supported.</p><a href=""news_createarticle.php>Try Again</a></div>';
                $uploadOk = 0;
            }

            // Check if $upload is set to 0 by an error
            if ($upload == 0) {
                $response = '<div class="form-response error"><p>Error, your article image was not saved. You can try again by editing your image</p></div>';
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["news_articles_img"]["tmp_name"], $file)) {
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
            //define articles img variable
            $news_articles_img = basename($_FILES['news_articles_img']['name']);
        }

        //define all other variables
        $news_articles_title = mysqli_real_escape_string($db, $_POST['news_articles_title']);
        $news_articles_body = htmlentities($_POST['news_article_body']);
        $news_articles_status = $_POST['news_articles_status'];
        $user_id = $_POST['user_id'];
        date_default_timezone_set('Europe/London');
        $article_date = date('y-m-d');
        //insert new article into table
        $new_article = $db->prepare('INSERT INTO news_articles (news_articles_title, news_articles_date, news_articles_body, news_articles_img, news_articles_author, news_articles_status)VALUES(?,?,?,?,?,?)');
        $new_article->bind_param('ssssis', $news_articles_title, $article_date, $news_articles_body, $news_articles_img, $user_id, $news_articles_status);
        $new_article->execute();
        $new_article->close();
        //get the id number of the last article created
        $new_article_id = $db->insert_id;


        $response = '<div class="form-response"><p>Article saved. You can no longer edit it from this page. Click <a href="http://localhost/admin/news_article.php?action=view&news_articles_id='.$new_article_id.'">Here</a></p></div>';
    }

    if ($_POST['action'] == "edit") {
        //check that an image has been uploaded
        if ($_FILES['news_articles_img']['name'] == null) {
            //dont overwrite the exisiting image file name
            $news_articles_title = mysqli_real_escape_string($db, $_POST['news_articles_title']);
            $news_articles_body = htmlentities($_POST['news_article_body']);
            $news_articles_status = $_POST['news_articles_status'];
            $news_articles_id = $_POST['news_articles_id'];
            
            //Update news Article
            $update_article = $db->prepare('UPDATE news_articles SET news_articles_title=?, news_articles_body=?, news_articles_status=?  WHERE news_articles_id =?');
            $update_article->bind_param('sssi', $news_articles_title, $news_articles_body, $news_articles_status, $news_articles_id);
            $update_article->execute();
            $update_article->close();
            $response = '<div class="form-response"><p>Article saved.</p></div>';
        } else {
            //if a new image has been uploaded then run update everything including the image
            //if there is an image uploaded then save it to the folder
            //////////////////////sort the image upload first////////////////////////////////////////
            $dir = "../assets/img/news/";
            $file = $dir . basename($_FILES['news_articles_img']['name']);
            $imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["news_articles_img"]["tmp_name"]);
            if ($check !== false) {
                $upload = 1;
            } else {
                $response = '<div class="form-response error"><p>File type not supported. Please try again.</p><a href=""news_createarticle.php>Try Again</a></div>';
            }
            // Check file size
            if ($_FILES["news_articles_img"]["size"] > 1048576) {
                $response = '<div class="form-response error"><p>Image size is too large.</p><a href=""news_createarticle.php>Try Again</a></div>';
                $upload = 0;
            }
            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"

            ) {
                $response = '<div class="form-response error"><p>Sorry, only JPG, JPEG, PNG images are supported.</p><a href=""news_createarticle.php>Try Again</a></div>';
                $uploadOk = 0;
            }

            // Check if $upload is set to 0 by an error
            if ($upload == 0) {
                $response = '<div class="form-response error"><p>Error, your article image was not saved. You can try again by editing your image</p></div>';
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["news_articles_img"]["tmp_name"], $file)) {
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
            //define articles img variable
            $news_articles_img = basename($_FILES['news_articles_img']['name']);
            //define other variables
            $news_articles_title = mysqli_real_escape_string($db, $_POST['news_articles_title']);
            $news_articles_body = htmlentities($_POST['news_article_body']);
            $news_articles_status = $_POST['news_articles_status'];
            $news_articles_id = $_POST['news_articles_id'];
            //Update news Article
            $update_article = $db->prepare('UPDATE news_articles SET news_articles_title=?, news_articles_body=?,news_articles_img=?, news_articles_status=?  WHERE news_articles_id =?');
            $update_article->bind_param('ssssi', $news_articles_title, $news_articles_body,$news_articles_img, $news_articles_status, $news_articles_id);
            $update_article->execute();
            $update_article->close();
            $response = '<div class="form-response"><p>Article saved.</p></div>';
        }
    }
}


echo $response;
