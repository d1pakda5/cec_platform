<?php
$type = isset($_GET['type']) && $_GET['type'] !='' ? $_GET['type'] : 'csv';
$url = "http://www.smsalertbox.com/api/data.php?uid=616e6b697473616c6573&pin=5375d40e5c093&rtype=".$type."&version=4&data=ESB";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$output = curl_exec($ch);
curl_close($ch);

print_r($output);
