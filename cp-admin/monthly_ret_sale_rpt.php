<?php
    session_start();
    if(!isset($_SESSION['admin'])) {
    	header("location:index.php");
    	exit();
    }
    include('../config.php');
    include('../system/class.pagination.php');
    $tbl = new ListTable();
    ini_set('memory_limit','1280M');
    set_time_limit(9999999999);
    $month = $_GET['month'];
    $year = $_GET['year'];
    $admin_id=$_GET['admin_id'];
    // $sWhere = "where user.status='1'and (user.user_type=6 or user.user_type=5)" ;
    if(isset($_GET['admin_id']) && $_GET['admin_id']!='') {
    	$sWhere .= " AND user.assign_id='".mysql_real_escape_string($_GET['admin_id'])."' ";
    }
    
    $arUser1 = array();
    $qry1 = $db->query("SELECT * FROM apps_admin WHERE user_level='a' and status='1' $sArray ");
    while($rst1 = $db->fetchNextObject($qry1)) {
    	$arUser1[] = array('admin_id'=>$rst1->admin_id, 'name'=>$rst1->fullname);
    }
    
    $statement="apps_user user inner join apps_user user2 on user.dist_id=user2.uid left join apps_wallet wallet on user.uid=wallet.uid where user.status='1'and (user.user_type=6 or user.user_type=5) ";
    //Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('monthly_ret_sale_rpt.php');


    $meta['title'] = "Monthly Sale Report By Manager";
    include('header.php');
    
    ?>
<script>
    jQuery(document).ready(function() {
    	jQuery('#from').datepicker({
    		format: 'yyyy-mm-dd'
    	});
    	jQuery('#to').datepicker({
    		format: 'yyyy-mm-dd'
    	});
    });
    function doExcel(){
    	var from = jQuery('#from').val();
    	var to = jQuery('#to').val();
    	var type = jQuery('#type').val();
    	var uid = jQuery('#uid').val();
    	window.location='excel/transaction.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
    }
    function report(ele)
    {
    	var admin_id = jQuery(ele).attr('admin_id');
    	var month = jQuery(ele).attr('month');
    	var year = jQuery(ele).attr('year');
    	window.location='monthly_sale_rpt_manager.php?admin_id='+admin_id+'&month='+month+'&year='+year;
    }
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/tableHeadFixer.js"></script>

