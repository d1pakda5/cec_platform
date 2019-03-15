<?php

include('../config.php');
if($_GET['user_type']!='5')
{
   
if($_GET['to_bank_account'] == '' || $_GET['pay_mode'] == '' || $_GET['amount'] == '' || $_GET['payment_date'] == ''|| $_GET['reg_mobile'] == '') {
	echo "Oops, Some manditory fields are empty.";	
	} else {
		$distributor_uid = htmlentities(addslashes($_GET['distributor_uid']),ENT_QUOTES);
		$to_bank_account = htmlentities(addslashes($_GET['to_bank_account']),ENT_QUOTES);
		$amount = htmlentities(addslashes($_GET['amount']),ENT_QUOTES);
		$transaction_ref_no = htmlentities(addslashes($_GET['transaction_ref_no']),ENT_QUOTES);
		$your_bank_name = htmlentities(addslashes($_GET['your_bank_name']),ENT_QUOTES);
		$your_bank_account = htmlentities(addslashes($_GET['your_bank_account']),ENT_QUOTES);
		
		if(!empty($_FILES)) {	
			$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg','.JPG','.GIF','.BMP','.PNG','');
			$max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).	
			$str = "../uploads/";
			$filename = $_FILES['receipt']['name']; 
			$file = time().$_FILES['receipt']['name'];
			move_uploaded_file($_FILES["file"]["tmp_name"],$str.$file);

				// $s = move_uploaded_file($_FILES['receipt']['tmp_name'],$str.$file);
			
			
		} else {
			$file = "";
		}
		
		$db->execute("INSERT INTO `fund_requests`(`request_id`, `request_date`, `request_user`, `request_to`, `user_type`,`reg_mobile`, `to_bank_account`, `your_bank_name`, `your_bank_account`, `pay_mode`, `payment_date`, `amount`, `transaction_ref_no`, `file_attachment`, `status`) VALUES ('', NOW(), '".$distributor_uid."', '0', '3', '".$_GET['reg_mobile']."', '".$to_bank_account."', '".$your_bank_name."', '".$your_bank_account."', '".$_GET['pay_mode']."', '".$_GET['payment_date']."', '".$amount."', '".$transaction_ref_no."', '".$file."', '0')");
	        $user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$distributor_uid."' ");
			$text="Fund Request Received from ".$user_info->company_name;
		    smsSendSingle('8600250250', $text, 'fund_transfer');
	echo "Submited successfully";		
	
}
}
else
{
    if($_GET['to_bank_account'] == '' || $_GET['pay_mode'] == '' || $_GET['amount'] == '' || $_GET['payment_date'] == ''|| $_GET['reg_mobile'] == '') {
	echo "Oops, Some manditory fields are empty.";	
	} else {
		$ret_uid = htmlentities(addslashes($_GET['ret_uid']),ENT_QUOTES);
		$dist_uid = htmlentities(addslashes($_GET['dist_uid']),ENT_QUOTES);
		$to_bank_account = htmlentities(addslashes($_GET['to_bank_account']),ENT_QUOTES);
		$amount = htmlentities(addslashes($_GET['amount']),ENT_QUOTES);
		$transaction_ref_no = htmlentities(addslashes($_GET['transaction_ref_no']),ENT_QUOTES);
		$your_bank_name = htmlentities(addslashes($_GET['your_bank_name']),ENT_QUOTES);
		$your_bank_account = htmlentities(addslashes($_GET['your_bank_account']),ENT_QUOTES);
		
		if(!empty($_FILES)) {	
			$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg','.JPG','.GIF','.BMP','.PNG','');
			$max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).	
			$str = "../uploads/";
			$filename = $_FILES['receipt']['name']; 
			$file = time().$_FILES['receipt']['name'];
			move_uploaded_file($_FILES["file"]["tmp_name"],$str.$file);

				// $s = move_uploaded_file($_FILES['receipt']['tmp_name'],$str.$file);
			
			
		} else {
			$file = "";
		}
		
		$db->execute("INSERT INTO `fund_requests`(`request_id`, `request_date`, `request_user`, `request_to`, `user_type`,`reg_mobile`, `to_bank_account`, `your_bank_name`, `your_bank_account`, `pay_mode`, `payment_date`, `amount`, `transaction_ref_no`, `file_attachment`, `status`) VALUES ('', NOW(), '".$ret_uid."', '".$dist_uid."', '5', '".$_GET['reg_mobile']."', '".$to_bank_account."', '".$your_bank_name."', '".$your_bank_account."', '".$_GET['pay_mode']."', '".$_GET['payment_date']."', '".$amount."', '".$transaction_ref_no."', '".$file."', '0')");
	echo "Submited successfully";		
	
}


}
