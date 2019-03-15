<?php
session_start();
include('../config.php');
$uid = isset($_GET["u"]) && $_GET["u"]!='' ? mysql_real_escape_string($_GET["u"]) : '0';
$opr = isset($_GET["o"]) && $_GET["o"]!='' ? mysql_real_escape_string($_GET["o"]) : '0';
$amt = isset($_GET["a"]) && $_GET["a"]!='' ? mysql_real_escape_string($_GET["a"]) : '0';
$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".trim($uid)."' AND status='1' ");
$dist_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".trim($user_info->dist_id)."' AND status='1' ");
$mdist_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".trim($dist_info->mdist_id)."' AND status='1' ");
$sCommission = getUserCommission(trim($user_info->mdist_id), $opr, $amt, 'r');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Testing Commission</title>
</head>
<body>
<?php
echo "Parameter";
echo "<br>";
echo "Uid: ".$uid;
echo "<br>";
echo "Operator: ".$opr;
echo "<br>";
echo "Amount: ".$amt;
echo "<br>";
echo "<br>";
echo "<br>";
?>
<?php
echo "From Retailer Row";
echo "<br>";
echo "Retailer Name: ".$user_info->company_name." ( ".$user_info->uid." ) ";
echo "<br>";
echo "Distributor Uid: ".$user_info->dist_id;
echo "<br>";
echo "Master Distributor Uid: ".$user_info->mdist_id;
echo "<br>";
echo "<br>";
echo "<br>";
?>
<?php
echo "Distributor Name: ".$dist_info->company_name." ( ".$dist_info->uid." ) ";
echo "<br>";
echo "Master Distributor Name: ".$mdist_info->company_name." ( ".$mdist_info->uid." ) ";
echo "<br>";
print_r($sCommission);
?>
</body>
</html>
