<?php
$jsList = $_GET['js_data'];
$jsList = json_decode($jsList);
$jsTransaction = $jsList->Transaction;	
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list"></i> List Transaction</div>
		<div class="pull-right">
			<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-success">Back</a>
		</div>	
	</div>
	<div class="panel-body no-padding">
		<table class="table table-condensed table-striped">
			<thead>
				<tr>
					<th>Date</th>
					<th>Agent TransId</th>
					<th>MR TransId</th>
					<th>Topup TransId</th>
					<th>Account Number</th>
					<th>Amount</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($jsTransaction as $trans) { ?>
				<tr>
					<td><?php echo $trans->TransDateTime;?></td>
					<td><?php echo $trans->AgentTransId;?></td>
					<td><?php echo $trans->MrTransId;?></td>
					<td><?php echo $trans->TopupTransId;?></td>
					<td><?php echo $trans->BenefAccNo;?></td>
					<td><?php echo $trans->Amount;?></td>
					<td><?php echo $trans->Status;?></td>
					<td>
						<?php if($trans->Status=='FAILED' && $trans->Reinitiate=='FALSE') {?>
						<a href="javascript:void(0)" onClick="getReInitiate('<?php echo $trans->AgentTransId;?>','<?php echo $trans->Amount;?>');" class="btn btn-xs btn-primary">Re-Initiate</a>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>