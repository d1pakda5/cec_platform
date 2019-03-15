<?php

include('../config.php');
		header("Access-Control-Allow-Origin: *");


$sWhere = "WHERE status = '0' ";

$query = $db->query("SELECT * FROM app_notification $sWhere");

if($db->numRows($query) < 1) echo "No Result Found";
$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
									?>