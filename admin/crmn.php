<?php   

include(dirname(__FILE__).'/header.php');
include(dirname(__FILE__).'/user_session_check.php');
include(dirname(dirname(__FILE__)).'/objects/class_users.php');
include(dirname(dirname(__FILE__)).'/objects/class_order_client_info.php');
$database=new laundry_db();
$conn=$database->connect();
$database->conn=$conn;
$user=new laundry_users();
$order_client_info=new laundry_order_client_info();
$user->conn=$conn;
$order_client_info->conn=$conn;
?>
<div id="allidsnm">
<?php  
$reg_user_data = $user->readall();
while($r_data = mysqli_fetch_array($reg_user_data)){
	?>
	<input type="hidden" id="proid<?php   echo filter_var($r_data['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($r_data['id'], FILTER_SANITIZE_STRING); ?>" />
	<input type="hidden" id="prodt<?php   echo filter_var($r_data['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($r_data['first_name']." ".$r_data['last_name'], FILTER_SANITIZE_STRING); ?>" />
	<?php  
}
?>
<script type="text/javascript">

jQuery(document).ready(function(){
	jQuery('.jqte-test').jqte();
	
	var jqteStatus = true;
	jQuery(".status").click(function()
	{
		jqteStatus = jqteStatus ? false : true;
		jQuery('.jqte-test').jqte({"status" : jqteStatus})
	});
});
</script>
</div>

    <div id="lda-customers-listing" class="panel tab-content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title"><?php echo filter_var($label_language_values['crm'], FILTER_SANITIZE_STRING);	?></h1>
            </div>
			<div class="panel-body">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#registered-customers-listing"><?php echo filter_var($label_language_values['registered_customers'], FILTER_SANITIZE_STRING);	?></a></li>
					<li><a data-toggle="tab" href="#guest-customers-listing"><?php echo filter_var($label_language_values['guest_customers'], FILTER_SANITIZE_STRING);	?></a></li>
				</ul>
				<div class="tab-content">
					<div id="registered-customers-listing" class="tab-pane fade in active">
						<h3 class="pull-left"><?php echo filter_var($label_language_values['registered_customers'], FILTER_SANITIZE_STRING);	?></h3>
						<button  data-toggle="modal" data-target="#add_new_customer" class="btn btn-primary pull-right"><i class="fa fa-plus" aria-hidden="true"></i><?php echo filter_var($label_language_values['new_user'], FILTER_SANITIZE_STRING); ?></button>
						<div id="accordion" class="panel-group">
							<table id="post_list" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
								<tr>
									<th><?php echo filter_var($label_language_values['client_name'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['email'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['phone'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['zip_code'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['city'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['state'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['date_and_time'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['bookings'], FILTER_SANITIZE_STRING);	?></th>
								</tr>
								</thead>
							</table>
							
							<div class="form-field form-required col-xs-12 mb-30">
							<div class="col-xs-12 ">
							<input type="hidden" id="idsdata" class="form-control">
<div class="multiselect" id="app" style="max-height: 30% !important;">
  <div class="selected-hold" :class="{ focus: hasFocus }" @click.self="$refs.search.focus()" @keydown.left="traverseSelected('left')" @keydown.right="traverseSelected('right')" @keyup.delete="traverseSelectedDelete">
	  <div class="selected-item" :class="{ active: activeHorizontal === index }" v-for="(itm, index) in selectedList" :key="index">
		<span class="item-label">{{ itm }}</span>
		<button type="button" class="close" @click="removeSelected(index)" tabindex="-1">
			<span>×</span>
		  </button>
	  </div>
	  <input type="text" class="item-search" ref="search" :style="{ width: searchTermWidth }" v-model.trim="searchTerm" @keydown.down="traverseList('next')" @keydown.up="traverseList('prev')" @keydown.delete="backspaceDelete" @keyup.enter="addActive" @keyup="lastTerm = searchTerm" @focus="showSuggestPanel = true; hasFocus = true; activeHorizontal = -1" @blur="showSuggestPanel = false; hasFocus = false">
	  <div ref="tester" class="text-tester">{{ searchTerm }}</div>
	  <div class="suggest-panel" v-show="showSuggestPanel" ref="panel">
		<div class="suggest-item" :class="{ active: activeVertical === index, disabled: selectedList.includes(row.value || row) }" v-for="(row, index) in filteredSuggest" :key="index" @mousedown.prevent="addSelected(row.value || row)" @mouseover="activeVertical = index" v-html="hightlightWord(row)"></div>
	  </div>
	</div>
  </div>
  <span id="idlist" class="error"></span>
								</div>
<button  data-toggle="modal" data-target="#send_email_sms" class="btn btn-primary pull-left col-xs-5 m-15-50"><?php echo filter_var($label_language_values['send_message'], FILTER_SANITIZE_STRING);	?></button>
<a href="emlsms.php" class="btn btn-primary pull-right col-xs-5 m-15-50"><?php echo filter_var($label_language_values['all_messages'], FILTER_SANITIZE_STRING);	?></a>
							</div>
						
							<div id="registered-details" class="modal fade booking-details-modal">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title"><?php echo filter_var($label_language_values['registered_customers_bookings'], FILTER_SANITIZE_STRING);	?></h4>
										</div>
										<div class="modal-body myregcust_modal">
											<div class="table-responsive">
												<table id="registered-client-booking-details_new" class="display table table-striped table-bordered" cellspacing="0" width="100%">
													<thead>
													<tr>
														<th style="width: 9px !important;">#</th>
														<th style="width: 67px !important;"><?php echo filter_var($label_language_values['laundry_service'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 44px !important;"><?php echo filter_var($label_language_values['booking_serve_date'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 39px !important;"><?php echo filter_var($label_language_values['booking_status'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 70px !important;"><?php echo filter_var($label_language_values['payment_method'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 257px !important;"><?php echo filter_var($label_language_values['more_details'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 170px !important;"><?php echo filter_var($label_language_values['staff_booking_status'], FILTER_SANITIZE_STRING);	?></th>
													</tr>
													</thead>
													<tbody id="details_booking_display">
												  
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="guest-customers-listing" class="tab-pane fade">
						<h3><?php echo filter_var($label_language_values['guest_customers'], FILTER_SANITIZE_STRING);	?></h3>
						<div id="accordion" class="panel-group">
							<table id="guest-client-table" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
								<tr>
									<th><?php echo filter_var($label_language_values['client_name'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['email'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['phone'], FILTER_SANITIZE_STRING);	?></th>
									<th><?php echo filter_var($label_language_values['bookings'], FILTER_SANITIZE_STRING);	?></th>
								</tr>
								</thead>
								<tbody>
								<?php  
								$guest_user_data =  $user->read_all_guestuser();
								while($g_data = mysqli_fetch_array($guest_user_data)){
									?>
									<tr id="myguest_<?php   echo filter_var($g_data['order_id'], FILTER_SANITIZE_STRING);	?>">
										<td><?php if($g_data['client_name'] != ''){echo filter_var($g_data['client_name'], FILTER_SANITIZE_STRING);}else{echo filter_var('N/A', FILTER_SANITIZE_STRING);} ?></td>
										<td><?php echo filter_var($g_data['client_email'], FILTER_SANITIZE_STRING); ?></td>
										<td><?php if(strlen($g_data['client_phone'])>6){echo filter_var($g_data['client_phone'], FILTER_SANITIZE_STRING);}else{echo filter_var('N/A', FILTER_SANITIZE_STRING);} ?></td>
										<td class="ld-bookings-td">
											<a class="btn btn-primary myguestcust_bookings" data-email="<?php echo filter_var($g_data['client_email'], FILTER_SANITIZE_STRING); ?>" href="#guest-details" data-toggle="modal" data-id="<?php echo filter_var($g_data['order_id'], FILTER_SANITIZE_STRING);	?>">
												<?php   echo filter_var($label_language_values['bookings'], FILTER_SANITIZE_STRING);	?>
												
											</a>
											<a data-id="<?php echo filter_var($g_data['order_id'], FILTER_SANITIZE_STRING);	?>" class="btn btn-danger col-sm-offset-1 mybtndelete_guest_customers_entry"><i class="fa fa-trash"></i> <?php   echo filter_var($label_language_values['delete'], FILTER_SANITIZE_STRING);	?></a>
										</td>
									</tr>
								<?php  
								}
								?>
								</tbody>
							</table>
							<div id="guest-details" class="modal fade booking-details-modal">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title"><?php echo filter_var($label_language_values['guest_customers_bookings'], FILTER_SANITIZE_STRING);	?></h4>
										</div>
										<div class="modal-body">
											<div class="table-responsive">
												<table id="guest-client-booking-details_new" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
													<thead>
													<tr>
														<th style="width: 9px !important;">#</th>
														<th style="width: 67px !important;"><?php echo filter_var($label_language_values['cleaning_service'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 44px !important;"><?php echo filter_var($label_language_values['booking_serve_date'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 39px !important;"><?php echo filter_var($label_language_values['booking_status'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 70px !important;"><?php echo filter_var($label_language_values['payment_method'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 257px !important;"><?php echo filter_var($label_language_values['more_details'], FILTER_SANITIZE_STRING);	?></th>
														<th style="width: 170px !important;"><?php echo filter_var($label_language_values['staff_booking_status'], FILTER_SANITIZE_STRING);	?></th>
													</tr>
													</thead>
													<tbody id="details_booking_display_guest">
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
			</div>
        </div>
    </div>
	
	<div class="modal fade" id="send_email_sms" role="dialog">
    <div class="modal-dialog modal-lg">
    
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo filter_var($label_language_values['message'], FILTER_SANITIZE_STRING);	?></h4>
        </div>
        <div class="modal-body of-h">
			
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#email_add"><?php echo filter_var($label_language_values['email'], FILTER_SANITIZE_STRING);	?></a></li>
				<li><a data-toggle="tab" href="#sms_add"><?php echo filter_var($label_language_values['sms'], FILTER_SANITIZE_STRING);	?></a></li>
			</ul>
			<div class="tab-content">
				<div id="email_add" class="tab-pane fade in active">
					<h3 class="crm_email_label"><?php echo filter_var($label_language_values['email'], FILTER_SANITIZE_STRING);	?></h3>
					<div id="" class="form-horizontal mt-30 col-xs-12">
					<form id="eml" enctype="multipart/form-data">

						<div class="form-field form-required col-xs-12 mb-15">
							<label class="col-xs-2 control-label text-right"><?php echo filter_var($label_language_values['subject'], FILTER_SANITIZE_STRING);	?></label>
							<div class="col-xs-10 "><input type="text" class="form-control" id="email_sub" required="required" /></div>
						</div>
						<div class="form-field form-required col-xs-12 mb-15">
							<label class="col-xs-2 control-label text-right"><?php echo filter_var($label_language_values['message'], FILTER_SANITIZE_STRING);	?></label>
						<div class="col-xs-10 "><textarea id="email_msg" name="textarea" class="col-xs-8 jqte-test"></textarea>
							</div>
						</div>
						<div class="form-field form-required col-xs-12 mb-15">
							<label class="col-xs-2 control-label text-right"><?php echo filter_var($label_language_values['add_attachment'], FILTER_SANITIZE_STRING);	?></label>
							<div class="col-xs-10 ">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="fake-file-button-browse" type="button" class="btn btn-default">
											<span class="glyphicon glyphicon-paperclip"></span>
										</button>
									</span>
									<input type="file" id="files-input-upload" style="display:none">
									<input type="text" id="fake-file-input-name" disabled="disabled" placeholder="File not selected" class="form-control">
								</div>
							</div>
						</div>
						
						<div class="form-field form-required col-xs-12 mb-10">
							<label class="col-xs-2"></label>
							<div class="col-xs-10 "><label class="col-xs-2 eml_errors error"></label><a class="btn btn-success" id="eml_sub_add"><i class=""></i><?php echo filter_var($label_language_values['send'], FILTER_SANITIZE_STRING);	?></a></div>
						</div>

					</form>
					</div>
				</div>
				<div id="sms_add" class="tab-pane fade">
					<h3 class="crm_email_label"><?php echo filter_var($label_language_values['sms'], FILTER_SANITIZE_STRING);	?></h3>
					<div id="" class="form-horizontal mt-30 col-xs-12">
					<form id="sms" enctype="multipart/form-data">

						<div class="form-field form-required col-xs-12 mb-15">
							<label class="col-xs-2 control-label text-right"><?php echo filter_var($label_language_values['message'], FILTER_SANITIZE_STRING);	?></label>
							<div class="col-xs-10 "><textarea id="sms_msg" class="form-control"></textarea></div>
						</div>
						
						<div class="form-field form-required col-xs-12 mb-10">
							<label class="col-xs-2"></label>
							<div class="col-xs-10 "><label class="col-xs-2 sms_errors error"></label><a class="btn btn-success" id="sms_sub_add"><i class=""></i><?php echo filter_var($label_language_values['send'], FILTER_SANITIZE_STRING);	?></a></div>
						</div>

					</form>
					</div>
				</div>
			</div>
			
        </div>
        <div class="modal-footer">
          <button type="button" class="cls btn btn-default" data-dismiss="modal"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING);	?></button>
        </div>
      </div>
      
    </div>
  </div>
	
	<div class="modal fade" id="delete_new_customer" role="dialog">
    <div class="modal-dialog modal-sm">
    
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
		
		<div class="modal-body of-h">
		<table class="form-horizontal" cellspacing="0">
			<tbody>
			<tr>
				<td>
				<h4 class="modal-title"><?php echo filter_var($label_language_values['delete_this_customer?'], FILTER_SANITIZE_STRING);	?></h4>
					
				</td>
			</tr>
			</tbody>
		</table>
		</div>
		<div class="modal-footer">
          <a value="Delete" class="btn btn-danger btn-sm mybtndelete_register_customers_entry"><?php echo filter_var($label_language_values['yes'], FILTER_SANITIZE_STRING);	?></a>
					<button id="ld-close-popover-customerss" data-dismiss="modal" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING);	?></button>
        </div>

      </div>
      
    </div>
  </div>
	
 <div class="modal fade" id="add_new_customer" role="dialog">
    <div class="modal-dialog modal-lg">
    
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo filter_var($label_language_values['add_new_customer'], FILTER_SANITIZE_STRING);	?></h4>
        </div>
        <div class="modal-body of-h">
		<form class="add_new_user_add" id="add_new_user_add">
			<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12">
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6">
					<label class="control-label"><?php echo filter_var($label_language_values['preferred_email'], FILTER_SANITIZE_STRING); ?></label>
					<input type="email" id="admin_cus_email" name="admin_cus_email" placeholder="<?php echo filter_var($label_language_values['your_valid_email_address'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6">
					<label class="control-label"><?php echo filter_var($label_language_values['preferred_password'], FILTER_SANITIZE_STRING); ?></label>
					<input type="password" id="admin_cus_pwd" name="admin_cus_pwd" placeholder="<?php echo filter_var($label_language_values['password'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12 mt-20">
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6">
					<label class="control-label"><?php echo filter_var($label_language_values['first_name'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_fstnm" name="admin_cus_fstnm" placeholder="<?php echo filter_var($label_language_values['first_name'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6">
					<label class="control-label"><?php echo filter_var($label_language_values['last_name'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_lstnm" name="admin_cus_lstnm" placeholder="<?php echo filter_var($label_language_values['last_name'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6 mt-15">
					<label class="control-label" for="manualy_new_user_phone"><?php echo filter_var($label_language_values['phone'], FILTER_SANITIZE_STRING); ?></label>
					
					<input type="tel" id="admin_cus_phno" name="admin_cus_phno" class="form-control" placeholder="070 123 4567">
				</div>
				<div class="col-md-6 col-sm-6 col-sm-6 col-xs-6 mt-15">
					<label class="control-label"><?php echo filter_var($label_language_values['street_address'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_str_addr" name="admin_cus_str_addr" placeholder="<?php echo filter_var($label_language_values['street_address_placeholder'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12 mt-15">
				<div class="col-md-4 col-sm-4 col-sm-4 col-xs-4">
					<label class="control-label"><?php echo filter_var($label_language_values['zip_code'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_zipcode" name="admin_cus_zipcode" placeholder="<?php echo filter_var($label_language_values['zip_code_placeholder'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
				<div class="col-md-4 col-sm-4 col-sm-4 col-xs-4">
					<label class="control-label"><?php echo filter_var($label_language_values['city'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_city" name="admin_cus_city" placeholder="<?php echo filter_var($label_language_values['city_placeholder'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
				<div class="col-md-4 col-sm-4 col-sm-4 col-xs-4">
					<label class="control-label"><?php echo filter_var($label_language_values['state'], FILTER_SANITIZE_STRING); ?></label>
					<input type="text" id="admin_cus_state" name="admin_cus_state" placeholder="<?php echo filter_var($label_language_values['state_placeholder'], FILTER_SANITIZE_STRING); ?>" class="form-control">
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12 mt-15">
				<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12">
					<label class="control-label"><?php echo filter_var($label_language_values['special_requests_notes'], FILTER_SANITIZE_STRING); ?></label>
					<textarea class="form-control" id="admin_cus_note" name="admin_cus_note"></textarea>
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12 mt-15">
				<div class="col-md-12 col-sm-12 col-sm-12 col-xs-12">
					<button type="button" class="btn btn-primary" id="new_cus_add_admin"><?php echo filter_var($label_language_values['save'], FILTER_SANITIZE_STRING); ?></button>
				</div>
			</div>
		</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING); ?></button>
        </div>
      </div>
      
    </div>
  </div>
  <script src="../assets/jquery_editor/jquery-te-1.4.0.min.js"></script>

<?php  
include(dirname(__FILE__).'/footer.php');
?>