<?php
session_start();
include('config.php');
$cnt = 1;
$db->execute("UPDATE `operators` SET commission_type='f', is_surcharge='y', surcharge_type='f', surcharge_value='3' WHERE service_type IN (5,6,7,8) ");
?>
