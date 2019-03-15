<?php
class CyberPlat {
	function cyberplatUrl($operator) {
		if($operator == 'RC') {
			//AIRCEL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay_check.cgi/1',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay.cgi/1',
				'status'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay_status.cgi'
			);
		} else if($operator == 'RA') {
			//AIRTEL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/at/at_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/at/at_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/at/at_pay_status.cgi'
			);
		} else if($operator == 'RB') {
			//BSNL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/205',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/205',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator == 'TB') {
			//BSNL VALIDITY/SPECIAL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/219',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/219',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator == 'RI') {
			//IDEA
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/id/id_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/id/id_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/id/id_pay_status.cgi'
			);
		} else if($operator == 'RM') {
			//MTNL TOPUP
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/212',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/212',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator == 'MR') {
			//MTNL VALIDITY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/215',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/215',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator == 'RZ' || $operator == 'DM') {
			//MTS
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay_status.cgi'
			);
		} else if($operator == 'RR' || $operator == 'RG' || $operator == 'DR') {
			//RELAINCE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi'
			);
		} else if($operator == 'RT' || $operator == 'RN') {
			//TATA INDICOM
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay_status.cgi'
			);
		} else if($operator == 'RD' || $operator == 'RJ' || $operator == 'DC') {
			//TATA DOCOMO
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi'
			);
		} else if($operator == 'TD') {
			//TATA DOCOMO SPECIAL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi'
			);
		} else if($operator == 'RU' || $operator == 'RK') {
			//UNINOR
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/un/un_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/un/un_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/un/un_pay_status.cgi'
			);
		} else if($operator == 'RO' || $operator == 'TO') {
			//VIDEOCON
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay_status.cgi'
			);
		} else if($operator == 'RV' || $operator == 'RS') {
			//VODAFONE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi'
			);
		} else if($operator == 'RH') {
			//AIRTEL DTH
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi'
			);
		} else if($operator == 'DB') {
			//BIG TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay_status.cgi'
			);
		} else if($operator == 'DD') {
			//DISH TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay_status.cgi'
			);
		} else if($operator == 'DS') {
			//SUN TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/213',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/213',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator == 'DV') {
			//VIDEOCOND2H
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay_status.cgi'
			);
		} else if($operator == 'DT') {
			//TATASKY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay_status.cgi'
			);
		} else if($operator == 'BCK') {
			//AIRCEL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/288',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/288',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BA') {
			//AIRTEL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi/225',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi/225',
				'status'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi/225'
			);
		} else if($operator == 'PC') {
			//BSNL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/231',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/231',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BI') {
			//IDEA POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'PL') {
			//LOOP POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi'
			);
		} else if($operator == 'PG' || $operator == 'PT') {
			//RELAINCE POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi/251',
				'pay'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi/251',
				'status'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi'
			);
		} else if($operator == 'BT' || $operator == 'BC') {
			//TATA DOCOMO POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/233',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/233',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BV') {
			//VODAFONE POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/234',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/234',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'AL') {
			//AIRTEL LANDLINE 
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/239',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/239',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BL') {
			//MTNL LANDLINE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/240',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/240',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'TK') {
			//TIKONA BROADBAND BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay_check.cgi/261',
				'pay'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay.cgi/261',
				'status'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay_status.cgi'
			);
		} else if($operator == 'EB') {
			//BSES RAJHDHANI BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/236',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/236',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'EY') {
			//BSES YAMUNA BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/237',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/237',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'ED') {
			//NORTH DELHI POWER BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/238',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/238',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'ER') {
			//RELAINCE ENERGY MUMBAI BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/235',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/235',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'GM') {
			//MAHANAGAR GAS BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/241',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/241',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'GGC') {
			//Gujarat Gas Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/321',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/321',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'II') {
			//ICICI PRU LIFE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/243',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/243',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'IT') {
			//TATA AIG LIFE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/242',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/242',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'ME') {
			//MSEB
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/342',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/342',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'MPMKV') {
			//MP
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/345',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/345',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'SPT') {
			//Southern Power Telangana
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/312',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/312',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'SPAP') {
			//Southern Power Andhra Pradesh
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/331',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/331',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'RVV') {
			//Rajasthan Vidyut Vitran Nigam Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/330',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/330',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BESC') {
			//Bangalore Electricity Supply Company
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/315',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/315',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'BMES') {
			//Brihan Mumbai Electric Supply and Transport Undertaking
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/340',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/340',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator == 'TP') {
			//Torrent Power
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else {
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		}		
		return $cp_url;
	}
	
	function cyberplatRequest($qs,$url){		
		$urln = $url."?inputmessage=".urlencode($qs);
		$opts = array( 
			'http'=>array( 
			'method'=>"GET", 
			'header'=>array("Content-type: application/x-www-form-urlencoded\r\n") 
			) 
		);		
		$context = stream_context_create($opts); 	
		$response = file_get_contents($urln,false,$context);
		return $response;
	}
	
	function cyberplatError($code){
		if($code == '0') {
			$result = "Successfully completed";
		} elseif($code == '1') {
			$result = "Session with this number already exists.";
		} elseif($code == '2') {
			$result = "Invalid Dealer code.";
		} elseif($code == '3') {
			$result = "Invalid acceptance outlet code.";
		} elseif($code == '4') {
			$result = "Invalid Operator code.";
		} elseif($code == '5') {
			$result = "Invalid session code format.";
		} elseif($code == '6') {
			$result = "Invalid EDS.";
		} elseif($code == '7') {
			$result = "Invalid amount format or amount value is out of admissible range.";
		} elseif($code == '8') {
			$result = "Invalid phone number format.";
		} elseif($code == '9') {
			$result = "Invalid format of personal account number.";
		} elseif($code == '10') {
			$result = "Invalid request message format.";
		} elseif($code == '11') {
			$result = "Session with such a number does not exist.";
		} elseif($code == '12') {
			$result = "The request is made from an unregistered IP.";
		} elseif($code == '21') {
			$result = "Not enough funds for effecting the payment.";
		} elseif($code == '23') {
			$result = "Invalid phone (account) number.";
		} elseif($code == '24') {
			$result = "Error of connection with the provider’s server or a routine break in.";
		} elseif($code == '30') {
			$result = "General system failure.";
		} elseif($code == '32') {
			$result = "Repeated payment within 60 minutes from the end of payment effecting process";
		} elseif($code == '33') {
			$result = "This denomination is applicable in <Flexi OR Special> category";
		} elseif($code == '37') {
			$result = "An attempt of referring to the gateway that is different from the gateway at the previous";
		} elseif($code == '45') {
			$result = "No license is available for accepting payments to the benefit of this operator.";
		} elseif($code == '81') {
			$result = "Exceeded the maximum payment amount.";
		} elseif($code == '82') {
			$result = "Daily debit amount has been exceeded.";
		} elseif($code == '88') {
			$result = "Duplicate Recharge";
		} elseif($code == '137') {
			$result = "wrong key or passphrase";
		} elseif($code == '224') {
			$result = "Operator Server Down";
		} else {
			$result = "NA";
		}		
		return $result;	
	}
}
?>