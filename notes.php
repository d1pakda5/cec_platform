<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('config.php');
include('system/class.pagination.php');
$tbl = new ListTable();
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d", strtotime($from));
$aTo = date("Y-m-d", strtotime($to));

$sWhere = "WHERE 1 ";

if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND (note.title LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR note.description LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND note.status='".mysql_real_escape_string($_GET['status'])."' ";
} 
else
{
    $sWhere .= " AND note.status='0' ";
}

$statement = "personal_notes note $sWhere ORDER BY note.note_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"]!='' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('notes.php');



if(isset($_POST['note'])) {
	if($_POST['title'] == '' || $_POST['description'] == '' ) {
		$error = 1;		
	} else {
		$title = htmlentities(addslashes($_POST['title']),ENT_QUOTES);		
			$description = htmlentities(addslashes($_POST['description']),ENT_QUOTES);			
			$note_date=date("Y-m-d");
			$status=0;
			$db->execute("INSERT INTO personal_notes(note_date, title, description, status) VALUES ('".$note_date."','".$title."','".$description."','".$status."')");
			$error = 4;
			header("location:notes.php?error=4");
			}
}
if(isset($_POST['trash'])) {
	
			$db->execute("UPDATE `personal_notes` SET `status`=2 WHERE id=".$_POST['trash']);
			$error = 9;
			header("location:notes.php?error=9");
			
}
if(isset($_POST['work_done'])) {
	
			$db->execute("UPDATE `personal_notes` SET `status`=1 WHERE id=".$_POST['work_done']);
			$error = 8;
			header("location:notes.php?error=8");
			
}
if(isset($_POST['saved'])) {
	
			$db->execute("UPDATE `personal_notes` SET `status`=0 WHERE id=".$_POST['saved']);
			$error = 7;
			header("location:notes.php?error=7");
			
}


$meta['title'] = "Notes";

?>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" id="theme-style">
<link rel="stylesheet" href="css/theme.css" type="text/css" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/fancybox2/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="js/datepicker/datepicker.css">
<script src="http://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script src="js/datepicker/bootstrap-datepicker.js"></script>

<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add note?")) {
        form.submit();
      }
		},
	  rules: {
	  	title: {
				required: true
			},
			description: {
				required:true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	
});
	
});
</script>
<style>

    td{
        width:10%;
        word-break: break-all;
        word-wrap: break-word;
    }
    a{
        color:black;
        font-weight:500;
    }
    
</style>

<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Notes</div>
			<div class="pull-right">				
				<a href="notes-add.php" class="btn btn-primary"  ><i class="fa fa-plus"></i> Add New Note</a>
			</div>
		</div>
		<?php  if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> Note Added successfully.
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
		<?php }  else if($error == 9) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Note Marked as a trash
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 8) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Note Marked as a Wrok Done.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 7) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> Note Marked as a Saved.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Notes</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" class="form-control">
									<option value="">---Select Status---</option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>Saved</option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>Work Done</option>
									<option value="2" <?php if(isset($_GET['status']) && $_GET['status'] == "2") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered col-md-12">
					<thead>
						<tr>
							<th width="5%">S.No</th>
							<th width="5%">Date</th>
							<th width="20%">Title</th>
							<th width="30%">Description</th>
							<th width="10%">Status</th>
							<th width="20%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php						
						$query = $db->query("SELECT note.* FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><b><?php echo $result->note_date;?></b></td>
							<td><?php echo $result->title;?></td>
							<td ><a href="notes-edit.php?id=<?php echo $result->id;?>" > <?php echo "View in Details";?></a></td>
							
							<td>
								<span class="btn btn-xs btn-default">
								<?php if($result->status=='0') {?>
									<i class="fa fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-minus-circle text-red"></i>
								<?php }?>
								</span>
							</td>
							<td>	
								<form action="" method="post" id="trashform" class="form-horizontal">
								
								<button class="btn btn-danger" type="submit"  value="<?php echo $result->id;?>" name="trash" id="trash" >
								<i class="fa fa-trash"></i></button> 
								&nbsp;&nbsp;
							    <button class="btn btn-warning" type="submit" value="<?php echo $result->id;?>" name="work_done" id="work_done">
								<i class="fa fa-check"></i></button> 
								&nbsp;&nbsp;
								<button class="btn btn-success" type="submit" value="<?php echo $result->id;?>" name="saved" id="saved">
								<i class="fa fa-download"></i></button> 
							</td>
							
						</tr>
						<?php } ?>
					</tbody>
					
				</table>
			
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>


<?php include('footer.php'); ?>