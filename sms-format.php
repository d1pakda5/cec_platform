<?php
session_start();
include('config.php');
$statement = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">	
<title>Login</title>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" id="theme-style">
<link rel="stylesheet" href="css/theme.css" type="text/css" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<style>
.page {
	margin-top:15px;
	margin-bottom:80px;
	float:left;
	width:100%;
	background:#fff;
	padding:15px;
}
h2 {
	font-size:24px;
	font-weight:700;
}
h3 {
	font-size:18px;
	font-weight:700;
}
.btn-and:hover {
	margin-top:-5px;
}
.btn-java:hover {
	margin-top:-5px;
}
</style>
</head>
<body class="hold-transition login-page">
<div class="header-login">
	<div class="container">
		<a href="#"><img src="images/sms-format.png" /></a>
		<a href="ErechargeAsia.jar"><img src="images/java.png" /></a>
		<a href="ClickEcharge.apk"><img src="images/android.png" /></a>
	</div>
</div>
<div class="container">
	<div class="page">
		<h2 class="text-center">SMS/LONGCODE RECHARGE FORMAT</h2>
		<div class="row">
			<div class="col-md-12">
				<h3 class="server-txt">
					Keyword : <b class="text-danger">RR</b>,  <br /><br />
					LONGCODE NUMBER : <b class="text-primary">9223050005, 9960499605 </b><br /><br />
				</h3>				
				<table class="table table-bordered table-condensed table-striped">
					<thead>
						<tr>
							<th width="35%">Service Type</th>
							<th width="25%">Service Code</th>
							<th>Example Format</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Check Balance</td>
							<td>CB</td>
							<td>RR CB</td>
						</tr>
						<tr>
							<td>Change Pin</td>
							<td>CP</td>
							<td>RR CP</td>
						</tr>
						<tr>
							<td>Last Recharge</td>
							<td>LS</td>
							<td>RR LS</td>
						</tr>
						<tr>
							<td>Fund Transfer</td>
							<td>ST</td>
							<td>RR ST<span class="boldx">[Mobile]</span>A<span class="boldx">[Amount]</span></td>
						</tr>
						<tr>
							<td>Fund Revert</td>
							<td>TF</td>
							<td>RR TF<span class="boldx">[Mobile]</span>A<span class="boldx">[Amount]</span></td>
						</tr>
						<tr>
							<td>Recharge Enquiry</td>
							<td>CK</td>
							<td>RR CK<span class="boldx">[Txn No]</span></td>
						</tr>
						<tr>
							<td>Complaint Register</td>
							<td>TR</td>
							<td>RR TR<span class="boldx">[Txn No]</span></td>
						</tr>
						<tr>
							<td>Complaint Status</td>
							<td>CS</td>
							<td>RR CS<span class="boldx">[Txn No]</span></td>
						</tr>
						<tr>
							<td>Account Status</td>
							<td>SR</td>
							<td>RR SR<span class="boldx">[Mobile]</span></td>
						</tr>
						<tr>
							<td>Recharge</td>
							<td><span class="boldx">[Operator Code]</span></td>
							<td>RR <span class="boldx">[Operator]</span><span class="boldx">[Mobile/Account]</span>A<span class="boldx">[Amount]</span></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h3>SMS Formats &amp; Service Commands</h3>
				<div class="separator-2"></div>
				<table class="table table-bordered table-condensed table-striped">
					<thead>
						<tr>
							<th>Operator Name</th>
							<th>Type</th>
							<th>Service Code</th>
							<th>Example Format</th>
						</tr>
					</thead>
					<tbody>
						<?php					
						$scnt=1;
						$QsProduct=$db->query("SELECT opr.*, ser.service_name FROM operators opr LEFT JOIN service_type ser ON opr.service_type = ser.service_type_id ORDER BY opr.service_type,opr.operator_name ASC");
						while($rslt=$db->fetchNextObject($QsProduct)) {
						?>
						<tr>
							<td><?php echo $rslt->operator_name;?></td>
							<td align="center"><?php echo $rslt->service_name;?></td>
							<td align="center"><?php echo $rslt->operator_longcode;?></td>
							<td>RR <?php echo $rslt->operator_longcode;?><span class="boldx">[Mobile]</span>A<span class="boldx">[Amount]</span></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>		
</div>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5748343406abb9034a415aa3/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>
