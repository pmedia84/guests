<?php 
if(isset($_GET['action'])){
    include("../connect.php");
    //if GET request comes from clicking button
    if($_GET['action'] == "loadreviews"){
        $reviews_query = ('SELECT * FROM reviews ORDER BY reviews_date_time DESC');
        $reviews = $db->query($reviews_query);
        $result_num = $reviews->num_rows;
        if($result_num >= 1){
            foreach ($reviews as $review) {
                $rating = $review['reviews_rating'];
                switch ($rating) {
                    case "1":
                      $rating= "                   
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>";
                      break;
                    case "2":
                      $rating="
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>";
                      break;
                    case "3":
                        $rating="
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color'></i>
                        <i class='fa fa-star star-color'></i>";
                      break;
                      case "4":
                        $rating="
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color'></i>";
                        break;
                        case "5":
                            $rating="
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>";
                            break;
                    default:
                    $rating= "                   
                    <i class='fa fa-star star-color star-color-rated'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>";
                  }

                //define image sections as set out from checkboxes 
                echo "<div class='std-card review-card my-3'>
                        <h2>".$review['reviews_author_name']."</h2>
                        <p>".$review['reviews_relative_time_description']."</p>
                        <div class='stars'>
                            ".$rating."
                        </div>
                        <p>".$review['reviews_text']."</p>



                    
    
                
                </div>";
            }
        }

    }

    if($_GET['action']=="download"){
      //perform get request and find reviews
      $place_id = "ChIJReBlEir510cRYIV3Yjs3hbk";
      $api_key ="AIzaSyBzvazSCRnX-e9GJ2_NUqvPMR1r3BWipDs";
      $url = "https://maps.googleapis.com/maps/api/place/details/json?fields=reviews&place_id=$place_id&key=$api_key&reviews_sort=newest";
      $review_data = json_decode($url, true);
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_REFERER, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      $str = curl_exec($curl);
      // get http code for error handling
      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      $response = json_decode($str, true);
      $reviews = $response['result']['reviews'];

      //remove any old reviews from table first
      $delete_reviews = "DELETE FROM reviews";
      if(mysqli_query($db, $delete_reviews)){
        //successfully removed old reviews, now insert the new ones
        foreach($reviews as $review){
            $author_name = $db->escape_string($review['author_name']);
            $author_url = $review['author_url'];
            $profileimg = $db->real_escape_string($review['profile_photo_url']);
            $rating = $db->escape_string($review['rating']);
            $relative_time = $db->escape_string($review['relative_time_description']);
            $text = $db->escape_string($review['text']);
            $time = $db->escape_string($review['time']);
            $date = date('Y-m-d H:i:s', $time);
            mysqli_query($db, "INSERT INTO `reviews` (reviews_author_name, reviews_author_url, reviews_profile_photo_url, reviews_rating, reviews_relative_time_description, reviews_text, reviews_date_time) VALUES ('$author_name','$author_url','$profileimg','$rating','$relative_time','$text','$date')") or die($db->error);
        }
        $reviews_query = ('SELECT * FROM reviews ORDER BY reviews_date_time DESC');
        $reviews = $db->query($reviews_query);
        $result_num = $reviews->num_rows;
        if($result_num >= 1){
            foreach ($reviews as $review) {
                $rating = $review['reviews_rating'];
                switch ($rating) {
                    case "1":
                      $rating= "                   
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>";
                      break;
                    case "2":
                      $rating="
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color star-color-rated'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>
                      <i class='fa fa-star star-color'></i>";
                      break;
                    case "3":
                        $rating="
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color'></i>
                        <i class='fa fa-star star-color'></i>";
                      break;
                      case "4":
                        $rating="
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color star-color-rated'></i>
                        <i class='fa fa-star star-color'></i>";
                        break;
                        case "5":
                            $rating="
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>
                            <i class='fa fa-star star-color star-color-rated'></i>";
                            break;
                    default:
                    $rating= "                   
                    <i class='fa fa-star star-color star-color-rated'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>
                    <i class='fa fa-star star-color'></i>";
                  }

                //define image sections as set out from checkboxes 
                echo "<div class='std-card review-card my-3'>
                        <h2>".$review['reviews_author_name']."</h2>
                        <p>".$review['reviews_relative_time_description']."</p>
                        <div class='stars'>
                            ".$rating."
                        </div>
                        <p>".$review['reviews_text']."</p>



                    
    
                
                </div>";
            }
        }
    }else{
         echo'<div class="form-response error"><p>Error deleting article, please try again.</p></div>';
     }
    }
   
}
