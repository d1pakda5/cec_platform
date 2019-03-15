<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE date LIKE '%".$aFrom."%' ";

if(isset($_POST['submit']))
{
    $query = $db->query("SELECT * FROM api_list where status =1 and api_id not in ('13','11')");
	while($result = $db->fetchNextObject($query)) 
	{
	    
    	$exists = $db->queryUniqueObject("SELECT id FROM api_book WHERE date='".$from."' AND api_id='".$result->api_id."'");
				if($exists) {
					$db->execute("UPDATE api_book SET opening_balance='".mysql_real_escape_string($_POST['opening_balance'][$result->api_id])."', closing_balance='".mysql_real_escape_string($_POST['closing_balance'][$result->api_id])."', fund_add='".$_POST['fundadd'][$result->api_id]."' , recharge='".$_POST['recharge'][$result->api_id]."', failed='".$_POST['failed'][$result->api_id]."', refunded='".$_POST['refunded'][$result->api_id]."', sale='".$_POST['sale'][$result->api_id]."',opening_balance1='".mysql_real_escape_string($_POST['opening_balance1'][$result->api_id])."', closing_balance1='".mysql_real_escape_string($_POST['closing_balance1'][$result->api_id])."', fund_add1='".$_POST['fundadd1'][$result->api_id]."' , recharge1='".$_POST['recharge1'][$result->api_id]."', failed1='".$_POST['failed1'][$result->api_id]."', refunded1='".$_POST['refunded1'][$result->api_id]."', sale1='".$_POST['sale1'][$result->api_id]."' WHERE date='".$from."' AND api_id='".$result->api_id."' ");
					}
					 else {
					
							$db->execute("INSERT INTO `api_book`(`id`, `date`, `api_id`, `opening_balance`, `closing_balance`, `fund_add`, `recharge`, `failed`, `refunded`, `sale`,`opening_balance1`,`closing_balance1`, `recharge1`, `pending1`, `failed1`, `refunded1`, `sale1`, `fund_add1`) VALUES ('','".$from."','".$result->api_id."','".mysql_real_escape_string($_POST['opening_balance'][$result->api_id])."','".mysql_real_escape_string($_POST['closing_balance'][$result->api_id])."','".$_POST['fundadd'][$result->api_id]."','".$_POST['recharge'][$result->api_id]."','".$_POST['failed'][$result->api_id]."','".$_POST['refunded'][$result->api_id]."','".$_POST['sale'][$result->api_id]."','".mysql_real_escape_string($_POST['opening_balance1'][$result->api_id])."','".mysql_real_escape_string($_POST['closing_balance1'][$result->api_id])."','".$_POST['fundadd1'][$result->api_id]."','".$_POST['recharge1'][$result->api_id]."','".$_POST['failed1'][$result->api_id]."','".$_POST['refunded1'][$result->api_id]."','".$_POST['sale1'][$result->api_id]."') ");
					}
						$error = 3;
			
    }
}

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-all-user.php');



$meta['title'] = "API Book Report";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
    
    // alert($(".click_it").attr("val"));
});


jQuery(document).ready(function() {
    
 
    
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});


});

