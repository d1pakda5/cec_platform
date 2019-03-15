<?php

include('../config.php');
		header("Access-Control-Allow-Origin: *");
if(isset($_GET['mdist']) && $_GET['mdist'] != '')
{
$aWhiteLabel = $db->queryUniqueObject("SELECT * FROM website_profile WHERE website_uid = '".$_GET['mdist']."' ");



 echo json_encode($aWhiteLabel);
								}	?>