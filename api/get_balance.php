<?php
include("../config.php");
header('Access-Control-Allow-Origin: *'); 
$API1_url = "http://api.egpay.in/GetAPIBalance.aspx?UID=".$eg_pay['uid']."&UPASS=".$eg_pay['pass']."&SUBUID=".$eg_pay['subuid']."&SUBUPASS=".$eg_pay['subupass'];
$API1_output = file_get_contents($API1_url);
$API1_xml = json_decode(json_encode((array) simplexml_load_string($API1_output)), 1);
$API1_balance = isset($API1_xml['API_Balance']) ? $API1_xml['API_Balance'] : '';
$Roundpay_url = "http://roundpayapi.in/API/APIService.aspx?userid=".$roundpay['userid']."&pass=".$roundpay['pass']."&Get=CB";
$Roundpay_output = file_get_contents($Roundpay_url);
$Roundpay_xml = json_decode(json_encode((array) simplexml_load_string($Roundpay_output)), 1);
$Roundpay_balance = isset($Roundpay_xml['STATUS']) && $Roundpay_xml['STATUS'] != '' ? $Roundpay_xml['STATUS'] : '';
$Ambika_url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&Get=CB&fmt=Json";
$Ambika_output = file_get_contents($Ambika_url);
$Ambika_xml = json_decode(($Ambika_output), true1);
$Ambika_balance = isset($Ambika_xml['STATUS']) && $Ambika_xml['STATUS'] != '' ? $Ambika_xml['STATUS'] : '';
$aroh_url ="http://arrohservice.in/apis/apireqnew.aspx?uid=2064&pass=66vvv1&mno=918600250250&msg=CB";
$aroh_output = file_get_contents($aroh_url);
$aroh_balance_split = explode(":", $aroh_output);
$aroh_balance = explode(" ", $aroh_balance_split[1]);
$aroh_final_balance=$aroh_balance[0];

$rechargea2z_url = "http://rechargea2z.com/API/APIService.aspx?userid=".$rechargea2z['userid']."&pass=".$rechargea2z['pass']."&Get=CB&fmt=Json";
$rechargea2z_output = file_get_contents($rechargea2z_url);
$rechargea2z_xml = json_decode(($rechargea2z_output), true);
$rechargea2z_balance = isset($rechargea2z_xml['STATUS']) && $rechargea2z_xml['STATUS'] != '' ? $rechargea2z_xml['STATUS'] : '';


$url_paymentall = "http://payment2all.com/multirecharge/balanceapi/run";
$fields = array(
'username' =>$paymentall['username'] ,
'password' => $paymentall['password']
);
//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_paymentall);


curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


$output_paymentall = curl_exec($ch);

$result_paymentall=explode("#",$output_paymentall);


if($result_paymentall[0]=='Success')
{
$balance_paymentall = isset($result_paymentall[1]) && $result_paymentall[1] != '' ? $result_paymentall[1] : '';
}




$url_arroh ="http://arrohservices.in/web-services/httpapi/check-balance?acc_no=".$arroh['acc_no']."&api_key=".$arroh['key'];
$output_arroh = file_get_contents($url_arroh);
$balance_arroh=explode(" ", $output_arroh);
$balance_arroh= $balance_arroh[1];


$url_esure = "http://esuresolution.com/API/APIService.aspx?userid=".$esure['userid']."&pass=".$esure['pass']."&Get=CB&fmt=Json";
$esure_output = file_get_contents($url_esure);
$esure_xml = json_decode(($esure_output), true);
$esure_balance = isset($esure_xml['STATUS']) && $esure_xml['STATUS'] != '' ? $esure_xml['STATUS'] : '';


								


$request_txn_no = time();
	
	include(DIR."/system/class.cyberplat.php");
	$cp = new CyberPlat();	
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no;
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//Mobile Verification	
	$cpurl = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);
	
	$cp_val_error = '';
	$cp_val_result = '';
	$cp_val_transid = '';
	$cp_val_msg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'REST=') !== false) {
			$_data = explode("=",$data);
			$cp_balance = trim($_data[1]);
		}
	}
	

$query = $db->query("SELECT * FROM api_list where status=1 ORDER BY api_id ASC ");

$html="";
$html.='<table class="table table-striped table-bordered table-hover table-responsive" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>API</th>
                <th>BALANCE</th>
            </tr>
        </thead>
        <tbody id="balance" >';
        
        
while($result = $db->fetchNextObject($query)) {

        if($result->api_id == '1'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $API1_balance;
                $html .= '</td></tr>';
        }
        else if($result->api_id == '2'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $balance_arroh;
                $html .= '</td></tr>';
        }
        else if($result->api_id == '6'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $Roundpay_balance;
                $html .= '</td></tr>';
        }
        else if($result->api_id == '7'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $rechargea2z_balance;
                $html .= '</td></tr>';
        }
        else if($result->api_id == '9'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $Ambika_balance;
                $html .= '</td></tr>';
        }
        else if($result->api_id == '10'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $cp_balance;
                $html .= '</td></tr>';
        
        }
        else if($result->api_id == '14'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $balance_paymentall;
                $html .= '</td></tr>';
        
        }
         else if($result->api_id == '16'){
                $html .= '<tr><td>';
                $html .= $result->api_name;
                $html .= '</td><td>';
                $html .= $esure_balance;
                $html .= '</td></tr>';
        
        }
  
}

$html.='</tbody >
    </table>';
 echo $html;
exit();

// $balance=array();	
// $balance[]=array("api_1"=>$API1_balance,"api_2"=>$aroh_final_balance,"api_6"=>$Roundpay_balance ,"api_7"=>$rechargea2z_balance,"api_9"=>$Ambika_balance,"api_10"=>$cp_balance);
// print_r(json_encode($balance));




?>