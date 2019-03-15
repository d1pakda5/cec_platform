<?php
include('../config.php');
include('../system/class.gst.php');
$gst = new GST();
//
$uid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : '0';
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
$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$user->uid."' ");
//
$sWhere = "WHERE rch.uid='".$uid."' AND rch.status='0' AND billing_type='".$type."' ";

$dt = $year."-".$month;
$sWhere .= "AND rch.request_date BETWEEN '".$dt."-01 00:00:00' AND '".$dt."-31 23:59:59' ";
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere GROUP BY rch.operator_id ORDER BY opr.service_type ASC";

$comms = [];
$query = $db->query("SELECT * FROM apps_commission WHERE uid='".$uid."' ");
while($result = $db->fetchNextObject($query)) {
	$comms[] = $result;
}
$months = $gst->getMonthList();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Invoice-<?php echo $uid;?></title>
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
<script>
function printDiv(divtags) {
	var headstr = "<html><head><title>Invoice-<?php echo $uid;?></title></head><body>";
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
		<div class="btn-group btn-group-sm hidden-print">
			<a href="javascript:window.print()" class="btn btn-default"><i class="fa fa-print"></i> Save</a>
			<a href="javascript:void()" onClick="printDiv('divPrint');" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
			<a href="getpdf.php?a=d&id=<?php echo $uid;?>" class="btn btn-default"><i class="fa fa-download"></i> Download</a>
		</div>
	</div>
</div>
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
					<td style="width: 25%;">Invoice Number<br /><strong>Jul/2017/15</strong></td>
					<td style="width: 25%;">Date<br /><strong>31-July-2017</strong></td>
				</tr>
				<tr>
					<td>Dealer Code<br /><strong><?php echo $uid;?></strong></td>
					<td>Mode/Terms of Payment<br /><strong>Advance</strong></td>
				</tr>
				<tr>
					<td colspan="2">
						<p style="font-size:12px;">SELLER</p>
						<p><b>CLICK E CHARGE SERVICES PRIVATE LIMITED</b><br />
						Office No. 6, Ganesham I Commercial Complex<br />
						Pimple Saudagar, Pune <br />
						Maharashtra, India - 411027<br />
						Telephone: 020-228677, 8600250250<br />
						E-Mail:- ankitsales@gmail.com<br />
						GSTIN: 27AAGCC1604F1Z4<br>
						PAN: 
						</p>
					</td>
					<td colspan="2">
						<p style="font-size:12px;">BUYER</p>
						<p><b><?php echo $user->company_name;?></b><br />
						<?php echo $user->address;?><br />
						<?php echo $user->city;?><br />
						<?php echo $user->states;?>, India - 411027<br />
						Telephone: <?php echo $user->phone;?>, <?php echo $user->mobile;?><br />
						E-Mail:- <?php echo $user->email;?><br />
						GSTIN: <?php echo $user->gstin;?> &nbsp;&nbsp;PAN: <?php echo $user->panno;?><br>
						Place of Supply: <?php echo $user->states;?>
						</p>
					</td>						
				</tr>
			</table>			
			<table class="table table-bordered">
				<tr style="font-weight: bold">
					<td width="6%">S. No.</td>
					<td>Description</td>
					<td width="10%">HSNCODE</td>
					<td width="10%">GST Rate</td>
					<td width="15%">Quantity</td>
					<td width="10%">Rate</td>
					<td width="7%">Per</td>
					<td width="15%">Amount</td>
				</tr>
				<?php
				$tot_netamt = 0;
				$scnt = 1;				
				$query = $db->query("SELECT SUM(rch.amount) AS amt, rch.operator_id, opr.operator_name, opr.hsn_sac_code FROM {$statement} ");
				if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
				while($result = $db->fetchNextObject($query)) {
					$comper = $gst->getCommissionPer($result->operator_id,$comms,$user);
					$tcomms = $gst->getCommissionTotal($result->amt,$result->operator_id,$comms,$user);
					$tamount = round($gst->getTaxableAmount($tcomms),4);
					$cgst = ($tamount*9)/100;
					$sgst = ($tamount*9)/100;
					$igst = ($tamount*18)/100;
					$aftamt = $result->amt - $tcomms;
					$netamt = round($gst->getTaxableAmount($aftamt),4);
					$rate = $netamt/$result->amt;
					$tot_netamt += $netamt;			
				?>
				<tr>
					<td><?php echo $scnt++;?>.</td>
					<td><?php echo $result->operator_name;?></td>
					<td><?php echo $result->hsn_sac_code;?></td>
					<td>18%</td>					
					<td align="right"><?php echo $result->amt;?></td>
					<td><?php echo round($rate,3);?></td>
					<td>Unit</td>
					<td align="right" class="tot_netamt"><?php echo $netamt;?></td>
				</tr>
				<?php } ?>
				<?php		
				$statement2 = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere ORDER BY opr.service_type ASC";	
				$query = $db->query("SELECT SUM(rch.amount) AS amt FROM {$statement2} ");
				$result = $db->fetchNextObject($query);
					$txamt = ($result->amt*100/109);
					$sgs = round($result->amt-$txamt,4);	
					$cgs = round($result->amt-$txamt,4);
				?>
				<tr style="font-weight: bold">
					<td align="right" colspan="7">Total</td>
					<td style="text-align: right"><?php echo $tot_netamt;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="7">SGST 9%</td>
					<td style="text-align: right"><?php echo $sgs;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="7">CGST 9%</td>
					<td style="text-align: right"><?php echo $cgs;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right" colspan="4"></td>
					<td align="right"><?php echo $result->amt;?></td>
					<td align="right" colspan="2">Total</td>
					<td style="text-align: right"><?php echo $tot_netamt+$sgs+$cgs;?></td>
				</tr>
				<tr>
					<td colspan="8">
						<span class="pull-left">Amount Chargeable (in words)<br><b><?php echo ucwords($gst->numberTowords($tot_netamt+$sgs+$cgs));?></b></span>
						<span class="pull-right">E. & O.E</span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td colspan="7">Tax Summary</td>
				</tr>
				<tr>
					<td rowspan="2">HSN/SAC</td>
					<td rowspan="2" width="12%">Taxable Value</td>
					<td colspan="2" width="20%">Central Tax</td>
					<td colspan="2" width="20%">State Tax</td>
					<td rowspan="2" width="15%">Total Tax Amount</td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>Amount</td>
					<td>Rate</td>
					<td>Amount</td>
				</tr>
				<tr>
					<td>996111</td>
					<td align="right"><?php echo $tot_netamt;?></td>
					<td>9%</td>
					<td align="right"><?php echo $cgs;?></td>
					<td>9%</td>
					<td align="right"><?php echo $cgs;?></td>
					<td align="right"><?php echo $sgs+$cgs;?></td>
				</tr>
				<tr style="font-weight: bold">
					<td align="right">Total</td>
					<td align="right"><?php echo $tot_netamt;?></td>
					<td></td>
					<td align="right"><?php echo $cgs;?></td>
					<td></td>
					<td align="right"><?php echo $cgs;?></td>
					<td align="right"><?php echo $sgs+$cgs;?></td>
				</tr>
				<tr>
					<td colspan="7">
						<span class="pull-left">Amount Chargeable (in words)<br><b><?php echo ucwords($gst->numberTowords($sgs+$cgs));?></b></span>
						<span class="pull-right">E. & O.E</span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<tr>
					<td width="60%"><b>Declaration</b><br>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</td>
					<td align="right">
						for <b>CLICK E CHARGE SERVICES PRIVATE LIMITED</b>
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
</body>
</html>
