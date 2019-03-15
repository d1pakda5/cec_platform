<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['operator_id']=='' || $_POST['product_name']=='' || $_POST['price']=='') {
		$error = 1;		
	} else {		
		$product_name = htmlentities(addslashes($_POST['product_name']),ENT_QUOTES);
		$product_detail = htmlentities(addslashes($_POST['product_detail']),ENT_QUOTES);
		$retail_price = htmlentities(addslashes($_POST['retail_price']),ENT_QUOTES);
		$price = htmlentities(addslashes($_POST['price']),ENT_QUOTES);		
		$exists = $db->queryUniqueObject("SELECT * FROM products WHERE operator_id='".$_POST['operator_id']."'");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("INSERT INTO `products`(`operator_id`, `product_name`, `product_detail`, `retail_price`, `price`, `product_img`, `status`) VALUES ('".$_POST['operator_id']."', '".$product_name."', '".$product_detail."', '".$retail_price."', '".$price."', '', '".$_POST['status']."')");
			$error = 3;
		}		
	}
}
$meta['title'] = "Products";
include('header.php');
?>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Products <small>/ Add</small></div>
			<div class="pull-right">
				<a href="products.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Added successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some fields are empty!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-plus-square"></i> Add</h3>
			</div>
			<form action="" method="post" id="operatorForm" class="form-horizontal">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Product :</label>
							<div class="col-sm-8 jrequired">
								<select name="operator_id" id="operator_id" class="form-control no-full-width">
									<option value="">-- select --</option>
									<?php
									$query = $db->query("SELECT * FROM operators WHERE service_type='10'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->operator_id;?>"><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Product Name :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="product_name" id="product_name" class="form-control" placeholder="PRODUCT NAME">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Description :</label>
							<div class="col-sm-8 jrequired">
								<textarea type="text" name="product_detail" id="product_detail" class="form-control" placeholder="PRODUCT DESCRIPTION"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Price (MRP) :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="retail_price" id="retail_price" class="form-control no-full-width" placeholder="ENTER PRICE MRP">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Price (Selling) :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="price" id="price" class="form-control no-full-width" placeholder="ENTER PRICE (SELLING)">
							</div>
						</div>
						<div class="form-group"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control no-full-width">
									<option value="">-- select --</option>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="row">
					<div class="col-md-12">
						<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
							<i class="fa fa-save"></i> Save
						</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>