<?php   

ob_start();
session_start();
include(dirname(dirname(__FILE__)).'/header.php');
include(dirname(dirname(__FILE__)).'/config.php');
include(dirname(dirname(__FILE__)).'/objects/class_connection.php');
include(dirname(dirname(__FILE__)).'/objects/class_setting.php');
include(dirname(dirname(__FILE__)).'/objects/class_booking.php');
include(dirname(dirname(__FILE__)).'/objects/class_payment_hook.php');
include(dirname(dirname(__FILE__)).'/objects/class_services.php');

$con = new laundry_db();
$conn = $con->connect();

$setting = new laundry_setting();
$setting->conn = $conn;

$booking=new laundry_booking();
$booking->conn=$conn;

$service=new laundry_services();
$service->conn=$conn;

$payment_hook = new laundry_paymentHook();
$payment_hook->conn = $conn;
$payment_hook->payment_extenstions_exist();
$purchase_check = $payment_hook->payment_purchase_status();

$stripe_trans_id = '';
$twocheckout_trans_id = '';
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='complete_booking'){
		if (isset($_POST['st_token']) && filter_var($_POST['st_token']!='' && $_POST['net_amount'], FILTER_SANITIZE_STRING)!=0) {			
			require_once('../assets/stripe/stripe.php');
			$partialdeposite_status = $setting->get_option('ld_partial_deposit_status');
			if($partialdeposite_status=='Y'){
				$stripe_amt = number_format(filter_var($_POST['partial_amount'], FILTER_SANITIZE_STRING),2,".",',');
			}else{
				$stripe_amt = number_format(filter_var($_POST['net_amount'], FILTER_SANITIZE_STRING),2,".",',');
			}
			if(filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL)!=''){ 
				$emails=filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL); 
			  }else{ 
				$emails=filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
			  }
			\Stripe\Stripe::setApiKey($setting->get_option("ld_stripe_secretkey"));
			  $error = '';
			  $success = '';
			   
			 try { 				
				$objcharge = new \Stripe\Charge;
		
					$striperesponse = $objcharge::Create(array(
											"amount" => round(((double)$stripe_amt)*100),
											"currency" => $setting->get_option('ld_currency'),
											"source" => filter_var($_POST['st_token'], FILTER_SANITIZE_STRING),
											"description"=>filter_var($_POST['firstname'], FILTER_SANITIZE_STRING).' , '.$emails
											));
					$stripe_trans_id = $striperesponse->id;
												
			  }
			  catch (Exception $e) {
				$error = $e->getMessage();				
				echo filter_var($error, FILTER_SANITIZE_STRING);die;
			  }					 
					
	}else if (isset($_POST['twoctoken']) && filter_var($_POST['twoctoken']!='' && $_POST['net_amount'], FILTER_SANITIZE_STRING)!=0) {			
			require_once('../assets/twocheckout/Twocheckout.php');
			$twocc_private_key = $setting->get_option("ld_2checkout_privatekey");
			$twocc_sellerId = $setting->get_option("ld_2checkout_sellerid");
			$twocc_sandbox_mode = $setting->get_option("ld_2checkout_sandbox_mode");
			if($twocc_sandbox_mode == 'Y'){
				$twocc_sandbox = true;
			}else{
				$twocc_sandbox = false;
			}
			Twocheckout::privateKey($twocc_private_key); 
			Twocheckout::sellerId($twocc_sellerId); 
			Twocheckout::sandbox($twocc_sandbox);
			Twocheckout::verifySSL(false);
			if(filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL)!=''){
				$emails=filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL);
			}else{
				$emails=filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
			}
			$last_order_id=$booking->last_booking_id();
			if($last_order_id=='0' || $last_order_id==null){
				$orderid = 1000;
			}else{
				$orderid = $last_order_id+1;
			}
			$partialdeposite_status = $setting->get_option('ld_partial_deposit_status');
			if($partialdeposite_status=='Y'){
				$twocheckout_amt = number_format(filter_var($_POST['partial_amount'], FILTER_SANITIZE_STRING),2,".",',');
			}else{
				$twocheckout_amt = number_format(filter_var($_POST['net_amount'], FILTER_SANITIZE_STRING),2,".",',');
			}
			try {
				$charge = Twocheckout_Charge::auth(array(
					"merchantOrderId" => $orderid,
					"token"      => $_REQUEST['twoctoken'],
					"currency"   => $setting->get_option('ld_currency'),
					"total"      => $twocheckout_amt,
					"billingAddr" => array(
						"name" => filter_var($_POST['firstname'].' '.$_POST['lastname'], FILTER_SANITIZE_STRING),
						"addrLine1" => filter_var($_POST['address'], FILTER_SANITIZE_STRING),
						"city" => filter_var($_POST['city'], FILTER_SANITIZE_STRING),
						"state" => filter_var($_POST['state'], FILTER_SANITIZE_STRING),
						"zipCode" => filter_var($_POST['zipcode'], FILTER_SANITIZE_STRING),
						"country" => $setting->get_option('ld_company_country'),
						"email" => $emails,
						"phoneNumber" => filter_var($_POST['phone'], FILTER_SANITIZE_STRING)
					)
				));
				
				if ($charge['response']['responseCode'] == 'APPROVED') {
					$twocheckout_trans_id = $charge['response']['transactionId'];
				}
			} catch (Twocheckout_Error $e) {
				$error = $e->getMessage();
				echo filter_var($error, FILTER_SANITIZE_STRING);die;
			}	 
					
	}
	
	
	$total_discount =  @number_format($_SESSION['freq_dis_amount'],2,".",',') + @number_format(filter_var($_POST['discount'], FILTER_SANITIZE_STRING),2,".",',');

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
	$coupon_code = addslashes(filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING));
	$user_city = addslashes(filter_var($_POST['user_city'], FILTER_SANITIZE_STRING));
	$user_state = addslashes(filter_var($_POST['user_state'], FILTER_SANITIZE_STRING));
	$notes = addslashes(filter_var($_POST['notes'], FILTER_SANITIZE_STRING));

	$array_value = array('existing_username' => filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL), 'existing_password' => $_POST['existing_password'], 'password' => $_POST['password'], 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'phone' => $phone, 'user_address' => $user_address, 'user_zipcode' => $user_zipcode, 'user_city' => $user_city, 'user_state' => $user_state, 'address' => $address, 'zipcode' => $zipcode, 'city' => $city, 'state' => $state, 'notes' => $notes, 'contact_status' => $_POST['contact_status'], 'payment_method' => $_POST['payment_method'], 'amount' => $_POST['amount'], 'discount' => number_format($total_discount, 2, ".", ','), 'taxes' => $tax, 'partial_amount' => $_POST['partial_amount'], 'net_amount' => $_POST['net_amount'], 'booking_pickup_date_time_start' => $_POST['booking_pickup_date_time_start'], 'booking_pickup_date_time_end' => $_POST['booking_pickup_date_time_end'], 'booking_delivery_date_time_start' => $_POST['booking_delivery_date_time_start'], 'booking_delivery_date_time_end' => $_POST['booking_delivery_date_time_end'], 'coupon_code' => $_POST['coupon_code'], 'action' => "complete_booking", 'coupon_discount' => $_POST['discount'], 'cc_card_num' => $_POST['cc_card_num'],'cc_exp_month' => $_POST['cc_exp_month'],'cc_exp_year' => $_POST['cc_exp_year'],'cc_card_code' => $_POST['cc_card_code'],'guest_user_status' => $_POST['guest_user_status'],'is_login_user' => $_POST['is_login_user'],'service_name' => $service_name,'coupon_code'=> $coupon_code,'self_service'=> $_POST["self_service_status"],'show_delivery_date'=> $_POST["show_delivery_date"],'staff_id'=> "");

	$_SESSION['ld_details']=$array_value;
	
	/* payumoney payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'payumoney'){
		header('location:'.FRONT_URL.'payumoney_payment_process.php');
		exit(0);
	}	
	
	/*paypal payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'paypal'){
		header('location:'.FRONT_URL.'pp_payment_process.php');
		exit(0);
	}
	/*Stripe payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'stripe-payment'){
		$_SESSION['ld_details']['stripe_trans_id'] = 	$stripe_trans_id;
		header('location:'.FRONT_URL.'booking_complete.php');
		exit(0);
	}
	/*2checkout payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == '2checkout-payment'){
		$_SESSION['ld_details']['twocheckout_trans_id'] = 	$twocheckout_trans_id;
		header('location:'.FRONT_URL.'booking_complete.php');
		exit(0);
	}	
	/*pay locally payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'pay at venue'){
		$transaction_id ='';
		header('location:'.FRONT_URL.'booking_complete.php');
		exit(0);
	}	
	/*bank transfer payment method*/
	if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'bank transfer'){
		$transaction_id ='';
		header('location:'.FRONT_URL.'booking_complete.php');
		exit(0);
	}
	/*card payment method*/
	else if(filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING) == 'card-payment'){
		$transaction_id ='';
		header('location:'.FRONT_URL.'authorizenet_payment_process.php');
		exit(0);
	}
	
	/* Payment Extension method */
	
	if(sizeof($purchase_check)>0){
		$payment_status = "off";
		$check_pay = 'N';
		foreach($purchase_check as $key=>$val){
			if($val == 'Y'){
				echo filter_var($payment_hook->payment_checkout_hook($key), FILTER_SANITIZE_STRING);
			}
		}
	}
} ?>