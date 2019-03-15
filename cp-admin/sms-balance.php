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
									$url = "http://bhashsms.com/api/checkbalance.php?user=clickecharge&pass=123456";
									$output = file_get_contents($url);
									echo $output;
								} else if($result->sms_api_id == '2') {
								    
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    $output = curl_exec($ch);
									echo "NA";
								} else if($result->sms_api_id == '3') {
									$url = "http://203.212.70.200/smpp/creditstatus.jsp?user=ankitsenderid&password=hotmail02";
									$output = file_get_contents($url);
									echo $output;
								} else if($result->sms_api_id == '4') {
								    $url="http://bulksms.clickecharge.com/api/balance.php?authkey=218607Aq0elvjbB4ol5b13f181&type=4";
									$ch = curl_init();
                                	curl_setopt($ch, CURLOPT_URL, $url);
                                	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                	$output = curl_exec($ch);
                                	echo $output;
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