<?php
function _gstBillingType($type) {
	if($type=='1') {
		$result = "P2P";
	}elseif($type=='2') {
		$result = "P2A";
	} elseif($type=='3') {
		$result = "SUR";
	} else {
		$result = "-";
	}
	return $result;
}
function _getCommAmount($amount,$comm,$sur,$sur_value){
	if($sur=='y') {
		$result = $sur_value;
	} else {
		$result = $amount*$comm/100;
	}
	return $result;
}
function _gstTaxAmount($amount){
	$taxable = $amount*100/118;	
	$gsttax = $amount-$taxable;
	$taxable = round($taxable,4);
	$gsttax = round($gsttax,4);
	return array($taxable,$gsttax);
}