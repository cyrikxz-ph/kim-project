<?php  

session_start();
include(dirname(dirname(dirname(__FILE__))).'/config.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_connection.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_setting.php');
include(dirname(dirname(dirname(__FILE__))) . "/objects/class_services.php");
include(dirname(dirname(dirname(__FILE__)))."/header.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_gc_hook.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_payment_hook.php");
include(dirname(dirname(dirname(__FILE__))).'/objects/class.phpmailer.php');
$database=new laundry_db();
$setting=new laundry_setting();
$conn=$database->connect();
$database->conn=$conn;
$setting->conn=$conn;
$objservice = new laundry_services();
$objservice->conn = $conn;
$gc_hook = new laundry_gcHook();
$gc_hook->conn = $conn;
$payment_hook = new laundry_paymentHook();
$payment_hook->conn = $conn;
$lang = $setting->get_option("ld_language");
$label_language_values = array();
$language_label_arr = $setting->get_all_labelsbyid($lang);

if($setting->get_option('ld_smtp_authetication') == 'true'){
	$mail_SMTPAuth = '1';
	if($setting->get_option('ld_smtp_hostname') == "smtp.gmail.com"){
		$mail_SMTPAuth = 'Yes';
	}
	
}else{
	$mail_SMTPAuth = '0';
	if($setting->get_option('ld_smtp_hostname') == "smtp.gmail.com"){
		$mail_SMTPAuth = 'No';
	}
}

$mail = new laundry_phpmailer();
$mail->Host = $setting->get_option('ld_smtp_hostname');
$mail->Username = $setting->get_option('ld_smtp_username');
$mail->Password = $setting->get_option('ld_smtp_password');
$mail->Port = $setting->get_option('ld_smtp_port');
$mail->SMTPSecure = $setting->get_option('ld_smtp_encryption');
$mail->SMTPAuth = $mail_SMTPAuth;
$mail->CharSet = "UTF-8";

$payment_hook->payment_extenstions_exist();
$purchase_check = $payment_hook->payment_purchase_status();

