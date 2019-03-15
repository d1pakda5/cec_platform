<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['note'])) {
	if( $_POST['data'] == '' ) {
		$error = 1;		
	} else {
		$title = htmlentities(addslashes($_POST['title']),ENT_QUOTES);		
			$data = ($_POST['data']);
			$reminder_description = htmlentities(addslashes($_POST['reminder_description']),ENT_QUOTES);
			$reminder_date_from = htmlentities(addslashes($_POST['reminder_date_from']),ENT_QUOTES);
			$reminder_date_to = htmlentities(addslashes($_POST['reminder_date_to']),ENT_QUOTES);
			$note_date=date("Y-m-d");
			$status=0;
			$type = htmlentities(addslashes($_POST['type']),ENT_QUOTES);
			if($type=="note")
			{
			$db->execute("INSERT INTO personal_notes(note_date, title, description, type, status) VALUES ('".$note_date."','".$title."','".$data."','".$type."','".$status."')");
			$error = 4;
			header("location:notes.php?error=4");
			}
			else if($type=="reminder")
			{
			$db->execute("INSERT INTO personal_notes(note_date, title, description, type, reminder_date_from,reminder_date_to, status) VALUES ('".$note_date."','".$title."','".$reminder_description."','".$type."','".$reminder_date_from."','".$reminder_date_to."','".$status."')");
			$error = 4;
			header("location:notes.php?error=4");
			}
			}
}


$meta['title'] = "Notes Add";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="http://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<!--<script src="../js/nicEdit/nicEdit.js"></script>-->
		

		 <script data-sample="1">
				
			</script>
<script>
	
// bkLib.onDomLoaded(function(){
//   var myInstance = new nicEditor().panelInstance('description');
// });
</script>
<script>
jQuery(document).ready(function() {
	jQuery('#reminder_date_from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#reminder_date_to').datepicker({
		format: 'yyyy-mm-dd'
	});
	CKEDITOR.plugins.addExternal( 'simplebox', 'https://sdk.ckeditor.com/samples/assets/plugins/simplebox/', 'plugin.js' );

				CKEDITOR.replace( 'data', {

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
</script>
<script>
function select_type()
{
    var type=$("#type").val();
   
    if(type=="note")
    {   $("#textPane").css("display","block");
        $("#reminder").css("display","none");
        $("#date_from").css("display","none");
        $("#date_to").css("display","none");
    }
    else if(type=="reminder")
    {   
        $("#reminder").css("display","block");
        $("#date_from").css("display","block");
        $("#date_to").css("display","block");
        $("#textPane").css("display","none");
        
    }
}

	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add note?")) {
        form.submit();
      }
		},
	  rules: {
	  	    reminder_date_from: {
				required:true
			},
			reminder_date_to: {
				required:true
			},
			reminder_description: {
				required:true
			},
			type: {
				required:true
			},
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
				
      	        <form action="" method="post" id="fundForm" enctype="multipart/form-data" class="form-horizontal">
						<div class="row">
							<div class="col-md-10">	
						        <div class="form-group">
									<label class="col-sm-3 control-label">Type :</label>
									<div class="col-sm-5 jrequired">
									    <select name="type" id="type" class="form-control" onchange="select_type()">
									       <option value="note">Note</option>
									        <option value="reminder">Reminder</option>
									    </select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Title :</label>
									<div class="col-sm-9 jrequired">
										<input type="text" name="title" id="title" class="form-control">
									</div>
								</div>
								<div id="textPane"  class="form-group">
									<label class="col-sm-3 control-label">Description :</label>
									<div class="col-sm-9 jrequired">
										<textarea name="data" id="data" rows="15" cols="80"></textarea>
									</div>
								</div>
								<div id="reminder" style="display:none" class="form-group">
									<label class="col-sm-3 control-label">Reminder Description :</label>
									<div class="col-sm-9 jrequired">
										<textarea name="reminder_description" id="reminder_description" class="form-control" placeholder="Enter text content here" rows="8"></textarea>
									</div>
								</div>
								<div id="date_from" style="display:none" class="form-group">
									<label class="col-sm-3 control-label">Reminder From Date :</label>
									<div class="col-sm-5 jrequired">
									    <input type="text" size="8" name="reminder_date_from" id="reminder_date_from" value="" placeholder="Reminder From Date " class="form-control">
									</div>
								</div>
								<div id="date_to" style="display:none" class="form-group">
									<label class="col-sm-3 control-label">Reminder To Date :</label>
									<div class="col-sm-5 jrequired">
									    <input type="text" size="8" name="reminder_date_to" id="reminder_date_to" value="" placeholder="Reminder To Date " class="form-control">
									</div>
								</div>
							</div>
						</div>

		
        <button type="submit" name="note" id="note" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Submit</button>
		</form>
     </div>
 </div>

		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>

<?php include('footer.php'); ?>