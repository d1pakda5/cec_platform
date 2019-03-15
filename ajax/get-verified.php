<?php
session_start();
include("../config.php");
if(isset($_POST["operator"]) && $_POST["operator"]!='' && isset($_POST["account"]) && $_POST["account"]!='' && isset($_POST["amount"]) && $_POST["amount"]!='') {
	$operator = isset($_POST["operator"]) ? mysql_real_escape_string($_POST["operator"]) : '0';
	$account = isset($_POST["account"]) ? mysql_real_escape_string($_POST["account"]) : '0';
	$amount = isset($_POST["amount"]) ? mysql_real_escape_string($_POST["amount"]) : '0';
	
	$customer_account = isset($_POST['customer_account']) ? htmlentities(addslashes($_POST['customer_account']),ENT_QUOTES):'';
	$dob = isset($_POST['dob']) ? htmlentities(addslashes($_POST['dob']),ENT_QUOTES):'';
	//NORTH BIHAR / SOUTH BIHAR
	$sub_division = isset($_POST['sub_division']) ? htmlentities(addslashes($_POST['sub_division']),ENT_QUOTES):'';
	//RELIANCE ENERGY
	$billing_cycle = isset($_POST['bill_cycle']) ? htmlentities(addslashes($_POST['bill_cycle']),ENT_QUOTES):'';
	//MSEDC LIMITED
	$billing_unit = isset($_POST['billing_unit']) ? htmlentities(addslashes($_POST['billing_unit']),ENT_QUOTES):'';
	$pc_number = isset($_POST['pc_number']) ? htmlentities(addslashes($_POST['pc_number']),ENT_QUOTES):'';
	//TORRENT POWER
	$billing_city = isset($_POST['billing_city']) ? htmlentities(addslashes($_POST['billing_city']),ENT_QUOTES):'';	
	//BSNL LANDLINE SERVICE TYPE
	$bsnl_service_type = isset($_POST['bsnl_service_type']) ? htmlentities(addslashes($_POST['bsnl_service_type']),ENT_QUOTES):'';	
	//MAHANAGAR GAS LIMITED
	$bill_group_no = isset($_POST['bill_group_no']) ? htmlentities(addslashes($_POST['bill_group_no']),ENT_QUOTES):'';	
	
	$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE (api_id='10' or api_id='13') AND operator_id='".$operator."' ");
	if($operator_info) {
		echo "<b>Bill Parameter:</b><br>";
		echo "Operator : ".$operator_info->operator_name."<br>";
		echo "Number : ".$account."<br>";
		echo "Amount : ".$amount."<br>";
		//BSNL
		if($operator=='43') {
			echo "Customer A/c : ".$customer_account."<br>";
			echo "Service Type :".$bsnl_service_type."<br>";
		}
		//MTNL DELHI
		if($operator=='45') {
			echo "Customer A/c : ".$customer_account."<br>";
		}
		//MSEDC
		if($operator=='58') {
			echo "Billing Unit : ".$billing_unit."<br>";
			echo "Process Cycle : ".$pc_number."<br>";
		}
		//TORRENT
		if($operator=='64') {
			echo "City : ".$billing_city."<br>";
		}
		//MAHANAGAR GAS LIMITED
		if($operator=='51') {
			echo "Bill Group Number : ".$bill_group_no."<br>";
		}
		include(DIR."/library/cyberplat-verification-api.php");
	} else {			
		echo "FAILED:<br>Validation server has been down, Please try after some time.";
	}
} else {
	echo "FAILED:<br>Please submit all the required parameters to get proper response!";
}
?>