<?php
include('config.php');
include('class.gst.php');
$gst = new GST();
include('../system/class.numtowords.php');
$nw = new Numbers_Words();
//
$status = isset($_GET['status']) && $_GET['status']!='' ? mysql_real_escape_string($_GET['status']) : '';
$uid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '4';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : date("Y");
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : '0';
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' ");
if(!$user) {
	header("location:index.php");
	exit();
}
//
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$sWhere = "WHERE uid='".$uid."' AND bill_type IN (2,3) AND rch_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
//
$months = $gst->getMonthList();
$invoice_date = date("t-m-Y", strtotime($year."-".$month."-01"));
$invoice_month = date("d-m-Y", strtotime($year."-".$month."-01"));
$invoiceid = 0;
$invoice_num = "";
$in_month = explode("-",$invoice_month);
$invoice_month_ymd = $in_month[2]."-".$in_month[1]."-".$in_month[0];
$invoice = $db->queryUniqueObject("SELECT * FROM gst_debitnote WHERE uid='".$uid."' AND invoice_month='".$invoice_month_ymd."' ");
if($invoice) {
	$invoiceid = $invoice->id;
}	
$seller_info = $gst->getSellerDetail('0',$user);
$buyer_info = $gst->getSellerDetail('1',$user);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Debit-Invoice-<?php echo $uid;?></title>
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
		url : 'ajax-generate-debit.php',
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
		<?php if($invoiceid!="") { ?>
		<div class="btn-group btn-group-sm hidden-print">
			<a href="javascript:void()" onClick="printDiv('divPrint');" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
			<a href="pdfd.php?id=<?php echo $invoiceid;?>&type=<?php echo $type;?>" class="btn btn-default"><i class="fa fa-download"></i> Download</a>
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
					<td colspan="4"><h3 class="text-center"><b>DEBIT NOTE AGAINST RECHARGE</b></h3></td>
				</tr>
				<tr>
					<td width="50%" rowspan="2" colspan="2">
						<img src='../images/logo-ec.png' class="img-responsive" />
					</td>
					<td style="width: 25%;">Invoice Number<br /><strong><?php echo $invoiceid;?></strong></td>
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
					<td>Description</td>
					<td width="20%">Recharge Amount</td>
					<td width="20%">Debit Amount</td>
				</tr>
				<?php
				$a_statement = "SELECT SUM(rch_amount) AS amount, SUM(IF(bill_type='2', rch_comm_value, 0)) AS comm_amount FROM gst_monthly_txns $sWhere ";
				$query = $db->query($a_statement);
				$row = $db->fetchNextObject($query);
				$description = "Debit against recharge received";
				$recharge_amount = $row->amount;
				$comm_amount = $row->comm_amount;
				$debit_amount = $recharge_amount - $comm_amount;
				?>
				<tr>
					<td><?php echo $description;?></td>
					<td><?php echo $recharge_amount;?></td>
					<td align="right" class="tot_netamt"><?php echo $debit_amount;?></td>
				</tr>	
				<tr style="font-weight: bold">
					<td align="right" colspan="2">Total</td>
					<td style="text-align: right"><?php echo $debit_amount;?></td>
				</tr>
				<tr>
					<td colspan="11">
						<span class="pull-left">Amount Chargeable (in words)<br><b><?php echo ucwords($nw->toCurrency($debit_amount));?></b></span>
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
				<tr>
					<td colspan="2" align="center"><b>THIS IS NOT A TAX INVOICE</b></td>
				</tr>
			</table>
			<center>This is a Computer Generated Invoice and does not require signatures</center>
		</div>
	<div>	
</div>
	<input type="hidden" name="bill_type" id="billType" value="debit note">
	<input type="hidden" name="description" id="description" value="<?php echo $description;?>">
	<input type="hidden" name="unit" id="unit" value="Unit">
	<input type="hidden" name="uid" id="uid" value="<?php echo $uid;?>">
	<input type="hidden" name="invoice_date" id="invoiceDate" value="<?php echo $invoice_date;?>">
	<input type="hidden" name="invoice_month" id="invoiceMonth" value="<?php echo $invoice_month;?>">
	<input type="hidden" name="bill_mode" id="billMode" value="Advance">
	<input type="hidden" name="amount" id="amountAmt" value="<?php echo $recharge_amount;?>">
	<input type="hidden" name="commission" id="commissionAmt" value="<?php echo $comm_amount;?>">
	<input type="hidden" name="debit_amount" id="debitAmt" value="<?php echo $debit_amount;?>">
</form>
</body>
</html>