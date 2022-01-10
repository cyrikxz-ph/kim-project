<?php  
include(dirname(__FILE__) . '/header.php');
include(dirname(dirname(__FILE__)) . "/objects/class_dayweek_avail.php");
include(dirname(dirname(__FILE__)) . "/objects/class_staff_commision.php");
include(dirname(__FILE__).'/user_session_check.php');
include(dirname(dirname(__FILE__))."/objects/class_adminprofile.php");
include(dirname(dirname(__FILE__))."/objects/class_offbreaks.php");
include(dirname(dirname(__FILE__))."/objects/class_offtimes.php");
$obj_offtime = new laundry_offtimes();
$obj_offtime->conn = $conn;
$setting = new laundry_setting();
$setting->conn = $conn;
$getdateformat=$setting->get_option('ld_date_picker_date_format');
$con = new laundry_db();
$conn = $con->connect();
$objdayweek_avail = new laundry_dayweek_avail();
$objdayweek_avail->conn = $conn;
$objoffbreaks = new laundry_offbreaks();
$objoffbreaks->conn = $conn;
$time_int = $objdayweek_avail->getinterval();
$time_interval = $time_int[2];
$time_format = $setting->get_option('ld_time_format');
$objadmin = new laundry_adminprofile();
$objadmin->conn=$conn;
$staff_commision = new laundry_staff_commision();
$staff_commision->conn=$conn;

