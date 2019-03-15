<?php
include('../config.php');
include('../system/class.gst.php');
$gst = new GST();
include('../system/class.numtowords.php');
$nw = new Numbers_Words();
//
$status = isset($_GET['status']) && $_GET['status']!='' ? mysql_real_escape_string($_GET['status']) : '';
$uid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '1';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : date("Y");
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : '0';
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' ");
if(!$user) {
	header("location:index.php");
	exit();
}
$balance = "ERROR";
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id='".$user->user_id."' ");
if($wallet) {
	$balance = $wallet->balance;
}	
//
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$sWhere = "";
if($user->user_type=='4') {
	$ret = '';
	$qry = $db->query("SELECT uid FROM apps_user WHERE dist_id='".$user->uid."' ");
	while($rlt = $db->fetchNextObject($qry)) {
		$ret .= $rlt->uid.", ";
	}
	$ret .= '0';
	$sWhere = "WHERE rch.uid IN ($ret) AND rch.status='0' AND opr.billing_type='".$type."' AND rch.request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
} else {
	$sWhere = "WHERE rch.uid='".$uid."' AND rch.status='0' AND opr.billing_type='".$type."' AND rch.request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere GROUP BY opr.item_group ORDER BY opr.item_group ASC";
// Commissions
if($user->user_type=='1') {
	$comm_uid = $uid;
} else if($user->user_type=='4') {
	$comm_uid = $uid;
} else if($user->user_type=='5') {
	$comm_uid = $user->dist_id;
} else {
	$comm_uid = 0;
}
$comms = [];
$query = $db->query("SELECT * FROM usercommissions WHERE uid='".$comm_uid."' ");
while($result = $db->fetchNextObject($query)) {
	$comms[] = $result;
}
//
$months = $gst->getMonthList();
$invoice_date = date("t-m-Y", strtotime($year."-".$month."-01"));
$invoice_month = date("d-m-Y", strtotime($year."-".$month."-01"));
$invoiceid = 0;
$invoice_num = "";
$in_month = explode("-",$invoice_month);
$invoice_month_ymd = $in_month[2]."-".$in_month[1]."-".$in_month[0];
$invoice = $db->queryUniqueObject("SELECT * FROM gstinvoices WHERE uid='".$uid."' AND invoice_month='".$invoice_month_ymd."' AND bill_type='".$type."' ");
if($invoice) {
	$invoiceid = $invoice->id;
	$invoice_num = $invoice->invoice_num;
}	
$seller_info = $gst->getSellerDetail('0',$user);
$buyer_info = $gst->getSellerDetail('1',$user);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>P2P-Invoice-<?php echo $uid;?></title>
<link rel="stylesheet" href="../css/bootstrap.css">
<style type="text/css">
.header {
	width:100%;
	float:left;
	background:rgba(255,255,255,0.9);
	border-bottom:1px solid #ddd;
	padding:8px 0px;
	margin-bottom:10px;
	position:fixed;
	z-index:9999;
}
.invoice-body {
	width:100%;
	float:left;
	padding:0px;
	margin-top:60px;
}
.container {
	width:920px;
}
table p {
	margin-bottom:0px;
}
table h3 {
	margin:0px;
}
.table {
	margin-bottom:0px;
}
.table-bordered th,
.table-bordered td {
	border: 1px solid #333 !important;
}
.format {
	font-size: 14px;
}
@page {
	size: A4;
	margin: 0.07;
}
@media print {
	html, body {
		width: 210mm;
		height: 297mm;
	}
	/* ... the rest of the rules ... */
}
.table-bordered {
	border-color:#333333;
}
</style>
<script src="../js/jquery.min.js"></script>
<script>
function generateInvoice(){
	$.ajax({
		type : "POST",
		cache : false,
		url : 'ajax/ajax-generate-invoice.php',
		data : $("#genInvoice").serializeArray(),
		success : function(data) {
			if(data=='0') {				
				alert("Error, Cannot Generate Invoice, Try Again");
			} else {
				alert("Success, Invoice generated succesfully");
				window.location.reload();
			}
		}
	});
}
function printDiv(divtags) {
	var headstr = "<html><head><title>Invoice-<?php echo $invoice_num;?></title></head><body>";
	var footstr = "</body></html>";
	var newstr = document.all.item(divtags).innerHTML;
	var oldstr = document.body.innerHTML;
	document.body.innerHTML = headstr+newstr+footstr;
	window.print();
	document.body.innerHTML = oldstr;
	return false;
}
</script>
</head>
<body>
<div class="header">
	<div class="container-fluid text-right">
		<?php if($invoice_num!="") { ?>
		<div class="btn-group btn-group-sm hidden-print">
			<a href="javascript:void()" onClick="printDiv('divPrint');" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
			<a href="../gst/getpdf.php?id=<?php echo $invoiceid;?>&type=<?php echo $type;?>" class="btn btn-default"><i class="fa fa-download"></i> Download</a>
		</div>
		<?php }else{ ?>
		<div class="btn-group btn-group-sm hidden-print">
			<a href="javascript:void()" onClick="generateInvoice();" class="btn btn-default"><i class="fa fa-print"></i> Generate Invoice</a>
		</div>
		<?php } ?>
	</div>
</div>
<form name="gen_invoice" id="genInvoice">
<div class="invoice-body">
	<div class="container">
		<div id="divPrint">
			<table class="table table-bordered">
				<tr>
					<td colspan="4"><h3 class="text-center"><b>TAX INVOICE</b></h3></td>
				</tr>
				<tr>
					<td width="50%" rowspan="2" colspan="2">
						<img src='../images/logo-ec.png' class="img-responsive" />
					</td>
					<td style="width: 25%;">Invoice Number<br /><strong><?php echo $invoice_num;?></strong></td>
					<td style="width: 25%;">Date<br /><strong><?php echo $invoice_date;?></strong></td>
				</tr>
				<tr>
					<td>Dealer Code<br /><strong><?php echo $uid;?></strong></td>
					<td>Mode/Terms of Payment<br /><strong>Advance</strong></td>
				</tr>
				<tr>
					<td colspan="2">
						<p style="font-size:12px;">SELLER</p>
						<p><b><?php echo $seller_info['cn'];?></b><br />
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
						<p style="font-size:12px;">BUYER</p>
						<p><b><?php echo $buyer_info['cn'];?></b><br />
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
			<table class="table table-bordered">
				<tr style="font-weight: bold">
					<td width="8%">S. No.</td>
					<td>Description</td>
					<td width="10%">HSNCODE</td>
					<td width="15%">Quantity</td>
					<td width="10%">Rate</td>
					<td width="10%">Per</td>
					<td width="15%">Amount</td>
				</tr>
				<?php
				$statement1 = "SELECT SUM(rch.amount) AS amt, rch.operator_id, opr.operator_name, opr.service_type, opr.hsn_sac_code, opr.item_group FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere AND item_group='1' GROUP BY rch.operator_id";
				$item1 = $gst->getRowByItemGroup($statement1,$comms,$user->user_type);
				$item1_desc = $gst->getItemGroupName('1');
				$item1_sac = $gst->getItemGroupSac('1');
				$item1_rate =  round($item1['taxable']/$item1['gross'],4);
				?>
				<tr>
					<td>1.
						<input type="hidden" name="item_id[]" id="item_id" value="1">
						<input type="hidden" name="item_desc[]" id="item_desc" value="<?php echo $item1_desc;?>">
						<input type="hidden" name="item_sac[]" id="item_sac" value="<?php echo $item1_sac;?>">
						<input type="hidden" name="item_amt[]" id="item_amt" value="<?php echo $item1['gross'];?>">
						<input type="hidden" name="item_net[]" id="item_net" value="<?php echo $item1['net'];?>">
						<input type="hidden" name="item_taxable[]" id="item_taxable" value="<?php echo $item1['taxable'];?>">
						<input type="hidden" name="item_tax[]" id="item_tax" value="<?php echo $item1['gst'];?>">
						<input type="hidden" name="item_rate[]" id="item_rate" value="<?php echo $item1_rate;?>">
						<input type="hidden" name="item_unit[]" id="item_unit" value="Unit">
					</td>
					<td><?php echo $item1_desc;?></td>
					<td><?php echo $item1_sac;?></td>
					<td align="right"><?php echo $item1['gross'];?></td>
					<td><?php echo $item1_rate;?></td>
					<td>Unit</td>
					<td align="right" class="tot_netamt"><?php echo $item1['taxable'];?></td>
				</tr>
				<?php
				$statement2 = "SELECT SUM(rch.amount) AS amt, rch.operator_id, opr.operator_name, opr.service_type, opr.hsn_sac_code, opr.item_group FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere AND item_group='2' GROUP BY rch.operator_id";
				$item2 = $gst->getRowByItemGroup($statement2,$comms,$user->user_type);
				$item2_desc = $gst->getItemGroupName('2');
				$item2_sac = $gst->getItemGroupSac('2');
				$item2_rate =  round($item2['taxable']/$item2['gross'],4);
				?>				
				<tr>
					<td>2.
						<input type="hidden" name="item_id[]" id="item_id" value="2">
						<input type="hidden" name="item_desc[]" id="item_desc" value="<?php echo $item2_desc;?>">
						<input type="hidden" name="item_sac[]" id="item_sac" value="<?php echo $item2_sac;?>">
						<input type="hidden" name="item_amt[]" id="item_amt" value="<?php echo $item2['gross'];?>">
						<input type="hidden" name="item_net[]" id="item_net" value="<?php echo $item2['net'];?>">
						<input type="hidden" name="item_taxable[]" id="item_taxable" value="<?php echo $item2['taxable'];?>">
						<input type="hidden" name="item_tax[]" id="item_tax" value="<?php echo $item2['gst'];?>">
						<input type="hidden" name="item_rate[]" id="item_rate" value="<?php echo $item2_rate;?>">
						<input type="hidden" name="item_unit[]" id="item_unit" value="Unit">
					</td>
					<td><?php echo $item2_desc;?></td>
					<td><?php echo $item2_sac;?></td>
					<td align="right"><?php echo $item2['gross'];?></td>
					<td><?php echo $item2_rate;?></td>
					<td>Unit</td>
					<td align="right" class="tot_netamt"><?php echo $item2['taxable'];?></td>
				</tr>
				<?php
				$total_gross_amount = $item1['gross'] + $item2['gross'];
				$total_net_amount = $item1['net'] + $item2['net'];
				$total_taxable_amount = $item1['taxable'] + $item2['taxable'];
				$gst_sum = $gst->getTaxSummary($total_net_amount,$user->gst_type);
				$gross_total = $total_taxable_amount+$gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
				$tax_total = $gst_sum['cgst_amount']+$gst_sum['sgst_amount']+$gst_sum['igst_amount'];
				?>
				<tr style="font-weight: bold">
					<td align="right" colspan="6">Total</td>
					<td style="text-align: right"><?php echo $total_taxable_amount;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="6">CGST @ <?php echo $gst_sum['cgst_rate'];?></td>
					<td style="text-align: right"><?php echo $gst_sum['cgst_amount'];?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="6">SGST @ <?php echo $gst_sum['sgst_rate'];?></td>
					<td style="text-align: right"><?php echo $gst_sum['sgst_amount'];?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="6">IGST @ <?php echo $gst_sum['igst_rate'];?></td>
					<td style="text-align: right"><?php echo $gst_sum['igst_amount'];?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="3">Total</td>
					<td align="right"><?php echo $total_gross_amount;?></td>
					<td colspan="2" align="right"></td>
					<td style="text-align: right"><?php echo $gross_total;?></td>
				</tr>
				<tr>
					<td colspan="11">
						<span class="pull-left">Amount Chargeable (in words)<br><b><?php echo ucwords($nw->toCurrency($gross_total));?></b></span>
						<span class="pull-right">E. & O.E</span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td colspan="9">Tax Summary</td>
				</tr>
				<tr>
					<td rowspan="2">HSN/SAC</td>
					<td rowspan="2" width="12%">Taxable Value</td>
					<td colspan="2" width="18%">Central Tax</td>
					<td colspan="2" width="18%">State Tax</td>
					<td colspan="2" width="18%">Integrated Tax</td>
					<td rowspan="2" width="15%">Total Tax Amount</td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>Amount</td>
					<td>Rate</td>
					<td>Amount</td>
					<td>Rate</td>
					<td>Amount</td>
				</tr>
				<tr>
					<td><?php echo $item2_sac;?></td>
					<td align="right"><?php echo $total_taxable_amount;?></td>
					<td><?php echo $gst_sum['cgst_rate'];?></td>
					<td align="right"><?php echo $gst_sum['cgst_amount'];?></td>
					<td><?php echo $gst_sum['sgst_rate'];?></td>
					<td align="right"><?php echo $gst_sum['sgst_amount'];?></td>
					<td><?php echo $gst_sum['igst_rate'];?></td>
					<td align="right"><?php echo $gst_sum['igst_amount'];?></td>
					<td align="right"><?php echo $tax_total;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right">Total</td>
					<td align="right"><?php echo $total_taxable_amount;?></td>
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
						<span class="pull-left">Tax Amount (in words)<br><b><?php echo ucwords($nw->toCurrency($tax_total));?></b></span>
						<span class="pull-right">E. & O.E</span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td width="60%"><b>Declaration</b><br>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</td>
					<td align="right">
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
			<center>This is a Computer Generated Invoice and does not require signatures</center>
		</div>
	<div>	
</div>
	<input type="hidden" name="bill_type" id="billType" value="<?php echo $type;?>">
	<input type="hidden" name="uid" id="uid" value="<?php echo $uid;?>">
	<input type="hidden" name="invoice_date" id="invoiceDate" value="<?php echo $invoice_date;?>">
	<input type="hidden" name="invoice_month" id="invoiceMonth" value="<?php echo $invoice_month;?>">
	<input type="hidden" name="bill_mode" id="billMode" value="Advance">
	<input type="hidden" name="sub_total" id="subTotalAmt" value="<?php echo $total_taxable_amount;?>">
	<input type="hidden" name="tax_total" id="totalTaxAmt" value="<?php echo $tax_total;?>">
	<input type="hidden" name="total_amt" id="totalAmt" value="<?php echo $gross_total;?>">
	<input type="hidden" name="tax_type" id="taxType" value="<?php echo $user->gst_type;?>">
</form>
</body>
</html>