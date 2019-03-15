<?php
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) && $_GET['id'] !='' ? mysql_real_escape_string($_GET['id']) : 0;
$array['recharge_status'] = getRechargeStatusList();
$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.uid, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
?>
<?php if($recharge_info) {?>
<table class="table table-bordered">
	<tr>
		<th align="center" colspan="7"><b>Recharge Details</b></th>
	</tr>	
	<tr>
		<th>Date</th>
		<th>Txn No.</th>
		<th>Operator</td>
		<th>Mobile/Acc</th>
		<th>Amount</th>
		<th>Status</th>
	</tr>
	<tr>
		<td class="f11cBlue"><?php echo $recharge_info->request_date;?></td>
		<td><?php echo $recharge_info->recharge_id;?></td>
		<td><?php echo $recharge_info->operator_name;?></td>
		<td><?php echo $recharge_info->account_no;?></td>
		<td><?php echo $recharge_info->amount;?></td>
		<td><?php echo getRechargeStatusLabel($array['recharge_status'],$recharge_info->status);?></td>
	</tr>	
</table>
<?php } ?>

<table class="table table-bordered">		
	<tr>
		<th align="center" colspan="7"><b>Transaction Details</b></th>
	</tr>
	<tr>
		<th>Record Id</th>
		<th>Txn. Date</th>
		<th>Txn. No. Ref</th>
		<th>Txn. Type</th>
		<th>Debit</th>
		<th>Credit</th>
		<th>Balance</th>
	</tr>
	<?php
	$query = $db->query("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_info->recharge_id."' ORDER BY transaction_id DESC");
	while($result = $db->fetchNextObject($query)) {
	?>
	<tr>
		<td><?php echo $result->transaction_id;?></td>
		<td><?php echo $result->transaction_date;?></td>
		<td><?php echo $result->transaction_ref_no;?></td>												
		<td class="f11c"><?php echo $result->transaction_term;?></td>
		<?php if($result->type == 'dr') { ?>
		<td align="right"><?php echo round($result->amount,2);?></td>
		<td align="right"></td>
		<?php } else { ?>							
		<td align="right"></td>
		<td align="right"><?php echo round($result->amount,2);?></td>
		<?php } ?>
		<td align="right"><?php echo round($result->closing_balance,2);?></td>
	</tr>
	<?php } ?>
</table>