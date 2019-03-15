<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

$meta['title'] = "Recharge";
include('header.php');
?>
<link rel="stylesheet" href="../css/product.css">
<div class="content">
	<div class="container">
		<?php if($error == 4) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Recharge successful!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Already refunded!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Insert a valid Transaction Id!!
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
					<div class="page-title pull-left">Products</div>
				</div>
			</div>
		</div>
		<div class="row">
			<?php
			$query = $db->query("SELECT * FROM products WHERE status='1' ORDER BY product_name ASC");
			while($row = $db->fetchNextObject($query)) {
			?>
			<div class="col-sm-3">
				<div class="cards">
					<?php if($row->product_img=='') {?>
					<div class="img"><img src="../images/no-product-img.png" class="img-responsive" /></div>
					<?php }else{ ?>
					<div class="img"><img src="../<?php echo $row->product_img;?>" class="img-responsive" /></div>
					<?php } ?>
					<div class="title"><?php echo $row->product_name;?></div>
					<div class="price">
						<?php if($row->retail_price > $row->price) { ?>
							<small><i class="fa fa-inr"></i> <?php echo $row->retail_price;?></small>
						<?php } ?>
						<i class="fa fa-inr"></i> <?php echo $row->price;?>
					</div>
					<div class="bottom">
					    <?php if($_SESSION['retailer_uid']=='20032374')
								{?>
									<a href="products-detail.php?id=<?php echo $row->id;?>" disabled id="buyNow-<?php echo $row->id;?>" onclick="buyNow(<?php echo $row->id;?>);" class="btn btn-success">PAY NOW</a>

								<?php } else {
								?>
									<a href="products-detail.php?id=<?php echo $row->id;?>" id="buyNow-<?php echo $row->id;?>" onclick="buyNow(<?php echo $row->id;?>);" class="btn btn-success">PAY NOW</a>

								<?php }?>
					
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<!-- Recharge Plan Modal -->
<div class="modal fade" id="rechargePlan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title Enquiry" id="myModalLabel">Search You Plans</h5>
			</div>
			<div class="modal-body">
				Coming Soon
			</div>
		</div>
	</div>
</div>
<!-- Recharge verify Modal -->
<div class="modal fade" id="rechargeVerify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<img src="../images/loader.gif" />
			</div>
		</div>
	</div>
</div>
<script>
jQuery(function($){
	$('.ireff_prepaid').click(function(ev){
		$('#rechargePlan').modal('show');
	});
});
</script>
<?php include('footer.php'); ?>