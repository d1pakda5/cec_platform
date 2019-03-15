<?php
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
		array ('id' => '1', 'name' => 'ICICI - 024405004417')
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
		array ('id' => '7', 'status' => 'PROCESSED')
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
			} else {
				$result = "<span class='label'>";
			}
			$result .= $value['status']."</span>";
		}
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