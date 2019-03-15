<?php
session_start();
$jsList = $_GET['js_data'];
$jsList = json_decode($jsList);
$jsBeneficary = $jsList->Beneficiary;	
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list"></i> Sender Details</div>
	</div>
	<div class="panel-body no-padding">
		<table class="table table-condensed table-striped">
			<thead>
				<tr>
					<th>Mobile Number</th>
					<th>Wallet Balance</th>
					<th>Available Limit</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $jsList->SenderDetail->Mobileno;?></td>
					<td id="senderWalletBalance"><?php echo $jsList->SenderDetail->Balance;?></td>
					<td><?php echo $jsList->SenderDetail->RemitLimitAvailable;?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list"></i> List Beneficiary</div>
		<div class="pull-right">
			<a href="javascript:void(0)" onclick="getBeneficiaryForm()" class="btn btn-success">Add Beneficiary</a>
			<a href="javascript:void(0)" onclick="getTransaction()" class="btn btn-primary">History</a>
			<a href="javascript:void(0)" onclick="getBalance()" class="btn btn-primary">Balance</a>
		</div>		
	</div>
	<div class="panel-body no-padding">
		<table id="tblBeneficiary" class="table table-condensed table-striped">
			<thead>
				<tr>
					<th></th>
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
				<?php foreach($jsBeneficary as $dataBen) { ?>
				<tr id="<?php echo $dataBen->BeneficiaryCode;?>">
					<td><input type="radio" name="ben_row" onclick="enableButton('<?php echo $dataBen->BeneficiaryCode;?>')" /></td>
					<td><?php echo $dataBen->BeneficiaryName;?></td>
					<td><?php echo $dataBen->BeneficiaryCode;?></td>
					<td><?php echo $dataBen->AccountNumber;?></td>
					<td><?php echo $dataBen->AccountType;?></td>
					<td><?php echo $dataBen->IFSC;?></td>
					<td><?php echo $dataBen->BeneficiaryType;?></td>
					<td><?php echo $dataBen->Active;?></td>
					<td>
						<a href="javascript:void(0)" onClick="benRemittance('<?php echo $dataBen->BeneficiaryName;?>','<?php echo $dataBen->BeneficiaryType;?>','<?php echo $dataBen->BeneficiaryCode;?>','<?php echo $dataBen->AccountNumber;?>','<?php echo $dataBen->AccountType;?>','<?php echo $dataBen->IFSC;?>','<?php echo $_SESSION['retailer_uid'];?>');" id="rem_<?php echo $dataBen->BeneficiaryCode;?>" class="btn btn-xs btn-primary disabled">Transfer</a>
						<a href="javascript:void(0)" onClick="benValidate('<?php echo $dataBen->BeneficiaryType;?>','<?php echo $dataBen->BeneficiaryCode;?>','<?php echo $dataBen->IFSC;?>','<?php echo $_SESSION['retailer_uid'];?>');" id="val_<?php echo $dataBen->BeneficiaryCode;?>" class="btn btn-xs btn-warning disabled">Validate</a>
						<a href="javascript:void(0)" onClick="benDelete('<?php echo $dataBen->BeneficiaryCode;?>','<?php echo $dataBen->IFSC;?>');" id="del_<?php echo $dataBen->BeneficiaryCode;?>" class="btn btn-xs btn-danger disabled"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>