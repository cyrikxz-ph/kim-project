<?php  
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='complete_booking'){
	ob_start();
	session_start();
	include(dirname(dirname(__FILE__)).'/header.php');
	include(dirname(dirname(__FILE__)).'/config.php');
	include(dirname(dirname(__FILE__)).'/objects/class_connection.php');
	include(dirname(dirname(__FILE__)).'/objects/class_setting.php');
	include(dirname(dirname(__FILE__)).'/objects/class_booking.php');
	include(dirname(dirname(__FILE__)).'/objects/class_services.php');
	include(dirname(dirname(__FILE__)).'/objects/class_front_first_step.php');
	include(dirname(dirname(__FILE__)).'/objects/class_users.php');
	include(dirname(dirname(__FILE__)).'/objects/class_order_client_info.php');
	include(dirname(dirname(__FILE__)).'/objects/class_coupon.php');
	include(dirname(dirname(__FILE__)).'/objects/class_payments.php');
	include(dirname(dirname(__FILE__)).'/objects/class.phpmailer.php');
	include(dirname(dirname(__FILE__)).'/objects/class_general.php');
	include(dirname(dirname(__FILE__)).'/objects/class_email_template.php');
	include(dirname(dirname(__FILE__)).'/objects/class_adminprofile.php');
	include(dirname(dirname(__FILE__)).'/objects/plivo.php');
	include(dirname(dirname(__FILE__)).'/assets/twilio/Services/Twilio.php');
	include(dirname(dirname(__FILE__))."/objects/class_dashboard.php");
	include(dirname(dirname(__FILE__))."/objects/class_nexmo.php");
	
	$con = new laundry_db();
	$conn = $con->connect();
	
	$setting = new laundry_setting();
	$setting->conn = $conn;

	$booking=new laundry_booking();
	$booking->conn=$conn;
	
	$objdashboard = new laundry_dashboard();
	$objdashboard->conn = $conn;

	$objadminprofile = new laundry_adminprofile();
	$objadminprofile->conn = $conn;

	$nexmo_admin = new laundry_ld_nexmo();
	$nexmo_client = new laundry_ld_nexmo();

	$first_step=new laundry_first_step();
	$first_step->conn=$conn;

	$email_template = new laundry_email_template();
	$email_template->conn=$conn;

	$general=new laundry_general();
	$general->conn=$conn;

	$user=new laundry_users();
	$order_client_info=new laundry_order_client_info();
	$settings=new laundry_setting();
	$coupon=new laundry_coupon();
	$payment = new laundry_payments();
	$service = new laundry_services();

	$user->conn=$conn;
	$order_client_info->conn=$conn;
	$settings->conn=$conn;
	$coupon->conn=$conn;
	$payment->conn=$conn;
	$service->conn=$conn;

	$appointment_auto_confirm=$settings->get_option('ld_appointment_auto_confirm_status');
	$last_order_id=$booking->last_booking_id();

	$symbol_position=$settings->get_option('ld_currency_symbol_position');
	$decimal=$settings->get_option('ld_price_format_decimal_places');

	$company_email=$settings->get_option('ld_email_sender_address');
	$company_name=$settings->get_option('ld_email_sender_name');

    $phone = "";
    if (substr(filter_var($_POST['phone'], FILTER_SANITIZE_STRING), 0, 1) === '+')
    {
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    }
    else
    {
        $country_codes = explode(',',$setting->get_option("ld_company_country_code"));
        $phone = $country_codes[0].filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    }
	if($setting->get_option("ld_tax_vat_status") == 'N'){
		$tax = 0;
	}else{
		$tax = filter_var($_POST['taxes'], FILTER_SANITIZE_STRING);
	}
	
	$service->id = $_SESSION['service_id'];
  $service_name = $service->get_service_name_for_mail();
	
	$email = addslashes(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $firstname = addslashes(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
    $lastname = addslashes(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
    $address = addslashes(filter_var($_POST['address'], FILTER_SANITIZE_STRING));
    $zipcode = addslashes(filter_var($_POST['zipcode'], FILTER_SANITIZE_STRING));
    $city = addslashes(filter_var($_POST['city'], FILTER_SANITIZE_STRING));
    $state = addslashes(filter_var($_POST['state'], FILTER_SANITIZE_STRING));
    $user_address = addslashes(filter_var($_POST['user_address'], FILTER_SANITIZE_STRING));
    $user_zipcode = addslashes(filter_var($_POST['user_zipcode'], FILTER_SANITIZE_STRING));
    $user_city = addslashes(filter_var($_POST['user_city'], FILTER_SANITIZE_STRING));
    $user_state = addslashes(filter_var($_POST['user_state'], FILTER_SANITIZE_STRING));
    $notes = addslashes(filter_var($_POST['notes'], FILTER_SANITIZE_STRING));
		$staff_id = filter_var($_POST['staff_id'], FILTER_SANITIZE_STRING);
	
	$array_value = array('existing_username' => filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL), 'existing_password' => $_POST['existing_password'], 'password' => $_POST['password'], 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'phone' => $phone, 'user_address' => $user_address, 'user_zipcode' => $user_zipcode, 'user_city' => $user_city, 'user_state' => $user_state, 'address' => $address, 'zipcode' => $zipcode, 'city' => $city, 'state' => $state, 'notes' => $notes,'staff_id' => $staff_id, 'contact_status' => $_POST['contact_status'], 'payment_method' => $_POST['payment_method'], 'amount' => $_POST['amount'], 'taxes' => $tax, 'partial_amount' => '', 'net_amount' => $_POST['net_amount'], 'booking_pickup_date_time_start' => $_POST['booking_pickup_date_time_start'], 'booking_pickup_date_time_end' => $_POST['booking_pickup_date_time_end'], 'booking_delivery_date_time_start' => $_POST['booking_delivery_date_time_start'], 'booking_delivery_date_time_end' => $_POST['booking_delivery_date_time_end'], 'coupon_code' => $_POST['coupon_code'], 'action' => "complete_booking", 'coupon_discount' => $_POST['discount'], 'cc_card_num' => '','cc_exp_month' => '','cc_exp_year' => '','cc_card_code' => '','guest_user_status' => $_POST['guest_user_status'],'service_name' => $service_name,'self_service'=> $_POST["self_service_status"],'show_delivery_date'=> $_POST["show_delivery_date"]);

	$_SESSION['ld_details']=$array_value;
	
	$transaction_id ='';
	include('manual_booking_complete.php');
}
?>