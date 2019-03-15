<?php
$token = $_SESSION['token'];
$website['phone'] = SITEPHONE;
$website['email'] = SITEEMAIL;
$website['name'] = SITENAME;
$website['url'] = SITEURL;
$website['logo'] = SITELOGO;
$aRetailer = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$_SESSION['retailer_uid']."' ");
$aWhiteLabel = $db->queryUniqueObject("SELECT * FROM website_profile WHERE website_uid = '".$aRetailer->mdist_id."' ");
if($aWhiteLabel) {
	$website['phone'] = $aWhiteLabel->support_number;
	$website['email'] = $aWhiteLabel->support_email;
	$website['name'] = $aWhiteLabel->website_name;
	$website['url'] = $aWhiteLabel->website_url;
	$website['menu_color'] = $aWhiteLabel->menu_color;
	if($aWhiteLabel->website_logo != '') {
		$website['logo'] = $aWhiteLabel->website_logo;
	}
}