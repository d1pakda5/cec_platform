<?php
//$jsList = $_POST['js_data'];
//$jsList = $jsList['DATA'];
//$jsTransaction =$jsList['DATA']['TRANSACTION_DETAILS'];	
$jsList = $_POST['js_data'];
$jsList = json_decode($jsList, true);
$jsTransaction = $jsList['DATA']['TRANSACTION_DETAILS'];
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list-alt"></i> List Transaction</div>
		<div class="pull-right">
			<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-sm btn-success">Back</a>
		</div>	
	</div>
	<div class="panel-body no-padding">
		<table class="table table-condensed table-bordered">
			<thead>
				<tr>
					<th></th>
					<th>Date</th>
					<th>Benf. Name</th>
					<th>Benf. ID</th>
					<th>Bank Account</th>
					<th>Bank Ref.</th>
					<th>Amount</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach($jsTransaction as $trans) { ?>
				<tr>
					<td align="center">
						<a href="javascript:void(0);"></a>
					</td>
					<td><?php echo date("d/m/Y, h:i A", strtotime($trans['TRANSACTION_DATE']));?></td>
					<td><?php echo $trans['BENE_NAME'];?></td>
					<td><?php echo $trans['BENE_ID'];?></td>
					<td><?php echo $trans['BANK_ACCOUNTNO'];?></td>
					<td><?php echo $trans['BANK_REFERENCE_NO'];?></td>
					<td><?php echo $trans['TRANSFER_AMOUNT'];?></td>
					<td><?php echo $trans['TRANSACTION_STATUS'];?></td>
					<td>
						<?php if($trans['TRANSACTION_STATUS']=='FAILED' || $trans['TRANSACTION_STATUS']=='FALSE') {?>
							<a href="javascript:void(0)" onClick="getRefund('<?php echo $trans['CUSTOMER_REFERENCE_NO'];?>','<?php echo $trans['TRANSFER_AMOUNT'];?>');" class="btn btn-xs btn-primary">Refund</a>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>