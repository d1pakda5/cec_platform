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

$sWhere = "WHERE (rqst.user_type = '3' or rqst.user_type='6') ";
if(isset($_GET['f']) && $_GET['f'] !='' && isset($_GET['t']) && $_GET['t'] !='') {
	$sWhere .= " AND rqst.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( request_user LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND rqst.status = '".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND rqst.status = '0' ";
}

$statement = "fund_requests rqst LEFT JOIN apps_user user ON rqst.request_user = user.uid $sWhere ORDER BY request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('fund-request.php');
$paymode = getPaymentModeList();
$meta['title'] = "Fund Request";
include('header.php');
?>
<script>
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
			<div class="page-title">Fund Request</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Fund Request</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
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
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php $array['pay_status'] = getPaymentStatusList();
									foreach($array['pay_status'] as $data) { ?>
									<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status']) && $_GET['status'] == $data['id']) {?>selected="selected"<?php }?>><?php echo $data['name'];?></option>
									<?php }	?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-basic">
					<thead>
						<tr>
							<th width="6%">S. No.</th>
							<th>Date</th>
							<th>User</th>
							<th>Amount</th>
							<th>To Bank Account</th>
							<th>From Bank</th>
							<th>From Bank Account No</th>
							<th>Payment Mode</th>
							<th>Reg. Mobile</th>
							<th>Payment Date</th>
							<th>Transaction Ref No</th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center" title="<?php echo $result->id;?>"><?php echo $scnt++;?></td>
							<td><?php echo $result->request_date;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<td><?php echo $result->amount;?></td>
							<td><?php echo $result->to_bank_account;?></td>
							<td><?php echo $result->your_bank_name;?></td>
							<td><?php echo $result->your_bank_account;?></td>
						    <td><?php echo getPaymentMode($paymode, $result->pay_mode);?></td>
						    <td><?php echo $result->reg_mobile;?></td>
							<td><?php echo $result->payment_date;?></td>
							<td><?php echo $result->transaction_ref_no;?></td>
							<td style="text-align:center;">
								<a href="fund-request-detail.php?id=<?php echo $result->request_id;?>" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php');?>