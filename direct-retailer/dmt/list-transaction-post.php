<?php
$jsList = $_GET['js_data'];
$jsList = json_decode($jsList);
$jsTransaction = $jsList->Transaction;	
print_r($jsTransaction);
?>