if ($language_label_arr[1] != "" || $language_label_arr[3] != "" || $language_label_arr[4] != "" || $language_label_arr[5] != "")
{
	$default_language_arr = $setting->get_all_labelsbyid("en");
	if($language_label_arr[1] != ''){
		$label_decode_front = base64_decode($language_label_arr[1]);
	}else{
		$label_decode_front = base64_decode($default_language_arr[1]);
	}
	if($language_label_arr[3] != ''){
		$label_decode_admin = base64_decode($language_label_arr[3]);
	}else{
		$label_decode_admin = base64_decode($default_language_arr[3]);
	}
	if($language_label_arr[4] != ''){
		$label_decode_error = base64_decode($language_label_arr[4]);
	}else{
		$label_decode_error = base64_decode($default_language_arr[4]);
	}
	if($language_label_arr[5] != ''){
		$label_decode_extra = base64_decode($language_label_arr[5]);
	}else{
		$label_decode_extra = base64_decode($default_language_arr[5]);
	}
	
	$label_decode_front_unserial = unserialize($label_decode_front);
	$label_decode_admin_unserial = unserialize($label_decode_admin);
	$label_decode_error_unserial = unserialize($label_decode_error);
	$label_decode_extra_unserial = unserialize($label_decode_extra);
    
	$label_language_arr = array_merge($label_decode_front_unserial,$label_decode_admin_unserial,$label_decode_error_unserial,$label_decode_extra_unserial);
	
	foreach($label_language_arr as $key => $value){
		$label_language_values[$key] = urldecode($value);
	}
}
else
{
    $default_language_arr = $setting->get_all_labelsbyid("en");
	
	$label_decode_front = base64_decode($default_language_arr[1]);
	$label_decode_admin = base64_decode($default_language_arr[3]);
	$label_decode_error = base64_decode($default_language_arr[4]);
	$label_decode_extra = base64_decode($default_language_arr[5]);
		
	
	$label_decode_front_unserial = unserialize($label_decode_front);
	$label_decode_admin_unserial = unserialize($label_decode_admin);
	$label_decode_error_unserial = unserialize($label_decode_error);
	$label_decode_extra_unserial = unserialize($label_decode_extra);
    
	$label_language_arr = array_merge($label_decode_front_unserial,$label_decode_admin_unserial,$label_decode_error_unserial,$label_decode_extra_unserial);
	
	foreach($label_language_arr as $key => $value){
		$label_language_values[$key] = urldecode($value);
	}
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='add_specail_offer'){
 $setting->option_name="ld_special_offer";
 $setting->option_value=filter_var($_POST['special_offer_check'], FILTER_SANITIZE_STRING);
 $result=$setting->update_special_offer(); 
 $setting->option_name="ld_special_offer_text";
 $setting->option_value=filter_var($_POST['special_text'], FILTER_SANITIZE_STRING);
 $result=$setting->update_special_offer();
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='change_language_status'){
	$setting->lang=filter_var($_POST['lang'], FILTER_SANITIZE_STRING);
	$setting->language_status=filter_var($_POST['language_status'], FILTER_SANITIZE_STRING);
	$status_change = $result=$setting->language_label_status(); 
	if($status_change){
		echo filter_var("ok", FILTER_SANITIZE_STRING);
	}else{
		echo filter_var("not_ok", FILTER_SANITIZE_STRING);
	}
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='update_company_setting'){
    $labels_option=array(
        'ld_company_name'=>ucwords(filter_var($_POST['company_name'], FILTER_SANITIZE_STRING)),
        'ld_company_email'=>filter_var($_POST['company_email'], FILTER_SANITIZE_EMAIL),
        'ld_company_address'=>filter_var($_POST['company_address'], FILTER_SANITIZE_STRING),
        'ld_company_city'=>ucwords(filter_var($_POST['company_city'], FILTER_SANITIZE_STRING)),
        'ld_company_state'=>ucwords(filter_var($_POST['company_state'], FILTER_SANITIZE_STRING)),
        'ld_company_country_code'=>filter_var($_POST['company_country_code'], FILTER_SANITIZE_STRING),
        'ld_company_zip_code'=>ucwords(filter_var($_POST['company_zipcode'], FILTER_SANITIZE_STRING)),
        'ld_company_country'=>ucwords(filter_var($_POST['company_country'], FILTER_SANITIZE_STRING)),
        'ld_company_logo'=>filter_var($_POST['company_logo'], FILTER_SANITIZE_STRING),
        'ld_company_phone'=>filter_var($_POST['company_phone'], FILTER_SANITIZE_STRING),		
        'ld_timezone'=>filter_var($_POST['time_zone'], FILTER_SANITIZE_STRING),
				'ld_language'=>filter_var($_POST['sel_language'], FILTER_SANITIZE_STRING)
    );
    foreach($labels_option as $option_key=>$option_value){
        $add3=$setting->set_option($option_key,$option_value);
    }
   
	
    chmod(dirname(dirname(dirname(__FILE__)))."/assets/images/services", 0777);
    $used_images = $objservice->get_used_images();
    $used_staff_images = $objservice->get_used_staff_images();
    $imgarr = array();
		
    while($img  = mysqli_fetch_array($used_images)){
					print_r($img);
        $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[0]);
        array_push($imgarr,$filtername);
    }
    while($img  = mysqli_fetch_array($used_staff_images)){
        $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[0]);
        array_push($imgarr,$filtername);
    }
    array_push($imgarr,"default");
    array_push($imgarr,"default_service");
    array_push($imgarr,"default_service1");
    $dir = dirname(dirname(dirname(__FILE__)))."/assets/images/services/";
    $cnt = 1;
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($cnt > 2){
                $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
                if (in_array($filtername, $imgarr)) {
                }else if($file=='..'){
					continue;
				}					
                else{
                    unlink(dirname(dirname(dirname(__FILE__)))."/assets/images/services/".$file);
                }
            }
            $cnt++;
        }
        closedir($dh);
    }
	ob_clean();ob_start();
	if($add3){
		echo filter_var("updated", FILTER_SANITIZE_STRING);
    }else{
        echo filter_var("Record Not Added", FILTER_SANITIZE_STRING);
    }
	
}
if(isset($_POST['ld_google_analytics_code'])){
	if(isset($_FILES)){
		if($_FILES['ld_seo_og_image']['name'] != ''){
			$img = time().'.'.pathinfo($_FILES['ld_seo_og_image']['name'], PATHINFO_EXTENSION);
			$img_type3=array('jpg','jpeg','png','gif');
			$destination3="../images/og_tag_img/".$img;
			$og_image_type=pathinfo($destination3,PATHINFO_EXTENSION);
			if(in_array($og_image_type,$img_type3)){
				move_uploaded_file($_FILES['ld_seo_og_image']['tmp_name'],$destination3);
				$labels_option=array(
					'ld_google_analytics_code'=>filter_var($_POST['ld_google_analytics_code'], FILTER_SANITIZE_STRING),
					'ld_page_title'=>ucwords(filter_var($_POST['ld_page_meta_tag'], FILTER_SANITIZE_STRING)),
					'ld_seo_og_title'=>filter_var($_POST['ld_seo_og_title'], FILTER_SANITIZE_STRING),
					'ld_seo_og_type'=>filter_var($_POST['ld_seo_og_type'], FILTER_SANITIZE_STRING),
					'ld_seo_og_url'=>filter_var($_POST['ld_seo_og_url'], FILTER_SANITIZE_STRING),
					'ld_seo_og_image'=>$img,
					'ld_seo_meta_description'=>filter_var($_POST['ld_seo_meta_description'], FILTER_SANITIZE_STRING)
				);
				foreach($labels_option as $option_key=>$option_value){
					$add3=$setting->set_option($option_key,$option_value);
				}
			}else{
				echo filter_var("Invalid Image Type", FILTER_SANITIZE_STRING);
				die;
				
			}
		}else{
			$labels_option=array(
				'ld_google_analytics_code'=>filter_var($_POST['ld_google_analytics_code'], FILTER_SANITIZE_STRING),
				'ld_page_title'=>ucwords(filter_var($_POST['ld_page_meta_tag'], FILTER_SANITIZE_STRING)),
				'ld_seo_og_title'=>filter_var($_POST['ld_seo_og_title'], FILTER_SANITIZE_STRING),
				'ld_seo_og_type'=>filter_var($_POST['ld_seo_og_type'], FILTER_SANITIZE_STRING),
				'ld_seo_og_url'=>filter_var($_POST['ld_seo_og_url'], FILTER_SANITIZE_STRING),
				'ld_seo_meta_description'=>filter_var($_POST['ld_seo_meta_description'], FILTER_SANITIZE_STRING)
			);
			foreach($labels_option as $option_key=>$option_value){
				$add3=$setting->set_option($option_key,$option_value);
			}
		}
	}else{
		$labels_option=array(
			'ld_google_analytics_code'=>filter_var($_POST['ld_google_analytics_code'], FILTER_SANITIZE_STRING),
			'ld_page_title'=>ucwords(filter_var($_POST['ld_page_meta_tag'], FILTER_SANITIZE_STRING)),
			'ld_seo_og_title'=>filter_var($_POST['ld_seo_og_title'], FILTER_SANITIZE_STRING),
			'ld_seo_og_type'=>filter_var($_POST['ld_seo_og_type'], FILTER_SANITIZE_STRING),
			'ld_seo_og_url'=>filter_var($_POST['ld_seo_og_url'], FILTER_SANITIZE_STRING),
			'ld_seo_meta_description'=>filter_var($_POST['ld_seo_meta_description'], FILTER_SANITIZE_STRING)
		);
		foreach($labels_option as $option_key=>$option_value){
			$add3=$setting->set_option($option_key,$option_value);
		}
	}
}
/*Update Company logo*/
elseif(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='delete_logo'){
    $update_logo=array('ld_company_logo'=>"");
    foreach($update_logo as $option_key=>$option_value){
        $logo=$setting->set_option($option_key,$option_value);
    }
}
/* Below code is use for save value of General settings */
elseif(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='update_general_setting'){
    switch(filter_var($_POST['currency'], FILTER_SANITIZE_STRING)) {
        case 'ALL': $currency_symbol = 'Lek'; break;
        case 'AFN': $currency_symbol = '؋'; break;
        case 'ARS': $currency_symbol = '$'; break;
        case 'AWG': $currency_symbol = 'ƒ'; break;
        case 'AUD': $currency_symbol = '$'; break;
        case 'AZN': $currency_symbol = 'ман'; break;
        case 'AED': $currency_symbol = 'د.إ'; break;
        case 'ANG': $currency_symbol = 'NAƒ'; break;
        case 'BSD': $currency_symbol = '$'; break;
        case 'BBD': $currency_symbol = '$'; break;
        case 'BYR': $currency_symbol = 'p.'; break;
        case 'BZD': $currency_symbol = 'BZ$'; break;
        case 'BMD': $currency_symbol = '$'; break;
        case 'BOB': $currency_symbol = '$b'; break;
        case 'BAM': $currency_symbol = 'KM'; break;
        case 'BWP': $currency_symbol = 'P'; break;
        case 'BGN': $currency_symbol = 'лв'; break;
        case 'BRL': $currency_symbol = 'R$'; break;
        case 'BND': $currency_symbol = '$'; break;
        case 'BDT': $currency_symbol = 'Tk'; break;
        case 'BIF': $currency_symbol = 'FBu'; break;
        case 'KHR': $currency_symbol = '៛'; break;
        case 'CAD': $currency_symbol = '$'; break;
        case 'KYD': $currency_symbol = '$'; break;
        case 'CLP': $currency_symbol = '$'; break;
        case 'CNY': $currency_symbol = '¥'; break;
        case 'CYN': $currency_symbol = '¥'; break;
        case 'COP': $currency_symbol = '$'; break;
        case 'CRC': $currency_symbol = '₡'; break;
        case 'HRK': $currency_symbol = 'kn'; break;
        case 'CUP': $currency_symbol = '₱'; break;
        case 'CZK': $currency_symbol = 'Kč'; break;
        case 'CVE': $currency_symbol = 'Esc'; break;
        case 'CHF': $currency_symbol = 'CHF'; break;
        case 'DKK': $currency_symbol = 'kr'; break;
        case 'DOP': $currency_symbol = 'RD$'; break;
        case 'DJF': $currency_symbol = 'Fdj'; break;
        case 'DZD': $currency_symbol = 'دج'; break;
        case 'XCD': $currency_symbol = '$'; break;
        case 'EGP': $currency_symbol = '£'; break;
        case 'SVC': $currency_symbol = '$'; break;
        case 'EEK': $currency_symbol = 'kr'; break;
        case 'EUR': $currency_symbol = '€'; break;
        case 'ETB': $currency_symbol = 'Br'; break;
        case 'FKP': $currency_symbol = '£'; break;
        case 'FJD': $currency_symbol = '$'; break;
        case 'GHC': $currency_symbol = '¢'; break;
        case 'GIP': $currency_symbol = '£'; break;
        case 'GTQ': $currency_symbol = 'Q'; break;
        case 'GGP': $currency_symbol = '£'; break;
        case 'GYD': $currency_symbol = '$'; break;
        case 'GMD': $currency_symbol = 'D'; break;
        case 'GNF': $currency_symbol = 'FG'; break;
        case 'HNL': $currency_symbol = 'L'; break;
        case 'HKD': $currency_symbol = '$'; break;
        case 'HUF': $currency_symbol = 'Ft'; break;
        case 'HRK': $currency_symbol = 'kn'; break;
        case 'HTG': $currency_symbol = 'G'; break;
        case 'ISK': $currency_symbol = 'kr'; break;
        case 'INR': $currency_symbol = 'Rs.'; break;
        case 'IDR': $currency_symbol = 'Rp'; break;
        case 'IRR': $currency_symbol = '﷼'; break;
        case 'IMP': $currency_symbol = '£'; break;
        case 'ILS': $currency_symbol = '₪'; break;
        case 'JMD': $currency_symbol = 'J$'; break;
        case 'JPY': $currency_symbol = '¥'; break;
        case 'JEP': $currency_symbol = '£'; break;
        case 'KZT': $currency_symbol = 'лв'; break;
        case 'KPW': $currency_symbol = '₩'; break;
        case 'KRW': $currency_symbol = '₩'; break;
        case 'KGS': $currency_symbol = 'лв'; break;
        case 'KES': $currency_symbol = 'KSh'; break;
        case 'KMF': $currency_symbol = 'KMF'; break;
        case 'LAK': $currency_symbol = '₭'; break;
        case 'LVL': $currency_symbol = 'Ls'; break;
        case 'LBP': $currency_symbol = '£'; break;
        case 'LRD': $currency_symbol = '$'; break;
        case 'LTL': $currency_symbol = 'Lt'; break;
        case 'MKD': $currency_symbol = 'ден'; break;
        case 'MYR': $currency_symbol = 'RM'; break;
        case 'MUR': $currency_symbol = '₨'; break;
        case 'MXN': $currency_symbol = '$'; break;
        case 'MNT': $currency_symbol = '₮'; break;
        case 'MZN': $currency_symbol = 'MT'; break;
        case 'MDL': $currency_symbol = 'MDL'; break;
        case 'MOP': $currency_symbol = '$'; break;
        case 'MRO': $currency_symbol = 'UM'; break;
        case 'MVR': $currency_symbol = 'Rf'; break;
        case 'MWK': $currency_symbol = 'MK'; break;
        case 'MAD': $currency_symbol = 'د.م.'; break;
        case 'NAD': $currency_symbol = '$'; break;
        case 'NPR': $currency_symbol = '₨'; break;
        case 'ANG': $currency_symbol = 'ƒ'; break;
        case 'NZD': $currency_symbol = '$'; break;
        case 'NIO': $currency_symbol = 'C$'; break;
        case 'NGN': $currency_symbol = '₦'; break;
        case 'NOK': $currency_symbol = 'kr'; break;
        case 'OMR': $currency_symbol = '﷼'; break;
        case 'PKR': $currency_symbol = '₨'; break;
        case 'PAB': $currency_symbol = 'B/.'; break;
        case 'PYG': $currency_symbol = 'Gs'; break;
        case 'PEN': $currency_symbol = 'S/.'; break;
        case 'PHP': $currency_symbol = '₱'; break;
        case 'PLN': $currency_symbol = 'zł'; break;
        case 'PGK': $currency_symbol = 'K'; break;
        case 'QAR': $currency_symbol = '﷼'; break;
        case 'RON': $currency_symbol = 'lei'; break;
        case 'RUB': $currency_symbol = 'руб'; break;
        case 'SHP': $currency_symbol = '£'; break;
        case 'SAR': $currency_symbol = '﷼'; break;
        case 'RSD': $currency_symbol = 'Дин.'; break;
        case 'SCR': $currency_symbol = '₨'; break;
        case 'SGD': $currency_symbol = '$'; break;
        case 'SBD': $currency_symbol = '$'; break;
        case 'SOS': $currency_symbol = 'S'; break;
        case 'ZAR': $currency_symbol = 'R'; break;
        case 'LKR': $currency_symbol = '₨'; break;
        case 'SEK': $currency_symbol = 'kr'; break;
        case 'CHF': $currency_symbol = 'CHF'; break;
        case 'SRD': $currency_symbol = '$'; break;
        case 'SYP': $currency_symbol = '£'; break;
        case 'SLL': $currency_symbol = 'Le'; break;
        case 'STD': $currency_symbol = 'Db'; break;
        case 'TWD': $currency_symbol = 'NT'; break;
        case 'THB': $currency_symbol = '฿'; break;
        case 'TTD': $currency_symbol = 'TTD'; break;
        case 'TRY': $currency_symbol = '₤'; break;
        case 'TVD': $currency_symbol = '$'; break;
        case 'TOP': $currency_symbol = 'T$'; break;
        case 'TZS': $currency_symbol = 'x'; break;
        case 'UAH': $currency_symbol = '₴'; break;
        case 'GBP': $currency_symbol = '£'; break;
        case 'USD': $currency_symbol = '$'; break;
        case 'UYU': $currency_symbol = '$U'; break;
        case 'UZS': $currency_symbol = 'лв'; break;
        case 'UGX': $currency_symbol = 'USh'; break;
        case 'VEF': $currency_symbol = 'Bs'; break;
        case 'VND': $currency_symbol = '₫'; break;
        case 'VUV': $currency_symbol = 'Vt'; break;
        case 'WST': $currency_symbol = 'WS$'; break;
        case 'XAF': $currency_symbol = 'BEAC'; break;
        case 'XOF': $currency_symbol = 'BCEAO'; break;
        case 'XPF': $currency_symbol = 'F'; break;
        case 'YER': $currency_symbol = '﷼'; break;
        case 'ZWD': $currency_symbol = 'Z$'; break;
        case 'ZAR': $currency_symbol = 'R'; break;
        default: $currency_symbol = '$'; break;
    }
    $ld_minimum_delivery_days = filter_var($_POST['ld_minimum_delivery_days'], FILTER_SANITIZE_STRING);
    $ld_show_delivery_date = filter_var($_POST['ld_show_delivery_date'], FILTER_SANITIZE_STRING);
	
    $postalcode = preg_split('/\r\n|[\r\n]/', filter_var($_POST['ld_postal_code'], FILTER_SANITIZE_STRING));
    $converted_postalcode = implode(',',$postalcode);
    $ld_option=array(
		'ld_calculation_policy'=>filter_var($_POST['ld_calculation_policy'], FILTER_SANITIZE_STRING),
        'ld_time_interval'=>filter_var($_POST['time_interval'], FILTER_SANITIZE_STRING),
        'ld_allow_privacy_policy'=>filter_var($_POST['ld_allow_privacy_policy'], FILTER_SANITIZE_STRING),
        'ld_privacy_policy_link'=>urldecode(filter_var($_POST['ld_privacy_policy_link'], FILTER_SANITIZE_STRING)),
        'ld_addons_default_design'=>filter_var($_POST['ld_addons_default_design'], FILTER_SANITIZE_STRING),
        'ld_method_default_design'=>filter_var($_POST['ld_method_default_design'], FILTER_SANITIZE_STRING),
        'ld_service_default_design'=>filter_var($_POST['ld_service_default_design'], FILTER_SANITIZE_STRING),
        'ld_cart_scrollable'=>filter_var($_POST['ld_cart_scrollable'], FILTER_SANITIZE_STRING),
        'ld_terms_condition_link'=>urldecode(filter_var($_POST['ld_terms_condition_link'], FILTER_SANITIZE_STRING)),
        'ld_front_desc'=>urldecode($_POST['ld_front_desc']),
        'ld_min_advance_booking_time'=>filter_var($_POST['min_advanced_booking'], FILTER_SANITIZE_STRING),
        'ld_max_advance_booking_time'=>filter_var($_POST['max_advanced_booking'], FILTER_SANITIZE_STRING),
        'ld_booking_padding_time'=>filter_var($_POST['booking_padding_time'], FILTER_SANITIZE_STRING),
        'ld_service_padding_time_before'=>filter_var($_POST['service_padding_time_before'], FILTER_SANITIZE_STRING),
        'ld_service_padding_time_after'=>filter_var($_POST['service_padding_time_after'], FILTER_SANITIZE_STRING),
        'ld_cancellation_buffer_time'=>filter_var($_POST['cancelled_buffer_time'], FILTER_SANITIZE_STRING),
        'ld_reshedule_buffer_time'=>filter_var($_POST['reshedule_buffer_time'], FILTER_SANITIZE_STRING),
        'ld_currency'=>filter_var($_POST['currency'], FILTER_SANITIZE_STRING),
        'ld_currency_symbol_position'=>filter_var($_POST['currency_symbol_position'], FILTER_SANITIZE_STRING),
        'ld_price_format_decimal_places'=>filter_var($_POST['price_format_decimal_places'], FILTER_SANITIZE_STRING),
        'ld_tax_vat_status'=>filter_var($_POST['tax_vat_1'], FILTER_SANITIZE_STRING),
        'ld_tax_vat_type'=>filter_var($_POST['percent_flatfree'], FILTER_SANITIZE_STRING),
        'ld_tax_vat_value'=>filter_var($_POST['tax_vat_value'], FILTER_SANITIZE_STRING),
        'ld_postalcode_status'=>filter_var($_POST['postal_code_1'], FILTER_SANITIZE_STRING),
        'ld_partial_deposit_status'=>filter_var($_POST['status_partial'], FILTER_SANITIZE_STRING),
		'ld_cancelation_policy_status'=>filter_var($_POST['cancel_policy_status'], FILTER_SANITIZE_STRING),
		'ld_cancel_policy_header'=>filter_var($_POST['cancel_policy_header'], FILTER_SANITIZE_STRING),
		'ld_cancel_policy_textarea'=>filter_var($_POST['cancel_policy_textarea'], FILTER_SANITIZE_STRING),
		'ld_partial_type'=>filter_var($_POST['partial_percent_flatfree'], FILTER_SANITIZE_STRING),
        'ld_partial_deposit_amount'=>filter_var($_POST['partial_deposit_amount'], FILTER_SANITIZE_STRING),
        'ld_partial_deposit_message'=>filter_var($_POST['partial_deposit_message'], FILTER_SANITIZE_STRING),
        'ld_thankyou_page_url'=>urldecode(filter_var($_POST['thanks_url'], FILTER_SANITIZE_URL)),
        'ld_allow_multiple_booking_for_same_timeslot_status'=>filter_var($_POST['allow_multiple_booking_for_same_timeslot'], FILTER_SANITIZE_STRING),
        'ld_appointment_auto_confirm_status'=>filter_var($_POST['appointment_auto_confirmation'], FILTER_SANITIZE_STRING),
        'ld_star_show_on_front'=>filter_var($_POST['star_show_on_frontend'], FILTER_SANITIZE_STRING),
        'ld_allow_day_closing_time_overlap_booking'=>filter_var($_POST['allow_time_overlap_booking'], FILTER_SANITIZE_STRING),
        'ld_allow_terms_and_conditions'=>filter_var($_POST['allow_terms_and_condition'], FILTER_SANITIZE_STRING),
        'ld_allow_front_desc'=>filter_var($_POST['ld_allow_front_desc'], FILTER_SANITIZE_STRING),
        'ld_show_self_service'=>filter_var($_POST['ld_show_self_service'], FILTER_SANITIZE_STRING),
        'ld_minimum_delivery_days'=>$ld_minimum_delivery_days,
        'ld_show_delivery_date'=>$ld_show_delivery_date,
        'ld_currency_symbol'=>$currency_symbol,
				'ld_user_zip_code'=>filter_var($_POST['ld_user_zip_code'], FILTER_SANITIZE_STRING)
    );

    foreach($ld_option as $option_key=>$option_value){
        $add3=$setting->set_option($option_key,$option_value);
    }
	$setting->set_option_postal($converted_postalcode);
    if($add3){
        /* 
			$lng=$setting->get_option($_SESSION['b_id'],'ld_languages');
			$lng=filter_var($_POST['languages'], FILTER_SANITIZE_STRING);
			setcookie('bt-language',$lng, time() + (86400 * 30), "/"); 
		*/
        echo filter_var("updated", FILTER_SANITIZE_STRING);
    }
}

