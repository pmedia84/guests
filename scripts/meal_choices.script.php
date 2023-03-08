<?php 
if (isset($_POST['action']) && $_POST['action'] == "set_choices") {
    include("../connect.php");
     $meal_choices = $db->prepare('INSERT INTO meal_choices (menu_item_id, choice_order_id)VALUES(?,?)');
     foreach($_POST['guest_meal_choices'] as $guest_choice){
        //check to see if a meal choices order has been set up for this guest.
        $meal_order_q = $db->query('SELECT choice_order_id FROM meal_choice_order WHERE guest_id='.$guest_choice['guest_id']);
        if($meal_order_q->num_rows >0){
            $meal_order_r=mysqli_fetch_assoc($meal_order_q);
            $choice_order_id = $meal_order_r['choice_order_id'];
            
        }else{
            //if not then set one up
            $new_choice_order = $db->query('INSERT INTO meal_choice_order (guest_id) VALUES('.$guest_choice['guest_id'].')');
            $choice_order_id = $db->insert_id;
            
        }
        //next add the meal choices
        $meal_choices->bind_param("ii", $guest_choice['menu_item_id'], $choice_order_id);
        $meal_choices->execute();
     }
     $meal_choices->close();
    
    exit("1");
}
?>