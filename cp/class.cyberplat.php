<?php
class CyberPlat {
	function cyberplatUrl($operator) {
		if($operator=='RC' || $operator=='XC') {
			//AIRCEL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay_check.cgi/1',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay.cgi/1',
				'status'=>'https://in.cyberplat.com/cgi-bin/ac/ac_pay_status.cgi'
			);
		} else if($operator=='RA' || $operator=='XA') {
			//AIRTEL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/at/at_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/at/at_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/at/at_pay_status.cgi'
			);
		} else if($operator=='RB' || $operator=='XB') {
			//BSNL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/205',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/205',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator=='TB' || $operator=='YC' || $operator=='B3' || $operator=='X3') {
			//BSNL VALIDITY/SPECIAL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/219',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/219',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator=='RI' || $operator=='XI') {
			//IDEA
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/id/id_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/id/id_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/id/id_pay_status.cgi'
			);
		} else if($operator=='RM' || $operator=='YM') {
			//MTNL TOPUP
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/212',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/212',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator=='MR' || $operator=='XM') {
			//MTNL VALIDITY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/215',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/215',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator=='RZ' || $operator=='DM' || $operator=='XS') {
			//MTS
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/mt/mt_pay_status.cgi'
			);
		} else if($operator=='RR' || $operator=='RG' || $operator=='DR' || $operator=='XR' || $operator=='XG') {
			//RELAINCE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi'
			);
		} else if($operator=='RS') {
			//RELAINCE JIO
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay_status.cgi'
			);
		} else if($operator=='RT' || $operator=='RN' || $operator=='XT' || $operator=='XK') {
			//TATA INDICOM
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/tt/tt_pay_status.cgi'
			);
		} else if($operator=='RD' || $operator=='RJ' || $operator=='DC' || $operator=='XD' || $operator=='XJ') {
			//TATA DOCOMO
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi'
			);
		} else if($operator=='TD' || $operator=='XO') {
			//TATA DOCOMO SPECIAL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi'
			);
		} else if($operator=='RU' || $operator=='RK' || $operator=='XU' || $operator=='YU') {
			//UNINOR
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/un/un_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/un/un_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/un/un_pay_status.cgi'
			);
		} else if($operator=='RO' || $operator=='TO' || $operator=='XN' || $operator=='YN') {
			//VIDEOCON
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vm/vm_pay_status.cgi'
			);
		} else if($operator=='RV' || $operator=='XV') {
			//VODAFONE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi'
			);
		} else if($operator=='DA' || $operator=='YA') {
			//AIRTEL DTH
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi'
			);
		} else if($operator=='DB' || $operator=='YB') {
			//BIG TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/bt/bt_pay_status.cgi'
			);
		} else if($operator=='DD' || $operator=='YD') {
			//DISH TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/dt/dt_pay_status.cgi'
			);
		} else if($operator=='DS' || $operator=='YS') {
			//SUN TV
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/213',
				'pay'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/213',
				'status'=>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi'
			);
		} else if($operator=='DV' || $operator=='YV') {
			//VIDEOCOND2H
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vc/vc_pay_status.cgi'
			);
		} else if($operator=='DT' || $operator=='YT') {
			//TATASKY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/ts/ts_pay_status.cgi'
			);
		} else if($operator=='PC') {
			//AIRCEL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/288',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/288',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='PA') {
			//AIRTEL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi/225',
				'pay'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi/225',
				'status'=>'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi/225'
			);
		} else if($operator=='PB') {
			//BSNL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/231',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/231',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='PI') {
			//IDEA POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='PL') {
			//LOOP POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
				'pay'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
				'status'=>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi'
			);
		} else if($operator=='PR' || $operator=='PG') {
			//RELAINCE POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi/251',
				'pay'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi/251',
				'status'=>'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi'
			);
		} else if($operator=='PD' || $operator=='PT') {
			//TATA DOCOMO POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/233',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/233',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='PV') {
			//VODAFONE POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/234',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/234',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='LA') {
			//AIRTEL LANDLINE 
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/239',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/239',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='LB') {
			//BSNL LANDLINE 
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/344',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/344',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='LM') {
			//MTNL LANDLINE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/240',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/240',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='TK') {
			//TIKONA BROADBAND BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay_check.cgi/261',
				'pay'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay.cgi/261',
				'status'=>'https://in.cyberplat.com/cgi-bin/tk/tk_pay_status.cgi'
			);		
		} else if($operator=='GM') {
			//MAHANAGAR GAS BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/241',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/241',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='GG') {
			//Gujarat Gas Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/321',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/321',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='GI') {
			//INDRAPRASTH GAS
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='GA') {
			//ADANI GAS
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);	
		} else if($operator=='II') {
			//ICICI PRU LIFE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/243',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/243',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='IT') {
			//TATA AIG LIFE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/242',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/242',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EBR') {
			//BSES RAJHDHANI BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/236',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/236',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EBY') {
			//BSES YAMUNA BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/237',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/237',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='END') {
			//NORTH DELHI POWER BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/238',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/238',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ERE') {
			//RELAINCE ENERGY MUMBAI BILL
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/235',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/235',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);	
		} else if($operator=='EMS') {
			//Maharastra State Electricity (MSEDC)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/342',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/342',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EMK') {
			//MP Madhya Kshetra Vidyut Vitaran - Bhopal
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/345',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/345',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ESP') {
			//Southern Power Telangana
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/312',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/312',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ESA') {
			//Southern Power Andhra Pradesh
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/331',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/331',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ERV') {
			//Rajasthan Vidyut Vitran Nigam Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/330',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/330',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EBS') {
			//Bangalore Electricity Supply Company
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/315',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/315',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EMU') {
			//Brihan Mumbai Electric Supply and Transport Undertaking
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/340',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/340',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ETO') {
			//Torrent Power
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ENP') {
			//Noida Power Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/335',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/335',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EPK') {
			//Madhya Pradesh Paschim Kshetra Vidyut Vitaran - Indore
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/326',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/326',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ECE') {
			//Calcutta Electricity Supply Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/317',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/317',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ECS') {
			//Chhattisgarh State Electricity Board
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/318',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/318',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='XXX') {
			//India Power Corporation Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='XXX') {
			//Jamshedpur Utilities and Services Company Limited
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
		global $db;	
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