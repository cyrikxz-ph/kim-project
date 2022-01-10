<?php  
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include(dirname(dirname(dirname(__FILE__))).'/header.php');
include(dirname(dirname(dirname(__FILE__))).'/config.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_connection.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_users.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_order_client_info.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_setting.php');
include(dirname(dirname(dirname(__FILE__)))."/objects/class_services.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_services_methods_units.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_design_settings.php");
include(dirname(dirname(dirname(__FILE__))).'/objects/class_general.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_front_first_step.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_adminprofile.php');
include(dirname(dirname(dirname(__FILE__))).'/objects/class_rating_review.php');

$database= new laundry_db();
$conn=$database->connect();
$database->conn=$conn;

$first_step=new laundry_first_step();
$first_step->conn=$conn;


$general=new laundry_general();
$general->conn=$conn;

$objadmin=new laundry_adminprofile();
$objadmin->conn=$conn;

$objrating_review=new laundry_rating_review();
$objrating_review->conn=$conn;

$user=new laundry_users();
$order_client_info=new laundry_order_client_info();
$settings=new laundry_setting();

$user->conn=$conn;
$order_client_info->conn=$conn;
$settings->conn=$conn;

$objservice = new laundry_services();
$objservice->conn=$conn;

$objservice_method_unit = new laundry_services_methods_units();
$objservice_method_unit->conn = $conn;

$objdesignset = new laundry_design_settings();
$objdesignset->conn = $conn;

$symbol_position=$settings->get_option('ld_currency_symbol_position');
$decimal=$settings->get_option('ld_price_format_decimal_places');

$lang = $settings->get_option("ld_language");
$label_language_values = array();
$language_label_arr = $settings->get_all_labelsbyid($lang);

