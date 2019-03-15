<?php
?>
<div class="box">
	<div class="box-header">
		<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Sender</h3>
	</div>
	<div class="box-body">	
		<form class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label">First Name :</label>
				<div class="col-sm-5">
					<input type="text" name="fname" id="fname" class="form-control" placeholder="Enter First Name" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">Last Name :</label>
				<div class="col-sm-5">
					<input type="text" name="lname" id="lname" class="form-control" placeholder="Enter Last Name" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">Mobile Number :</label>
				<div class="col-sm-5">
					<input type="text" name="smobile" id="smobile" class="form-control" value="<?php echo $account;?>" placeholder="Enter Mobile Number" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">&nbsp;</label>
				<div class="col-sm-5">
					<input type="submit" name="submit" id="sendSubmit" class="btn btn-success" />
				</div>
			</div>							
		</form>
	</div>
</div>