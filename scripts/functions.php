<?php
function check_login()
{
    if (!isset($_SESSION['loggedin'])) {
        $location = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?location=" . $location);
    }
}

class Module
{

    public $name;
    public $status;

    function module_name($name)
    {

        $this->name = $name;
    }
    function status()
    {
        include("../connect.php");
        $modules_query = $db->query('SELECT module_status FROM modules WHERE module_name= "' . $this->name . '"');
        $modules_r = mysqli_fetch_assoc($modules_query);
        $module_status = $modules_r['module_status'];
        $this->status = $module_status;
        return $this->status;
        $db->close();
    }
}
//*modules
$guest_list_m = new Module();
$guest_list_m->module_name("Guest List");

$news_m = new Module();
$news_m->module_name("News");

$image_gallery = new Module();
$image_gallery->module_name("Image Gallery");

$events = new Module();
$events->module_name("Events");

$price_list = new Module();
$price_list->module_name("Price List");

$invite_manager = new Module();
$invite_manager->module_name("Invite Manager");

$guest_messaging = new Module();
$guest_messaging->module_name("Guest Messaging");

$gift_list_m = new Module();
$gift_list_m->module_name("Gift List");

$menu_builder = new Module();
$menu_builder->module_name("Menu Builder");

$meal_choices_m = new Module();
$meal_choices_m->module_name("Meal Choices");

$guest_image_gallery = new Module();
$guest_image_gallery->module_name("Guest Image Gallery");
class Wedding_module
{
    public $name;
    public $status;

    function module_name($name)
    {

        $this->name = $name;
    }
    function status()
    {
        include("../connect.php");
        $modules_query = $db->query('SELECT wedding_module_status FROM wedding_modules WHERE wedding_module_name= "' . $this->name . '"');
        $modules_r = mysqli_fetch_assoc($modules_query);
        $module_status = $modules_r['wedding_module_status'];
        $this->status = $module_status;
        return $this->status;
        $db->close();
    }
}
//build the wedding modules
$guest_area = new Wedding_module();
$guest_area->module_name("Guest Area");
$guest_add_remove = new Wedding_module();
$guest_add_remove->module_name("Add & Remove Guests");
$meal_choices_wedmin = new Wedding_module();
$meal_choices_wedmin->module_name("Meal Choices");
$guest_area_gallery = new Wedding_module();
$guest_area_gallery->module_name("Guest Image Gallery");


//* User class for the login system and using the details throughout the script

class User
{
    public $user_id;
    public $user_type;
    public $user_name;
    public $logged_in;
    public $guest_id;
    public $guest_type;
    public $guest_group_id;
    public $menu;

    
    function user_id()
    {
        $this->user_id = $_SESSION['user_id'];
        return $this->user_id;
    }
    function logged_id()
    {
        $this->logged_in = $_SESSION['logged_in'];
        return $this->logged_in;
    }
    function user_type()
    {
        include("../connect.php");
        $user_type_q = $db->query("SELECT user_type FROM users WHERE user_id=" . $this->user_id() . "");
        $user_type_r = mysqli_fetch_assoc($user_type_q);
        $type = $user_type_r['user_type'];
        $this->user_type = $type;
        return $this->user_type;
    }
    function guest_id()
    {
        include("../connect.php");
        $q = $db->query("SELECT guest_id FROM users WHERE user_id=" . $this->user_id() . "");
        $r = mysqli_fetch_assoc($q);
        $this->guest_id = $r['guest_id'];
        return $this->guest_id;
    }
    function user_name()
    {
        include("../connect.php");
        $q = $db->query("SELECT user_name FROM users WHERE user_id=" . $this->user_id() . "");
        $r = mysqli_fetch_assoc($q);
        $this->user_name = $r['user_name'];
        return $this->user_name;
    }
    function meal_choices()
    {
        //find if this guest needs to provide meal choices
        include("../connect.php");
        $q = $db->query("SELECT wedding_events.event_id, wedding_events.event_name, menu.event_id, menu.menu_id, menu.menu_name, invitations.event_id, invitations.guest_id FROM wedding_events LEFT JOIN menu ON menu.event_id = wedding_events.event_id LEFT JOIN invitations ON invitations.event_id=wedding_events.event_id WHERE invitations.guest_id=" . $this->guest_id);
        $r = mysqli_fetch_assoc($q);

        if($r['menu_id']==NULL){
            $this->menu=0;
            return $this->menu;
        }
        if($r['menu_id']>0){
            $this->menu=1;
            return $this->menu;
        }
        
        
        
    }
}



//*load wedding info with a class
class Wedding
{

    public $wedding_id;
    public $wedding_name;
    public $wedding_date;
    public $wedding_time;
    public $wedding_email;
    public $wedding_phone;
    public $wedding_contact_name;