if ($language_label_arr[1] != "" && $language_label_arr[3] != "" && $language_label_arr[4] != "" && $language_label_arr[5] != "")
{
	$label_decode_front = base64_decode($language_label_arr[1]);
	$label_decode_admin = base64_decode($language_label_arr[3]);
	$label_decode_error = base64_decode($language_label_arr[4]);
	$label_decode_extra = base64_decode($language_label_arr[5]);
		
	
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
    $default_language_arr = $settings->get_all_labelsbyid("en");
    
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
$calculation_policy = $settings->get_option("ld_calculation_policy");
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='get_existing_user_data'){
    $user->existing_username=trim(strip_tags(mysqli_real_escape_string($conn,filter_var($_POST['existing_username'], FILTER_SANITIZE_EMAIL))));
    $user->existing_password=md5(filter_var($_POST['existing_password'], FILTER_SANITIZE_STRING));
    $existing_login=$user->check_login();
    if(!$existing_login){
        $u_msg=array();
        $u_msg['status']="Incorrect Email Address or Password";
        echo json_encode($u_msg);die();
    }else{
        unset($_SESSION['ld_adminid']);
        $_SESSION['ld_login_user_id']=$existing_login[0];
        $_SESSION['ld_useremail']=$existing_login[1];

        $u_msg=array();
        $u_msg['status']="Login Sucessfull";
        $u_msg['id']=$existing_login[0];
        $u_msg['email']=$existing_login[1];
        $u_msg['password']=filter_var($_POST['existing_password'], FILTER_SANITIZE_STRING);
        $u_msg['firstname']=$existing_login[3];
        $u_msg['lastname']=$existing_login[4];
        $u_msg['phone']=$existing_login[5];
        $u_msg['zip']=$existing_login[6];
        $u_msg['address']=$existing_login[7];
        $u_msg['city']=$existing_login[8];
        $u_msg['state']=$existing_login[9];
        $u_msg['notes']=$existing_login[10];
        $u_msg['contact_status']=$existing_login[13];

        echo json_encode($u_msg);die();
    }
}/*  Code For Existing User Login  */
else if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='get_login_user_data'){
    if(!isset($_SESSION['ld_login_user_id'])){
        $u_msg=array();
        $u_msg['status']="No Login";
        echo json_encode($u_msg);die();
    }
    $user->existing_username=trim(strip_tags(mysqli_real_escape_string($conn,$_SESSION['ld_useremail'])));
    $existing_login=$user->check_login_user();
    if(!$existing_login){
        $u_msg=array();
        $u_msg['status']="Incorrect Email Address or Password";
        echo json_encode($u_msg);die();
    }else{
        unset($_SESSION['ld_adminid']);
        $_SESSION['ld_login_user_id']=$existing_login[0];
        $_SESSION['ld_useremail']=$existing_login[1];

        $u_msg=array();
        $u_msg['status']="Login Sucessfull";
        $u_msg['id']=$existing_login[0];
        $u_msg['email']=$existing_login[1];
        $u_msg['password']=$existing_login[2];
        $u_msg['firstname']=$existing_login[3];
        $u_msg['lastname']=$existing_login[4];
        $u_msg['phone']=$existing_login[5];
        $u_msg['zip']=$existing_login[6];
        $u_msg['address']=$existing_login[7];
        $u_msg['city']=$existing_login[8];
        $u_msg['state']=$existing_login[9];
        $u_msg['notes']=$existing_login[10];
        $u_msg['contact_status']=$existing_login[13];

        echo json_encode($u_msg);die();
    }
}
/* code for logout frontend */
elseif(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='logout'){
    if(isset($_SESSION['ld_login_user_id'])){
        unset($_SESSION['ld_login_user_id']);
        unset($_SESSION['ld_useremail']);
        echo filter_var("logout successful", FILTER_SANITIZE_STRING);
    }
}
/* get add-on on click of service */
elseif(isset($_POST['get_service_units'])) {
	$addons_data=$objservice_method_unit->get_units_for_front();
	$objservice->services_id=filter_var($_POST['service_id'], FILTER_SANITIZE_STRING);
	$_SESSION['service_id']=filter_var($_POST['service_id'], FILTER_SANITIZE_STRING);
	$countser = $objservice->get_count_service();
	$countserlim = $objservice->get_count_service_limit();
	if($countser < $countserlim)	{
    if(mysqli_num_rows($addons_data) > 0){
		?>
		<script>
		jQuery(document).ready(function() {
			jQuery('.ld-tooltip-addon').tooltipster({
				animation: 'grow',
				delay: 20,
				theme: 'tooltipster-shadow',
				trigger: 'hover'
			});
		});
		</script>
        <div class="ld-list-header">
            <h3 class="header3 header_bg"><?php echo filter_var($label_language_values['select_articles'], FILTER_SANITIZE_STRING); ?></h3>
			 <?php  if($settings->get_option("ld_front_tool_tips_status")=='on' && $settings->get_option("ld_front_tool_tips_addons_services")!=''){?>
			<a class="ld-tooltip-addon" href="#" data-toggle="tooltip" title="<?php echo $settings->get_option("ld_front_tool_tips_addons_services");?>."><i class="fa fa-info-circle fa-lg"></i></a>	
			<?php  } ?>
            <p class="ld-sub" style="display: none;"><?php echo filter_var($label_language_values['for_initial_laundry_only_contact_us_to_apply_to_recurrings'], FILTER_SANITIZE_STRING); ?></p>
        </div>
            <ul class="addon-service-list fl remove_addonsss">
                <?php 
                if(mysqli_num_rows($addons_data) > 0){
                    while($adonsdata =mysqli_fetch_array($addons_data)){ 
														$uname = "unit_".$adonsdata['id'];
                            ?>
                            <li class="ld-sm-6 ld-md-4 ld-lg-3 ld-xs-12 mb-15 add_addon_class_selected">
                            
                                <input type="checkbox" name="addon-checkbox" class="addon-checkbox addons_servicess_2" data-id="<?php echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" id="ld-addon-<?php echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" data-unamee="<?php echo filter_var($uname, FILTER_SANITIZE_STRING); ?>" />
                                <label class="ld-addon-ser border-c" for="ld-addon-<?php echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>"><span></span>
                                    <div class="addon-price"><?php echo filter_var($general->ld_price_format($adonsdata['base_price'],$symbol_position,$decimal), FILTER_SANITIZE_STRING); ?></div>
                                    <div class="ld-addon-img"><img src="<?php
                                        if($adonsdata['image'] == '' && $adonsdata['predefine_image'] == ''){
                                            echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'/assets/images/services/default.png';
                                        }
                                        else
                                        { 
																					if($adonsdata['image'] == ''){
                                            echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'/assets/images/article-icons/'.$adonsdata['predefine_image'];
																					}
																					else
																					{
																						echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'/assets/images/services/'.$adonsdata['image'];
																					}
                                        } ?>" /></div>

                                        <div class="addon-name fl ta-c"><?php echo filter_var($adonsdata['units_title'], FILTER_SANITIZE_STRING); ?></div>
                                </label>
                                <div class="ld-addon-count border-c  add_minus_button add_minus_buttonid<?php  echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>">
                                    <div class="ld-btn-group">
                                        <button id="minus<?php  echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" class="minus ld-btn-left ld-small-btn" type="button" data-units_id="<?php echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" data-unit_name="<?php echo filter_var($adonsdata['units_title'], FILTER_SANITIZE_STRING); ?>" data-unamee="<?php echo filter_var($uname, FILTER_SANITIZE_STRING); ?>" data-minlimit="<?php echo filter_var($adonsdata['minlimit'], FILTER_SANITIZE_STRING); ?>">-</button>

                                        <input type="text" value="0" class="ld-btn-text addon_qty data_addon_qtyrate qtyyy_<?php echo filter_var($uname, FILTER_SANITIZE_STRING); ?>" />

                                        <button id="add<?php  echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" data-db-qty="<?php echo filter_var($adonsdata["maxlimit"], FILTER_SANITIZE_STRING); ?>" class="add ld-btn-right float-right ld-small-btn" type="button" data-units_id="<?php echo filter_var($adonsdata['id'], FILTER_SANITIZE_STRING); ?>" data-unit_name="<?php echo filter_var($adonsdata['units_title'], FILTER_SANITIZE_STRING); ?>" data-unamee="<?php echo filter_var($uname, FILTER_SANITIZE_STRING); ?>" data-minlimit="<?php echo filter_var($adonsdata['minlimit'], FILTER_SANITIZE_STRING); ?>">+</button>
                                    </div>
                                </div>

                                
                            </li>
                        <?php
                    }
                }else{
                    ?>
                    <p class="ld-sub"><?php echo filter_var($label_language_values['extra_services_not_available'], FILTER_SANITIZE_STRING); ?></p>
                <?php 
                }
                ?>
            </ul>
        <?php 
    }else{
        echo filter_var($label_language_values["extra_services_not_available"], FILTER_SANITIZE_STRING);
    }
	}else{
		?><label class="empty_cart_error" id="empty_cart_error" style="display: block; color: rgb(255, 0, 0);"><?php echo filter_var($label_language_values['sorry_this_service_is_closed_now'], FILTER_SANITIZE_STRING); ?></label><?php
	}
}
elseif(isset($_POST['get_postal_code'])){
    @ob_clean();
    ob_start();
    $postal_code_list =$settings->get_option_postal();
    $res = explode(',',strtolower($postal_code_list));
    echo json_encode($res);
}