<div class="content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title">Reports <small>/ Monthly Sale</small></div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-list"></i> Monthly Sale Report By Retailer</h3>
            </div>
            
            <div class="box-filter padding-20 row">
                <div class="col-sm-1">
                    </div>
                <form method="get" id="rptRecharge" class="">
                   	<div class="col-sm-3">
							<div class="form-group">
							    <label>Account Manager</label>
								<select name="admin_id" class="form-control">
									<option value="">---Select Account Manager---</option>
									<?php foreach($arUser1 as $key=>$data) { ?>
									<option value="<?php echo $data['admin_id'];?>" <?php if(isset($_GET['admin_id']) && $_GET['admin_id'] == $data['admin_id']) { ?> selected="selected"<?php } ?>><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
							</div>
					   </div>
					   <div class="col-sm-2">
							<div class="form-group">
								<label>Show</label>
								<select name="show" class="form-control">
									<option value="10" <?php if($limit == '10') { ?> selected="selected"<?php } ?>>10</option>
									<option value="25" <?php if($limit == '25') { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if($limit == '100') { ?> selected="selected"<?php } ?>>100</option>
									<option value="200" <?php if($limit == '200') { ?> selected="selected"<?php } ?>>200</option>
									
								</select>
							</div>
						</div>
                    <div class="">
                        <input type="hidden" id="month" name="month" value="<?php echo $month ?>">
                    </div>
                    <div class="">
                        <input type="hidden" id="year" name="year" value="<?php echo $year ?>">
                    </div>
                   
            
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label">&nbsp;</label><br>
                    <input type="submit" value="Filter" class="btn btn-warning">
                </div>
            </div>
            </form>
        </div>
        <?php
            $last_day=date("t", strtotime($year."-".$month."-01"));
            // echo "SELECT user.*, user2.company_name as distname, wallet.balance as balance from {$statement} $sWhere LIMIT {$startpoint}, {$limit}";die;
            $myRetailors=$db->query("SELECT user.*, user2.company_name as distname, wallet.balance as balance from {$statement} $sWhere LIMIT {$startpoint}, {$limit}");
            ?>
        <div class="span12" style="margin-left: 0px;margin-top: 20px;">
            <div class="grid-view" style="height: 400px;">
                <table id="report" class="table table-bordered table-stripped">
                    <thead>
                        <tr>
                            <th style="vertical-align: middle;" rowspan="2">Sr #</th>
                             <th style="vertical-align: middle;" rowspan="2">Retailor</th>
                            <th style="vertical-align: middle;" rowspan="2">Distributor</th>
                            <th style="vertical-align: middle;" rowspan="2">Current Balance</th>
                            <th style="vertical-align: middle;" rowspan="2">Total</th>
                            <?php
                                for($i=1;$i<=$last_day;$i++)
                                {
                                	echo '<th style="text-align:center;">'.$i.'</th>';	
                                }
                                ?>
                        </tr>
                        <tr>
                            <?php
                                for($i=1;$i<=$last_day;$i++)
                                {
                                	$day_name=date("D", strtotime($year."-".$month."-".$i));
                                
                                	echo '<th>'.$day_name.'</th>';	
                                }
                                ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                           	$total_current_balance=0;
                            $total_total=0;
                            $balance_total=0;
                            $day_total=array();
                            for($j=0;$j<$last_day;$j++)
                            {
                            	$day_total[]=0;
                            }
                            $k=0;
                            
                            while($result = $db->fetchNextObject($myRetailors)) 
                            {
                            	
                            
                            	echo '<tr>';
                            	echo '<td>'.(++$k).'</td>';
                            	echo '<td>'.$result->company_name.' ('.$result->uid.')'.'</th>';
                            	
                            	echo '<td>'.$result->distname.'</td>';
                            	$row=array();	
                            	for($j=1;$j<=$last_day;$j++)
                            	{
                            		$dte=date("Y-m-d", strtotime($year."-".$month."-".$j));
                            		$sql="select sum(amount) as sale_amount from transactions where account_id=".$result->uid." and transaction_date like'%".$dte."%' and transaction_term='RECHARGE'";
                            	
                            		$resultDay=$db->queryUniqueObject($sql);
                            		$sale_amount=($resultDay->sale_amount=="")?0:$resultDay->sale_amount;
                            		$row[]=array('sale_amount' =>$sale_amount);
                            		$day_total[$j-1] = $day_total[$j-1] + $sale_amount;
                            	}
                            
                            	$total=0;
                            
                            	for($cont=0;$cont<count($row);$cont++)
                            	{
                            		$total=$total+$row[$cont]["sale_amount"];
                            		
                            	}
                            
                            
                            	$total_total = $total_total + $total;
                            	
                            
                                echo '<td>'.round($result->balance,0).'</td>';
                                $total_current_balance=$total_current_balance+$result->balance;
                            	echo '<td>'.round($total,0).'</td>';
                            	for($j=0;$j<count($row);$j++)
                            	{
                            		
                            			echo '<td>'.round($row[$j]['sale_amount'],0).'</td>';
                            		
                            	}
                            	echo '</tr>';
                            }
                            ?>
                           
                            <tfoot>
                        <tr class="tdfix">
                            <td style="background-color: #e8eef2;z-index: 2 !important;">&nbsp;</td>
                            <td style="background-color: #e8eef2;z-index: 2 !important;">&nbsp;</td>
                           
                            <td style="text-align:right;background-color: #e8eef2;z-index: 2 !important;"><b>Total</b></td>
                            <td style="background-color: #e8eef2;z-index: 2 !important;"><?php echo round($total_current_balance,0);?> </td>
                            <td style="background-color: #e8eef2;z-index: 2 !important;"><?php echo round($total_total,0);?> </td>
                           
                            <?php
                                for($i=0;$i<count($day_total);$i++)
                                {
                                	echo '<td style="background-color: #e8eef2;z-index: 1 !important;">'.round($day_total[$i],0).'</td>';
                                }
                                ?>
                        </tr>
                    </tfoot>
                     </tbody>
                </table>
            </div>
        </div>
        <div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
	</div>
    </div>
    
</div>
</div>
<script type="text/javascript">
    $("#report").tableHeadFixer({'left' : 5 ,'head' : true,'foot' : true}); 
        
</script>
<style type="text/css">
    td{
    background-color: white;
    }
    .tdfix td{
            z-index: 2 !important;
            
    }
</style>
<?php include('footer.php');?>