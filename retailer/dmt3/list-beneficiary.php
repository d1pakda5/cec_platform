<?php
session_start();
$jsList = $_GET['js_data'];
$jsList = json_decode($jsList);
// print_r($jsList->data->remitter->name);die;
$jsBeneficary = $jsList->data->beneficiary;	
$str=$jsList->data->remitter->name;
$name=explode(" ",$str);
$fName=$name[0];
$lName=$name[1];
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list"></i> Sender Details</div>
	</div>
	<div class="panel-body no-padding">
		<table class="table table-condensed table-bordered table-striped">
			<thead>
			<tbody>
				<tr>
					<td width="15%">Name</td>
					<td width="35%"><?php echo $jsList->data->remitter->name;?>
					<td width="15%">KYC Status</td>
					<td width="35%"><?php echo $jsList->data->remitter->kycstatus;?></td>
				</tr>
				<tr>
					<td width="15%">Mobile</td>
					<td><?php echo $jsList->data->remitter->mobile;?></td>
					<td width="15%">MAX Allowed TXN</td>
					<td width="35%"><?php echo $jsList->data->remitter->perm_txn_limit;?></td>
				</tr>
				<tr>
					<td width="15%">Address</td>
					<td><?php echo $jsList->data->remitter->address;?></td>
					<td width="15%">State</td>
					<td><?php echo $jsList->data->remitter->state;?></td>
				</tr>
				<tr>
					<td width="15%">City</td>
					<td><?php echo $jsList->data->remitter->city;?></td>
					<td width="15%">Pincode</td>
					<td><?php echo $jsList->data->remitter->pincode;?></td>
				</tr>
				<tr>
					<td>Available Balance</td>
					<td id="senderWalletBalance"><?php echo $jsList->data->remitter->remaininglimit;?></td>
					<td>consumed Balance</td>
					<td><?php echo $jsList->data->remitter->consumedlimit;?></td>
				</tr>
				<tr>
					<td>Kyc Docs</td>
					<td id="senderWalletBalance"><?php echo $jsList->data->remitter->kycdocs;?></td>
					<td>ID</td>
					<td id="remId"><?php echo $jsList->data->remitter->id;?></td>
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
			
		
		</div>		
	</div>
	
	<div class="panel-body no-padding">
		<table id="tblBeneficiary" class="table table-condensed table-striped">
			<thead>
				<tr>
					<th></th>
					<th>Beneficiary Name</th>
					<th>ID</th>
					<th>Account Number</th>
					<th>IFSC</th>
					<th>Bank Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($jsBeneficary as $dataBen) { ?>
				<tr id="<?php echo $dataBen->id;?>">
					<td><input type="radio" name="ben_row" onclick="enableButton('<?php echo $dataBen->id;?>')" /></td>
					<td><?php echo $dataBen->name;?></td>
					<td><?php echo $dataBen->id;?></td>
					<td><?php echo $dataBen->account;?></td>
					<td><?php echo $dataBen->ifsc;?></td>
					<td><?php echo $dataBen->bank;?></td>
					<td><?php if($dataBen->status=='1'){ echo 'Active';} else{echo 'InActive';}?></td>
					
					<td>
						<a href="javascript:void(0)" onClick="benRemittance('<?php echo $dataBen->name;?>','<?php echo $dataBen->id;?>','<?php echo $dataBen->account;?>','<?php echo $dataBen->ifsc;?>','<?php echo $_SESSION['retailer_uid'];?>');" id="rem_<?php echo $dataBen->id;?>" class="btn btn-xs btn-primary disabled">Transfer</a>
						<a href="javascript:void(0)" onClick="benValidate('<?php echo $dataBen->id;?>','<?php echo $dataBen->ifsc;?>','<?php echo $dataBen->account;?>','<?php echo $_SESSION['retailer_uid'];?>' );" id="val_<?php echo $dataBen->id;?>" class="btn btn-xs btn-warning disabled">Validate</a>
						<a href="javascript:void(0)" onClick="benDelete('<?php echo $dataBen->id;?>','<?php echo $jsList->data->remitter->id;?>','<?php echo $_SESSION['retailer_uid'];?>' );" id="del_<?php echo $dataBen->id;?>" class="btn btn-xs btn-danger disabled"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>