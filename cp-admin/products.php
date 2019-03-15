<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$msg = isset($_GET['msg']) && $_GET['msg']!='' ? mysql_real_escape_string($_GET['msg']) : '';
$statement = "products ORDER BY product_name ASC";
$meta['title'] = "Products";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Products</div>
			<div class="pull-right">				
				<a href="products-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<?php if($msg=='success') { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Products</h3>
			</div>			
			<div class="box-body no-padding">
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="5%">S. No.</th>
							<th width="5%">ID</th>
							<th>Product Name</th>							
							<th width="10%">Retail Price</th>
							<th width="10%">Selling Price</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT * FROM {$statement}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td align="center"><?php echo $result->operator_id;?></td>
							<td><?php echo $result->product_name;?></td>
							<td><?php echo $result->retail_price;?></td>
							<td><?php echo $result->price;?></td>
							<td style="text-align:center;">
								<a href="products-edit.php?id=<?php echo $result->id;?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>