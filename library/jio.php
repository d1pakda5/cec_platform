<?php
$error = isset($_GET['error']) && $_GET['error'] != '' ? $_GET['error'] : 0;
if(isset($_POST['submit'])) {

	if($_POST['mobile']=='' || $_POST['amount'] == '' || $_POST['pass'] == '') {
		echo "Inpput empty";
		echo "<br><br>";
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
			print_r($output);
			echo "<br><br>";
		} else {
			echo "NO output";
			echo "<br><br>";
		}
	}
}
?>
<form action="" method="post">
<input type="text" name="mobile" placeholder="mobile number" />
<input type="text" name="amount" placeholder="Amount" />
<input type="text" name="pass" value="Jio@1234" placeholder="Password" />
<input type="submit" name="submit" value="Submit" />
</form>