    function __construct()
    {
        include("../connect.php");
        $q = $db->query("SELECT * FROM wedding LIMIT 1");
        $r = mysqli_fetch_assoc($q);
        $this->wedding_id = $r['wedding_id'];
        $this->wedding_name = $r['wedding_name'];
        $this->wedding_date = $r['wedding_date'];
        $this->wedding_time = $r['wedding_time'];
        $this->wedding_email = $r['wedding_email'];
        $this->wedding_phone = $r['wedding_phone'];
        $this->wedding_contact_name = $r['wedding_contact_name'];
        $db->close();
    }
    function wedding_id()
    {
        return $this->wedding_id;
    }
    function wedding_name()
    {
        return $this->wedding_name;
    }
    function wedding_date()
    {
        return $this->wedding_date;
    }
    function wedding_time()
    {
        return $this->wedding_time;
    }
    function wedding_email()
    {
        return $this->wedding_email;
    }
    function wedding_phone()
    {
        return $this->wedding_phone;
    }
    function wedding_contact_name()
    {
        return $this->wedding_contact_name;
    }
}
//error handler function

class Guest_img
{

    public $guest_id;
    public $guest_name;
    public $desc;
    // Total amount of images posted from user submission
    public $img_total;
    public $msg;
    public $response_code;
    public $status;
    public $placement;
    //image submission ID
    public $sub_id;
    //the amount of images that were not successful
    public $img_errors;
    //total amount of images successful
    public $success_img;
    //* Response Codes
    //200: Success
    //400: Error
    function __construct()
    {
        $this->status = "Awaiting";
        $this->placement = "Gallery";
        $this->img_errors = 0;
        $this->success_img = 0;
        $this->img_total = 0;
    }
    function guest_name($guest_name)
    {
        $this->guest_name = $guest_name;
        return $this->guest_name;
    }
    function desc()
    {
        $this->desc = "Contributed by " . $this->guest_name;
        return $this->desc;
    }
    function guest_id($guest_id)
    {
        $this->guest_id = $guest_id;
        return $this->guest_id;
    }
    function img_total()
    {


        return $this->img_total;
    }
    //?upload images and create a submission request for admin.
    function create()
    {

        //check the post method has been sent
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            $this->msg = "Request Method Not Set";
            $this->response_code = 400;
            return;
        }
        $this->img_total = count($_FILES['gallery_img']['name']);
        //insert into db    
        include("../connect.php");
        //default submission status
        $submission_status = "Awaiting";
        //prepare an update statement
        $submission = $db->prepare('UPDATE image_submissions SET submission_status=? WHERE submission_id=?');
        //find image submission ID from DB, if no results, create a new one.
        $sub_q = $db->query("SELECT submission_id FROM image_submissions WHERE guest_id=" . $this->guest_id);
        $sub_r = mysqli_fetch_assoc($sub_q);
        if ($sub_q->num_rows > 0) {
            $this->sub_id = $sub_r['submission_id'];
            //set the submission to awaiting, so it shows up in admin area
            $submission->bind_param("si", $this->status, $this->sub_id);
            $submission->execute();
            $submission->close();
        } else {
            $sub_q = $db->query("INSERT INTO image_submissions (submission_id, guest_id, submission_status) VALUES ('','" . $this->guest_id . "' ,'" . $submission_status . "')");
            $this->sub_id = $db->insert_id;
        }
        //prepare the insert query for images table
        $img = $db->prepare('INSERT INTO images (image_description, image_filename,  image_placement, guest_id, status, submission_id)VALUES(?,?,?,?,?,?)');
        $sub_items = $db->prepare('INSERT INTO image_sub_items (submission_id, image_id, sub_item_status)VALUES(?,?,?)');
        //set the file name
        $newimgname = "gallery-img-0.webp";
        //set the upload path
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
        foreach ($_FILES['gallery_img']['name'] as $key => $val) {
            // Reject uploaded file larger than 3MB
            //only process files that are below the max file size
            if ($_FILES["gallery_img"]["size"][$key] < 20971520) {
                //check for errors
                if ($_FILES['gallery_img']['error'][$key] !== UPLOAD_ERR_OK) {
                    switch ($_FILES['error']['gallery_img'][$key]) {
                        case UPLOAD_ERR_PARTIAL:
                            $this->msg = "File only partially uploaded";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $this->msg = "No file was uploaded";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $this->msg = "File upload stopped by a PHP extension";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $this->msg = "File exceeds MAX_FILE_SIZE in the HTML form";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_INI_SIZE:
                            $this->msg = "File exceeds upload_max_filesize in php.ini";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $this->msg = "Temporary folder not found";
                            $this->response_code = 400;
                            return;
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $this->msg = "Failed to write file";
                            $this->response_code = 400;
                            return;
                            break;
                        default:
                            $this->msg = "Unknown upload error";
                            $this->response_code = 400;
                            return;
                            break;
                    }
                }

                // Use fileinfo to get the mime type
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($_FILES["gallery_img"]["tmp_name"][$key]);
                $mime_types = ["image/gif", "image/png", "image/jpeg", "image/jpg"];
                if (!in_array($_FILES["gallery_img"]["type"][$key], $mime_types)) {
                    $this->msg = "Invalid file type, only JPG, JPEG, PNG or Gif is allowed. One of your files has the type of: " . $mime_type;
                    $this->response_code = 400;
                    return;
                }
                $i = 0;
                //if the file exists already, set a prefix
                while (file_exists($dir)) {
                    $newimgname = "gallery-img-" . $i . ".webp";
                    $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/" . $newimgname;
                    $i++;
                }
                // convert into webp
                $info = getimagesize($_FILES['gallery_img']['tmp_name'][$key]);
                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($_FILES['gallery_img']['tmp_name'][$key]);
                } elseif ($info['mime'] == 'image/gif') {
                    $image = imagecreatefromgif($_FILES['gallery_img']['tmp_name'][$key]);
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($_FILES['gallery_img']['tmp_name'][$key]);
                }
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                if ($info['mime'] == 'image/jpeg') {
                    //detect the orientation of the uploaded file
                    @$exif = exif_read_data($_FILES["gallery_img"]["tmp_name"][$key]);
                }
                //rotate the image after converting
                if (isset($exif['Orientation']) && $exif['Orientation'] == 6) {
                    $image = imagerotate($image, -90, 0);
                }
                //convert into a webp image, if unsuccessful then increment into the error variable 
                if (!imagewebp($image, $dir, 60)) {
                    $this->img_errors++;
                    return;
                } else {
                    //set up posting to db
                    $image_filename = $newimgname;

                    //insert into database
                    $img->bind_param('sssisi', $this->desc, $image_filename,  $this->placement, $this->guest_id, $this->status, $this->sub_id);
                    $img->execute();
                    $image_id = $img->insert_id;
                    $sub_item_status = "Awaiting";
                    $sub_items->bind_param('iis', $this->sub_id, $image_id, $sub_item_status);
                    $sub_items->execute();
                    echo $db->error;
                    /// copy to website paths
                    $guests_dir = $_SERVER['DOCUMENT_ROOT'] . "/guests/assets/img/gallery/";
                    //copy the image to the guest directory
                    if (!copy($dir, $guests_dir . $newimgname)) {
                        //if unsuccessful
                        $this->msg = "Images were not copied successfully";
                        $this->response_code = 400;
                        return;
                    } else {
                        //if successful increment the successful img count
                        $this->success_img++;
                        $this->response_code = 200;
                    }
                }
            } else {
                $this->img_errors++;
            }
        }
        $img->close();
    }
    //?Delete Images
    function delete()
    {
        //image array, contains the db image id and the filename
        $images = $_POST['gallery_img'];
        //define how many images have been request for delete
        $this->img_total = count($images);
        /// copy to website paths
        $guests_dir = $_SERVER['DOCUMENT_ROOT'] . "/guests/assets/img/gallery/";
        //admin file path for deleting the images
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/admin/assets/img/gallery/";
        //$this->img_total = count($_POST['image_id']);
        //loop through the image ID array and delete images
        include("../connect.php");
        //test the connection
        if (mysqli_connect_error()) {
            $this->msg = "Connect error" . mysqli_connect_error();
            $this->response_code = 400;
            return;
        }
        //check the post method has been sent
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            $this->msg = "Request Method Not Set";
            $this->response_code = 400;
            return;
        }
        //Loop through each image in the POST array, delete the files and the db entry
        foreach ($images as $image) {

            if ($db->query('DELETE FROM images WHERE image_id =' . $image['image_id'] . ' AND guest_id = ' . $this->guest_id)) {
                //increment the success total by one for each successful image deleted
                $this->success_img++;
                $this->response_code = 200;
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            }
            if (fopen($guests_dir . $image['image_filename'], "w")) {
                unlink($guests_dir . $image['image_filename']);
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            }
            if (fopen($dir . $image['image_filename'], "w")) {
                unlink($dir . $image['image_filename']);
            } else {
                $this->img_errors++;
                $this->response_code = 400;
                $this->msg = "Could not delete image";
                return;
            };
        }
    }
    //return the message
    function msg()
    {
        return $this->msg;
    }
    //return the response code
    function response_code()
    {
        return $this->response_code;
    }
    //return how many images have been uploaded
    function img_error_amt()
    {
        return $this->img_errors;
    }
    function img_success_amt()
    {
        return $this->success_img;
    }
}
