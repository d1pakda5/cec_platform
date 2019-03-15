<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$meta['title'] = "SMS Live Balance";
include("header.php");
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">SMS Balance</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"> <i class="fa fa-list"></i> List SMS Balance</h3>
			</div>
			<div class="box-body no-padding">
				<table class="table">
					<thead>
						<tr>
							<th width="6%">S. No.</th>
							<th>SMS API Name</th>
							<th width="50%">Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT * FROM sms_api ORDER BY sms_api_id ASC ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->api_name;?></td>
							<td><b>
								<?php
								if($result->sms_api_id == '1') {
									$url = "http://sms.shubhsandesh.com/balance.asp?user=erechargeasia&password=ANKIT";
									$output = file_get_contents($url);
									echo $output;
								} else if($result->sms_api_id == '2') {
									echo "NA";
								} else if($result->sms_api_id == '3') {
									$url = "http://203.212.70.200/smpp/creditstatus.jsp?user=ankitsenderid&password=hotmail02";
									$output = file_get_contents($url);
									echo $output;
								} else if($result->sms_api_id == '4') {
									echo "NA";
								}
								?>
								</b>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>