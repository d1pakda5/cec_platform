<?php
	//Change Pin
	if($user_info->uid=='20032368' || $user_info->uid=='20032374')
	{
	    echo "service not available for demo account";
	    exit();
	}
	$pin = generatePin();
	$hashPin = hashPin($pin);
	$db->execute("UPDATE apps_user SET pin = '".$hashPin."' WHERE user_id = '".$user_info->user_id."' ");
	$message = smsPinChange($user_info->company_name, $pin);
	smsSendSingle($user_info->mobile, $message, 'pin');
	echo "Success,Pin has been successfully reset and sent to your registered mobile number";
