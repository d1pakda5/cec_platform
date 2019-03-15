<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(!isset($_GET['token']) || $_GET['token'] != $token) { exit("Token not match"); }
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['to_bank_account'] == '' || $_POST['pay_mode'] == '' || $_POST['amount'] == '' || $_POST['payment_date'] == '') {
		$error = 1;		
	} else {
		
		$to_bank_account = htmlentities(addslashes($_POST['to_bank_account']),ENT_QUOTES);
		$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);
		$transaction_ref_no = htmlentities(addslashes($_POST['transaction_ref_no']),ENT_QUOTES);
		$your_bank_name = htmlentities(addslashes($_POST['your_bank_name']),ENT_QUOTES);
		$your_bank_account = htmlentities(addslashes($_POST['your_bank_account']),ENT_QUOTES);
		
		if(!empty($_FILES['receipt']['name'])) {	
			$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg','.JPG','.GIF','.BMP','.PNG','');
			$max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).	
			$str = "../uploads/";
			$filename = $_FILES['receipt']['name']; // Get the name of the file (including file extension).
			$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.		
			if(!in_array($ext, $allowed_filetypes)) {
				$error = 2;
			} else {	
				$file = time().$_FILES['receipt']['name'];
				$s = move_uploaded_file($_FILES['receipt']['tmp_name'],$str.$file);
				$img_len = strlen($str.$file);
				$im_format = substr($str.$file, ($img_len-3), 3);
			}
		} else {
			$file = "";
		}
		if($transaction_ref_no!=""||$transaction_ref_no!=null)
		{
		$result1=$db->queryUniqueValue("select '".$transaction_ref_no."' in (select transaction_ref_no from fund_requests)");
		}
		if($result1==1){
			$error = 4;
		}
		else{
		$db->execute("INSERT INTO `fund_requests`(`request_id`, `request_date`, `request_user`, `request_to`, `user_type`,`reg_mobile`, `to_bank_account`, `your_bank_name`, `your_bank_account`, `pay_mode`, `payment_date`, `amount`, `transaction_ref_no`, `file_attachment`, `status`) VALUES ('', NOW(), '".$_SESSION['distributor_uid']."', '0', '3','".$_POST['reg_mobile']."', '".$to_bank_account."', '".$your_bank_name."', '".$your_bank_account."', '".$_POST['pay_mode']."', '".$_POST['payment_date']."', '".$amount."', '".$transaction_ref_no."', '".$file."', '0')");
			$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$_SESSION['distributor_uid']."' ");
    		$text="Fund Request Received from ".$user_info->company_name;
    		smsSendSingle('8600250250', $text, 'fund_transfer');
		$error = 3;		
    	}
    }
}
$array['pay_method'] = getPaymentModeList();
$meta['title'] = "Fund Request";
include('header.php');
?>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to submit request?")) {
        form.submit();
      }
		},
	  rules: {
	  	to_bank_account: {
				required: true
			},
			pay_mode: {
				required: true
			},
			amount: {
				required: true
			},
			reg_mobile: {
				required: true
			},
			payment_date: {
				required: true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
	jQuery('#payment_date').datepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Account <small>/ Fund Request</small></div>
			<div class="pull-right">
				<a href="rpt-fund-request.php" class="btn btn-info"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Submited successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Oops, Please upload recipt copy!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Oops, request already submitted using this Transaction ID
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }?>
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Fund Request</h3>
					</div>
					<form action="" method="post" enctype="multipart/form-data" id="fundForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-xs-12">In Bank Account <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="to_bank_account" id="to_bank_account" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Payment Mode <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<select name="pay_mode" id="pay_mode" class="form-control">
											<option value=""></option>
											<?php foreach($array['pay_method'] as $data) {?>
											<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Amount <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired"><input type="text" name="amount" id="amount" class="form-control" /></div>
								</div><div class="form-group">
									<label class="col-xs-12">Registered mobile in bank <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired"><input type="text" name="reg_mobile" id="reg_mobile" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Transaction ID (If Any) </label>
									<div class="col-xs-12 jrequired"><input type="text" name="transaction_ref_no" id="transaction_ref_no" class="form-control" /></div>
								</div>
								
								<div class="form-group">
									<label class="col-xs-12">Your Bank Name</label>
									<div class="col-xs-12 jrequired"><input type="text" name="your_bank_name" id="your_bank_name" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Your Bank Account</label>
									<div class="col-xs-12 jrequired"><input type="text" name="your_bank_account" id="your_bank_account" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Payment Date <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired"><input type="text" name="payment_date" id="payment_date" readonly="" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Attach Receipt (If Any)</label>
									<div class="col-xs-12"><input type="file" name="receipt" id="receipt" /></div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
							    
            					<?php if($_SESSION['distributor_uid']=='20032368')
            					{?>
                                    <button type="submit" disabled name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
            
            					<?php } else {
            					?>
                                    <button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
                                    
            					<?php }?>

								
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
				<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Bank Details</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="col-sm-6" style="border:3px solid #dddddd">
						    <img width="65%" src="../images/bom.png">
						    <p><b>CLICK E CHARGE SERVICES PVT LTD</b></p>
						    <p>Branch : <b>pimple saudagar</b></p>
                            <P>current a/c - <b>60249460037</b></P>
                            <p>IFSC - <b>MAHB0001443</b></p>
						</div>
						<div class="col-sm-6" style="border:3px solid #dddddd">
						    <img width="65%" src="../images/sbi.png">
						    <p><b>CLICK E CHARGE SERVICES PVT LTD</b></p>
						    <p>Branch : <b>pimple saudagar</b></p>
                            <P>current a/c - <b>35801634212</b></P>
                            <p>IFSC - <b>SBIN0019063</b></p>
						</div>
						<div class="col-sm-6" style="border:3px solid #dddddd">
						    <img width="65%" src="../images/icici.png">
						    <p><b>CLICK E CHARGE SERVICES PVT LTD</b></p>
						    <p>Branch : <b>pimple saudagar</b></p>
                            <P>current a/c - <b>169705000706</b></P>
                            <p>IFSC - <b>ICIC0001697</b></p>
						</div>
					</div>
				</div>
			</div>
			
			
			
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Last 10 Fund Transfer</h3>
					</div>
					<div class="box-body min-height-300">
						<table class="table table-basic">
							<thead>
								<tr>
									<th>Date</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$query = $db->query("SELECT * FROM transactions WHERE account_id = '".$_SESSION['distributor_uid']."' AND transaction_term = 'FUND' ORDER BY transaction_id DESC LIMIT 10 ");
								if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
								while($result = $db->fetchNextObject($query)) {
								?>
								<tr>
									<td><?php echo date("d/m/Y H:i:s", strtotime($result->transaction_date));?></td>
									<td><b><?php echo round($result->amount,2);?></b></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>