<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/admin/mailer/Exception.php';
if (isset($_POST['action']) && $_POST['action'] == "set_choices") {
    include("../connect.php");
    //find the name of the wedding
    $wed_name = $db->query('SELECT wedding_name, wedding_email FROM wedding LIMIT 1');
    $wed_name_r=mysqli_fetch_assoc($wed_name);
    $wedding_name = $wed_name_r['wedding_name'];
    $wedding_email = $wed_name_r['wedding_email'];
    $lead_guest_id = $_POST['guest_id'];

    $meal_choices = $db->prepare('INSERT INTO meal_choices (menu_item_id, choice_order_id)VALUES(?,?)');
    foreach ($_POST['guest_meal_choices'] as $guest_choice) {
        //check to see if a meal choices order has been set up for this guest.
        $meal_order_q = $db->query('SELECT choice_order_id FROM meal_choice_order WHERE guest_id=' . $guest_choice['guest_id']);
        if ($meal_order_q->num_rows > 0) {
            $meal_order_r = mysqli_fetch_assoc($meal_order_q);
            $choice_order_id = $meal_order_r['choice_order_id'];
        } else {
            //if not then set one up
            $new_choice_order = $db->query('INSERT INTO meal_choice_order (guest_id) VALUES(' . $guest_choice['guest_id'] . ')');
            $choice_order_id = $db->insert_id;
        }
        //next add the meal choices
        $meal_choices->bind_param("ii", $guest_choice['menu_item_id'], $choice_order_id);
        $meal_choices->execute();
    }
    $meal_choices->close();
    include("../inc/settings.php");
    //Send email to the wedding admins
    //load wedding details for email
    $wedding_query = ('SELECT wedding_id, wedding_name, wedding_email FROM wedding');
    $wedding = $db->query($wedding_query);
    $wedding_result = $wedding->fetch_assoc();
    //email subject
    $subject = $_POST['guest_name'] . " has provided their meal choices";
    //body of email to send to client as an auto reply
    //Lead guest Choices
    $choices_q = $db->query('SELECT meal_choices.menu_item_id, meal_choices.choice_order_id, menu_items.menu_item_name, menu_items.course_id, menu_courses.course_name, menu_courses.course_id, meal_choice_order.choice_order_id, meal_choice_order.guest_id, guest_list.guest_id  FROM meal_choices LEFT JOIN menu_items ON menu_items.menu_item_id=meal_choices.menu_item_id LEFT JOIN menu_courses ON menu_courses.course_id=menu_items.course_id LEFT JOIN meal_choice_order ON meal_choice_order.choice_order_id=meal_choices.choice_order_id LEFT JOIN guest_list ON guest_list.guest_id=meal_choice_order.guest_id WHERE guest_list.guest_id=' . $lead_guest_id . ' ORDER BY guest_list.guest_id, menu_courses.course_id  ');
    //guest choices into email, lead guest first
    $guest_choices = "";
    foreach ($choices_q as $choice) {
        $guest_choices .= "<h3>" . $choice['course_name'] . "</h3>";
        $guest_choices .= "<p>" . $choice['menu_item_name'] . "</p>";
    }
    //load group choices if this guest is a group organiser
    $group_choices = "";
    $guest_group_id = "";
    $group_num = $db->query("SELECT guest_group_id  FROM guest_groups WHERE guest_group_organiser=" . $lead_guest_id);
    if ($group_num->num_rows > 0) {
        $group_num_r = mysqli_fetch_assoc($group_num);
        $guest_group_id = $group_num_r['guest_group_id'];
        //load guest Group choices

        //load all group members
        $guest_group_q = $db->query('SELECT guest_id, guest_type, guest_fname, guest_sname, guest_group_id FROM guest_list WHERE guest_group_id=' . $guest_group_id . ' AND guest_type="Member"');
        $group_choices = "<div class='std-card'>";
        $group_choices .= "<h2>" . $_POST['guest_name'] . "'s Group Choices</h2>";
        $group_choices .= "<p>Here are their group's choices.</p>";
        //loop through each group member
        foreach ($guest_group_q as $member) {
            $m_choices_q = $db->query('SELECT meal_choices.menu_item_id, meal_choices.choice_order_id, menu_items.menu_item_name, menu_items.course_id, menu_courses.course_name, menu_courses.course_id, meal_choice_order.choice_order_id, meal_choice_order.guest_id, guest_list.guest_id, guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_group_id  FROM meal_choices LEFT JOIN menu_items ON menu_items.menu_item_id=meal_choices.menu_item_id LEFT JOIN menu_courses ON menu_courses.course_id=menu_items.course_id LEFT JOIN meal_choice_order ON meal_choice_order.choice_order_id=meal_choices.choice_order_id LEFT JOIN guest_list ON guest_list.guest_id=meal_choice_order.guest_id WHERE guest_list.guest_id=' . $member['guest_id'] . ' ORDER BY guest_list.guest_id, menu_courses.course_id');
            $group_choices .= "<h2>" . $member['guest_fname'] . "</h2>";
            foreach ($m_choices_q as $choice) {
                $group_choices .= "<h3>" . $choice['course_name'] . "</h3>";
                $group_choices .= "<p>" . $choice['menu_item_name'] . "</p>";
            }
        }

        $group_choices .= "</div>";
    }
    //profile link for email
    $profile_link = "https://" . $_SERVER['SERVER_NAME'] . "/admin/guest?action=view&guest_id=" . $lead_guest_id;
    //* Email banner links
    $admin_choices="https://".$_SERVER['SERVER_NAME'] . "/admin/meal_choices";
    $admin_menu_builder="https://".$_SERVER['SERVER_NAME'] . "/admin/menu";
    $admin_guest_list="https://".$_SERVER['SERVER_NAME'] . "/admin/guest_list";
    //! fetch the template
    $body = file_get_contents("choice-template.html");
    //*Banner Links
    $body = str_replace(["{{admin_choices}}"], [$admin_choices], $body);
    $body = str_replace(["{{admin_menu_builder}}"], [$admin_menu_builder], $body);
    $body = str_replace(["{{admin_guest_list}}"], [$admin_guest_list], $body);
    //* Email body
    $body = str_replace(["{{wedding_name}}"], [$wedding_name], $body);
    $body = str_replace(["{{guest_name}}"], [$_POST['guest_name']], $body);
    $body = str_replace(["{{guest_choices}}"], [$guest_choices], $body);
    $body = str_replace(["{{group_choices}}"], [$group_choices], $body);
    $body = str_replace(["{{profile_link}}"], [$profile_link], $body);
    //configure email to send to users
    //stored in separate file
    //From Server
    $fromserver = $username;
    $email_to = $wedding_email;
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = $host; // Enter your host here
    $mail->SMTPAuth = true;
    $mail->Username = $username; // Enter your email here
    $mail->Password = $pass; //Enter your password here
    $mail->Port = 25;
    $mail->From = $from;
    $mail->FromName = $fromname;
    $mail->Sender = $fromserver; // indicates ReturnPath header
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->IsHTML(true);
    $mail->AddAddress($email_to);
    if (!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
    exit("1");
}
