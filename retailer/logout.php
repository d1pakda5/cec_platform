<?php
session_start();
include('../config.php');
if(isset($_SESSION['retailer'])) {
    $aRetailer = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$_SESSION['retailer_uid']."' ");
	$db->execute("UPDATE activity_login SET is_online = 'n', logout_time = NOW() WHERE login_id = '".$_SESSION['rt_login_id']."' ");
	unset($_SESSION['retailer_name']);
	unset($_SESSION['retailer']);
	unset($_SESSION['rt_login_id']);
}
if(isset($_SESSION['whitelabel']))
{
    if($_SESSION['whitelabel']== $aRetailer->mdist_id)
    {
        unset($_SESSION['whitelabel']);
        unset($_SESSION['retailer_uid']);
        header('location:../'.$_SESSION['loginpage']);
    }
}
else
{
    unset($_SESSION['retailer_uid']);
    header('location:../login.php');    
}

?>
