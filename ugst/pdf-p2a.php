<?php
session_start();
include('config.php');
include('class.gst.php');
$gst = new GST();
include('../system/class.numtowords.php');
$nw = new Numbers_Words();
//
$invoiceid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$invoice = $db->queryUniqueObject("SELECT * FROM gst_p2ainvoices WHERE id='".$invoiceid."'");
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
$seller_info = $gst->getSellerDetail('1',$user);
$buyer_info = $gst->getSellerDetail('0',$user);
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
		<td colspan="4" align="center"><h3><b>SAMPLE INVOICE REFERENCE</b></h3></td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			<p style="font-size:12px;">
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
			<p style="font-size:12px;">INVOICE TO<br />
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
	<tr>
		<td style="width: 25%;">Invoice Number<br /><strong><?php echo $invoiceid;?></strong></td>
		<td style="width: 25%;">Dealer Code<br /><strong></strong></td>
		<td style="width: 25%;">Mode/Terms of Payment<br /><strong>Advance</strong></td>
		<td style="width: 25%;">Date<br /><strong><?php echo $invoice_date;?></strong></td>
	</tr>
</table>			
<table class="table" cellspacing="0">
	<tr style="font-weight: bold">
		<td width="8%">S. No.</td>
		<td width="56%">Description</td>
		<td width="18%">HSN/SAC</td>
		<td width="18%">Amount</td>
	</tr>
	<?php
	$scnt = 1;				
	$query = $db->query("SELECT * FROM gst_p2ainvoices WHERE id='".$invoiceid."' ORDER BY id ASC");
	$result = $db->fetchNextObject($query);
	$gst_sum = $gst->getTaxSummary($result->total_amount,$user->gst_type);
	$gross_total = $result->sub_total+$gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
	$tax_total = $gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
	?>
	<tr>
		<td><?php echo $scnt++;?>.</td>
		<td><?php echo $result->description;?></td>
		<td><?php echo $result->hsn_sac;?></td>
		<td align="right" class="tot_netamt"><?php echo $result->sub_total;?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3">Total</td>
		<td style="text-align: right"><?php echo $result->sub_total;?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3">CGST <?php echo $gst_sum['cgst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['cgst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3">SGST <?php echo $gst_sum['sgst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['sgst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3">IGST <?php echo $gst_sum['igst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['igst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3">Total</td>
		<td style="text-align: right"><?php echo $gross_total;?></td>
	</tr>
	<tr>
		<td colspan="4">
			<table cellspacing="0">
				<tr>
					<td width="80%">Amount Chargeable (in words)<br><b><?php echo ucwords($nw->toCurrency($gross_total));?></b></td>
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