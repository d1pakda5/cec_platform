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
$id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;


if(isset($_POST['note'])) {
	if($_POST['description'] == '' ) {
		$error = 1;		
	} else {
		$title = htmlentities(addslashes($_POST['title']),ENT_QUOTES);		
			$description = ($_POST['description']);			
			$note_date=date("Y-m-d");
			$status=0;
			$db->execute("UPDATE `personal_notes` SET `note_date`='".$note_date."',`title`='".$title."',`description`='".$description."',`status`='".$status."'WHERE id='".$id."'");
			$error = 4;
			header("location:notes.php?error=4");
			}
}
$note_info = $db->queryUniqueObject("SELECT * FROM personal_notes WHERE id = '".$id."' ");
if(!$note_info) header("location:notes.php");

$meta['title'] = "Notes | Edit ";

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

<!--<script>-->
	
<!--bkLib.onDomLoaded(function(){-->
<!--  var myInstance = new nicEditor().panelInstance('description');-->
<!--});-->
<!--</script>-->
<script>
jQuery(document).ready(function() {
    	CKEDITOR.plugins.addExternal( 'simplebox', 'https://sdk.ckeditor.com/samples/assets/plugins/simplebox/', 'plugin.js' );

				CKEDITOR.replace( 'description', {

					// Add the Simple Box plugin.
					extraPlugins: 'simplebox',

					// Besides editor's main stylesheet load also simplebox styles.
					// In the usual case they can be added to the main stylesheet.
					contentsCss: [
						'assets/plugins/simplebox/styles/contents.css',
						'https://cdn.ckeditor.com/4.8.0/full-all/contents.css'
					],

					// Set height to make more content visible.
					//height: 500
				} );
});
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add note?")) {
        form.submit();
      }
		},
	  rules: {
	  	
			description: {
				required:true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	
});
	

</script>

<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Notes</div>
				<div class="pull-right">				
				<a href="notes.php" class="btn btn-primary"  ><i class="fa fa-list"></i> list Notes</a>
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
		<?php }?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> Add Notes</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				
      	        <form action="" method="post" id="fundForm" class="form-horizontal">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-sm-3 control-label">Title :</label>
									<div class="col-sm-9 jrequired">
										<input type="text" name="title" id="title" class="form-control" value="<?php echo $note_info->title;?>">
									</div>
								</div>
								<div id="textPane" class="form-group" >
									<label class="col-sm-3 control-label">Description :</label>
									<div class="col-sm-9 jrequired">
										<textarea name="description" id="description" class="form-control" placeholder="Enter text content here" rows="50"><?php echo stripslashes($note_info->description);?></textarea>
									</div>
								</div>
							</div>
						</div>

		
        <button type="submit" name="note" id="note" class="btn btn-primary pull-right"><i class="fa fa-save"></i>  Update</button>
		</form>
     </div>
 </div>

		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>

<?php include('footer.php'); ?>