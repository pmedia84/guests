<?php
include("../connect.php");
$response = ""; //response message
//Post request to update an address
if (isset($_POST['address_id'])) {
    //declare variables
    $address_id = $_POST['address_id'];
    $address_line_1 = mysqli_real_escape_string($db, $_POST['address_line_1']);
    $address_line_2 = mysqli_real_escape_string($db, $_POST['address_line_2']);
    $address_line_3 = mysqli_real_escape_string($db, $_POST['address_line_3']);
    $address_county = mysqli_real_escape_string($db, $_POST['address_county']);
    $address_pc = mysqli_real_escape_string($db, $_POST['address_pc']);
    //Update address record
    $update_address = $db->prepare('UPDATE addresses SET address_line_1=?, address_line_2=?, address_line_3=?, address_county=?, address_pc=?  WHERE address_id =?');
    $update_address->bind_param('sssssi',$address_line_1, $address_line_2, $address_line_3, $address_county, $address_pc, $address_id);
    $update_address->execute();
    $update_address->close();

    //find current address and echo out
    $curaddress = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $curaddress->execute();
    $curaddress->store_result();
    $curaddress->bind_result($address_id, $address_line_1, $address_line_2, $address_line_3, $address_county, $address_pc);
    $curaddress->fetch();
    $curaddress->close();
    $db->close();
    $response = '
<p>' . $address_line_1 . '</p>
<p>' . $address_line_2 . '</p>
<p>' . $address_line_3 . '</p>
<p>' . $address_county . '</p>
<p>' . $address_pc . '</p>';
}
//initial load of addresses
if (isset($_GET['action'])) {


    //find business address details.
    $address_id = $_GET['address_id'];
    $curaddress = $db->prepare('SELECT * FROM addresses WHERE address_id =' . $address_id);

    $curaddress->execute();
    $curaddress->store_result();
    $curaddress->bind_result($address_id, $address_line_1, $address_line_2, $address_line_3, $address_county, $address_pc);
    $curaddress->fetch();
    $curaddress->close();
    $db->close();
    $response = '
<p>' . $address_line_1 . '</p>
<p>' . $address_line_2 . '</p>
<p>' . $address_line_3 . '</p>
<p>' . $address_county . '</p>
<p>' . $address_pc . '</p>';
}

echo $response;
