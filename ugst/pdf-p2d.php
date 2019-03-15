<?php
session_start();
include('config.php');
include('class.gst.php');
$gst = new GST();
include('../system/class.numtowords.php');
$nw = new Numbers_Words();
//
$invoiceid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$invoice = $db->queryUniqueObject("SELECT * FROM gst_debitnote WHERE id='".$invoiceid."'");
if(!$invoice) {
	exit();
}	
$in_date = explode("-",$invoice->invoice_date);
$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0]; 
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$invoice->uid."' ");
if(!$user) {
	exit();
}
$months = $gst->getMonthList();
$seller_info = $gst->getSellerDetail('0',$user);
$buyer_info = $gst->getSellerDetail('1',$user);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Invoice-<?php echo $invoiceid;?></title>
<style>
	body {font-size:11px; font-family: "Helvetica", Arial, sans-serif;}
	.table, .table td {border:1px solid #333; padding:5px;}
	.tr-bg {background:#CCCCCC;}
	p {margin:0px; padding:0px;}	
</style>
</head>
<body>
<table class="table" cellspacing="0">
	<tr>
		<td colspan="4" align="center"><h3><b>DEBIT NOTE AGAINST RECHARGE</b></h3></td>
	</tr>
	<tr>
		<td width="50%" rowspan="2" colspan="2">
			<img src="../images/gst-logo-small.png" class="img-responsive" />
		</td>
		<td style="width: 25%;">Invoice Number<br /><strong><?php echo $invoice->id;?></strong></td>
		<td style="width: 25%;">Date<br /><strong><?php echo $invoice_date;?></strong></td>
	</tr>
	<tr>
		<td>Dealer Code<br /><strong><?php echo $invoice->uid;?></strong></td>
		<td>Mode/Terms of Payment<br /><strong><?php echo $invoice->bill_mode;?></strong></td>
	</tr>
	<tr>
		<td colspan="2">
			<p style="font-size:12px;">SELLER<br />
			<b><?php echo $seller_info['cn'];?></b><br />
			<?php echo $seller_info['address'];?><br />
			<?php echo $seller_info['city'];?><br>
			<?php echo $seller_info['state'];?>, <?php echo $seller_info['country'];?> <?php echo $seller_info['pin'];?><br />
			Telephone: <?php echo $seller_info['phone'];?><br />
			E-Mail:- <?php echo $seller_info['email'];?><br />
			GSTIN: <?php echo $seller_info['gstin'];?><br>
			PAN: <?php echo $seller_info['pan'];?>
			</p>
		</td>
		<td colspan="2">
			<p style="font-size:12px;">BUYER<br />
			<b><?php echo $buyer_info['cn'];?></b><br />
			<?php echo $buyer_info['address'];?><br />
			<?php echo $buyer_info['city'];?><br>
			<?php echo $buyer_info['state'];?>, <?php echo $buyer_info['country'];?> <?php echo $buyer_info['pin'];?><br />
			Telephone: <?php echo $buyer_info['phone'];?><br />
			E-Mail:- <?php echo $buyer_info['email'];?><br />
			GSTIN: <?php echo $buyer_info['gstin'];?><br>
			PAN: <?php echo $buyer_info['pan'];?><br>
			Place of Supply: <?php echo $buyer_info['state'];?>
			</p>
		</td>						
	</tr>
</table>			
<table class="table" cellspacing="0">
	<tr style="font-weight: bold">
		<td width="60%">Description</td>
		<td width="20%">Recharge Amount</td>
		<td width="20%">Debit Amount</td>
	</tr>
	<tr>
		<td><?php echo $invoice->description;?></td>
		<td><?php echo $invoice->recharge_amount;?></td>
		<td align="right" class="tot_netamt"><?php echo $invoice->debit_amount;?></td>
	</tr>	
	<tr style="font-weight: bold">
		<td align="right" colspan="2">Total</td>
		<td style="text-align: right"><?php echo $invoice->debit_amount;?></td>
	</tr>	
	<tr>
		<td colspan="7">
			<table cellspacing="0">
				<tr>
					<td width="80%">Amount Chargeable (in words)<br><b><?php echo ucwords($nw->toCurrency($invoice->debit_amount));?></b></td>
					<td width="20%" align="right">E. & O.E</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table class="table table-bordered" cellspacing="0">
	<tr>
		<td width="60%"><b>Declaration</b><br>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</td>
		<td width="40%" align="right">
			for <b><?php echo $seller_info['cn'];?></b>
			<br>
			&nbsp;
			<br>
			<br>
			&nbsp;
			<br>
			Authorised Signatory
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><b>THIS IS NOT A TAX INVOICE</b></td>
	</td>
</table>
<table cellspacing="0">
	<tr>
		<td align="center">
			<br>This is a Computer Generated Invoice and does not require signatures.
		</td>
	</tr>
</table>
</body>
</html>