<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $meta['title'];?></title>

<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/font-awesome.min.css">
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<link rel="stylesheet" href="../css/stylesheet.css" type="text/css" />
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script>
$(window).on('load',function(){
        jQuery('#myModal').modal('show');
    });
	</script>
</head>
<style>
.box {margin-bottom:0px;}
.bg-status {
	background:#27ae61!important;
	border-color:#27ae61!important;
	color:#FFFFFF!important;
}
.bg-status-api {
	background:#36a2cf!important;
	border-color:#36a2cf!important;
}
.fancy-body-inner {
	width:100%;
	float:left;
}
.fancy-body-inner .table {border-collapse:collapse; margin-bottom:0px;}
.fancy-body-inner .table td {border:1px solid #eee; padding:2px 8px!important; vertical-align:top;}
</style>
<body class="bg-body">
<div class="menu">
	<div class="navbar" role="navigation">
		<div class="navbar-header">
			<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#dr-menu">
				<span class="sr-only">Toggle navigation</span>
				<span class="fa fa-bars"></span>
			</button>						
		</div>
		<div id="dr-menu" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="index.php">&nbsp;<i class="fa fa-lg fa-home"></i>&nbsp;</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="api-user.php">API User</a></li>
						<li><a href="master-distributor.php">Master Distributor</a></li>
						<li><a href="distributor.php">Distributor</a></li>
						<li><a href="retailer.php">Retailer</a></li>
						<li><a href="direct_retailer.php">Direct Retailer</a></li>

						<li><a href="all-users.php">All User</a></li>
						<li><a href="all-user-details.php">User Details</a></li>
						<li class="divider"></li>
						<li><a href="rpt-kyc.php">KYC Verification</a></li>
						<li><a href="kyc-list.php">KYC's</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Funds <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="fund-add.php">Add/Deduct Fund</a></li>
						<li><a href="fund-request.php">Fund Request</a></li>
						<li><a href="fund-deduct-x.php">Fund Deduct</a></li>
						<li><a href="set_target.php">Monthly Sale Target</a></li>
					</ul>
				</li>
				<li><a href="complaints.php">Complaints</a></li>
				<li><a href="tickets.php">Support</a></li>				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="live-recharge.php">Live Recharge</a></li>
						<li><a href="rpt-recharge.php">Recharge Report</a></li>
						<li><a href="rpt-offline.php">Offline Report</a></li>
						<li><a href="rpt-transactions.php">Transaction Report</a></li>
						<li><a href="rpt-user-transactions.php">User Transaction Report</a></li>
						<li><a href="rpt-commission.php">Commission Report</a></li>
						
						<li><a href="rpt-user-summary.php">User Summary Report</a></li>
						<li><a href="rpt-daybook.php">Day Book Report</a></li>
						<li><a href="rpt-api-book.php">API DayBook Report</a></li>
						<li><a href="rpt-all-user.php">All User Report</a></li>
						<li><a href="rpt-api-balance.php">API Balance Report</a></li>
						<li><a href="rpt-status.php">Recharge Status</a></li>
						<li><a href="rpt-status-transaction.php">Transaction Status</a></li>
						<li><a href="rpt-gst-deduct.php">GST Deduct Report</a></li>
						<li><a href="rpt-long-code.php">Long Code Report</a></li>
						<li><a href="rpt-received-sms.php">Mobile SMS Report</a></li>
						<li><a href="rpt-sent-sms.php">Sent SMS Report</a></li>
						<li><a href="rpt-login-activity.php">Login Activity Report</a></li>
						<li class="divider"></li>
						<li><a href="rpt-api-response.php">API Response Report</a></li>
						<li><a href="rpt-api-callback.php">API Callback Report</a></li>
						<li><a href="rpt-user-callback.php">User Callback Report</a></li>
					</ul>
				</li>	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Products <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="products.php">Products</a></li>
						<li class="divider"></li>
						<li><a href="orders.php">Orders</a></li>
					</ul>
				</li>					
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="operator.php">Operators</a></li>
						<li><a href="sample-ref-no.php">Transaction Ref No.</a></li>
						<li><a href="service.php">Services</a></li>
						<li><a href="api.php">API's</a></li>
						<li><a href="offline-denomination.php">Offline Denominations</a></li>
						<li><a href="denomination.php">Denomination's</a></li>
						<li><a href="api-balance.php">API Balance</a></li>
						<li class="divider"></li>
						<li class="navbar-plain-text"><b>Money Transfer</b></li>
						<li class="divider"></li>
						<li><a href="dmt-setting.php">DMT Settings</a></li>
						<li class="divider"></li>
					</ul>
				</li>				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Utilities <span class="caret"></span></a>
					<ul class="dropdown-menu">
					    <li><a href="mobile_notification.php">Mobile Notifications</a></li>
						<li><a href="notification.php">Notification</a></li>
						<li><a href="mobile-change-request.php">Mobile Change</a></li>
						<li class="divider"></li>
						<li><a href="send-email.php">Send Email</a></li>
						<li class="navbar-plain-text"><b>SMS Settings</b></li>
						<li><a href="send-sms.php">Send SMS</a></li>
						<li><a href="sms-api.php">SMS API</a></li>
						<li><a href="sms-settings.php">SMS Settings</a></li>
						<li><a href="sms-balance.php">SMS Balance</a></li>
						<li class="divider"></li>
						<li><a href="dmt-activation-request.php">DMT Activations</a></li>
						<li class="divider"></li>
						<li><a href="admin-user.php">Admin Users</a></li>
						<li><a href="assign-manager.php">Assign Manager</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">GST Txns <span class="caret"></span></a>
					<ul class="dropdown-menu">						
						<li><a href="rpt-retailer-gst-recharge-wise.php">Retailer (GST Recharge Wise)</a></li>
						<li><a href="rpt-distributor-gst-recharge-wise.php">Distributor (GST Recharge Wise)</a></li>
						<li><a href="rpt-apiuser-gst-recharge-wise.php">API User (GST Recharge Wise)</a></li>
						<li><a href="rpt-gst-txn-monthwise.php">GST Report (Old)</a></li>
						<li><a href="gst-invoice-user.php">GST Invoice Users</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">GST Invoices <span class="caret"></span></a>
					<ul class="dropdown-menu">						
						<li><a href="gst-invoices.php">List Invoices</a></li>
						<li><a href="gst-users.php">List GST Users</a></li>
					</ul>
				</li>
				
				<li>
				    <form action="" method="POST" id="fundForm" class="form-horizontal">
				        <input type="text"  name="s_rec_236" style="margin-top: 11px;width: 147px;font-size: 23px;" value="<?php if(isset($_POST['s_rec_236'])) { echo $_POST['s_rec_236']; }?>" placeholder="Txn No."><input type="submit" value="submit_rec_236" name="submit_rec_236" id="submit_rec_236" style="display: none;" class="btn btn-warning ">
				        </form></li>
				         <li><a href="notes-add.php" style="font-size: 27px;padding: 0px;margin: 13px;"><i class="fa fa-edit"></i></a></li>
			</ul>
				
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown pull-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Profile <span class="caret"></span></a>
					<ul class="dropdown-menu">
					    <li><a href="notes-add.php">Notes</a></li>
						<li><a href="profile.php">Profile</a></li>
						<li><a href="change-password.php">Change Password</a></li>
						<li><a href="change-pin.php">Reset Pin</a></li>
						<li><a href="update-fund.php">Update Balance</a></li>
						<li class="divider"></li>
						<li><a href="logout.php">Logout <i class="fa fa-sign-out"></i></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
<?php
if(isset($_POST['submit_rec_236'])) {
    

if(isset($_POST['s_rec_236']) && $_POST['s_rec_236']!='') {
    header("location:notes.php?error=4");
	$aStr23 = mysql_real_escape_string($_POST['s_rec_236']);
	$sWhere = "where recharge.recharge_id='".$aStr23."'";
	$statement23 = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid LEFT JOIN transactions tr ON recharge.recharge_id=tr.transaction_ref_no LEFT JOIN gst_transactions gst ON recharge.recharge_id=gst.recharge_id LEFT JOIN apps_response_log res ON recharge.recharge_id=res.txn_no $sWhere ORDER BY recharge.request_date DESC";
    $result23 = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, user.company_name, tr.*,gst.*,res.* FROM {$statement23}");
    $result25=$db->queryUniqueObject("select * from apps_reverse_response where reverse_response_content like '%".$aStr23."%'");
    // print_r($result24);die;
    $array23['recharge_status'] = getRechargeStatusList();
						if($result23) { 
						  // print_r($result23);die;
						    ?>
						    <div id="myModal" class="modal fade" role="dialog" style="margin-left: -17%;">
                              <div class="modal-dialog">
                            
                                <!-- Modal content-->
                                <div class="modal-content" style="width:154%">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    
                                  </div>
                                  <div class="modal-body">
						      <div class="box" style="width:100%;">
                            	<div class="box-header bg-status">
                            		<h3 class="box-title">Recharge Info : <?php echo $result23->recharge_id;?></h3>
                            	</div>
                            	<div class="box-body no-padding">
                            		<div class="fancy-body-inner">
                            		    <table class="table table-bordered">
                                        	<tr>
                                        		<th align="center" colspan="8"><b>Recharge Details</b></th>
                                        	</tr>	
                                        	<tr>
                                        	    <th>User Details</th>
                                        		<th>Date</th>
                                        		<th>Operator</td>
                                        		<th>Mobile/Acc</th>
                                        		<th>Amount</th>
                                        		<th>Operator Ref No.</th>
                                        		<th>API</th>
                                        		<th>API Status</th>
                            				    <th>Status</th>
                                        	</tr>
                                        	<tr>
                                        	    <td><?php echo $result23->company_name;?> (<?php echo $result23->uid;?>)</td>
                                        		<td class="f11cBlue"><?php echo $result23->request_date;?></td>
                                        		<td><?php echo $result23->operator_name;?></td>
                                        		<td><?php echo $result23->account_no;?></td>
                                        		<td><?php echo $result23->amount;?></td>
                                        		<td><?php echo $result23->operator_ref_no;?></td>
                                        		<td><?php echo $result23->api_id;?></td>
                                        		<td><?php echo $result23->api_status;?></td>
                                        		<td><?php echo getRechargeStatusLabel($array23['recharge_status'],$result23->status);?></td>
                                        	</tr>	
                                        </table>
                                         <?php } ?>
                                         <table class="table table-bordered">		
                                            <tr>
                                                	<th align="center" colspan="7"><b>Transaction Details</b></th>
                                                </tr>
                                                <tr>
                                                	<th>Record Id</th>
                                                	<th>Txn. Date</th>
                                                	<th>Txn. No. Ref</th>
                                                	<th>Txn. Type</th>
                                                	<th>Debit</th>
                                                	<th>Credit</th>
                                                	<th>Balance</th>
                                                </tr>
                                                <?php
                                                $query24 = $db->query("SELECT * FROM transactions WHERE transaction_ref_no = '".$aStr23."' ORDER BY transaction_id DESC");
                                                while($result24 = $db->fetchNextObject($query24)) {
                                                ?>
                                                <tr>
                                                	<td><?php echo $result24->transaction_id;?></td>
                                                	<td><?php echo $result24->transaction_date;?></td>
                                                	<td><?php echo $result24->transaction_ref_no;?></td>												
                                                	<td class="f11c"><?php echo $result24->transaction_term;?></td>
                                                	<?php if($result24->type == 'dr') { ?>
                                                	<td align="right"><?php echo round($result24->amount,2);?></td>
                                                	<td align="right"></td>
                                                	<?php } else { ?>							
                                                	<td align="right"></td>
                                                	<td align="right"><?php echo round($result24->amount,2);?></td>
                                                	<?php } ?>
                                                	<td align="right"><?php echo round($result24->closing_balance,2);?></td>
                                                </tr>
                                                <?php } ?>
                                                </table>
                                                <table class="table table-bordered">		
                                                    <tr>
                                                        	<th align="center" colspan="7"><b>GST Details</b></th>
                                                        </tr>
                                                        <tr>
                                                            <th>Comm/Sur</th>
                                                        	<th>Gst Amnt Deduct</th>
                                                        	<th>Tds Value</th>
                                                            <th>Tds Rate</th>
                                                        	<th>Tds Amount</th>
                                                        	
                                                        </tr>
                                                        <tr>
                                                        	<td><?php echo $result23->rch_comm_value;?></td>
                                                        	<td><?php echo $result23->gst_amount_deduct;?></td>
                                                        	<td><?php echo $result23->tds_value;?></td>		
                                                        	<td><?php echo $result23->tds_rate;?></td>
                                                            <td><?php echo $result23->tds_amount;?></td>
                                                        	
                                                        </tr>
                                                        
                                                        </table>
                                                                                   
                                               			<table class="table table-condensed">
                                               			    <tr>
                                                            	<th align="center" colspan="7"><b>Response Details</b></th>
                                                            </tr>
                                            				<tr>
                                            					<td><b>API Response</b></td>
                                            					<td style="word-wrap: break-word;word-break: break-all;"><?php echo $result23->http_response_content;?></td>
                                            				</tr>
                                            				<tr>
                                            					<td><b>API CallBack</b></td>
                                            					<td style="word-wrap: break-word;word-break: break-all;"><?php echo $result25->reverse_response_content."(".$result25->response_time.")";?></td>
                                            				</tr>
                            			
                            			</table>
                            		</div>
                            	</div>
                            </div>
                        </div>
                   </div>
              </div>
            </div>
                            <?php } } ?>
                            



