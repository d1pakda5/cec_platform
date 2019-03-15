<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$scnt = 0;
//
$query = $db->query("SELECT * FROM gst_monthly_txns WHERE has_gst='1' AND user_type='5' GROUP BY uid ORDER BY id ASC");
while($row = $db->fetchNextObject($query)) {
	?>
	<table>
		<tr>
			<td><?php echo $row->uid;?></td>
		</tr>
	</table>
<?php
}