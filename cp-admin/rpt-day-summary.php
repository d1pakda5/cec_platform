<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$uid = isset($_GET["uid"]) && $_GET["uid"]!='' ? mysql_real_escape_string($_GET["uid"]) : '0';

$meta['title'] = "User Summary Reports";
include('header.php');
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."'");
if($user) {
	$__name = $user->company_name;
	$__uid = $user->uid;
} else {
	$__name = "";
	$__uid = "0";
}
?>
<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery("#ut").change(function(){
		jQuery.ajax({ 
			url: "ajax/list-users.php",
			type: "POST",
			data: "type="+jQuery(this).val(),
			async: false,
			success: function(data) {
				jQuery("select#uid").html(data);
			}
		});
	});
});
jQuery(document).ready(function() {
    var rechargeDebit=parseFloat($("#rechargeDebit").text());
    var failureDebit=parseFloat($("#failureDebit").text());
    var refundDebit=parseFloat($("#refundDebit").text());
    var revertDebit=parseFloat($("#revertDebit").text());
    var balanceDeduct=parseFloat($("#balanceDeduct").text());
    var debitAmount=rechargeDebit+failureDebit+refundDebit+revertDebit-balanceDeduct;
    $("#debitAmount").text(debitAmount);
    
    
    var rechargeCredit=parseFloat($("#rechargeCredit").text());
    var failureCredit=parseFloat($("#failureCredit").text());
    var refundCredit=parseFloat($("#refundCredit").text());
    var revertCredit=parseFloat($("#revertCredit").text());
    var balanceCredit=parseFloat($("#balanceCredit").text());
    // var creditAmount=rechargeCredit+failureCredit+refundCredit+revertCredit+balanceCredit;
    var creditAmount=balanceCredit;
    $("#creditAmount").text(creditAmount);
    var cl_bal=parseFloat($("#cl_balance").val());
    
    
    var commissionTotal=parseFloat($("#commissionTotal").text());
    var fakeDeduct=parseFloat($("#fakeDeduct").text());
    var tdeb=debitAmount;
    var tcre=creditAmount;
    $("#tdeb").text(tdeb);
    $("#tcre").text(tcre);
     $("#cl_bal").text(cl_bal);
    
    
});
function calculate()
{
    var op_bal=parseFloat($("#opening_balance").val());
    $("#cal_opbal").text(op_bal);
    var closing_balance=parseFloat($("#cl_balance").val());
    $("#cl_bal").text(closing_balance);
    var tcre=parseFloat($("#tcre").text());
    var tdeb=parseFloat($("#tdeb").text());
    var commissionTotal=parseFloat($("#commissionTotal").text());
    var rechargeDebit=parseFloat($("#rechargeDebit").text());
     var balanceDeduct=parseFloat($("#balanceDeduct").text());
//   alert(op_bal+"+"+tcre+"+"+commissionTotal+"-"+rechargeDebit+"-"+closing_balance+"-"+balanceDeduct);
   
    var difference=op_bal+tcre+commissionTotal-closing_balance-rechargeDebit-balanceDeduct;
    $("#diff").text(difference.toFixed(2));
    $("#equation").css("display","block");
     $("#equation").text(op_bal+"+"+tcre+"+"+commissionTotal+"-"+rechargeDebit+"-"+closing_balance+"-"+balanceDeduct);
    // alert(difference)
}
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/transaction.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Summary <small>/ <?php echo $__name;?> {<?php echo $__uid;?>}</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> User Summary</h3>
			</div>			
			<div class="box-body min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="row">
					
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
						</div>
					</form>
				</div>
				<?php
				//SUM(CASE WHEN type='cr' AND transaction_term='FUND' THEN amount ELSE 0 END) as balanceAdded 
				$qry0 = $db->query("
					SELECT 
					   
						SUM(IF(type='dr', amount, 0)) AS debitAmount,
						SUM(IF(type='dr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeDebit, 
						SUM(IF(type='dr' AND transaction_term='FAILURE', amount, 0)) AS failureDebit, 
						SUM(IF(type='dr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceDeduct,
						SUM(IF(type='dr' AND transaction_term='REVERT', amount, 0)) AS revertDebit, 
						SUM(IF(type='dr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundDebit,
						SUM(IF(type='cr', amount, 0)) AS creditAmount,
						SUM(IF(type='cr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeCredit,
						SUM(IF(type='cr' AND transaction_term='FAILURE', amount, 0)) AS failureCredit, 
						SUM(IF(type='cr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceCredit,
						SUM(IF(type='cr' AND transaction_term='REVERT', amount, 0)) AS revertCredit, 
						SUM(IF(type='cr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundCredit					
					FROM transactions WHERE account_id='0' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row0 = $db->fetchNextObject($qry);
				$qry = $db->query("
					SELECT 
					    SUM(IF(type='dr' AND (transaction_term='RECHARGE' or transaction_term='FAILURE' or transaction_term='REVERT' or transaction_term='REFUND' OR transaction_term='REFUNDED' ), amount, 0)) AS debitAmount2,
						SUM(IF(type='dr', amount, 0)) AS debitAmount,
						SUM(IF(type='dr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeDebit, 
						SUM(IF(type='dr' AND transaction_term='FAILURE', amount, 0)) AS failureDebit, 
						SUM(IF(type='dr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceDeduct,
						SUM(IF(type='dr' AND transaction_term='REVERT', amount, 0)) AS revertDebit, 
						SUM(IF(type='dr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundDebit,
						 SUM(IF(type='cr' AND (transaction_term='RECHARGE' or transaction_term='FAILURE' or transaction_term='REVERT' or transaction_term='REFUND' OR transaction_term='REFUNDED' ), amount, 0)) AS creditAmount2,
						SUM(IF(type='cr', amount, 0)) AS creditAmount,
						SUM(IF(type='cr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeCredit,
						SUM(IF(type='cr' AND transaction_term='FAILURE', amount, 0)) AS failureCredit, 
						SUM(IF(type='cr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceCredit,
						SUM(IF(type='cr' AND transaction_term='REVERT', amount, 0)) AS revertCredit, 
						SUM(IF(type='cr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundCredit					
					FROM transactions WHERE 1 AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row = $db->fetchNextObject($qry);
				$qry1 = $db->query("
					SELECT 
						SUM(amount) AS commissionTotal	
					FROM commission_details WHERE 1 AND added_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row1 = $db->fetchNextObject($qry1);
				$qry2 = $db->query("
					SELECT 
						SUM(IF(type='dr', amount, 0)) AS fakeDeduct	
					FROM trans_deduct WHERE 1 AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row2 = $db->fetchNextObject($qry2);
				
				$sumDebit = $db->queryUniqueObject("SELECT COALESCE(SUM(txnamount),0) AS totalDebit FROM transactions_adm WHERE txntype='dr' AND txndate BETWEEN '".$aFrom."' AND '".$aTo."' ");
				 
				$open_bal = "0";
				$recharge = $db->queryUniqueValue("SELECT sum(amount) from apps_recharge where status in(0,1,8) and request_date like '%".$from."%' ");
				$clb = $db->queryUniqueObject("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS closing_balance FROM apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid WHERE user.uid!='0' AND user.user_type IN (1,3,4,5,6) AND user.status='1' ORDER BY user.user_id DESC");
				
				?>
				<table class="table table-bordered">
					<tr>
						<td colspan="2">Opening Balance</td>
						<td><input type="text" id="opening_balance" onchange="calculate()"></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Debit Amount</td>
						<td id="debitAmount"></td>
						<td></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Recharge</td>
						<td></td>
						<td id="rechargeDebit"><?php echo round($recharge,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Failure</td>
						<td></td>
						<td id="failureDebit"><?php echo round($row->failureDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Refund</td>
						<td></td>
						<td id="refundDebit"><?php echo round($row->refundDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Revert</td>
						<td></td>
						<td id="revertDebit"><?php echo round($row->revertDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Fund Deduct</td>
						<td></td>
						<td id="balanceDeduct"><?php echo round($row0->balanceCredit,2);?></td>
					</tr>
					<tr>
						<td colspan="2">Backend Debit</td>
						<td id="fakeDeduct"><?php echo round($sumDebit->totalDebit,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Credit Amount</td>
						<td id="creditAmount"></td>
						<td></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Recharge</td>
						<td></td>
						<td id="rechargeCredit"><?php echo round($row->rechargeCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Failure</td>
						<td></td>
						<td id="failureCredit"><?php echo round($row->failureCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Refund</td>
						<td></td>
						<td id="refundCredit"><?php echo round($row->refundCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Revert</td>
						<td></td>
						<td id="revertCredit"><?php echo round($row->revertCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Fund Added</td>
						<td></td>
						<td id="balanceCredit"><?php echo round($row0->balanceDeduct,2);?></td>
					</tr>
					<tr>
						<td colspan="2">Commission Earned </td>
						<td id="commissionTotal"><?php echo round($row1->commissionTotal,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Closing Balance</td>
						<td id="closing_balance"><input type="text" id="cl_balance" onchange="calculate()" value="<?php echo round($clb->closing_balance,2);?>" ></td>
						<td></td>
					</tr>
				</table>
				<table class="table table-bordered" style="margin-top:30px;">
					<tr>
						<td>Opening Balance</td>
						<td>Total Credit</td>
						<td>Total Debit</td>
						<td>Closing Balance</td>
						<td>Difference Amount</td>
					</tr>
					<tr>
						<td id="cal_opbal"></td>
						
						<td id="tcre"></td>
						
						<td id="tdeb"></td>
						
						<td id="cl_bal"></td>
						
						<td id="diff"></td>
					</tr>
					<tr>
						<td id="equation" colspan=3 style="display:none"></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php');?>