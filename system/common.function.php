<?php
function getUserUID() {
	$sql = "INSERT INTO get_user_uid(id) values ('')";
  $result = mysql_query($sql) or die(mysql_error());
  if(mysql_affected_rows() == 1){
		$uid = mysql_insert_id();
	}
	return $uid;
}
function getWebsiteName($uid) {
	global $db;
	$row = $db->queryUniqueObject("SELECT website_name FROM website_profile WHERE website_uid = '".$uid."' ");
	if($row) {
    	$result = $row->website_name;
	} else {
		$result = SITENAME;
	}
	return $result;
}
function generatePassword($length = 8) {
	$chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789!@#$';
	$count = strlen($chars);
	for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= substr($chars, $index, 1);
	}
	return $result;
}
function hashPassword($password) {
  $algo = "$2a$07$";
  $authkey = AUTHKEY;
	$encode_password = base64_encode($password);
  $md5_password = md5($encode_password);
	$crypt_password = crypt($md5_password, $algo.$authkey);
	$result = substr($crypt_password, 0, 40);
  return $result;
}
//Genrate Pin
function generatePin($length = 4) {
	$chars = '0123456789';
	$count = strlen($chars);
	for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= substr($chars, $index, 1);
	}
	return $result;
}
function hashPin($Pin) {
	$split_pin = str_split($Pin);
	$encode_pin = '';
	foreach($split_pin as $value) {
		$encode_pin .= base64_encode($value);
	}
	$result = strtr(base64_encode($encode_pin), '+/=', '-_@');
  return $result;
}
function generateKey($length = 20) {
	$chars = 'abcdef1234567890';
	$count = strlen($chars);
	for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= substr($chars, $index, 1);
	}
	return $result;
}
function hashToken($id) {
  $algo1 = "$2a$07$";
	$algo2 = "$8x$09$";
	$encodeid = base64_encode($algo1.$id);
  $md5id = md5($encodeid);
	$result = md5($md5id.$algo2);
  return $result;
}
function getServiceStatus($s) {
	if($s == 'a') {
		$data = "Active";
	} else {
		$data = "Inactive";
	}
	return $data;
}
function getUserType($type) {
	if($type == '1') {
		$result = "API User";	
	} else if ($type == '2') {
		$result = "Administrator";	
	} else if ($type == '3') {
		$result = "Master Distributor";	
	} else if ($type == '4') {
		$result = "Distributor";	
	} else if ($type == '5') {	
		$result = "Retailer";	
	} else if ($type == '0') {
		$result = "All";	
	} else {
		$result = "-";	
	}
	return $result;
}
function getUserCuttOff($type) {
	if($type == '1') {
		$result = "100";	
	} else if ($type == '2') {
		$result = "100";	
	} else if ($type == '3') {
		$result = "100";	
	} else if ($type == '4') {
		$result = "100";	
	} else if ($type == '5') {	
		$result = "100";	
	} else if ($type == '0') {
		$result = "100";	
	} else {
		$result = "100";	
	}
	return $result;
}
function getTicketStatusList() {
	$data = array (
		array ('id' => '0', 'name' => 'OPEN'),
		array ('id' => '1', 'name' => 'CLOSED'),
		array ('id' => '2', 'name' => 'ANSWERED')
	);
	return $data;
}
function getTicketStatus($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['name'];
		}
	}
	return $result;
}
function getTicketUrgencyList() {
	$data = array (
		array ('id' => '1', 'name' => 'LOW'),
		array ('id' => '2', 'name' => 'MEDIUM'),
		array ('id' => '3', 'name' => 'HIGH')
	);
	return $data;
}
function getTicketUrgency($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['name'];
		}
	}
	return $result;
}
function getPaymentModeList() {
	$data = array (
		array ('id' => '1', 'name' => 'NEFT/RTGS/IMPS'),
		array ('id' => '2', 'name' => 'CASH DEPOSIT'),
		array ('id' => '3', 'name' => 'CASH IN HAND'),
		array ('id' => '4', 'name' => 'OTHER'),
	);
	return $data;
}
function getPaymentMode($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['name'];
		}
	}
	return $result;
}
function getPaymentStatusList() {
	$data = array (
		array ('id' => '0', 'name' => 'Pending'),
		array ('id' => '1', 'name' => 'Success'),
		array ('id' => '2', 'name' => 'Reject'),
		array ('id' => '3', 'name' => 'Other'),
	);
	return $data;
}
function getPaymentStatus($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['name'];
		}
	}
	return $result;
}
function getBankNameList() {
	$data = array (
		array ('id' => '0', 'name' => 'NONE'),
		array ('id' => '1', 'name' => 'ICICI - 147147147'),
		array ('id' => '2', 'name' => 'HDFC - 147147147'),
		array ('id' => '3', 'name' => 'SBI - 147147147'),
		array ('id' => '4', 'name' => 'BANK OF MAHARASHTRA - 147147147'),
	);
	return $data;
}
function getRechargeStatusList() {
	$data = array (
		array ('id' => '0', 'status' => 'SUCCESS'),
		array ('id' => '1', 'status' => 'PENDING'),
		array ('id' => '2', 'status' => 'FAILURE'),
		array ('id' => '3', 'status' => 'REFUNDED'),
		array ('id' => '4', 'status' => 'REVERT'),
		array ('id' => '5', 'status' => 'DISPUTE'),
		array ('id' => '6', 'status' => 'CANCELLED'),
		array ('id' => '7', 'status' => 'PROCESSED'),
		array ('id' => '8', 'status' => 'SUBMITTED')
	);
	return $data;
}
function getRechargeStatus($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['status'];
		}
	}
	return $result;
}
function getRechargeStatusLabel($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			if($value['id'] == '0') {			
				$result = "<span class='label label-success'>";
			} else if($value['id'] == '1') {
				$result = "<span class='label label-warning'>";
			} else if($value['id'] == '2') {
				$result = "<span class='label label-danger'>";
			} else if($value['id'] == '3') {
				$result = "<span class='label label-primary'>";
			} else if($value['id'] == '4') {
				$result = "<span class='label label-info'>";
			} else if($value['id'] == '5') {
				$result = "<span class='label label-warning'>";
			} else if($value['id'] == '6') {
				$result = "<span class='label label-danger'>";
			} else if($value['id'] == '7') {
				$result = "<span class='label label-success'>";
			} else if($value['id'] == '8') {
				$result = "<span class='label label-success'>";
			} else {
				$result = "<span class='label'>";
			}
			$result .= $value['status']."</span>";
		}
	}
	return $result;	
}
function getRechargeStatusUser($status) {
	$result = $status;	
	if($status == '0') {			
		$result = "SUCCESS";
	} else if($status == '1') {
		$result = "SUCCESS";
	} else if($status == '2') {
		$result = "FAILURE";
	} else if($status == '3') {
		$result = "REFUNDED";
	} else if($status == '4') {
		$result = "REVERT";
	} else if($status == '5') {
		$result = "DISPUTE";
	} else if($status == '6') {
		$result = "CANCELLED";
	} else if($status == '7') {
		$result = "PROCESSED";
	} else if($status == '8') {
		$result = "SUBMITTED";
	} else {
		$result = "SUCCESS!";
	}
	return $result;	
}
function getRechargeStatusLabelUser($status) {
	$result = $status;	
	if($status == '0') {			
		$result = "<span class='label label-success'>SUCCESS</span>";
	} else if($status == '1') {
		$result = "<span class='label label-success'>SUCCESS</span>";
	} else if($status == '2') {
		$result = "<span class='label label-danger'>FAILURE</span>";
	} else if($status == '3') {
		$result = "<span class='label label-primary'>REFUNDED</span>";
	} else if($status == '4') {
		$result = "<span class='label label-info'>REVERT</span>";
	} else if($status == '5') {
		$result = "<span class='label label-warning'>DISPUTE</span>";
	} else if($status == '6') {
		$result = "<span class='label label-danger'>CANCELLED</span>";
	} else if($status == '7') {
		$result = "<span class='label label-success'>PROCESSED</span>";
	} else if($status == '8') {
		$result = "<span class='label label-success'>SUBMITTED</span>";
	} else {
		$result = "<span class='label label-success'>SUCCESS!</span>";
	}
	return $result;	
}
function getComplaintStatusList() {
	$data = array (
		array ('id' => '0', 'status' => 'Open'),
		array ('id' => '1', 'status' => 'Closed'),
		array ('id' => '2', 'status' => 'On Hold'),
		array ('id' => '3', 'status' => 'In Progress')
	);
	return $data;
}
function getComplaintStatus($array, $status) {
	$result = $status;
	foreach($array as $key=>$value) {
		if($value['id'] == $status) {
			$result = $value['status'];
		}
	}
	return $result;
}
function getUserCommission($uid, $operator_id, $amount, $api='') {
	global $db;
	$row = $db->queryUniqueObject("SELECT * FROM apps_commission WHERE uid = '".$uid."' AND operator_id = '".$operator_id."' ");
	if($row) {
		if($api=='api') {
			if($row->is_surcharge == 'y') {
				if($row->is_percentage == 'y') {				
					$result = array('mdCom'=>$amount*$row->comm_mdist/100, 'dsCom'=>$amount*$row->comm_dist/100, 'rtCom'=>$amount*$row->comm_api/100, 'surcharge'=>'y', 'samount'=>$amount*$row->surcharge_value/100);
				} else {
					$result = array('mdCom'=>$row->comm_mdist, 'dsCom'=>$row->comm_dist, 'rtCom'=>$row->comm_ret, 'surcharge'=>'y', 'samount'=>$row->surcharge_value);
				}
			} else {
				$result = array('mdCom'=>$amount*$row->comm_mdist/100, 'dsCom'=>$amount*$row->comm_dist/100, 'rtCom'=>$amount*$row->comm_api/100, 'surcharge'=>'n', 'samount'=>'0');
			}
		} else {
			if($row->is_surcharge == 'y') {
				if($row->is_percentage == 'y') {				
					$result = array('mdCom'=>$amount*$row->comm_mdist/100, 'dsCom'=>$amount*$row->comm_dist/100, 'rtCom'=>$amount*$row->comm_ret/100, 'surcharge'=>'y', 'samount'=>$amount*$row->surcharge_value/100);
				} else {
					$result = array('mdCom'=>$row->comm_mdist, 'dsCom'=>$row->comm_dist, 'rtCom'=>$row->comm_ret, 'surcharge'=>'y', 'samount'=>$row->surcharge_value);
				}
			} else {
				$result = array('mdCom'=>$amount*$row->comm_mdist/100, 'dsCom'=>$amount*$row->comm_dist/100, 'rtCom'=>$amount*$row->comm_ret/100, 'surcharge'=>'n', 'samount'=>'0');
			}
		}
	} else {
		$result = array('mdCom'=>'0', 'dsCom'=>'0', 'rtCom'=>'0', 'surcharge'=>'n', 'samount'=>'0');
	}
	return $result;
}
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