</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ API Book</small></div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success" role="alert">
			Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Reports</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">

										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										
								<input type="submit" value="Filter" class="btn btn-warning">
									</div>
								</div>
							</div>
						</div>
					
						
						
						
					
					</form>
				</div>
				<form id="form1" method="post" action="" class="form-inline">
						
				<table class="table table-striped table-bordered table-condensed-sm">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th width="10%">Date</th>
							<th width="10%">API</th>
							<th width="15%">Opening Bal</th>
							<th width="15%">Closing Bal</th>
							<th width="10%">Fund Added</th>
							<th width="10%">Recharge</th>
							<th width="10%">Pending</th>
							<th width="10%">Failed</th>
							<th width="10%">Refunded</th>
							<th width="10%">Total Sale</th>
						</tr>
					</thead>
					<tbody>
					    
						<?php
						$query = $db->query("SELECT * FROM api_list where status =1 and api_id not in ('13','11')");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						   $api_info = $db->queryUniqueObject("SELECT * FROM api_book WHERE api_id='".$result->api_id."' AND date='".$from."' ");
						   	if($api_info) {
									$fund_add = $api_info->fund_add;
									$recharge = $api_info->recharge;
									$failed = $api_info->failed;
									$refunded = $api_info->refunded;
									$sale = $api_info->sale;
									$pending=$api_info->pending;
									$opening_balance=$api_info->opening_balance;
									$opening_balance1=$api_info->opening_balance1;
									$closing_balance=$api_info->closing_balance;
									$closing_balance1=$api_info->closing_balance1;
									$opening_balancediff=$opening_balance-$opening_balance1;
								
									$closing_balancediff=$closing_balance-$closing_balance1;
									$fund_add1 = $api_info->fund_add1;
									$recharge1 = $api_info->recharge1;
									$failed1 = $api_info->failed1;
									$refunded1 = $api_info->refunded1;
									$sale1 = $api_info->sale1;
									$pending1=$api_info->pending1;
									$fundadddiff = $fund_add-$fund_add1;
									$rechargediff = $recharge-$recharge1;
									$faileddiff = $failed-$failed1;
									$refundeddiff = $refunded-$refunded1;
									$salediff = $sale-$sale1;
									$pendingdiff=$pending-$pending1;
								
								} else {
									$fund_add = "";
									$recharge = "";
									$failed ="";
									$refunded = "";
									$sale = "";
									$fund_add1 = "";
									$recharge1 = "";
									$failed1 ="";
									$refunded1 = "";
									$sale1 = "";
									$fund_adddiff = "";
									$rechargediff = "";
									$faileddiff = "";
									$refundeddiff = "";
									$salediff = "";
									$pending1="";
									$pending="";
									$pendingdiff="";

								}
						?>
						<tr>
						    <input type="hidden" name="api_id" id="api_id" value="<?php echo  $result->api_id;?>" />
						    
							<td rowspan="3" align="center"><?php echo $scnt++;?></td>
							<td rowspan="3" ><?php echo $from;?></td>
							<td rowspan="2" ><?php echo $result->api_name;?></td>
							
						    <?php 
						    $amount_info = $db->queryUniqueObject("SELECT 
						SUM(IF(tr.type='dr', tr.amount, 0)) AS debitAmount,
						SUM(IF(tr.type='dr' AND tr.transaction_term='RECHARGE', tr.amount, 0)) AS rechargeDebit, 
						SUM(IF(tr.type='dr' AND tr.transaction_term='FAILURE', tr.amount, 0)) AS failureDebit, 
						SUM(IF(tr.type='dr' AND (tr.transaction_term='FUND' OR tr.transaction_term='DEDUCT FUND' OR tr.transaction_term='ADD FUND'), tr.amount, 0)) AS balanceDeduct,
						SUM(IF(tr.type='dr' AND tr.transaction_term='REVERT', tr.amount, 0)) AS revertDebit, 
						SUM(IF(tr.type='dr' AND (tr.transaction_term='REFUND' OR tr.transaction_term='REFUNDED'), tr.amount, 0)) AS refundDebit,
						SUM(IF(tr.type='cr', tr.amount, 0)) AS creditAmount,
						SUM(IF(tr.type='cr' AND tr.transaction_term='RECHARGE', tr.amount, 0)) AS rechargeCredit,
						SUM(IF(tr.type='cr' AND tr.transaction_term='FAILURE', tr.amount, 0)) AS failureCredit, 
						SUM(IF(tr.type='cr' AND (tr.transaction_term='FUND' OR tr.transaction_term='DEDUCT FUND' OR tr.transaction_term='ADD FUND'), tr.amount, 0)) AS balanceCredit,
						SUM(IF(tr.type='cr' AND tr.transaction_term='REVERT', tr.amount, 0)) AS revertCredit, 
						SUM(IF(tr.type='cr' AND (tr.transaction_term='REFUND' OR tr.transaction_term='REFUNDED'), tr.amount, 0)) AS refundCredit					
					FROM transactions tr left join apps_recharge rch on tr.transaction_ref_no=rch.recharge_id WHERE rch.api_id='".$result->api_id."' AND transaction_date like '%".$from."%'");
						$pending = $db->queryUniqueValue("SELECT SUM(tran.amount) FROM apps_recharge recharge LEFT JOIN transactions tran ON recharge.recharge_id=tran.transaction_ref_no WHERE recharge.request_date like '%".$from."%' AND tran.transaction_term='RECHARGE' AND tran.type='dr' and  recharge.status=1 and recharge.api_id='".$result->api_id."'");
						    ?>
						   
							<?php 
							if($api_info->opening_balance==""|| $api_info->opening_balance==null)
							{
							 $openbalance = $db->queryUniqueObject("Select * from api_balance where date='".$from."'");
							 ?>
							<td><?php  $name = "api".$result->api_id;
							echo $openbalance->$name;?></td>
							<?php } else {?>
							<td><?php echo $api_info->opening_balance;?></td>
							<?php } 
							if($api_info->closing_balance==""|| $api_info->closing_balance==null)
							{
							 $closebalance = $db->queryUniqueObject("Select * from api_balance where date=DATE_ADD('".$from."', INTERVAL 1 DAY)");
							 ?>
							<td><?php  $name1 = "api".$result->api_id;
							echo $closebalance->$name1;?></td>
							<?php } else { ?>
							 <td><?php echo $api_info->closing_balance;?></td>
							<?php }?>
							<input type="hidden" name="opening_balance[<?php echo $result->api_id;?>]" value="<?php echo $openbalance->$name;?>" />
						    <input type="hidden" name="closing_balance[<?php echo $result->api_id;?>]" value="<?php echo $closebalance->$name1;;?>" />
						    
						    
							<td><input type="text" size="10" id="fundadd<?php echo $result->api_id;?>" name="fundadd[<?php echo $result->api_id;?>]" value="<?php echo $fund_add;?>" class="fill-ds fill-Ddth" /></td>
							
						  <?php
							if($recharge!=""|| $recharge!=null)
							{?>
							 <td><?php echo $recharge;?></td>
							<?php } else { ?>
							 <td><?php echo round($amount_info->rechargeDebit,2);?></td>
							<?php }?>
							
							 <?php 
							if($pending!=""|| $pending!=null)
							{?>
							 <td><?php echo $pending;?></td>
							<?php } else { ?>
							 <td><?php echo round($pending);?></td>
							<?php }?>
							
							 <?php 
							if($failed!=""|| $failed!=null)
							{?>
							 <td><?php echo $failed;?></td>
							<?php } else { ?>
							 <td><?php echo round($amount_info->failureCredit,2);?></td>
							<?php }?>
							
							 <?php 
							if($refunded!=""|| $refunded!=null)
							{?>
							 <td><?php echo $refunded;?></td>
							<?php } else { ?>
							 <td><?php echo round($amount_info->refundCredit,2);?></td>
							<?php }?>
							
							 <?php 
							if($sale!=""|| $sale!=null)
							{?>
							 <td><?php echo $sale;?></td>
							<?php } else { ?>
							 <td><?php echo round($amount_info->rechargeDebit-$amount_info->failureCredit-$amount_info->refundCredit,2);?></td>
							<?php }?>
							
						    
						    
						    
								
									
								
									<input type="hidden" size="10" id="recharge<?php echo $result->api_id;?>" name="recharge[<?php echo $result->api_id;?>]" value="<?php echo round($amount_info->rechargeDebit,2);?>" class="fill-ds fill-Ddth" />
								
									<input type="hidden" size="10" id="pending<?php echo $result->api_id;?>" name="pending[<?php echo $result->api_id;?>]" value="<?php echo round($pending,2);?>" class="fill-ds fill-Ddth"  />
								
									<input type="hidden" size="10" id="failed<?php echo $result->api_id;?>" name="failed[<?php echo $result->api_id;?>]" value="<?php echo round($amount_info->failureCredit,2);?>" class="fill-ds fill-Ddth"  />
							
									<input type="hidden" size="10" id="refunded<?php echo $result->api_id;?>" name="refunded[<?php echo $result->api_id;?>]" value="<?php echo round($amount_info->refundCredit,2);?>" class="fill-ds fill-Ddth"  />
							
									<input type="hidden" size="10" id="sale<?php echo $result->api_id;?>" name="sale[<?php echo $result->api_id;?>]" value="<?php echo round($amount_info->rechargeDebit-$amount_info->failureCredit-$amount_info->refundCredit,2);?>" class="fill-ds fill-Ddth"  />
								
								</tr>
								<tr>
								<td>
									<input type="text" size="10" id="opening_balance1<?php echo $result->api_id;?>" name="opening_balance1[<?php echo $result->api_id;?>]" value="<?php echo $api_info->opening_balance1;?>" class="fill-ds fill-Ddth" />
								</td>	
								<td>
									<input type="text" size="10" id="closing_balance1<?php echo $result->api_id;?>" name="closing_balance1[<?php echo $result->api_id;?>]" value="<?php echo $api_info->closing_balance1;?>" class="fill-ds fill-Ddth" />
								</td>
								<td>
									<input type="text" size="10" id="fundadd1<?php echo $result->api_id;?>" name="fundadd1[<?php echo $result->api_id;?>]" value="<?php echo $fund_add1;?>" class="fill-ds fill-Ddth" />
								</td>	
								<td>
									<input type="text" size="10" id="recharge1<?php echo $result->api_id;?>" name="recharge1[<?php echo $result->api_id;?>]" value="<?php echo $recharge1;?>" class="fill-ds fill-Ddth" />
								</td>	
								<td>
									<input type="text" size="10" id="pending1<?php echo $result->api_id;?>" name="pending1[<?php echo $result->api_id;?>]" value="<?php echo $pending1; ?>" class="fill-ds fill-Ddth"  />
								</td>	
								<td>
									<input type="text" size="10" id="failed1<?php echo $result->api_id;?>" name="failed1[<?php echo $result->api_id;?>]" value="<?php echo $failed1;?>" class="fill-ds fill-Ddth" />
								</td>	
								<td>
									<input type="text" size="10" id="refunded1<?php echo $result->api_id;?>" name="refunded1[<?php echo $result->api_id;?>]" value="<?php echo $refunded1;?>" class="fill-ds fill-Ddth" />
								</td>
								<td>
									<input type="text" size="10" id="sale1<?php echo $result->api_id;?>" name="sale1[<?php echo $result->api_id;?>]" value="<?php echo $sale1;?>" class="fill-ds fill-Ddth" />
								</td>
								</tr>
								<tr>
								    <td><b>Difference</b></td>
                                    <td><b><?php echo $opening_balancediff;?></b></td>
                                    <td><b><?php echo $closing_balancediff;?></b></td>
                                    <td><b><?php echo $fundadddiff;?></b></td>
                                    <td><b><?php echo $rechargediff;?></b></td>
                                    <td><b><?php echo $pendingdiff;?></b></td>
                                    <td><b><?php echo $faileddiff;?></b></td>
                                    <td><b><?php echo $refundeddiff;?></b></td>
                                    <td><b><?php echo $salediff;?></b></td>
    				        	</tr>
						<?php } ?>
					</tbody>
					<tr>
								<td colspan='100%'>&nbsp;</td>
							</tr>
			            	<tr>
								
								<td colspan="7"></td>
								
							
								<td colspan="3">
									<input type="submit" name="submit" value="Update" class="btn btn-success" />
								</td>
							</tr>
						</table>
					</form>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php'); ?>