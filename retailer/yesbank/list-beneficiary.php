<?php
session_start();
$jsList = $_POST['js_data'];
$jsCustomer = $jsList['DATA'];
$jsBeneficary = $jsList['DATA']['BENEFICIARY_DATA'];
?>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-address-card"></i> Customer Details</div>
	</div>
	<div class="box-body">
		<table class="table table-condensed table-bordered">
			<tbody>
				<tr>
					<td width="15%">Name</td>
					<td width="35%"><?php echo $jsList['DATA']['SENDER_TITLE'];?> <?php echo $jsList['DATA']['SEDNER_FNAME'];?> <?php echo $jsList['DATA']['SENDER_LNAME'];?></td>
					<td width="15%">Customer Type</td>
					<td width="35%"><?php echo $jsList['DATA']['SENDER_CUSTTYPE'];?></td>
				</tr>
				<tr>
					<td width="15%">Gender</td>
					<td><?php echo $jsList['DATA']['SEDNER_GENDER'];?></td>
					<td width="15%">Email</td>
					<td width="35%"><?php echo $jsList['DATA']['SENDER_EMAIL'];?></td>
				</tr>
				<tr>
					<td width="15%">Address</td>
					<td><?php echo $jsList['DATA']['SENDER_ADDRESS1'];?></td>
					<td width="15%">State</td>
					<td><?php echo $jsList['DATA']['STATE'];?></td>
				</tr>
				<tr>
					<td width="15%">City</td>
					<td><?php echo $jsList['DATA']['CITY'];?></td>
					<td width="15%">Pincode</td>
					<td><?php echo $jsList['DATA']['PINCODE'];?></td>
				</tr>
				<tr>
					<td>Available Balance</td>
					<td id="senderWalletBalance"><?php echo $jsList['DATA']['SENDER_AVAILBAL'];?></td>
					<td>Monthly Balance</td>
					<td><?php echo $jsList['DATA']['SENDER_MONTHLYBAL'];?></td>
				</tr>
				<tr>
					<td>IS Validate</td>
					<td id="senderWalletBalance"><?php echo $jsBeneficary[0]['IS_BENEVERIFIED'];?></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="dmt box">
	<div class="box-heading">
		<div class="panel-title pull-left"><i class="fa fa-list-alt"></i> List Beneficiary</div>
		<div class="pull-right">
			<a href="javascript:void(0)" onclick="getBeneficiaryForm()" class="btn btn-sm btn-success">Add Beneficiary</a>
			<a href="javascript:void(0)" onclick="getDateBetween()" class="btn btn-sm btn-primary">History</a>
		</div>		
	</div>
	<div class="panel-body no-padding">
		<table id="tblBeneficiary" class="table table-condensed table-striped">
			<thead>
				<tr>
					<th></th>
					<th>Beneficiary Name</th>
					<th>ID</th>
					<th>Mobile</th>
					<th>Bank Name</th>
					<th>Account Number</th>
					<th>IFSC</th>
					<th>Verified</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($jsBeneficary as $dataBen) { ?>
				<tr id="<?php echo $dataBen['BENE_ID'];?>">
					<td align="center"><input type="radio" name="ben_row" onclick="enableButton('<?php echo $dataBen['BENE_ID'];?>')" /></td>
					<td><?php echo $dataBen['BENE_NAME'];?></td>
					<td><?php echo $dataBen['BENE_ID'];?></td>
					<td><?php echo $dataBen['BENE_MOBILENO'];?></td>
					<td><?php echo $dataBen['BENE_BANKNAME'];?></td>
					<td><?php echo $dataBen['BANK_ACCOUNTNO'];?></td>
					<td><?php echo $dataBen['BANKIFSC_CODE'];?></td>
					<td><?php if($dataBen['IS_BENEVERIFIED']=='true'){ echo "Yes";} else { echo "No";}?></td>
					<td>
						<a href="javascript:void(0)" onClick="benRemittance('<?php echo $dataBen['BENE_ID'];?>','<?php echo $dataBen['BENE_MOBILENO'];?>','<?php echo $dataBen['BENE_NAME'];?>','<?php echo $dataBen['BANK_ACCOUNTNO'];?>','<?php echo $dataBen['BENE_BANKNAME'];?>','<?php echo $dataBen['BANKIFSC_CODE'];?>');" id="rem_<?php echo $dataBen['BENE_ID'];?>" class="btn btn-xs btn-primary disabled">Transfer</a>
					</td>
					<td>						
						<a href="javascript:void(0)" onClick="benValidate('<?php echo $dataBen['BENE_ID'];?>','<?php echo $dataBen['BENE_MOBILENO'];?>','<?php echo $dataBen['BENE_NAME'];?>','<?php echo $dataBen['BANK_ACCOUNTNO'];?>','<?php echo $dataBen['BENE_BANKNAME'];?>','<?php echo $dataBen['BANKIFSC_CODE'];?>');" id="val_<?php echo $dataBen['BENE_ID'];?>" class="btn btn-xs btn-warning disabled">Validate</a>
					</td>
					<td>
						<a href="javascript:void(0)" onClick="benDeleteModal('<?php echo $dataBen['BENE_ID'];?>','<?php echo $dataBen['BENE_NAME'];?>','<?php echo $dataBen['BANK_ACCOUNTNO'];?>','<?php echo $dataBen['BANKIFSC_CODE'];?>');" id="del_<?php echo $dataBen['BENE_ID'];?>" class="btn btn-xs btn-danger disabled"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>