if(isset($_POST['get_search_staff_detail'])){
	$staff_list = filter_var($_POST['staff_search'], FILTER_SANITIZE_STRING);
	$get_staff =  explode(",",$staff_list); 
	 foreach($get_staff as $value){
		if($value!=""){ 
		$postal_code_staff_detail =$objadmin->get_search_staff_detail_byid($value);
		
		if($postal_code_staff_detail[1]!=''){
			$staff_image = "./assets/images/services/".$postal_code_staff_detail[1];
			$staff_image_mb = "../assets/images/services/".$postal_code_staff_detail[1];
		}else{
			$staff_image = "./assets/images/user.png";
			$staff_image_mb = "../assets/images/user.png";
		}
		
		echo '<li class="ld-sm-6 ld-md-4 ld-lg-3 ld-xs-12 remove_provider_class provider_select" data-id="'.$value.'">
				<input type="radio" name="provider-radio" data-staff_id ="'.$value.'" id="ld-provider-'.$value.'" class="provider_disable">
							<label class="ld-provider border-c img-circle" for="ld-provider-'.$value.'">
							<div class="ld-provider-img">
								<img class="ld-image img-circle ld-mb-show" src="'.$staff_image.'">
								<img class="ld-image img-circle ld-mb-hidden" src="'.$staff_image_mb.'">
							</div>

							</label>

							<div class="provider-name fl ta-c">'.$postal_code_staff_detail[0].'</div>';
		if($settings->get_option("ld_star_show_on_front") == "Y"){
			$objrating_review->staff_id = $value;
			$rating_details = $objrating_review->readall_by_staff_id();
			$rating_count = 0;
			$divide_count = 0;
			if(mysqli_num_rows($rating_details) > 0){
				while($row_rating_details = mysqli_fetch_assoc($rating_details)){
					$divide_count++;
					$rating_count+=(double)$row_rating_details['rating'];
				}
			}
			$rating_point = 0;
			if($divide_count != 0){
				$rating_point = round(($rating_count/$divide_count),1);
			}
			echo '<input id="staff_ratings" name="staff_ratings" class="rating staff_ratings_class staff_ratings'.$value.'" data-staff_id="'.$value.'" data-min="0" data-max="5" data-step="0.1" value="'.$rating_point.'" />';
		}
		echo '</li>';
		
 		}
		
	}
	?>
	<link rel="stylesheet" href="<?php  echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>/assets/css/star_rating.min.css" type="text/css" media="all">
	<script src="<?php  echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>/assets/js/star_rating_min.js" type="text/javascript"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.staff_ratings_class').each(function(){
				var order_id = jQuery(this).data("staff_id");
				jQuery(".staff_ratings"+order_id).rating('refresh', {disabled: true, showClear: false, showCaption: false});
			});
		});
	</script>
	<?php     
}

