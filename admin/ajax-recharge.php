<?php
include('../config.php');
function getShortName($name) {
	if(strlen($name) > 24) {
		$result = substr($name, 0, 24);
		$result = $result."..";
	} else {
		$result = $name;
	}
	return $result;
}
$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id=opr.operator_id LEFT JOIN api_list api ON recharge.api_id=api.api_id ORDER BY recharge.request_date DESC";
$array['recharge_status'] = getRechargeStatusList();
$query = $db->query("SELECT recharge.*, opr.operator_name FROM {$statement} LIMIT 50 ");
while($result = $db->fetchNextObject($query)) {
?>
<tr>							
	<td><?php echo date("d/m/Y H:i:s", strtotime($result->request_date));?></td>
	<td><?php echo $result->recharge_mode;?></td>
	<td><?php echo $result->recharge_id;?></td>
	<td><?php echo $result->api_id;?></td>
	<td><?php echo getShortName($result->operator_name);?></td>
	<td><?php echo $result->account_no;?></td>	
	<td align="right"><?php echo round($result->amount,2);?></td>
	<td><?php echo getRechargeStatusLabel($array['recharge_status'],$result->status);?></td>
	<td><?php echo $result->operator_ref_no;?></td>											
</tr>
<?php } ?>