$getdateformat=$setting->get_option('ld_date_picker_date_format');
$time_format = $setting->get_option('ld_time_format');
$timess = "";
if($time_format == "24"){
	$timess = "H:i";
}
else {
	$timess = "h:i A";
}
$symbol_position=$setting->get_option('ld_currency_symbol_position');
$decimal=$setting->get_option('ld_price_format_decimal_places');
$getcurrency_symbol_position=$setting->get_option('ld_currency_symbol_position');
?>
<div id="lda-staff-panel" class="panel tab-content np">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h1 class="panel-title"><?php echo filter_var($label_language_values['staff_details_add_new_and_manage_staff_payments'], FILTER_SANITIZE_STRING); ?></h1>
		</div>
		<div class="panel-body">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#add-new-staff"><?php echo filter_var($label_language_values['add_staff'], FILTER_SANITIZE_STRING); ?></a></li>
				<li><a data-toggle="tab" href="#staff-booking-payments"><?php echo filter_var($label_language_values['staff_bookings_and_payments'], FILTER_SANITIZE_STRING); ?></a></li>
			</ul>
			<div class="tab-content">
				<div id="add-new-staff" class="tab-pane fade in active">
					<div id="accordion" class="panel-group">
						<div class="ld-staff-list col-md-3 col-sm-4 col-xs-12 col-lg-3">
							<div class="ld-staff-container">
								<h3><?php echo filter_var($label_language_values['staff_members'], FILTER_SANITIZE_STRING);	?> <span>(<?php echo filter_var($objadmin->countall_staff(), FILTER_SANITIZE_STRING);	?>)</span>
									<a href="#ld-add-new-staff"  title="Add New Staff Member" role="button" class="btn btn-info pull-right" data-toggle="modal"><i class="fa fa-user-plus"></i> <?php  echo filter_var($label_language_values['add_new'], FILTER_SANITIZE_STRING);	?></a>
										 
										
								</h3>
								
								<ul class="nav nav-tab nav-stacked ld-left-staff" id="sortable">
									<?php  
										$all_staff = $objadmin->readall_staff();
										$i = 1;
										while($arr_staff = mysqli_fetch_array($all_staff)){
											if($i == 1 ){
												$trigger =  $i;
											}
											else {
												$trigger = "0";
											}
											
											$objadmin->id = $arr_staff['id'];
											$staff_read = $objadmin->readone();
											if($staff_read[16]==''){
												$imagepath=SITE_URL."assets/images/user.png";
											}else{
												$imagepath=SITE_URL."assets/images/services/".$staff_read[16];
											}
											?>
											<li class="staff-list br-2 staff_click staff_<?php  echo filter_var($trigger, FILTER_SANITIZE_STRING);	?> staff_c_<?php  echo filter_var($arr_staff['id'], FILTER_SANITIZE_STRING);	?>" id="" data-id = "<?php echo filter_var($arr_staff['id'], FILTER_SANITIZE_STRING);	?>">
												<a href="" data-toggle="pill">
												<span class="ld-staff-image"><img class="ld-staf-img-small small-staff-image<?php  echo filter_var($arr_staff['id'], FILTER_SANITIZE_STRING);	?>" src="<?php echo filter_var($imagepath, FILTER_SANITIZE_STRING);	?>" /></span>
												<span class="ld-staff-name"><?php echo filter_var($arr_staff['fullname'], FILTER_SANITIZE_STRING); ?></span>
												</a>
											</li>
											<?php 
											$i++;
										}
									?>
									
								</ul>
							</div>	
						</div>
						<div class="panel-body get_staff_details">
						</div>
					</div>	
				</div>	
				<div id="staff-booking-payments" class="tab-pane fade">
					<div class="panel-body pall-15">
						<h3><?php echo filter_var($label_language_values['staff_booking_details_and_payment'], FILTER_SANITIZE_STRING); ?></h3>
						<div id="accordion" class="panel-group">
							<div class="ld-calendar-top-bar">
								<div class="col-md-4 col-sm-6 col-xs-12 col-lg-4 mb-10">
									<label><?php echo filter_var($label_language_values['select_option_to_show_bookings'], FILTER_SANITIZE_STRING); ?></label>
									<div id="reportrange" class="form-control" >
										<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
										<span></span> <i class="fa fa-caret-down"></i>
									</div>
								</div>
									
								<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
									<label><?php echo filter_var($label_language_values['select_service'], FILTER_SANITIZE_STRING); ?></label><br />
								
									<select name="" class="selectpicker get_serid_for_staff_pymnt" data-size="10" style="display: none;" data-live-search="true">
										<option value="all" selected><?php echo filter_var($label_language_values['all_services'], FILTER_SANITIZE_STRING); ?></option>
										<?php 
										$readall_staff_pymt_ser = $staff_commision->readall_staff_pymt_ser();
										while($allser = mysqli_fetch_array($readall_staff_pymt_ser)){
											?>
											<option value="<?php echo filter_var($allser['id'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($allser['title'], FILTER_SANITIZE_STRING); ?></option>
											<?php 
										}
										?>
									</select>
								</div>
								<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
									<button type="button" class="form-group btn btn-info ld-btn-width ld-submit-btn mt-20 get_staff_bookingandpayment_by_dateser" name=""><?php echo filter_var($label_language_values['submit'], FILTER_SANITIZE_STRING); ?></button>
								</div>
							</div>
							<div class="table-responsive ser_staffpayment_append">
								<table id="staff-payments-details" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th><?php echo filter_var($label_language_values['service'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['app_date'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['customer'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['status'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['staff_name'], FILTER_SANITIZE_STRING); ?></th>
											<th><?php echo filter_var($label_language_values['net_total'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['commission_total'], FILTER_SANITIZE_STRING);	?></th>
											<th><?php echo filter_var($label_language_values['action'], FILTER_SANITIZE_STRING);	?></th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$all_bookings = $staff_commision->readall_booking();
										while($all = mysqli_fetch_array($all_bookings)){
											$service_name = $staff_commision->get_service_name($all['service_id']);
											$client_name = $staff_commision->get_client_name($all['client_id']);
											$staff_name = $staff_commision->get_staff_name($all['staff_ids']);
											$net_total = $staff_commision->get_net_total($all['order_id']);
											
											$data_staff_pymnt_id = explode(',',$all['staff_ids']);
											$get_booking_nettotal = 0;
											for($i=0;$i<sizeof($data_staff_pymnt_id);$i++){
												$staff_id = $data_staff_pymnt_id[$i];
												$get_booking_nettotal += (float)$staff_commision->get_booking_nettotal($staff_id, $all['order_id']);
											}
											
											if($all['booking_status'] == 'A'){
												$status = 'Active';
											}else if($all['booking_status'] == 'C'){
												$status = 'Confirm';
											}else if($all['booking_status'] == 'R'){
												$status = 'Rejected';
											}else if($all['booking_status'] == 'CC'){
												$status = 'Cancelled By Client';
											}else if($all['booking_status'] == 'CS'){
												$status = 'Cancelled By Staff';
											}else if($all['booking_status'] == 'CO'){
												$status = 'Completed';
											}else if($all['booking_status'] == 'MN'){
												$status = 'Mark As No Show';
											}else if($all['booking_status'] == 'RS'){
												$status = 'Rescheduled';
											}
										?>
											<tr>
												<td><?php echo filter_var($all['order_id'], FILTER_SANITIZE_STRING); ?></td>
												<td><?php echo filter_var($service_name, FILTER_SANITIZE_STRING); ?></td>
												<td><?php echo str_replace($english_date_array,$selected_lang_label,date($getdateformat,strtotime($all['booking_pickup_date_time_start']))); ?></td>
												<td><?php echo filter_var($client_name, FILTER_SANITIZE_STRING); ?></td>
												<td><?php echo filter_var($status, FILTER_SANITIZE_STRING); ?></td>
												<td><?php echo rtrim($staff_name,", "); ?></td>
												<td><?php echo filter_var($general->ld_price_format($net_total,$symbol_position,$decimal), FILTER_SANITIZE_STRING); ?></td>
												<td><?php echo filter_var($general->ld_price_format($get_booking_nettotal,$symbol_position,$decimal), FILTER_SANITIZE_STRING); ?></td>
												<td><a href="#add-staff-payment" role="button" class="btn btn-success show_staff_payment_details" data-toggle="modal" data-order_id="<?php echo filter_var($all['order_id'], FILTER_SANITIZE_STRING); ?>" data-staff_ids="<?php echo filter_var($all['staff_ids'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($label_language_values['staff_payment'], FILTER_SANITIZE_STRING);	?></a></td>
											</tr>
										<?php 
										}
										?>
									</tbody>
								</table>	
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<div id="ld-add-new-staff" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo filter_var($label_language_values['add_new_staff_member'], FILTER_SANITIZE_STRING);	?></h4>
			</div>
			<div class="modal-body">
				<form id="staff_insert" > 
				<table class="form-horizontal" cellspacing="0">
					<tbody>
					<tr class="form-field form-required">
						<td><label for="ab-newstaff-fullname"><?php echo filter_var($label_language_values['name'], FILTER_SANITIZE_STRING);	?></label></td>
						<td><input type="text" class="form-control staff_name" id="staff_name" name="staff_name" required="required" /></td>
					</tr>
					<tr class="form-field form-required">
						<td><label for="ab-newstaff-fullname"><?php echo filter_var($label_language_values['email'], FILTER_SANITIZE_STRING);	?></label></td>
						<td><input type="email" class="form-control staff_email" id="staff_email" name="staff_email" required="required" /></td>
					</tr>
					<tr class="form-field form-required">
						<td><label for="staff_pass"><?php echo filter_var("Password", FILTER_SANITIZE_STRING);	?></label></td>
						<td><input type="password" class="form-control staff_pass" id="staff_pass" name="staff_pass" required="required" /></td>
					</tr>
					
					</tbody>
				</table>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING);	?></button>
				<button type="button" class="btn btn-success save_staff"><?php echo filter_var($label_language_values['create'], FILTER_SANITIZE_STRING);	?></button>
			</div>
		</div>
	</div>
</div>



<div id="add-staff-payment" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content ">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo filter_var($label_language_values['add_payment_to_staff_account'], FILTER_SANITIZE_STRING);	?></h4>
			</div>
			<div class="modal-body">
				<table id="staff-payments-adding" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo filter_var($label_language_values['staff_name'], FILTER_SANITIZE_STRING);	?></th>
							<th><?php echo filter_var($label_language_values['amount_payable'], FILTER_SANITIZE_STRING);	?></th>
							<th><?php echo filter_var($label_language_values['advance_paid'], FILTER_SANITIZE_STRING);	?></th>
							<th><?php echo filter_var($label_language_values['net_total'], FILTER_SANITIZE_STRING); ?></th>
							
						</tr>
					</thead>
					<tbody class="custm_staff_payment_details">
					</tbody>
				</table>
				<span class="error hide comission_error_display"><?php   echo filter_var($label_language_values['commission_amount_should_not_be_greater_then_order_amount'], FILTER_SANITIZE_STRING); ?></span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_spc_popup" data-dismiss="modal"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING);	?></button>
				<button type="button" class="btn btn-primary save_sp_staff_commision"><?php echo filter_var($label_language_values['save_changes'], FILTER_SANITIZE_STRING);	?></button>
			</div>
		</div>
	</div>
</div>
<?php 
include(dirname(__FILE__) . '/footer.php');
?>
<script type="text/javascript">
    var ajax_url = '<?php echo filter_var(AJAX_URL, FILTER_VALIDATE_URL);	?>';
	var servObj={'site_url':'<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'assets/images/business/';	?>'};
    var imgObj={'img_url':'<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'assets/images/';	?>'};
</script>