if(isset($_POST['select_language'])){
	$_SESSION['current_lang'] = filter_var($_POST['set_language'], FILTER_SANITIZE_STRING);
}
/**item remove from cart**/

if(isset($_POST['cart_item_remove'])){
	$json_array = array();
	$final_duration_value = 0;
	for ($i = 0; $i < (count($_SESSION['ld_cart'])); $i++)
	{
		$method_type = '';
		if ($_SESSION['ld_cart'][$i]['units_id'] == filter_var($_POST['unit_id'], FILTER_SANITIZE_STRING))
		{
			unset($_SESSION['ld_cart'][$i]);
		}
	}
	$_SESSION['ld_cart'] = array_values($_SESSION['ld_cart']);
	if(sizeof($_SESSION['ld_cart']) == 0){
		$json_array['status'] = "empty calculation";
	}else{
	/**calculation start**/
	$c_rates = 0;
	$total_price = 0;
	for ($i = 0; $i < (count($_SESSION['ld_cart'])); $i++)
	{
		$c_rates = ($c_rates + $_SESSION['ld_cart'][$i]['unit_rate']);
		$total_price += ((float) $_SESSION['ld_cart'][$i]['unit_rate']) * ((float) $_SESSION['ld_cart'][$i]["unit_qty"]);
	}
	
	$total = $total_price;
	$final_subtotal = $total_price;
	if ($settings->get_option('ld_tax_vat_status') == 'Y')
	{
	if ($settings->get_option('ld_tax_vat_type') == 'F')
		{
		$flatvalue = $settings->get_option('ld_tax_vat_value');
		$taxamount = $flatvalue;
		}
	  else
	if ($settings->get_option('ld_tax_vat_type') == 'P')
		{
		$percent = $settings->get_option('ld_tax_vat_value');
		$percentage_value = $percent / 100;
		$taxamount = $percentage_value * $final_subtotal;
		}
	}
	else
	{
	$taxamount = 0;
	}

	if ($settings->get_option('ld_partial_deposit_status') == 'Y')
	{
		$grand_total = $final_subtotal + $taxamount;
		if ($settings->get_option('ld_partial_type') == 'F')
		{
			$p_deposite_amount = $settings->get_option('ld_partial_deposit_amount');
			$partial_amount = $p_deposite_amount;
			$remain_amount = $grand_total - $partial_amount;
		}
		elseif ($settings->get_option('ld_partial_type') == 'P')
		{
			$p_deposite_amount = $settings->get_option('ld_partial_deposit_amount');
			$percentages = $p_deposite_amount / 100;
			$partial_amount = $grand_total * $percentages;
			$remain_amount = $grand_total - $partial_amount;
		}
		else
		{
			$partial_amount = 0; 
			$remain_amount = 0; 
		}
	}
	else
	{
	$partial_amount = 0;
	$remain_amount = 0;
	}
	
	$json_array['status'] = "cart not empty";
	$json_array['cart_discount'] = $general->ld_price_format(0, $symbol_position, $decimal);
	$json_array['partial_amount'] = $general->ld_price_format($partial_amount, $symbol_position, $decimal);
	$json_array['remain_amount'] = $general->ld_price_format($remain_amount, $symbol_position, $decimal);
	$json_array['cart_tax'] = $general->ld_price_format($taxamount, $symbol_position, $decimal);
	$json_array['total_amount'] = $general->ld_price_format(($final_subtotal + $taxamount) , $symbol_position, $decimal);
	$json_array['cart_sub_total'] = $general->ld_price_format($total, $symbol_position, $decimal);
	/* calculation end */
	}
	echo json_encode($json_array);
}

?>
