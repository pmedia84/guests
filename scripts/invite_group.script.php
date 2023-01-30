<?php
session_start();
include("../connect.php");
$user_id = $_SESSION['user_id'];
//load guest group
// find the guest group that this user manages
$guest_group_id_query = $db->query('SELECT users.user_id, users.guest_id, guest_groups.guest_group_organiser, guest_groups.guest_group_id FROM users LEFT JOIN guest_groups ON guest_groups.guest_group_organiser=users.guest_id WHERE users.user_id =' . $user_id);
$group_id_result = $guest_group_id_query->fetch_assoc();
//define guest group id
$guest_group_id = $group_id_result['guest_group_id'];
//loads guest group list
$group_query = $db->query('SELECT guest_list.guest_fname, guest_list.guest_sname, guest_list.guest_id, guest_list.guest_group_id, guest_list.guest_type, guest_groups.guest_group_id, guest_groups.guest_group_name FROM guest_list LEFT JOIN guest_groups ON guest_groups.guest_group_id=guest_list.guest_group_id  WHERE guest_groups.guest_group_id=' . $guest_group_id . ' AND guest_list.guest_type = "Member"');
$group_result = $group_query->fetch_assoc();
?>

<div style="padding:16px;font-family:sans-serif;">
    <h2 style="text-align:center;">Their Group Information</h2>
    <div style="padding:16px; border: 10px solid #7f688d; border-radius: 10px;">

        <hr style="color:#7f688d;">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left">Name</th>
                    <th style="text-align: left">Dietary Requirements</th>
                </tr>
            </thead>
            <?php foreach($group_query as $group_member):?>
            <tr>
                <td><?= $group_member['guest_fname'].' '.$group_member['guest_sname'];?></td>
                <td></td>
            </tr>
            <?php endforeach;?>
        </table>    

    </div>
</div>