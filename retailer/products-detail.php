<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$requestid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$product = $db->queryUniqueObject("SELECT * FROM products WHERE id='".$requestid."' ");
if(!$product) {
	header("location:products.php");
	exit();
}
$meta['title'] = "Products";
include('header.php');
?>
<link rel="stylesheet" href="../css/product.css">
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
	});
});
</script>
<div class="content">
	<div class="container">	
		<?php if($error == 4) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Recharge successful!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<?php
		$qry = $db->query("SELECT * FROM notifications WHERE ntype='s' AND status='1' AND (user_type='5' OR user_type='0') AND notification_date_to >= CURDATE() ORDER BY notification_date DESC");
		if($db->numRows($qry) > 0) { ?>
		<div class="alert alert-notification">	
			<div class="row">
				<div class="col-xs-12 pull-right">			
					<marquee scrollamount="3" direction="scroll" onmouseover="this.setAttribute('scrollamount', 0, 0);" onmouseout="this.setAttribute('scrollamount', 3, 0);">
						<?php
						while($result = $db->fetchNextObject($qry)) { ?>
							<span class="text-alert" style="margin-right:40px;"><i class="fa fa-bullhorn"></i> <?php echo str_replace(array("<br>","<br/>"), "", $result->notification_content);?></span>
						<?php } ?>
					</marquee>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="row">	
			<div class="col-sm-12">
				<div class="page-header">
					<div class="page-title pull-left"><small>Product /</small> <?php echo $product->product_name;?></div>
					<div class="pull-right">
						<a href="products.php" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row" >
			<div class="col-sm-6" >
				<div class="cards">
					<?php if($product->product_img=='') {?>
					<div class="img"><img src="../images/no-product-img.png" /></div>
					<?php }else{ ?>
					<div class="img"><img src="../<?php echo $product->product_img;?>" /></div>
					<?php } ?>					
					<div class="detail" style="overflow: scroll;"><?php echo html_entity_decode($product->product_detail);?></div>
				</div>
			</div>
			<!-- End -->
			<div class="col-sm-6">
				<div class="cards pd-50">
					<form action="products-pay.php" method="post" id="mobileForm" class="form-horizontal">
					<input type="hidden" name="product_name" id="product_name" class="form-control" value="<?php echo $product->product_name;?>" />
					<input type="hidden" name="productid" id="productid" class="form-control" value="<?php echo $product->id;?>" />
					<input type="hidden" name="operator" id="operator" class="form-control" value="<?php echo $product->operator_id;?>" />
					<div class="form-group">
						<label>CUSTOMER MOBILE NUMBER</label>
						<div class="jrequired">
							<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label>CUSTOMER NAME</label>
						<div class="jrequired">
							<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter Customer Name" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label>CUSTOMER EMAIL</label>
						<div class="jrequired">
							<input type="text" name="customer_email" id="customer_email" class="form-control" placeholder="Enter Customer Email" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label>INSTALLATION ADDRESS</label>
						<div class="jrequired">
							<textarea type="text" name="address" id="address" class="form-control" placeholder="Enter installtion address" autocomplete="off" /></textarea>
						</div>
					</div>
					<div class="form-group">
						<label>CITY</label>
						<div class="jrequired">
							<input type="text" name="city" id="city" class="form-control" placeholder="Enter Customer City" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label>PINCODE</label>
						<div class="jrequired">
							<input type="number" name="pincode" id="pincode" class="form-control" placeholder="Enter Customer Pincode" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label>STATE</label>
						<div class="jrequired">
						<select name="state" id="state" class="form-control">
											<option value="">---Select---</option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>"><?php echo $rlt->states;?></option>
											<?php } ?>
						</select>
						</div>
					</div>
					<div class="form-group">
						<label>AMOUNT</label>
						<div class="jrequired">
							<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" value="<?php echo $product->price;?>" autocomplete="off" readonly="" />
						</div>
					</div>					
					<div class="form-group">
						<p>&nbsp;</p>
					</div>
					<div class="form-group">
						<label>PIN</label>
						<div class="row">
							<div class="col-xs-8 jrequired">
								<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
							</div>
							<div class="col-xs-4 jrequired">
								<input type="submit" name="submit" value="PAY NOW" class="btn btn-success btn-block" />
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>