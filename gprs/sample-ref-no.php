<?php
include("../config.php");
	header("Access-Control-Allow-Origin: *");
	
	$query2 = $db->query("SELECT sample_ref_no,operator_name FROM operators where operator_id in('1','2','3','5','9','10','20','27','28','30','35') order by operator_name ASC ");
	if($db->numRows($query2) < 1) echo "No Result Found";
    $output=array();
    
        while($result2 = $db->fetchNextObject($query2)) {
             $output[]=$result2;
           
        }
     echo json_encode($output);
    ?>