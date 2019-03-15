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
							<div class="form-group">
								<input type="text" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="Enter UID" class="form-control">
							</div>
						</div>
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
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row = $db->fetchNextObject($qry);
				$qry1 = $db->query("
					SELECT 
						SUM(amount) AS commissionTotal	
					FROM commission_details WHERE uid='".$uid."' AND added_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row1 = $db->fetchNextObject($qry1);
				$qry2 = $db->query("
					SELECT 
						SUM(IF(type='dr', amount, 0)) AS fakeDeduct	
					FROM trans_deduct WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row2 = $db->fetchNextObject($qry2);
				
				$open_bal = "0";
				$opb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date < '".$aFrom."' ORDER BY transaction_id DESC");
				if($opb) {
					$open_bal = $opb->closing_balance;
				} else {
					$opb1 = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ORDER BY transaction_id ASC");
					if($opb1) {
						$open_bal = $opb1->closing_balance;
					}
				}
				
				$clb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ORDER BY transaction_id DESC");
				
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
						<td colspan="2">Backend Debit</td>
						<td><?php echo round($row2->fakeDeduct,2);?></td>
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
						<td colspan="2">Commission Earned</td>
						<td><?php echo round($row1->commissionTotal,2);?></td>
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
						<?php if($user->user_type=='1' || $user->user_type=='5') {?>
						<td><?php $_tcre = $row->creditAmount; echo round($_tcre,2);?></td>
						<?php } else { ?>
						<td><?php $_tcre = $row->creditAmount+$row1->commissionTotal; echo round($_tcre,2);?></td>
						<?php } ?>
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