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
$rFrom = date("Y-m-d", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$rTo = date("Y-m-d", strtotime($to));
$rptdate = strtotime ( '+1 day' , strtotime ( $rTo ) ) ;
$rptdate = date ( 'Y-m-d' , $rptdate );

$uid = isset($_GET["uid"]) && $_GET["uid"]!='' ? mysql_real_escape_string($_GET["uid"]) : '0';

$meta['title'] = "Day Book Reports";
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
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/transaction.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function calculate()
{
    var comm_recv=$("#comm_recv").val();
    var comm_given=parseFloat($("#comm_given").text());
    var comm_gain=(comm_recv-comm_given);
    $("#comm_gain").text(comm_gain.toFixed(2));
    
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
				<h3 class="box-title"><i class="fa fa-list"></i> Day Book</h3>
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
				$qry = $db->query("
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
					FROM transactions WHERE account_id!=0 AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row = $db->fetchNextObject($qry);
				$qry1 = $db->query("
					SELECT 
						SUM(amount) AS commissionTotal	
					FROM commission_details WHERE 1 AND added_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row1 = $db->fetchNextObject($qry1);
				$sumDebit = $db->queryUniqueObject("SELECT COALESCE(SUM(txnamount),0) AS totalDebit FROM transactions_adm WHERE txntype='dr' AND txndate BETWEEN '".$aFrom."' AND '".$aTo."' ");
				
				$open_bal = "0";
				$opb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM all_user_rpt WHERE rpt_date like '%".$rFrom."%' ");
			
					$open_bal = $opb->closing_balance;
			if(isset($_GET["t"]) && $_GET["t"] != '')
			{
			    	$clb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM all_user_rpt WHERE rpt_date like '%".$rptdate."%' ");   
			}
			else
			{
    			$clb = $db->queryUniqueObject("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS closing_balance FROM apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid WHERE user.uid!='0' AND user.user_type IN (1,3,4,5,6) AND user.status='1' ORDER BY user.user_id DESC");
			}	
			
			
			$sWhere = "WHERE recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' AND tran.transaction_term='RECHARGE' AND tran.type='dr' and recharge.status=1 ";
			$statement = "apps_recharge recharge LEFT JOIN transactions tran ON recharge.recharge_id=tran.transaction_ref_no $sWhere ";
			$pending = $db->queryUniqueValue("SELECT SUM(tran.amount) FROM $statement");
				?>
				<table class="table table-bordered">
					<tr>
						<td colspan="2">Opening Balance</td>
						<td><?php echo round($open_bal,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Debit Amount</td>
						<td><?php echo round($row->debitAmount,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Recharge</td>
						<td></td>
						<td><?php echo round($row->rechargeDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Failure</td>
						<td></td>
						<td><?php echo round($row->failureDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Refund</td>
						<td></td>
						<td><?php echo round($row->refundDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Revert</td>
						<td></td>
						<td><?php echo round($row->revertDebit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Fund Deduct</td>
						<td></td>
						<td><?php echo round($row->balanceDeduct,2);?></td>
					</tr>
					<tr>
						<td colspan="2">BDB</td>
						<td><b><?php echo round($sumDebit->totalDebit,2);?></b></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Pendinng Amount</td>
						<td><b><?php echo round($pending,2);?></b></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Credit Amount</td>
						<td><?php echo round($row->creditAmount,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Recharge</td>
						<td></td>
						<td><?php echo round($row->rechargeCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Failure</td>
						<td></td>
						<td><?php echo round($row->failureCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Refund</td>
						<td></td>
						<td><?php echo round($row->refundCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Revert</td>
						<td></td>
						<td><?php echo round($row->revertCredit,2);?></td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td>Fund Added</td>
						<td></td>
						<td><?php echo round($row->balanceCredit,2);?></td>
					</tr>
					<tr>
						<td colspan="2">Commission Received</td>
						<td><input type="text" id="comm_recv" onchange="calculate()"></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Commission Given</td>
						<td id="comm_given" ><b><?php echo round($row1->commissionTotal,2);?></b></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Commission Gain</td>
						<td id="comm_gain"></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="2">Closing Balance</td>
						<td><?php echo round($clb->closing_balance,2);?></td>
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
						<td><?php echo round($open_bal,2);?></td>
						
						<td><?php $_tcre = $row->creditAmount; echo round($_tcre,2);?></td>
						
						
						
						<td><?php $_tdeb = $row->debitAmount+$row2->fakeDeduct; echo round($_tdeb,2);?></td>
						<td><?php echo round($clb->closing_balance,2);?></td>
						<td><?php $_diff = ($open_bal+$_tcre)-($_tdeb+$clb->closing_balance); echo round($_diff,2);?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php');?>