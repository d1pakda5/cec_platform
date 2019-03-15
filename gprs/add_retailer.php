<?php
include('../config.php');
if($_GET['user_type']=='3')
{
    echo "Service Not Available";
    exit();
}
if($_GET['user_type']=='4')
{
if($_GET['user_id'] == '' || $_GET['mobile'] == '' || $_GET['username'] == '' || $_GET['password'] == ''|| $_GET['company_name'] == '' || $_GET['full_name'] == '') {
	echo "Oops, Some manditory fields are empty.";	
	} else {
		$userid = htmlentities(addslashes($_GET['user_id']),ENT_QUOTES);
		$company_name = htmlentities(addslashes($_GET['company_name']),ENT_QUOTES);
		$full_name = htmlentities(addslashes($_GET['full_name']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_GET['mobile']),ENT_QUOTES);
		$username = htmlentities(addslashes($_GET['username']),ENT_QUOTES);
		$password = htmlentities(addslashes($_GET['password']),ENT_QUOTES);
		$uid = htmlentities(addslashes($_GET['uid']),ENT_QUOTES);
		$mdist_id = $_GET['mdist_id'];
		$dist_id =$_GET['uid'];
	    $hashPassword = hashPassword($password);
	    $exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$userid."' OR mobile = '".$mobile."' OR  username = '".$mobile."' ");
		if($exists) {
			echo "user already exist with this details";
			exit();
		}
		else
		{
		   $db->execute("INSERT INTO `apps_user`(`user_id`, `uid`, `user_type`, `mdist_id`, `dist_id`, `fullname`, `company_name`, `mobile`, `username`, `password`, `pin`,`is_access`, `is_verified`, `is_kyc`, `status`, `added_date`) VALUES ('', '".$userid."', '5', '".$mdist_id."', '".$dist_id."', '".$full_name."', '".$company_name."', '".$mobile."','".$mobile."', '".$hashPassword."', '', 'y', '', '0', '1', NOW())");	
			$user_id = $db->lastInsertedId();				
			$db->execute("INSERT INTO `apps_wallet`(`wallet_id`, `user_id`, `uid`, `balance`, `cuttoff`, `is_locked`, `update_time`) VALUES ('', '".$user_id."', '".$userid."', '0', '100', '0', NOW())");
			$websitename = getWebsiteName($mdist_id);
			$message = smsUserActivation($websitename, $mobile, $password, date("d-m-Y"));
			smsSendSingle($mobile, $message, 'registration');
			if($email != '') {
				mailNewClient($fullname, $company_name, $mobile, $email, $mobile, $password, $websitename);
			}
			echo "Reatailer Created Successfully";
		}
		
	    
	}
    
}		