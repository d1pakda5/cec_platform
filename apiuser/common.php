<?php
$token = $_SESSION['token'];
$website['phone'] = SITEPHONE;
$website['email'] = SITEEMAIL;
$website['name'] = SITENAME;
$website['url'] = SITEURL;
$website['logo'] = SITELOGO;
$aAPIUser = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$_SESSION['apiuser_uid']."' ");