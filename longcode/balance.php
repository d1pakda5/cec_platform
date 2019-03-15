<?php
$wallet = $db->queryUniqueObject("SELECT wallet_id,balance FROM apps_wallet WHERE user_id = '".$user_info->user_id."' ");
$message = smsBalanceCheck($user_info->company_name, $wallet->balance);
smsSendSingle($user_info->mobile, $message, 'check_balance');
?>