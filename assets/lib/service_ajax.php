<?php  
include(dirname(dirname(dirname(__FILE__)))."/config.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_connection.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_services.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_design_settings.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_setting.php");
include(dirname(dirname(dirname(__FILE__)))."/header.php");
$con = new laundry_db();
$conn = $con->connect();
$objservice = new laundry_services();
$objservice->conn = $conn;
$objdesignset = new laundry_design_settings();
$objdesignset->conn = $conn;
$settings = new laundry_setting();
$settings->conn = $conn;
$lang = $settings->get_option("ld_language");
$label_language_values = array();
$language_label_arr = $settings->get_all_labelsbyid($lang);
if ($language_label_arr[1] != "" || $language_label_arr[3] != "" || $language_label_arr[4] != "" || $language_label_arr[5] != "")
{
	$default_language_arr = $settings->get_all_labelsbyid("en");
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
if(isset($_POST['pos']) && isset($_POST['ids']))
{
    echo filter_var("yes in ", FILTER_SANITIZE_STRING);
    echo count(filter_var($_POST['ids'], FILTER_SANITIZE_STRING));
    for($i=0;$i<count(filter_var($_POST['ids'], FILTER_SANITIZE_STRING));$i++)
    {
        $objservice->position=filter_var($_POST['pos'][$i], FILTER_SANITIZE_STRING);
        $objservice->id=filter_var($_POST['ids'][$i], FILTER_SANITIZE_STRING);
        $objservice->updateposition();
    }
}
else if(isset($_POST['deleteid']))
{
    $objservice->id=filter_var($_POST['deleteid'], FILTER_SANITIZE_STRING);
    chmod(dirname(dirname(dirname(__FILE__)))."/assets/images/services", 0777);
	
    /* CODE TO DELETE ADDONS AND SERVICE IMAGE BEFORE DELETE SERVICE FORM TABLE */ 
	$methods = $objservice->get_exist_methods_by_serviceid(filter_var($_POST['deleteid'], FILTER_SANITIZE_STRING));
	
	while($r = mysqli_fetch_array($methods)){
		$methods_units = $objservice->get_exist_methods_units_by_methodid($r['id']);
		while($t = mysqli_fetch_array($methods_units))
		{
			$methods_units_rate = $objservice->get_exist_methods_units_rate_by_unitid($t['id']);
			while($mur = mysqli_fetch_array($methods_units_rate))
			{
				/* Service method unit rate delete */
				$objservice->delete_service_method_unit_rate($mur['id']);
			}
			/* Service method unit delete */
			$objservice->delete_method_unit($t['id']);
		}	   
		/* Service method delete */
		$objservice->delete_method($r['id']);
	}
    $objservice->delete_service();
}
elseif(isset($_POST['changestatus']))
{
    $objservice->id=filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $objservice->status = filter_var($_POST['changestatus'], FILTER_SANITIZE_STRING);
    $objservice->changestatus();
	if($objservice){
		if(filter_var($_POST['changestatus'], FILTER_SANITIZE_STRING)=='E'){
             echo filter_var($label_language_values['service_enable'], FILTER_SANITIZE_STRING);
		}else{
             echo filter_var($label_language_values['service_disable'], FILTER_SANITIZE_STRING);
		}
	}
}
elseif(isset($_POST['operationadd']))
{
    chmod(dirname(dirname(dirname(__FILE__)))."/assets/images/services", 0777);
    $objservice->title = filter_var(filter_var($_POST['title'], FILTER_SANITIZE_STRING), FILTER_SANITIZE_STRING);
    $t = $objservice->check_same_title();
    $cnt = mysqli_num_rows($t);
    if($cnt == 0){
        $objservice->color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
        $objservice->title = filter_var(mysqli_real_escape_string($conn,ucwords(filter_var($_POST['title'], FILTER_SANITIZE_STRING))), FILTER_SANITIZE_STRING);
        $objservice->description = mysqli_real_escape_string($conn,filter_var($_POST['description'], FILTER_SANITIZE_STRING));
        $objservice->status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
        $objservice->position = filter_var($_POST['position'], FILTER_SANITIZE_STRING);
				$objservice->service_limit = filter_var($_POST['max_order_per_day'], FILTER_SANITIZE_STRING);
        $insertid = $objservice->add_service();
        $objservice->image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
        $objservice->update_recordfor_image($insertid);
        /* REMOVE UNSED IMAGES FROM FOLDER */
        $used_images = $objservice->get_used_images();
        $imgarr = array();
        while($img  = mysqli_fetch_array($used_images)){
            $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[0]);
            array_push($imgarr,$filtername);
						$filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[1]);
            array_push($imgarr,$filtername);
						$filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[2]);
            array_push($imgarr,$filtername);
        }
        array_push($imgarr,"default");
        array_push($imgarr,"default_service");
        array_push($imgarr,"default_service1");
        print_r($imgarr);
        $dir = dirname(dirname(dirname(__FILE__)))."/assets/images/services/";
        $cnt = 1;
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if($cnt > 2){
                    $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
                    if (in_array($filtername, $imgarr)) {
                    }
                    else{
                        unlink(dirname(dirname(dirname(__FILE__)))."/assets/images/services/".$file);
                    }
                }
                $cnt++;
            }
            closedir($dh);
        }
    }
    else{
       echo filter_var("1", FILTER_SANITIZE_STRING);
    }
}
elseif(isset($_POST['operationedit']))
{
    chmod(dirname(dirname(dirname(__FILE__)))."/assets/images/services", 0777);
    $objservice->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $objservice->color = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
    $objservice->title = filter_var(mysqli_real_escape_string($conn,ucwords(filter_var($_POST['title'], FILTER_SANITIZE_STRING))), FILTER_SANITIZE_STRING);
    $objservice->description = mysqli_real_escape_string($conn,filter_var($_POST['description'], FILTER_SANITIZE_STRING));
    $objservice->image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
    $objservice->service_limit = filter_var($_POST['max_order_per_day'], FILTER_SANITIZE_STRING);
    $objservice->update_service();
    /* REMOVE UNSED IMAGES FROM FOLDER */
    $used_images = $objservice->get_used_images();
    $imgarr = array();
    while($img  = mysqli_fetch_array($used_images)){
        $filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[0]);
        array_push($imgarr,$filtername);
				$filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[1]);
        array_push($imgarr,$filtername);
				$filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $img[2]);
        array_push($imgarr,$filtername);
    }
    array_push($imgarr,"default");
    array_push($imgarr,"default_service");
    array_push($imgarr,"default_service1");
    print_r($imgarr);
    $dir = dirname(dirname(dirname(__FILE__)))."/assets/images/services/";
    $cnt = 1;
    if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if($cnt > 2){
					$filtername = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
					if (in_array($filtername, $imgarr)) {
					}
					else{
							unlink(dirname(dirname(dirname(__FILE__)))."/assets/images/services/".$file);
					}
				}
				$cnt++;
			}
			closedir($dh);
    }
}
elseif(isset($_POST['assigndesign']))
{
    $objdesignset->title=filter_var($_POST['divname'], FILTER_SANITIZE_STRING);
    $objdesignset->design=filter_var($_POST['designid'], FILTER_SANITIZE_STRING);
    $having = $objdesignset->readone();
    if(count($having[0])>0)
    {
        $objdesignset->update_setting_design();
    }
    else
    {
        $objdesignset->add_setting_design();
    }
}
/*Delete Service Image*/
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='delete_image'){
    $objservice->id=filter_var($_POST['service_id'], FILTER_SANITIZE_STRING);
    $objservice->image="";
    $del_image=$objservice->update_image();
}
?>