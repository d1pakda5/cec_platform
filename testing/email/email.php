<?php
include("../../config.php");
$email = "info.sunitech@gmail.com";
$subject = "JSK Technosoft Test Email";
$message = file_get_contents("testemail.html");
try {
	phpMailerFunction($email, $subject, $message);
	echo 'The email has been send successfully';
} 
catch(Exception $e) {
  echo 'Message: '.$e->getMessage();
}
?>
