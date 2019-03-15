<?php
class CyberPlat {
	function cyberplatUrl($operator) {
		if($operator=='AIRCELOB') {
			//AIRCEL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/288',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/288',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='VODAOB') {
			//VADAFONE POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/234',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/234',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='IDEAOB') {
			//IDEA POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='DOCOMOOB') {
			//DOCOMO POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/233',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/233',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BSNLMOB') {
			//BSNL POSTPAID
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/231',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/231',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ARTMOOB') {
			//AIRTEL LANDLINE
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/239',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/239

',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BSNLOB') {
			//MTNL VALIDITY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/344',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/344',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MTNLDELOB') {
			//MTNL DELHI
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/240',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/240',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MTNLMUMOB') {
			//MTNL MUMBAI
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/248',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/248',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BESTMUOB') {
			//MUMBAI ELECTRICITY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/340',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/340',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BSESRAOB') {
			//BSES Rajdhani Elect
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/236',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/236',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='IPCLOB') {
			//India Power Corporation Limited - Bihar
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/324',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/324',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='JUSCOOB') {
			//Jamshedpur Utilities and Services Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/325',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/325',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MPINDOB') {
			//Madhya Pradesh Paschim Kshetra Vidyut Vitaran Co. Ltd -Indore
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/326',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/326',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MSEBOB') {
			//MAHARSHTRA ELECTRICITY
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/342',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/342',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MPMKVVBOB') {
			//Madhya Pradesh Madhya Kshetra Vidyut Vitaran Company Limited - Bhopal
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/345',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/345',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='NDPLT') {
			//North Delhi Power Limited (Tata Power - DDL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/328',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/328',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='NPCL') {
			//Noida Power Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/335',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/335',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='RELENGOB') {
			//Reliance Energy
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/235',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/235',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='RVVNL') {
			//Rajasthan Vidyut Vitran Nigam Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/330',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/330',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='SPDCLOB') {
			//Southern Power Distribution Company Ltd of Andhra Pradesh( APSPDCL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/331',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/331',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='TPCOB') {
			//Torrent Power
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='TSECLOB') {
			//Tripura State Electricity Corporation Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/333',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/333',
			    'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BESLOB') {
			//Bharatpur Electricity Services Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/476',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/476',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BKESLOB') {
			//Bikaner Electricity Supply Limited (BkESL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/477',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/477',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='CESCOB') {
			//Calcutta Electricity Supply Ltd (CESC Ltd)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/317',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/317',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='DDEDOB') {
			//Daman and Diu Electricity Department
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/480',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/480',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='DGVCLOB') {
			//Dakshin Gujarat Vij Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/481',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/481',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='DNHPDCLOB') {
			//DNH Power Distribution Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/482',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/482',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='EPDCLOB') {
			//Eastern Power Distribution Company of Andhra Pradesh Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/483',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/483',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='KEDLOB') {
			//Kota Electricity Distribution Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/485',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/485',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MEPDCLOB') {
			//Meghalaya Power Distribution Corporati on Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/486',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/486',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MGVCLOB') {
			//Madhya Gujarat Vij Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/487',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/487',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);		
		} else if($operator=='ORDISCMOBB2C') {
			//ODISHA Discoms(B2C)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/329',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/329',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ORDISCMOBB2B') {
			//ODISHA Discoms(B2B)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/506',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/506',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='PGVCLOB') {
			//Paschim Gujarat Vij Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/488',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/488',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		}  else if($operator=='TPCOB') {
			//Tata Power – Mumbai
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/491',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/491',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi');
		} else if($operator=='UGVCLOB') {
			//Uttar Gujarat Vij Company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/492',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/492',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='IPCWB') {
			//India Power Corporation - West Bengal
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/495',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/495',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='UPCLE') {
			//Uttarakhand Power Corporation Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/496',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/496',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MVVLBE') {
			//Muzaffarpur Vidyut Vitran Limited-bihar
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/497',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/497',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);	
		} else if($operator=='UPPCLE') {
			//Uttar Pradesh Power Corp Ltd (UPPCL) - URBAN
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/499',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/499',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='NBPDCE') {
			//North Bihar Power Distribution Company Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/501',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/501',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='SBPDCE') {
			//South Bihar Power Distribution Company Ltd.
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/502',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/502',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='APDCLE') {
			//Assam Power Distribution Company Ltd (APDCL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/313',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/313',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='BESCOMOB') {
			//Bangalore Electricity Supply Company
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/315',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/315',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='CSEBOB') {
			//Chhattisgarh State Electricity Board
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/318',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/318',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MPINDOBE') {
			//Madhya Pradesh Paschim Kshetra Vidyut Vitaran Company Ltd (Indore) - NONRAPDR
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/339',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/339',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='ADANIOB') {
			//ADANI GAS
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/338',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/338',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='GSPCGASOB') {
			//Gujarat Gas company Limited
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/321',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/321',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='IGLGAS') {
			//Indraprasth Gas
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/310',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/310',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MHGLMUOB') {
			//Mahanagar Gas
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/241',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/241',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='HCGOB') {
			//Haryana City gas
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/484',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/484',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='SITEOB') {
			//Siti Energy (Delhi/UP/Haryana)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/489',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/489',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='TNGCLOB') {
			//Tripura Natural Gas Company Ltd
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/490',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/490',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		}  else if($operator=='SGLGAS') {
			// Sabarmati Gas Limited (SGL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/500',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/500',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='CONBBOB') {
			// Connect Broadband
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/479',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/479',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='UITBHIWOB') {
			// UIT Bhiwadi water
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/493',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/493',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='UJSOB2B') {
			// Uttarakhand Jal Sansthan(B2B)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/507',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/507',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='UJSOB2C') {
			// Uttarakhand Jal Sansthan(B2C)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/508',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/508',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='MCOG') {
			// Sabarmati Gas Limited (SGL)
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/498',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/498',
				'status'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi'
			);
		} else if($operator=='DLJB') {
			// Delhi Jal Board
			$cp_url = array(
				'check'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/494',
				'pay'=>'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/494',
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