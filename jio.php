<?php
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!='' ? $_GET['error'] : '';
$api1 = $db->queryUniqueObject("SELECT * FROM api_list WHERE api_id='12' ");
$api2 = $db->queryUniqueObject("SELECT * FROM api_list WHERE api_id='4' ");

if(isset($_POST['submit1'])) {

	if($_POST['mobile']=='' || $_POST['amount']=='' || $_POST['pass']=='') {
		$error = "Input empty";
	} else {		
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);
		$pass = htmlentities(addslashes($_POST['pass']),ENT_QUOTES);
		$url ="http://ajira.online/Jio.asmx/Recharge?macID=c4:0b:cb:64:60:7d&posID=101&userID=0682184292&cName=Aniketsales&password=".$pass."&number=".$mobile."&amount=".$amount;		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//{"OpeningBalance":"5298.00","TranactionID":"BR00004UB392","CustomerNumber":"8770789403","Amount":"309","Name":"LOKESH_THADANI","Status":"SUCCESS","Message":"Dear Partner Order BR00004UB392 successfully Processed on 8770789403 of Lokesh_Thadani with amount 309.00 Balance 4989.00"}
		
		if($output) {
			$json = json_decode($output, true);
			$api_status = isset($json['Status']) ? $json['Status'] : '';
			$api_status_details = isset($json['Message']) ? $json['Message'] : '';
			$operator_ref_no = isset($json['TranactionID']) && $json['TranactionID']!=''  ? $json['TranactionID'] : '';
			$error = $output;
		} else {
			$error = "NO output";
		}
	}
}
if(isset($_POST['submit2'])) {

	if($_POST['mobile']=='' || $_POST['amount']=='' || $_POST['pass']=='') {
		$error = "Input empty";
	} else {		
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);
		$pass = htmlentities(addslashes($_POST['pass']),ENT_QUOTES);
		$url ="http://ajira.online/Jio.asmx/Recharge?macID=c4:0b:cb:64:60:7d&posID=101&userID=0682203093&cName=Aniketsales&password=".$pass."&number=".$mobile."&amount=".$amount;		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//{"OpeningBalance":"5298.00","TranactionID":"BR00004UB392","CustomerNumber":"8770789403","Amount":"309","Name":"LOKESH_THADANI","Status":"SUCCESS","Message":"Dear Partner Order BR00004UB392 successfully Processed on 8770789403 of Lokesh_Thadani with amount 309.00 Balance 4989.00"}
		
		if($output) {
			$json = json_decode($output, true);
			$api_status = isset($json['Status']) ? $json['Status'] : '';
			$api_status_details = isset($json['Message']) ? $json['Message'] : '';
			$operator_ref_no = isset($json['TranactionID']) && $json['TranactionID']!=''  ? $json['TranactionID'] : '';
			$error = $output;
		} else {
			$error = "NO output";
		}
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JIO RECHARGE</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="margin-top:50px;">
	<div style="row">
		<div class="col-sm-12">
			<div class="alert"><?php echo $error;?>	</div>
		</div>	
	</div>
	<div style="row">	
		<div class="col-sm-6">	
			<div class="panel panel-default">
				<div class="panel-heading">
					JIO RECHARGE (AJIRA 1)
				</div>
				<div class="panel-body">
					<form action="" method="post">
						<div class="form-group">
							<input type="text" name="mobile" class="form-control" placeholder="mobile number" />
						</div>
						<div class="form-group">
							<input type="text" name="amount" class="form-control" placeholder="Amount" />
						</div>
						<div class="form-group">
							<input type="text" name="pass" class="form-control" value="<?php echo $api1->password;?>" placeholder="Password" />
						</div>
						<div class="form-group">
							<input type="submit" name="submit1" class="btn btn-primary" value="Submit" />
						</div>
					</form>
				</div>
			</div>
		</div>	
		<div class="col-sm-6">	
			<div class="panel panel-default">
				<div class="panel-heading">
					JIO RECHARGE (AJIRA 2)
				</div>
				<div class="panel-body">
					<form action="" method="post">
						<div class="form-group">
							<input type="text" name="mobile" class="form-control" placeholder="mobile number" />
						</div>
						<div class="form-group">
							<input type="text" name="amount" class="form-control" placeholder="Amount" />
						</div>
						<div class="form-group">
							<input type="text" name="pass" class="form-control" value="<?php echo $api2->password;?>" placeholder="Password" />
						</div>
						<div class="form-group">
							<input type="submit" name="submit2" class="btn btn-warning" value="Submit" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>