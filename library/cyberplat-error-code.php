<?php
function cpCode($code) {
	if($code=='0') {
		$result="Successful transaction";
	}elseif($code=='1'){
		$result="Session ID must be unique";
	}elseif($code=='2'){
		$result="Invalid dealer's code (SD)";
	}elseif($code=='3'){
		$result="Invalid outlet's code (AP)";
	}elseif($code=='4'){
		$result="Invalid operator’s code (OP)";
	}elseif($code=='5'){
		$result="Invalid format of session ID";
	}elseif($code=='6'){
		$result="Either key / password is wrong OR key expired";
	}elseif($code=='7'){
		$result="Invalid denomination. Please try with valid denomination.";
	}elseif($code=='8'){
		$result="Invalid format of phone number";
	}elseif($code=='9'){
		$result="Incorrect format of account number";
	}elseif($code=='10'){
		$result="Incorrect request message format";
	}elseif($code=='11'){
		$result="Transaction Request received without dealer transaction ID";
	}elseif($code=='12'){
		$result="IP address entered by client in the Dealer Cabinet portal";
	}elseif($code=='13'){
		$result="The outlet is not registred by operator";
	}elseif($code=='14'){
		$result="----------------";
	}elseif($code=='15'){
		$result="Operator is not supported";
	}elseif($code=='16'){
		$result="----------------";
	}elseif($code=='17'){
		$result="A phone number does not match to a previously entered one";
	}elseif($code=='18'){
		$result="An amount does not match to a previously entered one";
	}elseif($code=='19'){
		$result="An account does not match to a previously entered one";
	}elseif($code=='20'){
		$result="The payment is being completed";
	}elseif($code=='21'){
		$result="Dealer wallet balance exhausted";
	}elseif($code=='22'){
		$result="Not enough wallet balance, blocked wallet e.t.c.";
	}elseif($code=='23'){
		$result="Invalid Mobile Number. Make sure your number belongs to this provider.";
	}elseif($code=='24'){
		$result="Connectivity Issue from the operator cp Server. You may try later";
	}elseif($code=='25'){
		$result="The operator is temporary blocked";
	}elseif($code=='26'){
		$result="Dealer is temporary blocked";
	}elseif($code=='27'){
		$result="The account is temporary blocked";
	}elseif($code=='28'){
		$result="----------------";
	}elseif($code=='29'){
		$result="----------------";
	}elseif($code=='30'){
		$result="System Error";
	}elseif($code=='31'){
		$result="Exceeded number of simultaneously processed requests.";
	}elseif($code=='32'){
		$result="There should be time gap of 60 min for duplicate transactions with same number.";
	}elseif($code=='33'){
		$result="This denomination is applicable in <Flexi OR Special> category";
	}elseif($code=='34'){
		$result="Transaction ID is not found";
	}elseif($code=='35'){
		$result="Cannot change status of transaction";
	}elseif($code=='36'){
		$result="Required transaction is already in process";
	}elseif($code=='37'){
		$result="An attempt of referring to the gateway that is different from the gateway at the previous; New mobile series error";
	}elseif($code=='38'){
		$result="Invalid transaction date";
	}elseif($code=='39'){
		$result="An account is not found";
	}elseif($code=='40'){
		$result="PIN-card is not registered";
	}elseif($code=='41'){
		$result="Database error. Cannot insert a transaction";
	}elseif($code=='42'){
		$result="A receipt is not stored in DB";
	}elseif($code=='43'){
		$result="Session is expired. Try to start new session";
	}elseif($code=='44'){
		$result="Client cannot work at this server";
	}elseif($code=='45'){
		$result="Specific operator not activated for dealer";
	}elseif($code=='46'){
		$result="Cannot complete a failed transaction";
	}elseif($code=='47'){
		$result="Attempt to pay not in working hours";
	}elseif($code=='48'){
		$result="Session is not saved in database";
	}elseif($code=='49'){
		$result="----------------";
	}elseif($code=='50'){
		$result="System is temporary out of service";
	}elseif($code=='51'){
		$result="Details are not found in database";
	}elseif($code=='52'){
		$result="Dealer may be blocked";
	}elseif($code=='53'){
		$result="Outlet may be blocked";
	}elseif($code=='54'){
		$result="User (cashier) may be blocked";
	}elseif($code=='55'){
		$result="Invalid type of dealer";
	}elseif($code=='56'){
		$result="Invalid type of outlet";
	}elseif($code=='57'){
		$result="Invalid type of user (cashier)";
	}elseif($code=='81'){
		$result="Exceeded the max payment amount (if you set the overall amount limit in dealer cabinet portal)";
	}elseif($code=='82'){
		$result="Exceeded the max payment amount (if you set the daily amount limit in dealer cabinet portal)";
	}elseif($code=='83'){
		$result="Maximum payment amount for the outlet has been exceeded.";
	}elseif($code=='84'){
		$result="Daily total amount for the outlet has been exceeded.";
	}elseif($code=='85'){
		$result="AMOUNT ALL is invalid";
	}elseif($code=='86'){
		$result="Invalid rate value";
	}elseif($code=='87'){
		$result="Beneficiary is blocked";
	}elseif($code=='88'){
		$result="Duplicate number request received within allowed time frame of respective operator";
	}elseif($code=='89'){
		$result="A limit by a beneficiary is reached";
	}elseif($code=='224'){
		$result="Operator Server Down";
	}elseif($code=='333'){
		$result="Unknown error from Provider side";
	}else{
		$result="Invalid Result Found";
	}
	return $result;
}
?>