<?php
$listBen = $rst->Beneficiary;
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-angle-right"></i> List Beneficiary</div>
		<div class="pull-right">
			<a href="#" class="btn btn-success">Add Beneficiary</a>
			<a href="#" class="btn btn-primary">History</a>
			<a href="#" class="btn btn-primary">Balance</a>
		</div>		
	</div>
	<div class="panel-body no-padding">
		<table class="table table-condensed table-striped">
			<thead>
				<tr>
					<th>Beneficiary Name</th>
					<th>Code</th>
					<th>Account Number</th>
					<th>Account Type</th>
					<th>IFSC</th>
					<th>Ben. Type</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($listBen as $dataBen) { ?>
				<tr>
					<td><?php echo $dataBen->BeneficiaryName;?></td>
					<td><?php echo $dataBen->BeneficiaryCode;?></td>
					<td><?php echo $dataBen->AccountNumber;?></td>
					<td><?php echo $dataBen->AccountType;?></td>
					<td><?php echo $dataBen->IFSC;?></td>
					<td><?php echo $dataBen->BeneficiaryType;?></td>
					<td><?php echo $dataBen->Active;?></td>
					<td>
						<a href="#" onClick="transferBen();" class="btn btn-xs btn-primary">Transfer</a>
						<a href="#" onClick="validateBen();" class="btn btn-xs btn-warning">Validate</a>
						<a href="#" onClick="deleteBen();" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>