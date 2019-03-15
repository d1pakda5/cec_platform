<?php
session_start();
include('config.php');
include('class.gst.php');
$gst = new GST();
include('../system/class.numtowords.php');
$nw = new Numbers_Words();
//
$invoiceid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$invoice = $db->queryUniqueObject("SELECT * FROM gst_invoices WHERE id='".$invoiceid."'");
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
		<td colspan="4" align="center"><h3><b>TAX INVOICE</b></h3></td>
	</tr>
	<tr>
		<td width="50%" rowspan="2" colspan="2">
			<img src="../images/gst-logo-small.png" class="img-responsive" />
		</td>
		<td style="width: 25%;">Invoice Number<br /><strong><?php echo $invoice->invoice_num;?></strong></td>
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
		<td width="8%">S. No.</td>
		<td width="25%">Description</td>
		<td width="15%">HSN/SAC</td>
		<td width="17%">Quantity</td>
		<td width="10%">Rate</td>
		<td width="7%">Per</td>
		<td width="18%">Amount</td>
	</tr>
	<?php
	$total_taxable = 0;
	$scnt = 1;				
	$query = $db->query("SELECT * FROM gst_invoicesitems WHERE invoiceid='".$invoiceid."' ORDER BY id ASC");
	if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
	while($result = $db->fetchNextObject($query)) {
		$total_taxable += $result->taxable_amount;
	?>
	<tr>
		<td><?php echo $scnt++;?>.</td>
		<td><?php echo $result->description;?></td>
		<td><?php echo $result->hsn_sac;?></td>
		<td align="right"></td>
		<td><?php echo $result->rate;?></td>
		<td><?php echo $result->unit;?></td>
		<td align="right" class="tot_netamt"><?php echo $result->taxable_amount;?></td>
	</tr>
	<?php } ?>
	<?php		
	$query = $db->query("SELECT SUM(amount) AS amt, SUM(net_amount) AS net_amt, hsn_sac FROM gst_invoicesitems WHERE invoiceid='".$invoiceid."' ");
	$result = $db->fetchNextObject($query);	
		$gst_sum = $gst->getTaxSummary($result->net_amt,$user->gst_type);
		$gross_total = $total_taxable+$gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
		$tax_total = $gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
	?>
	<tr style="font-weight: bold">
		<td align="right" colspan="6">Total</td>
		<td style="text-align: right"><?php echo $total_taxable;?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="6">CGST <?php echo $gst_sum['cgst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['cgst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="6">SGST <?php echo $gst_sum['sgst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['sgst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="6">IGST <?php echo $gst_sum['igst_rate'];?></td>
		<td style="text-align: right"><?php echo $gst_sum['igst_amount'];?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right" colspan="3"></td>
		<td align="right"></td>
		<td align="right" colspan="2">Total</td>
		<td style="text-align: right"><?php echo $gross_total;?></td>
	</tr>
	<tr>
		<td colspan="7">
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
		<td colspan="9">Tax Summary</td>
	</tr>
	<tr>
		<td width="11%" rowspan="2">HSN/SAC</td>
		<td width="14%" rowspan="2">Taxable Value</td>
		<td width="19%" colspan="2">Central Tax</td>
		<td width="19%" colspan="2">State Tax</td>
		<td width="19%" colspan="2">Integrated Tax</td>
		<td width="18%" rowspan="2">Total Tax Amount</td>
	</tr>
	<tr>
		<td width="7%">Rate</td>
		<td width="12%">Amount</td>
		<td width="7%">Rate</td>
		<td width="12%">Amount</td>
		<td width="7%">Rate</td>
		<td width="12%">Amount</td>
	</tr>
	<?php		
	$a_hsn = $db->queryUniqueObject("SELECT * FROM gst_invoicesitems WHERE invoiceid='".$invoiceid."' AND hsn_sac='998592' ");
	$a_gst = $gst->getTaxSummary($a_hsn->net_amount,$user->gst_type);
	$a_tax_total = $a_gst['cgst_amount']+$a_gst['sgst_amount']+$a_gst['igst_amount'];
	?>
	<tr>
		<td><?php echo $a_hsn->hsn_sac;?></td>
		<td align="right"><?php echo $a_hsn->taxable_amount;?></td>
		<td width="7%"><?php echo $a_gst['cgst_rate'];?></td>
		<td width="12%" align="right"><?php echo $a_gst['cgst_amount'];?></td>
		<td width="7%"><?php echo $a_gst['sgst_rate'];?></td>
		<td width="12%" align="right"><?php echo $a_gst['sgst_amount'];?></td>
		<td width="7%"><?php echo $a_gst['igst_rate'];?></td>
		<td width="12%" align="right"><?php echo $a_gst['igst_amount'];?></td>
		<td align="right"><?php echo $a_tax_total;?></td>
	</tr>
	<?php		
	$b_hsn = $db->queryUniqueObject("SELECT * FROM gst_invoicesitems WHERE invoiceid='".$invoiceid."' AND hsn_sac='997159' ");
	$b_gst = $gst->getTaxSummary($b_hsn->net_amount,$user->gst_type);
	$b_tax_total = $b_gst['cgst_amount']+$b_gst['sgst_amount']+$b_gst['igst_amount'];
	?>
	<tr>
		<td><?php echo $b_hsn->hsn_sac;?></td>
		<td align="right"><?php echo $b_hsn->taxable_amount;?></td>
		<td width="7%"><?php echo $b_gst['cgst_rate'];?></td>
		<td width="12%" align="right"><?php echo $b_gst['cgst_amount'];?></td>
		<td width="7%"><?php echo $b_gst['sgst_rate'];?></td>
		<td width="12%" align="right"><?php echo $b_gst['sgst_amount'];?></td>
		<td width="7%"><?php echo $b_gst['igst_rate'];?></td>
		<td width="12%" align="right"><?php echo $b_gst['igst_amount'];?></td>
		<td align="right"><?php echo $b_tax_total;?></td>
	</tr>
	<tr style="font-weight: bold">
		<td align="right">Total</td>
		<td align="right"><?php echo $total_taxable;?></td>
		<td></td>
		<td align="right"><?php echo $gst_sum['cgst_amount'];?></td>
		<td></td>
		<td align="right"><?php echo $gst_sum['sgst_amount'];?></td>
		<td></td>
		<td align="right"><?php echo $gst_sum['igst_amount'];?></td>
		<td align="right"><?php echo $tax_total;?></td>
	</tr>
	<tr>
		<td colspan="9">
			<table cellspacing="0">
				<tr>
					<td width="80%">Tax Amount (in words)<br><b><?php echo ucwords($nw->toCurrency($tax_total));?></b></td>
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