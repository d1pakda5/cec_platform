 <?php 
 include('../config.php');
$uid= $_GET["uid"]; 
$aRequestDmt = $db->queryUniqueObject("SELECT * FROM dmt_activation_request WHERE dmt_request_uid='".$uid."' ORDER BY dmt_request_date DESC");
$option_info = $db->queryUniqueObject("SELECT * FROM dmt_options WHERE dmt_option_name='retailer_activation_charge' ");
if($option_info) {
	$activation_charge = $option_info->dmt_option_value;
} else {
	$activation_charge = "0";
}
if($aRequestDmt){


  if($aRequestDmt->dmt_request_status=='0') {
	    echo "Your request for money transfer is pending and updated soon.";
	} else { 
		 if($aRequestDmt->dmt_update_status=='2') { 
		
			echo "Your request has been cancelled, To re-initiate money transfer activation please click request activation button.";
		
		 } elseif($aRequestDmt->dmt_update_status=='1') { 
		echo "Your request has been accepted, If you see this message please contact administrator.";
		
		 } else { 
		echo "Your request has been cancelled, please contact administrator or your distributor for re-initiate";
		
		 } 
	 } 
 } else { 
echo "This service is inactive in your account. To activate money transfer service please click request activation button.";
	
 } 
?>
