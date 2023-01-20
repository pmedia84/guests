<?php
print_r($_POST);

$cars=$_POST['cars'];
foreach($cars as $car){
    echo $car['name'] .' '.$car['year'];
}