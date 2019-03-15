<?php
$jsList = $_GET['js_data'];
$jsList = json_decode($jsList);
$jsDefaultIfsc = $jsList->DefaultIfsc;
$jsBranchIfsc = $jsList->Branch;
?>
<table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th>IFSC</th>
			<th>Bank Name</th>
			<th>Branch Name</th>
			<th>City</th>
			<th>State</th>
			<th>Type</th>
		</tr>
	</thead>
	<tbody>
		<tr style="cursor:pointer" onclick="insertIfsc('<?php echo $jsDefaultIfsc->IFSC;?>')">
			<td><?php echo $jsDefaultIfsc->IFSC;?></td>
			<td><?php echo $jsDefaultIfsc->BankName;?></td>
			<td><?php echo $jsDefaultIfsc->BranchName;?></td>
			<td><?php echo $jsDefaultIfsc->City;?></td>
			<td><?php echo $jsDefaultIfsc->State;?></td>
			<td><?php echo $jsDefaultIfsc->TransferType;?></td>
		</tr>
		<?php foreach($jsBranchIfsc as $ifsc) { ?>
		<tr style="cursor:pointer" onclick="insertIfsc('<?php echo $ifsc->IFSC;?>')">
			<td><?php echo $ifsc->IFSC;?></td>
			<td><?php echo $ifsc->BankName;?></td>
			<td><?php echo $ifsc->BranchName;?></td>
			<td><?php echo $ifsc->City;?></td>
			<td><?php echo $ifsc->State;?></td>
			<td><?php if(isset($ifsc->TransferType)) {echo $ifsc->TransferType;}?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>