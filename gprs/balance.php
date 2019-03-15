<?php
	//Change Pin
	$agent_info = $user_info;
	$wallet = $db->queryUniqueObject("SELECT wallet_id,balance FROM apps_wallet WHERE user_id = '".$agent_info->user_id."' ");
	echo $agent_info->company_name.", Your current balance is Rs ". $wallet->balance;