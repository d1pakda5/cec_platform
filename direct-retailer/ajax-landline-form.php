<?php 
$xop = isset($_GET['xop']) && $_GET['xop'] != '' ? $_GET['xop'] : '0';

?>
<?php if($xop == '42') { ?>
<!--AIRTEL-->
<div class="form-group">
	<label>PHONE NUMBER (WITH STDCODE)</label>
	<div class="jrequired">
		<input type="text" name="account" id="account" class="form-control" placeholder="Enter landline Number With STD CODE" />
	</div>
</div>
<div class="form-group">
	<label>ACCOUNT NUMBER</label>
	<div class="jrequired">
		<input type="text" name="customer_account" id="customer_account" class="form-control" placeholder="Enter Customer Account Number" />
	</div>
</div>
<?php }elseif($xop == '43') { ?>
<!--BSNL-->
<div class="form-group">
	<label>PHONE NUMBER</label>
	<div class="jrequired">
		<input type="text" name="account" id="account" class="form-control" placeholder="Enter landline Number (1-10 Digit)" />
	</div>
</div>
<div class="form-group">
	<label>ACCOUNT NUMBER</label>
	<div class="jrequired">
		<input type="text" name="customer_account" id="customer_account" class="form-control" placeholder="Enter Account Number (10 Digits)" />
	</div>
</div>
<div class="form-group">
	<label>SERVICE TYPE</label>
	<div class="jrequired">
		<select name="bsnl_service_type" id="bsnl_service_type" class="form-control">
			<option value=""></option>
			<option value="LLI">LLI – Landline Individual</option>
			<option value="LLC">LLC – Landline Corporate</option>
		</select>
	</div>
</div>
<?php }elseif($xop == '44'){ ?>
<!--MTNL-->
<div class="form-group">
	<label>PHONE NUMBER</label>
	<div class="jrequired">
		<input type="text" name="account" id="account" class="form-control" placeholder="Enter landline Number" />
	</div>
</div>
<div class="form-group">
	<label>CUSTOMER ACCOUNT NUMBER</label>
	<div class="jrequired">
		<input type="text" name="customer_account" id="customer_account" class="form-control" placeholder="Enter Customer Account Number" />
	</div>
</div>
<?php }elseif($xop == '60'){ ?>
<!--TIKONA-->
<div class="form-group">
	<label>Tikona Account Number</label>
	<div class="jrequired">
		<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" />
	</div>
</div>
<?php } ?>