/* Google Calendar Start */

if($gc_hook->gc_purchase_status() == 'exist'){
	echo filter_var($gc_hook->gc_settings_save_ajax_hook(), FILTER_SANITIZE_STRING);
	echo filter_var($gc_hook->gc_setting_configure_ajax_hook(), FILTER_SANITIZE_STRING);
	echo filter_var($gc_hook->gc_setting_disconnect_ajax_hook(), FILTER_SANITIZE_STRING);
	echo filter_var($gc_hook->gc_staff_settings_save_ajax_hook(), FILTER_SANITIZE_STRING);
	echo filter_var($gc_hook->gc_staff_setting_configure_ajax_hook(), FILTER_SANITIZE_STRING);
	echo filter_var($gc_hook->gc_staff_setting_disconnect_ajax_hook(), FILTER_SANITIZE_STRING);
}

/* Google Calendar End */

/* Below code is use for save value of payment settings */
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='payment_setting'){
    $payment_option=array(
        'ld_all_payment_gateway_status'=>filter_var($_POST['payemnt_gateway_all'], FILTER_SANITIZE_STRING),
        'ld_pay_locally_status'=>filter_var($_POST['payemnt_locally'], FILTER_SANITIZE_STRING),
        'ld_paypal_express_checkout_status'=>filter_var($_POST['payemnt_paypal'], FILTER_SANITIZE_STRING),
        'ld_paypal_api_username'=>filter_var($_POST['username'], FILTER_SANITIZE_STRING),
        'ld_paypal_api_password'=>filter_var($_POST['password'], FILTER_SANITIZE_STRING),
        'ld_paypal_api_signature'=>filter_var($_POST['signature'], FILTER_SANITIZE_STRING),
        'ld_paypal_guest_payment_status'=>filter_var($_POST['payemnt_guest'], FILTER_SANITIZE_STRING),
        'ld_paypal_test_mode_status'=>filter_var($_POST['test_mode'], FILTER_SANITIZE_STRING),
        'ld_stripe_payment_form_status'=>filter_var($_POST['stripe_payment'], FILTER_SANITIZE_STRING),
        'ld_stripe_secretkey'=>filter_var($_POST['secretkey'], FILTER_SANITIZE_STRING),
        'ld_stripe_publishablekey'=>filter_var($_POST['publishablekey'], FILTER_SANITIZE_STRING),
		'ld_authorizenet_status'=>filter_var($_POST['authorize_net_status'], FILTER_SANITIZE_STRING),
		'ld_authorizenet_API_login_ID'=>filter_var($_POST['autorize_login_ID'], FILTER_SANITIZE_STRING),
		'ld_authorizenet_transaction_key'=>filter_var($_POST['authorize_transaction_key'], FILTER_SANITIZE_STRING),
		'ld_authorize_sandbox_mode'=>filter_var($_POST['authorize_test_mode'], FILTER_SANITIZE_STRING),
		'ld_2checkout_sandbox_mode'=>filter_var($_POST['twocheckout_testmode'], FILTER_SANITIZE_STRING),
		'ld_2checkout_status'=>filter_var($_POST['twocheckout_payment'], FILTER_SANITIZE_STRING),
		'ld_2checkout_privatekey'=>filter_var($_POST['twocheckout_privatekey'], FILTER_SANITIZE_STRING),
		'ld_2checkout_publishkey'=>filter_var($_POST['twocheckout_publishkey'], FILTER_SANITIZE_STRING),
		'ld_2checkout_sellerid'=>filter_var($_POST['twocheckout_sellerid'], FILTER_SANITIZE_STRING),
		'ld_payumoney_status'=>filter_var($_POST['payumoney_status'], FILTER_SANITIZE_STRING),
		'ld_payumoney_merchant_key'=>filter_var($_POST['payumoney_merchantkey'], FILTER_SANITIZE_STRING),
		'ld_payumoney_salt'=>filter_var($_POST['payumoney_saltkey'], FILTER_SANITIZE_STRING),
		/*new add*/
		'ld_bank_name'=>filter_var($_POST['bank_name'], FILTER_SANITIZE_STRING),
		'ld_account_name'=>filter_var($_POST['account_name'], FILTER_SANITIZE_STRING),
		'ld_account_number'=>filter_var($_POST['account_number'], FILTER_SANITIZE_STRING),
		'ld_branch_code'=>filter_var($_POST['branch_code'], FILTER_SANITIZE_STRING),
		'ld_ifsc_code'=>filter_var($_POST['ifsc_code'], FILTER_SANITIZE_STRING),
		'ld_bank_description'=>filter_var($_POST['bank_description'], FILTER_SANITIZE_STRING),
		'ld_bank_transfer_status'=>filter_var($_POST['bank_status'], FILTER_SANITIZE_STRING)
    );
    foreach($payment_option as $option_key=>$option_value){
        $add3=$setting->set_option($option_key,$option_value);
    }
    if($add3){
        echo filter_var("updated", FILTER_SANITIZE_STRING);
    }
}
/* Below code is use for save value of E-mail notification */
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='email_setting'){
    $email_option=array(
        'ld_admin_email_notification_status'=>filter_var($_POST['admin_email'], FILTER_SANITIZE_EMAIL),
        'ld_staff_email_notification_status'=>filter_var($_POST['staff_email'], FILTER_SANITIZE_EMAIL),
        'ld_client_email_notification_status'=>filter_var($_POST['client_email'], FILTER_SANITIZE_EMAIL),
        'ld_email_sender_name'=>addslashes(filter_var($_POST['sender_name'], FILTER_SANITIZE_STRING)),
        'ld_email_sender_address'=>filter_var($_POST['sender_email'], FILTER_SANITIZE_EMAIL),
        'ld_admin_optional_email'=>filter_var($_POST['admin_optional_email'], FILTER_SANITIZE_EMAIL),
        'ld_email_appointment_reminder_buffer'=>filter_var($_POST['appointment_reminder'], FILTER_SANITIZE_STRING),
		'ld_smtp_hostname'=>filter_var($_POST['hostname'], FILTER_SANITIZE_STRING),
        'ld_smtp_username'=>filter_var($_POST['username'], FILTER_SANITIZE_EMAIL),
        'ld_smtp_password'=>filter_var($_POST['password'], FILTER_SANITIZE_STRING),
        'ld_smtp_port'=>filter_var($_POST['port'], FILTER_SANITIZE_STRING),
		'ld_smtp_encryption'=>filter_var($_POST['encryptiontype'], FILTER_SANITIZE_STRING),
		'ld_smtp_authetication'=>filter_var($_POST['autheticationtype'], FILTER_SANITIZE_STRING),
    );
    foreach($email_option as $option_key=>$option_value){
        $add3=$setting->set_option($option_key,$option_value);
    }
    if($add3){
        echo filter_var("updated", FILTER_SANITIZE_STRING);
    }
}
/* Below code is use for save value of SMS Notification settings */
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='sms_reminder'){
    $sms_notification=array(
        'ld_sms_service_status'=>filter_var($_POST['status_sms_service'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_account_SID'=>filter_var($_POST['account_sid'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_auth_token'=>filter_var($_POST['auth_token'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_sender_number'=>filter_var($_POST['sender_number'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_send_sms_to_client_status'=>filter_var($_POST['status_sms_to_client'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_send_sms_to_admin_status'=>filter_var($_POST['status_sms_to_admin'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_send_sms_to_staff_status'=>filter_var($_POST['status_sms_to_staff'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_admin_phone_number'=>filter_var($_POST['admin_phone'], FILTER_SANITIZE_STRING),
        /*PLIVO SETTINGS*/
        'ld_sms_plivo_account_SID'=>filter_var($_POST['account_sid_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_auth_token'=>filter_var($_POST['auth_token_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_sender_number'=>filter_var($_POST['sender_number_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_send_sms_to_client_status'=>filter_var($_POST['status_sms_to_client_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_send_sms_to_admin_status'=>filter_var($_POST['status_sms_to_admin_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_send_sms_to_staff_status'=>filter_var($_POST['status_sms_to_staff_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_admin_phone_number'=>filter_var($_POST['admin_phone_p'], FILTER_SANITIZE_STRING),
        'ld_sms_plivo_status'=>filter_var($_POST['sms_plivo_status'], FILTER_SANITIZE_STRING),
        'ld_sms_twilio_status'=>filter_var($_POST['sms_twilio_status'], FILTER_SANITIZE_STRING),
		/* Nexmo Settings */
		'ld_sms_nexmo_status'=>filter_var($_POST['sms_nexmo_status'], FILTER_SANITIZE_STRING),
        'ld_nexmo_api_key'=>filter_var($_POST['sms_nexmo_api_key'], FILTER_SANITIZE_STRING),
        'ld_nexmo_api_secret'=>filter_var($_POST['sms_nexmo_api_secret'], FILTER_SANITIZE_STRING),
        'ld_nexmo_from'=>filter_var($_POST['sms_nexmo_from'], FILTER_SANITIZE_STRING),
        'ld_nexmo_status'=>filter_var($_POST['sms_nexmo_statuss'], FILTER_SANITIZE_STRING),
        'ld_sms_nexmo_send_sms_to_client_status'=>filter_var($_POST['sms_nexmo_statu_send_client'], FILTER_SANITIZE_STRING),
        'ld_sms_nexmo_send_sms_to_admin_status'=>filter_var($_POST['sms_nexmo_statu_send_admin'], FILTER_SANITIZE_STRING),
        'ld_sms_nexmo_send_sms_to_staff_status'=>filter_var($_POST['sms_nexmo_statu_send_staff'], FILTER_SANITIZE_STRING),
        'ld_sms_nexmo_admin_phone_number'=>filter_var($_POST['sms_nexmo_admin_phone'], FILTER_SANITIZE_STRING),
		/* textlocal settings */
		'ld_sms_textlocal_account_username'=>filter_var($_POST['sms_textlocal_username'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_account_hash_id'=>filter_var($_POST['sms_textlocal_hashid'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_send_sms_to_client_status'=>filter_var($_POST['sms_textlocal_status_send_client'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_send_sms_to_admin_status'=>filter_var($_POST['sms_textlocal_status_send_admin'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_send_sms_to_staff_status'=>filter_var($_POST['sms_textlocal_status_send_staff'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_status'=>filter_var($_POST['sms_textlocal_status'], FILTER_SANITIZE_STRING),
		'ld_sms_textlocal_admin_phone'=>filter_var($_POST['textlocal_admin_phone'], FILTER_SANITIZE_STRING)
    );
    foreach($sms_notification as $option_key=>$option_value){
        $add3=$setting->set_option($option_key,$option_value);
    }
    if($add3){
        echo filter_var("updated", FILTER_SANITIZE_STRING);
    }
}

if(isset($_POST['assigndesign'])){
    $design_id = filter_var($_POST['designid'], FILTER_SANITIZE_STRING);
    $option = filter_var($_POST['divname'], FILTER_SANITIZE_STRING);
    $setting->set_option($option,$design_id);
}
/* Language settings */
elseif(isset($_POST['change_language'])){
	$update_labels = filter_var($_POST['update_labels'], FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
	foreach (filter_var($_POST['labels_front_error'], FILTER_SANITIZE_STRING) as $key => $value) {
		$language_front_error[$key] = $value;
	}
	$language_label_arr = $setting->get_all_labelsbyid_from_id($id);
	
	$language_front_arr = $language_label_arr[1];
	$language_admin_arr = $language_label_arr[3];
	$language_error_arr = $language_label_arr[4];
	$language_extra_arr = $language_label_arr[5];
	$language_front_error_arr = base64_encode(serialize($language_front_error));
	
	$setting->update_labels_languages($language_front_arr, $language_admin_arr, $language_error_arr, $language_extra_arr, $language_front_error_arr, $id);		
}
elseif(isset($_POST['get_all_labels'])){
    $lang = filter_var($_POST['oflang'], FILTER_SANITIZE_STRING);
    $langarr = $setting->get_all_labelsbyid($lang);
	
	if ($langarr[1] != "" || $langarr[3] != "" || $langarr[4] != "" || $langarr[5] != "" || $langarr[6] != "")
	{
		$default_language_arr = $setting->get_all_labelsbyid("en");
		
		if($langarr[1] != ''){
			$label_decode_front = base64_decode($langarr[1]);
		}else{
			$label_decode_front = base64_decode($default_language_arr[1]);
		}
		if($langarr[3] != ''){
			$label_decode_admin = base64_decode($langarr[3]);
		}else{
			$label_decode_admin = base64_decode($default_language_arr[3]);
		}
		if($langarr[4] != ''){
			$label_decode_error = base64_decode($langarr[4]);
		}else{
			$label_decode_error = base64_decode($default_language_arr[4]);
		}
		if($langarr[5] != ''){
			$label_decode_extra = base64_decode($langarr[5]);
		}else{
			$label_decode_extra = base64_decode($default_language_arr[5]);
		}
		if($langarr[6] != ''){
			$label_decode_front_error = base64_decode($langarr[6]);
		}else{
			$label_decode_front_error = base64_decode($default_language_arr[6]);
		}
		$label_decode_front_unserial = unserialize($label_decode_front);
		$label_decode_admin_unserial = unserialize($label_decode_admin);
		$label_decode_error_unserial = unserialize($label_decode_error);
		$label_decode_extra_unserial = unserialize($label_decode_extra);
		$label_decode_front_error_unserial = unserialize($label_decode_front_error);
				
		?>
		<div class="language_status" data-id="<?php echo filter_var($lang, FILTER_SANITIZE_STRING); ?>">
			<input class="lda-toggle-checkbox2 language_status_change" data-id="<?php echo filter_var($lang, FILTER_SANITIZE_STRING); ?>" data-toggle="toggle" data-size="small" type='checkbox' name="language_status" <?php if($langarr[7] == "Y"){echo filter_var('checked', FILTER_SANITIZE_STRING);} ?> data-on="<?php echo filter_var($label_language_values['enable'], FILTER_SANITIZE_STRING);?>" data-off="<?php echo filter_var($label_language_values['disable'], FILTER_SANITIZE_STRING);?>" data-onstyle='success' data-offstyle='danger' />
		</div>
		
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#detail_spssfront"><?php echo filter_var($label_language_values['frontend_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spssadmin"><?php echo filter_var($label_language_values['admin_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spsserror"><?php echo filter_var($label_language_values['errors'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spssextra"><?php echo filter_var($label_language_values['extra_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<?php  if($langarr[6] == ''){ ?>
				<li><a data-toggle="tab" href="#detail_spsfront_error"><?php echo filter_var($label_language_values['front_error_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<?php  } ?>
		</ul>
		<div class="tab-content">
			<div id="detail_spssfront" class="tab-pane fade in active">
				<form id="ld-frontend-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_frontend_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_front_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_front" name="ctfrontlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_frontend_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spssadmin" class="tab-pane fade">
				<form id="ld-admin-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_admin_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_admin_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_admin" name="ctadminlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_admin_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spsserror" class="tab-pane fade">
				<form id="ld-error-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_error_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_error_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_error" name="cterrorlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_error_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spssextra" class="tab-pane fade">
				<form id="ld-extra-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_extra_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_extra_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_extra" name="ctextralabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"/>
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_extra_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<?php  if($langarr[6] == ''){ ?>
				<div id="detail_spsfront_error" class="tab-pane fade">
					<form id="ld-ferror-labels-settings" method="post" type="" class="ld-labels-settings" >
						<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
						<div class="row lda-top-right">
							<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_ferror_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
						</div>
						
						<table class="form-inline ld-common-table" >
							<?php  
							foreach ($label_decode_front_error_unserial as $key => $value) 
							{
								$final_value = str_replace('_', ' ', $key);
								?>
								<tr>
								<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
								<td>
									<div class="form-group">
										<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_extra" name="ctfr_errorlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
									</div>
								</td>
								</tr>
							<?php  } ?>
							<tr>
								<td colspan="2">
									<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_ferror_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
								</td>
							</tr>
						</table>
					</form>
				</div>
			<?php  } ?>
		</div>
		<?php  
    }
	else
	{
		$default_language_arr = $setting->get_all_labelsbyid("en");
		
		$label_decode_front = base64_decode($default_language_arr[1]);
		$label_decode_admin = base64_decode($default_language_arr[3]);
		$label_decode_error = base64_decode($default_language_arr[4]);
		$label_decode_extra = base64_decode($default_language_arr[5]);
		$label_decode_front_error = base64_decode($default_language_arr[6]);
		
		$label_decode_front_unserial = unserialize($label_decode_front);
		$label_decode_admin_unserial = unserialize($label_decode_admin);
		$label_decode_error_unserial = unserialize($label_decode_error);
		$label_decode_extra_unserial = unserialize($label_decode_extra);
		$label_decode_front_error_unserial = unserialize($label_decode_front_error);
		?>
		<div class="language_status" data-id="<?php echo filter_var($lang, FILTER_SANITIZE_STRING); ?>">
             <input class="lda-toggle-checkbox2"  data-id="<?php echo filter_var($lang, FILTER_SANITIZE_STRING); ?>" data-toggle="toggle" data-size="small"  type='checkbox' name="language_status"   data-on="<?php echo filter_var($label_language_values['enable'], FILTER_SANITIZE_STRING);?>" data-off="<?php echo filter_var($label_language_values['disable'], FILTER_SANITIZE_STRING);?>"  data-onstyle='success' data-offstyle='danger' />
         </div> 
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#detail_spssfront"><?php echo filter_var($label_language_values['frontend_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spssadmin"><?php echo filter_var($label_language_values['admin_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spsserror"><?php echo filter_var($label_language_values['errors'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spssextra"><?php echo filter_var($label_language_values['extra_labels'], FILTER_SANITIZE_STRING);?></a></li>
			<li><a data-toggle="tab" href="#detail_spsfront_error"><?php echo filter_var($label_language_values['front_error_labels'], FILTER_SANITIZE_STRING);?></a></li>
		</ul>
		<div class="tab-content">
			<div id="detail_spssfront" class="tab-pane fade in active">
				<form id="ld-frontend-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_frontend_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_front_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_front" name="ctfrontlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_frontend_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spssadmin" class="tab-pane fade">
				<form id="ld-admin-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_admin_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_admin_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_admin" name="ctadminlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_admin_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spsserror" class="tab-pane fade">
				<form id="ld-error-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_error_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_error_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_error" name="cterrorlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_error_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spssextra" class="tab-pane fade">
				<form id="ld-extra-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_extra_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_extra_unserial as $key => $value) {
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_extra" name="ctextralabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"/>
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_extra_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="detail_spsfront_error" class="tab-pane fade">
				<form id="ld-ferror-labels-settings" method="post" type="" class="ld-labels-settings" >
					<input type="hidden" value="<?php echo filter_var($_POST['oflang'], FILTER_SANITIZE_STRING); ?>" name="ld_selected_lang_labels" />
					<div class="row lda-top-right">
						<span class="pull-right lda-setting-fix-btn" style="margin: 5px 40px !important;"> <input class="btn btn-success" type="submit" name="btn_submit_ferror_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
					</div>
					
					<table class="form-inline ld-common-table" >
						<?php  
						foreach ($label_decode_front_error_unserial as $key => $value) 
						{
							$final_value = str_replace('_', ' ', $key);
							?>
							<tr>
							<td><label class="englabel_<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>"><?php echo filter_var($final_value, FILTER_SANITIZE_STRING);?></label></td>
							<td>
								<div class="form-group">
									<input type="text" size="50" value="<?php echo urldecode($value);?>" class="form-control langlabel_extra" name="ctfr_errorlabelct<?php  echo filter_var($key, FILTER_SANITIZE_STRING);?>" />
								</div>
							</td>
							</tr>
						<?php  } ?>
						<tr>
							<td colspan="2">
								<span class="lda-setting-fix-btn"> <input class="btn btn-success" type="submit" name="btn_submit_ferror_labels" value="<?php echo filter_var($label_language_values['save_labels_setting'], FILTER_SANITIZE_STRING);?>"></span>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<?php 
	}
}
elseif(isset($_POST['change_language'])){
	$update_labels = filter_var($_POST['update_labels'], FILTER_SANITIZE_STRING);
	$language_front_error = array();
	$alllang = $setting->get_all_labelsbyid(filter_var($_POST['id'], FILTER_SANITIZE_STRING));
	while($all = mysqli_fetch_array($alllang))
	{
		$language_label_arr = $this->get_all_labelsbyid($all[2]);
		
		$label_decode_front = base64_decode($language_label_arr[1]);
		$label_decode_admin = base64_decode($language_label_arr[3]);
		$label_decode_error = base64_decode($language_label_arr[4]);
		$label_decode_extra = base64_decode($language_label_arr[5]);
		$label_decode_front_error_labels = base64_decode($language_label_arr[6]);

		$label_decode_front_unserial = unserialize($label_decode_front);
		$label_decode_admin_unserial = unserialize($label_decode_admin);
		$label_decode_error_unserial = unserialize($label_decode_error);
		$label_decode_extra_unserial = unserialize($label_decode_extra);
		$label_decode_front_error_labels_unserial = unserialize($label_decode_front_error_labels);
		
		/* UPDATE ALL CODE WITH NEW URLENCODE PATTERN */
		foreach($label_decode_front_unserial as $key => $value){
			$label_decode_front_unserial[$key] = urldecode($value);
		}
		foreach($label_decode_admin_unserial as $key => $value){
			$label_decode_admin_unserial[$key] = urldecode($value);
		}
		foreach($label_decode_error_unserial as $key => $value){
			$label_decode_error_unserial[$key] = urldecode($value);
		}
		foreach($label_decode_extra_unserial as $key => $value){
			$label_decode_extra_unserial[$key] = urldecode($value);
		}
		foreach(filter_var($_POST['labels_front_error'], FILTER_SANITIZE_STRING) as $key => $value){
			$label_decode_front_error_labels_unserial[$key] = urldecode($value);
		}	
		$language_front_arr = base64_encode(serialize($label_decode_front_unserial));
		$language_admin_arr = base64_encode(serialize($label_decode_admin_unserial));
		$language_error_arr = base64_encode(serialize($label_decode_error_unserial));
		$language_extra_arr = base64_encode(serialize($label_decode_extra_unserial));
		$language_form_error_arr = base64_encode(serialize($label_decode_front_error_labels_unserial));

		$update_default_lang = "UPDATE `ld_languages` SET `label_data` = '".$language_front_arr."', `admin_labels` = '".$language_admin_arr."', `error_labels` = '".$language_error_arr."', `extra_labels` = '".$language_extra_arr."', `front_error_labels` = '".$language_form_error_arr."' WHERE `id` = '".filter_var($_POST['id'], FILTER_SANITIZE_STRING)."'";
		mysqli_query($this->conn, $update_default_lang);
	}
	foreach (filter_var($_POST['labels_front_error'], FILTER_SANITIZE_STRING) as $key => $value) {
		$language_front[$key] = $value;
	}
	$language_front_arr = base64_encode(serialize($language_front));
	$language_admin_arr = base64_encode(serialize($language_admin));
	$language_error_arr = base64_encode(serialize($language_error));
	$language_extra_arr = base64_encode(serialize($language_extra));
	
	$setting->insert_labels_languages($language_front_arr, $language_admin_arr, $language_error_arr, $language_extra_arr, '', $update_labels);	
}
elseif(isset($_POST['manage_form_fields_setting'])){
	$notes = array(filter_var($_POST['ld_bf_notes_1'],$_POST['ld_bf_notes_2'],$_POST['ld_bf_notes_3'],$_POST['ld_bf_notes_4'], FILTER_SANITIZE_STRING)); 
	$final_notes = implode(",",$notes);
	$firstname = array(filter_var($_POST['ld_bf_first_name_1'],$_POST['ld_bf_first_name_2'],$_POST['ld_bf_first_name_3'],$_POST['ld_bf_first_name_4'], FILTER_SANITIZE_STRING));
	$final_first_name = implode(",",$firstname);
	
	$lastname = array(filter_var($_POST['ld_bf_last_name_1'],$_POST['ld_bf_last_name_2'],$_POST['ld_bf_last_name_3'],$_POST['ld_bf_last_name_4'], FILTER_SANITIZE_STRING));
	$final_last_name = implode(",",$lastname);
	
	$phone = array(filter_var($_POST['ld_bf_phone_1'],$_POST['ld_bf_phone_2'],$_POST['ld_bf_phone_3'],$_POST['ld_bf_phone_4'], FILTER_SANITIZE_STRING));
	$final_phone = implode(",",$phone);
	
	$address = array(filter_var($_POST['ld_bf_address_1'],$_POST['ld_bf_address_2'],$_POST['ld_bf_address_3'],$_POST['ld_bf_address_4'], FILTER_SANITIZE_STRING));
	$final_address = implode(",",$address);
	
	$zip = array(filter_var($_POST['ld_bf_zip_1'],$_POST['ld_bf_zip_2'],$_POST['ld_bf_zip_3'],$_POST['ld_bf_zip_4'], FILTER_SANITIZE_STRING));
	$final_zip = implode(",",$zip);
	
	$city = array(filter_var($_POST['ld_bf_city_1'],$_POST['ld_bf_city_2'],$_POST['ld_bf_city_3'],$_POST['ld_bf_city_4'], FILTER_SANITIZE_STRING));
	$final_city = implode(",",$city);
	
	$state = array(filter_var($_POST['ld_bf_state_1'],$_POST['ld_bf_state_2'],$_POST['ld_bf_state_3'],$_POST['ld_bf_state_4'], FILTER_SANITIZE_STRING));
	$final_state = implode(",",$state);
	
	
	
	$prefered_password = array("on","Y",filter_var($_POST['preferred_password_min'],$_POST['preferred_password_max'], FILTER_SANITIZE_STRING));
	$final_pre_password = implode(",",$prefered_password);
	$final_lang_dd = filter_var($_POST['front_lang_dd'], FILTER_SANITIZE_STRING);
	
	$manage_from_fields=array(
        'ld_show_coupons_input_on_checkout'=>filter_var($_POST['coupon_checkout'], FILTER_SANITIZE_STRING),
		'ld_company_header_address'=>filter_var($_POST['company_header_address'], FILTER_SANITIZE_STRING),
		'ld_company_service_desc_status'=>filter_var($_POST['company_service_desc_status'], FILTER_SANITIZE_STRING),
		'ld_company_willwe_getin_status'=>filter_var($_POST['company_willwe_getin_status'], FILTER_SANITIZE_STRING),
		'ld_company_logo_display'=>filter_var($_POST['company_logo_display'], FILTER_SANITIZE_STRING),
		'ld_company_title_display'=>filter_var($_POST['company_title_display'], FILTER_SANITIZE_STRING),
		'ld_appointment_details_display'=>filter_var($_POST['appointment_details_display'], FILTER_SANITIZE_STRING),
		'ld_subheaders'=>filter_var($_POST['ld_subheaders'], FILTER_SANITIZE_STRING),
		'ld_bf_notes' => $final_notes,
		'ld_bf_first_name'=> $final_first_name,
		'ld_bf_last_name'=> $final_last_name,
		'ld_bf_phone'=> $final_phone,
		'ld_bf_address'=> $final_address,
		'ld_bf_zip_code'=> $final_zip,
		'ld_bf_city'=> $final_city,
		'ld_bf_state'=> $final_state,
		'ld_bf_password'=>$final_pre_password,
		'ld_front_language_selection_dropdown'=>$final_lang_dd
		);
	foreach($manage_from_fields as $option_key=>$option_value){
        $setting->set_option($option_key,$option_value);
    }
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='front_tooltips_setting'){
	 $tooltips_option=array(
        'ld_front_tool_tips_status'=>filter_var($_POST['status_front_tooltips'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_my_bookings'=>filter_var($_POST['tooltips_my_booking'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_postal_code'=>filter_var($_POST['tooltips_postal_code'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_services'=>filter_var($_POST['tooltips_service'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_addons_services'=>filter_var($_POST['tooltips_addons_service'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_frequently_discount'=>filter_var($_POST['tooltips_frequently_discount'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_time_slots'=>filter_var($_POST['tooltips_time_slots'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_personal_details'=>filter_var($_POST['tooltips_personal_details'], FILTER_SANITIZE_STRING),
        'ld_front_tool_tips_promocode'=>filter_var($_POST['tooltips_promocode'], FILTER_SANITIZE_STRING),
        'ld_front_tool_payment_method'=>filter_var($_POST['tooltips_payment_method'], FILTER_SANITIZE_STRING),
    );
    foreach($tooltips_option as $option_key=>$option_value){
        $add_tips=$setting->set_option($option_key,$option_value);
    }
    if($add_tips){
        echo filter_var("updated", FILTER_SANITIZE_STRING);
    }else{
        echo filter_var("Record Not Added", FILTER_SANITIZE_STRING);
    }
}

/*Update Login Image*/
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='delete_login_image'){
    $update_logo=array('ld_login_image'=>"");
    foreach($update_logo as $option_key=>$option_value){
        $logo=$setting->set_option($option_key,$option_value);
    }
}

/*Update Front Image*/
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='delete_front_imge'){
    $update_logo=array('ld_front_image'=>"");
    foreach($update_logo as $option_key=>$option_value){
        $logo=$setting->set_option($option_key,$option_value);
    }
}
/* Send Email Invoice */
if(isset($_POST['send_email_invoice']) && filter_var($_POST['send_email_invoice'], FILTER_SANITIZE_STRING) == '1') {
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$link = filter_var($_POST['link'], FILTER_SANITIZE_STRING);
	$company_email = $setting->get_option('ld_company_email');
	$company_name = $setting->get_option('ld_email_sender_name');
	$company_address = $setting->get_option('ld_company_address'); 
	if($setting->get_option('ld_client_email_notification_status') == 'Y'){
		$client_email_body = $link;
        if($setting->get_option('ld_smtp_hostname') != '' && $setting->get_option('ld_email_sender_name') != '' && $setting->get_option('ld_email_sender_address') != '' && $setting->get_option('ld_smtp_username') != '' && $setting->get_option('ld_smtp_password') != '' && $setting->get_option('ld_smtp_port') != ''){
			$mail->IsSMTP();
        }else{
            $mail->IsMail();
        }
        $mail->SMTPDebug  = 1;
        $mail->IsHTML(true);
        $mail->From = $company_email;
        $mail->FromName = $company_name;
        $mail->Sender = $email;
        $mail->AddAddress($email, $name);
        $mail->Subject = 'Invoice';
        $mail->Body = $client_email_body;
        $mail->send();
		$mail->ClearAllRecipients();
    }
}
/* Payment Start */
if(sizeof($purchase_check)>0){
	foreach($purchase_check as $key=>$val){
		if($val == 'Y'){
			echo filter_var($payment_hook->payment_settings_save_ajax_hook($key), FILTER_SANITIZE_STRING);
		}
	}
}
/* Payment End */
?>