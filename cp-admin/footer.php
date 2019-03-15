<?php

$fundaFrom = date("Y-m-d 00:00:00");
$fundaTo = date("Y-m-d 23:59:59");

$fundwhere = "WHERE (rqst.user_type = '3' or rqst.user_type='6') ";
$fundwhere .= " AND rqst.request_date BETWEEN '".$fundaFrom."' AND '".$fundaTo."' ";
$fundwhere .= " AND rqst.status = '0' ";
$fundstatement = "fund_requests rqst LEFT JOIN apps_user user ON rqst.request_user = user.uid $fundwhere ORDER BY request_date DESC";
?>
<script>
$(document).ready(function () {
// 	$("#modalfundreq").modal('show');
});
</script>
<!-- Modal Popup Notification -->
<div class="modal fade" id="modalfundreq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="position:fixed;bottom:0;right:0;width:35%;">
		<div class="modal-content">
			<div class="modal-header" style="padding:5px;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="myModalLabel"><i class="fa fa-reorder"></i> Fund Request</h5>
			</div>
			<div class="modal-body" style="padding:0px;">
				<table class="table table-responsive table-striped table-bordered table-condensed">
					<thead>
						<tr>
                            <th>User</th>
							<th>Amount</th>
							<th>To Bank Account</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$fundquery = $db->query("SELECT * FROM {$fundstatement}  ");
						if($db->numRows($fundquery) < 1) {
						    echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						    ?>
						    <script>
                               jQuery("#modalfundreq").modal('hide');
                            </script>
						<?php  }
						else  {
						    ?><script>
                               jQuery("#modalfundreq").modal('show');
                            </script>
						<?php  }
						while($fundresult = $db->fetchNextObject($fundquery)) {
						?>
						<tr>
						    <td><?php echo $fundresult->company_name;?> (<?php echo $fundresult->uid;?>)</td>
							<td><?php echo $fundresult->amount;?></td>
							<td><?php echo $fundresult->to_bank_account;?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<footer class="footer">
	<div class="container-fluid">
		Copyright &copy; 2014-<?php echo date('Y');?> <a href="#" target="_blank"> <?php echo SITENAME;?></a>. All rights reserved. 
		<?php echo $db->getQueriesCount()." Query in ".$db->getExecTime()." Seconds";?>
		<div class="pull-right hidden-xs"> <b>Version</b> <?php echo VERSION;?></div>
	</div>
</footer>
</body>
</html>