<?php
session_start();
$admin_id=$_SESSION['accmgr'];
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['amount']=='') {
		$error = 1;		
	} else {
		$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['accmgr']."' ");
		if($admin_info && $admin_info->pin == hashPin($_POST['pin'])) {	
			$dist_id = htmlentities(addslashes($_POST['dist_id']),ENT_QUOTES);	
			$admin_id = htmlentities(addslashes($_POST['admin_id']),ENT_QUOTES);
			$ret_id = htmlentities(addslashes($_POST['ret_id']),ENT_QUOTES);		
			$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);	
			$month = htmlentities(addslashes($_POST['month']),ENT_QUOTES);	
			$year = htmlentities(addslashes($_POST['year']),ENT_QUOTES);	
			if($dist_id==""||$dist_id==null)
			{
				$result=$db->queryUniqueObject("select id from monthly_sale_target where month='".$month."' and year='".$year."' and dist_ret_id='".$ret_id."'");
			}
			else if($ret_id==""||$ret_id==null)
			{
				$result=$db->queryUniqueObject("select id from monthly_sale_target where month='".$month."' and year='".$year."' and dist_ret_id='".$dist_id."'");
			}
			$target_id=$result->id;
			if($target_id == null || $target_id=="")
			{
				if($dist_id==""||$dist_id==null)
				{
					$db->execute("INSERT INTO `monthly_sale_target`(`month`, `year`, `acc_manager_id`,  `dist_ret_id`, `target_value`) VALUES ('".$month."','".$year."','".$admin_id."','".$ret_id."','".$amount."')");
				}
				else 
				{
					$db->execute("INSERT INTO `monthly_sale_target`(`month`, `year`, `acc_manager_id`, `dist_ret_id`, `target_value`) VALUES ('".$month."','".$year."','".$admin_id."','".$dist_id."','".$amount."')");
				}
		 	}
		 	else
		 	{
		 		$db->execute("UPDATE `monthly_sale_target` SET `target_value`='".$amount."' WHERE id='".$target_id."'");
		 	}
			$error = 4;
		} else {
			$error = 2;
		}	
	}

}

$meta['title'] = "Monthly Sale Target";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">

function dist(){
	$("#dist_div").css("display","block");
	$("#ret_div").css("display","none");

}
function ret(){
	$("#dist_div").css("display","none");
	$("#ret_div").css("display","block");

}


jQuery(document).ready(function(){	
	var today = new Date(),
    yyyy = today.getFullYear(),
    inpYear = $('#year'),
    html = '';

	for (var i = 0; i < 5; i++, yyyy++) {
	    html = html + '<option>' + yyyy + '</option>';
	};    

	inpYear.html(html);

	var admin_id=$("#admin_id").val();
	jQuery.ajax({ 
			url: "ajax/list_dist.php",
			type: "POST",
			data: "admin_id="+admin_id,
			async: false,
			success: function(data) {
				jQuery("select#dist_id").html(data);
			}
		});
	jQuery.ajax({ 
			url: "ajax/list_ret.php",
			type: "POST",
			data: "admin_id="+admin_id,
			async: false,
			success: function(data) {
				jQuery("select#ret_id").html(data);
			}
		});

	
	
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add target?")) {
        form.submit();
      }
		},
	  rules: {
			month: {
				required:true
			},
			year: {
				required:true
			},
			amount: {
				required:true,
				number: true
			},
			pin: {
				required: true,
				minlength: 4,
				maxlength: 4
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"> Sale Target <small>Add</small></div>
			<div class="pull-right">
				<a href="admin-dashboard.php" class="btn btn-primary"><i class="fa fa-reply"></i> Dashboard</a>
			</div>
		</div>
		<?php if($error == 5) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> rollback due to internal error.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i>successful, Target updated.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }  else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Invalid PIN!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some parameters are missing!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-8">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Sale Target</h3>
					</div>
					<div class="col-md-12">	
							<div class="col-md-6">	
							</div>
							<div class="col-md-2">	
								<button type="button" onclick="dist()" class="btn btn-success"><i class="fa fa-user"></i> Distributor</button>
							</div>	
							<div class="col-md-2">	
								<button type="button" onclick="ret()" class="btn btn-warning"><i class="fa fa-user"></i> Retailor</button>
							</div>
							<div class="col-md-2">	
							</div>
						</div>
					<form action="" method="post" id="fundForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-sm-4 control-label">Month :</label>
									<div class="col-sm-8 jrequired">
										<select name="month" id="month" class="form-control">
										    <option value=''>--Select Month--</option>
										    <option value='1'>Janaury</option>
										    <option value='2'>February</option>
										    <option value='3'>March</option>
										    <option value='4'>April</option>
										    <option value='5'>May</option>
										    <option value='6'>June</option>
										    <option value='7'>July</option>
										    <option value='8'>August</option>
										    <option value='9'>September</option>
										    <option value='10'>October</option>
										    <option value='11'>November</option>
										    <option value='12'>December</option>
										 </select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Year :</label>
									<div class="col-sm-8 jrequired">
										<select name="year" id="year" class="form-control">
										    
										</select>
									</div>
								</div>
								<div class="form-group" id="dist_div" style="display: none">
									<label class="col-sm-4 control-label">Distributor :</label>
									<div class="col-sm-8 jrequired">
										<input type="hidden" name="admin_id" id="admin_id" value="
										<?php echo $admin_id ?>">
										<select name="dist_id" id="dist_id" class="form-control">

										</select>
									</div>
								</div>
								<div class="form-group" id="ret_div" style="display: none">
									<label class="col-sm-4 control-label">Retailor :</label>
									<div class="col-sm-8 jrequired">
										<select name="ret_id" id="ret_id" class="form-control">

										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label"> Target Amount :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="amount" id="amount" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label"> Pin :</label>
									<div class="col-sm-8 jrequired">
										<input type="password" name="pin" id="pin" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Notepad</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<textarea name="note" id="note" rows="21" cols="15" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>