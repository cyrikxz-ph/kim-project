<?php  
error_reporting(E_ALL);
ini_set('display_errors', 1);
include(dirname(dirname(dirname(__FILE__))) . "/config.php");
include(dirname(dirname(dirname(__FILE__))) . "/objects/class_connection.php");
include(dirname(dirname(dirname(__FILE__))) . "/header.php");
include(dirname(dirname(dirname(__FILE__))) . "/objects/class_services_methods_units.php");
include(dirname(dirname(dirname(__FILE__))) . "/objects/class_setting.php");
$con = new laundry_db();
$conn = $con->connect();
$objservice_method_unit = new laundry_services_methods_units();
$objservice_method_unit->conn = $conn;
$settings = new laundry_setting();
$settings->conn = $conn;
$method_default_design=$settings->get_option('ld_method_default_design');
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
if (isset($_POST['getservice_method_units'])) {
    $res = $objservice_method_unit->get_all_units();
    $i = 1;
	/* ?>
		<link rel="stylesheet" href="<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>/assets/css/bootstrap-toggle.min.css" type="text/css" media="all">
		<script src="<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>/assets/js/bootstrap-toggle.min.js" type="text/javascript" ></script>
		
	<?php  */
    while ($arrs = mysqli_fetch_array($res)) {
        $i++;
        ?>
		
        <div class="col-sm-12 col-md-12 col-xs-12">
            <li class="panel panel-default ld-clean-services-panel mysortlist_units" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>"  id="service_method_units_<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <div class="lda-col8 col-sm-8 col-md-9 np">
							<div class="pull-left">
								<i class="fa fa-th-list"></i>
							</div>
                            <span class="ld-clean-service-title-name" id="method_unit_name<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>"><?php echo filter_var($arrs['units_title'], FILTER_SANITIZE_STRING); ?></span>
                        </div>
                        <div class="pull-right lda-col4 col-sm-4 col-md-3 np">
						
                            <div class="lda-col4 lda-smu-endis">
                                <label for="sevice-endis-<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
									<input class='myservices_methods_units_status' data-toggle="toggle" data-size="small" type='checkbox' data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" <?php  if ($arrs['status'] == 'E') { echo filter_var("checked", FILTER_SANITIZE_STRING); } else { echo filter_var("", FILTER_SANITIZE_STRING); } ?> id="sevice-endis-<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" data-on="<?php echo filter_var($label_language_values['enable'], FILTER_SANITIZE_STRING);?>" data-off="<?php echo filter_var($label_language_values['disable'], FILTER_SANITIZE_STRING);?>" data-onstyle='success' data-offstyle='danger' />
									
							
                                </label>
                            </div>
                            <div class="pull-right lda-smu-del-toggle">
                                <div class="lda-col1">
                                <?php 
									$t = $objservice_method_unit->method_unit_isin_use($arrs['id']);
									if($t>0){
										?>
										<a data-toggle="popover" class="delete-clean-service-btn pull-right btn-circle btn-danger btn-sm" rel="popover" data-placement='top' title="<?php echo filter_var($label_language_values['unit_is_booked'], FILTER_SANITIZE_STRING);?>"> <i class="fa fa-ban"></i></a>
									<?php 
									}
									else
									{
										?>
                                    <a id="ld-delete-service-unit"  data-toggle="popover" class="delete-clean-service-unit-btn pull-right btn-circle btn-danger btn-sm" rel="popover" data-placement='left' title="<?php echo filter_var($label_language_values['delete_this_service_unit'], FILTER_SANITIZE_STRING);?>"> <i class="fa fa-trash" title="<?php echo filter_var($label_language_values['delete_service_unit'], FILTER_SANITIZE_STRING);?>"></i></a>
                                    <div id="popover-delete-service" style="display: none;">
                                        <div class="arrow"></div>
                                        <table class="form-horizontal" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <a data-service_method_unitid="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($label_language_values['delete'], FILTER_SANITIZE_STRING);?>" class="btn btn-danger btn-sm service-methods-units-delete-button"><?php echo filter_var($label_language_values['yes'], FILTER_SANITIZE_STRING);?>
                                                    </a>
                                                    <button id="ld-close-popover-delete-service" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo filter_var($label_language_values['cancel'], FILTER_SANITIZE_STRING);?>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
							<?php  } ?>
                                </div>
                                <div class="ld-show-hide pull-right">
                                    <input type="checkbox" name="ld-show-hide" class="ld-show-hide-checkbox" id="sp<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" >
                                    <label class="ld-show-hide-label" for="sp<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>"></label>
                                </div>
                            </div>
                        </div>
                    </h4>
                </div>
               <div id="detailmes_sp<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="servicemeth_details panel-collapse collapse">
                    <div class="panel-body">
                        <div class="ld-service-collapse-div col-sm-12 col-md-6 col-lg-6 col-xs-12">
                                <form id="service_method_unit_price<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" method="" type="" class="slide-toggle" >
                                    <table class="ld-create-service-table">
                                        <tbody>
                                        <tr>
                                            <td><label for=""><?php echo filter_var($label_language_values['article_name'], FILTER_SANITIZE_STRING);?></label></td>
                                            <td><div class="col-xs-12"><input type="text" name="unitname" id="txtedtunitname<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="form-control mytxtservice_method_uniteditname<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($arrs['units_title'], FILTER_SANITIZE_STRING); ?>"/></div></td>
                                        </tr>
																				<tr>
																					<td><label for="ld-service-desc"><?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?></label></td>
																					<td>
																							<div class="ld-clean-service-image-uploader">
																									<?php 
																									if($arrs['image']==''){
																											$imagepath=SITE_URL."assets/images/default_service.png";
																									}else{
																											$imagepath=SITE_URL."assets/images/services/".$arrs['image'];
																									}
																									?>
																									<img data-imagename="" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>serviceimage" src="<?php echo filter_var($imagepath, FILTER_SANITIZE_STRING);?>" class="ld-clean-service-image br-100" height="100" width="100">
																									<?php 
																									if($arrs['image']==''){
																											?>
																											<label for="ld-upload-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="ld-clean-service-img-icon-label old_cam_ser<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
																													<i class="ld-camera-icon-common br-100 fa fa-camera" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>camera"></i>
																													<i class="pull-left fa fa-plus-circle fa-2x" id="ctsc<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>plus"></i>
																											</label>
																									<?php 
																									}
																									?>
																									<input data-us="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="hide ld-upload-images" type="file" name="" id="ld-upload-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" />
																									<label for="ld-upload-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="ld-clean-service-img-icon-label new_cam_ser ser_cam_btn<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
																											<i class="ld-camera-icon-common br-100 fa fa-camera" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>camera"></i>
																											<i class="pull-left fa fa-plus-circle fa-2x" id="ctsc<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>plus"></i>
																									</label>
																									<?php 
																									if($arrs['image']!==''){
																											?>
																											<a id="ld-remove-service-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-pclsid="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-service_id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" class="pull-left br-100 btn-danger bt-remove-service-img btn-xs ser_del_icon ser_new_del<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" rel="popover" data-placement='left' title="<?php echo filter_var($label_language_values['remove_image'], FILTER_SANITIZE_STRING);?>"> <i class="fa fa-trash" title="<?php echo filter_var($label_language_values['remove_service_image'], FILTER_SANITIZE_STRING);?>"></i></a>
																									<?php 
																									}
																									
																									?>
																									<a id="ld-remove-service-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-pclsid="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-service_id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" class="pull-left br-100 btn-danger bt-remove-service-img btn-xs new_del_ser del_btn_popup<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" rel="popover" data-placement='left' title="<?php echo filter_var($label_language_values['remove_image'], FILTER_SANITIZE_STRING);?>"> <i class="fa fa-trash" title="<?php echo filter_var($label_language_values['remove_service_image'], FILTER_SANITIZE_STRING);?>" ></i></a>
																									<div id="popover-ld-remove-service-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" style="display: none;">
																											<div class="arrow"></div>
																											<table class="form-horizontal" cellspacing="0">
																													<tbody>
																													<tr>
																															<td>
																																	<a href="javascript:void(0)" id="" value="Delete" data-service_id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" class="btn btn-danger btn-sm delete_image" type="submit"><?php echo filter_var($label_language_values['yes'], FILTER_SANITIZE_STRING);?></a>
																																	<a href="javascript:void(0)" id="ld-close-popover-service-image" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo filter_var($label_language_values['cancel'], FILTER_SANITIZE_STRING);?></a>
																															</td>
																													</tr>
																													</tbody>
																											</table>
																									</div>
																							</div>
																							<label class="error_image" ></label>
																					<span class="lda-addon-img-icon"><?php echo filter_var($label_language_values['or'], FILTER_SANITIZE_STRING);?></span>
																					<div class="lda-addons-imagelist-dropdown fl">
																						<div class="lda-addons-selection-main">
																							<div class="lda-addon-is update_id" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" title="<?php echo filter_var($label_language_values['choose_your_article_image'], FILTER_SANITIZE_STRING);?>">
																							<?php  
																								if($arrs['predefine_image']!=""){
																							?>
																								<div class="lda-addons-list" id="addonid_<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="<?php echo filter_var($arrs['predefine_image'], FILTER_SANITIZE_STRING);?>">
																									<img class="lda-addons-image" src="../assets/images/article-icons/<?php echo filter_var($arrs['predefine_image'], FILTER_SANITIZE_STRING);?>" title="<?php echo filter_var($label_language_values['addon_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name"><?php echo filter_var($arrs['units_title'], FILTER_SANITIZE_STRING);?></h3>
																								</div>
																							<?php 
																								}
																								else
																								{
																							?>	
																								<div class="lda-addons-list" id="addonid_<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="">
																									<i class="lda-addons-image icon-puzzle icons"></i>
																									<h3 class="lda-addons-name"><?php echo filter_var($label_language_values['choose_your_article_image'], FILTER_SANITIZE_STRING);?></h3>
																								</div>
																							<?php
																								}
																							?>	
																							</div>
																							<div class="lda-addons-dropdown display_update_<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" style="display: none;">
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_22690.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_22690.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Top</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_23440.png" >
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_23440.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">T-shirt</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_23843.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_23843.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Men Shorts</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_29883.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_29883.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Pant</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_39670.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_39670.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Shirt</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_41290.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_41290.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Skirt</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_53632.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_53632.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Women Shorts</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_56238.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_56238.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Maxi</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_57660.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_57660.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Gown</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_61466.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_61466.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Blazer</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_71894.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_71894.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Tie</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_74051.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_74051.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Dress</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_75730.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_75730.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Jacket</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_81177.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_81177.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Trouser</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_98252.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_98252.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Suit</h3>
																								</div>
																								<div class="lda-addons-list select_addons" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-name="unit_99817.png">
																										<img class="lda-addons-image" src="../assets/images/article-icons/unit_99817.png" title="<?php echo filter_var($label_language_values['article_image'], FILTER_SANITIZE_STRING);?>">
																										<h3 class="lda-addons-name">Jeans</h3>
																								</div>
																							</div>
																						</div>
																					</div>

																							<div id="ld-image-upload-popuppcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" class="ld-image-upload-popup modal fade" tabindex="-1" role="dialog">
																									<div class="vertical-alignment-helper">
																											<div class="modal-dialog modal-md vertical-align-center">
																													<div class="modal-content">
																															<div class="modal-header">
																																	<div class="col-md-12 col-xs-12">
																																			<a data-us="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" class="btn btn-success ld_upload_unit_img" data-imageinputid="ld-upload-imagepcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>"><?php echo filter_var($label_language_values['crop_and_save'], FILTER_SANITIZE_STRING);?></a>
																																			<button type="button" class="btn btn-default hidemodal" data-dismiss="modal" aria-hidden="true"><?php echo filter_var($label_language_values['cancel'], FILTER_SANITIZE_STRING);?></button>
																																	</div>
																															</div>
																															<div class="modal-body">
																																	<img id="ld-preview-imgpcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" style="width: 100%;"  />
																															</div>
																															<div class="modal-footer">
																																	<div class="col-md-12 np">
																																			<div class="col-md-12 np">
																																					<div class="col-md-4 col-xs-12">
																																							<label class="pull-left"><?php echo filter_var($label_language_values['file_size'], FILTER_SANITIZE_STRING);?></label> <input type="text" class="form-control" id="pclsfilesize<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" name="filesize" />
																																					</div>
																																					<div class="col-md-4 col-xs-12">
																																							<label class="pull-left">H</label> <input type="text" class="form-control" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>h" name="h" />
																																					</div>
																																					<div class="col-md-4 col-xs-12">
																																							<label class="pull-left">W</label> <input type="text" class="form-control" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>w" name="w" />
																																					</div>
																																					
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>x1" name="x1" />
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>y1" name="y1" />
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>x2" name="x2" />
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>y2" name="y2" />
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>id" name="id" value="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" />
																																					<input id="pclsctimage<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>" type="hidden" name="ctimage" />
																																					<input type="hidden" id="recordid" value="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>">
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>ctimagename" class="pclsimg" name="ctimagename" value="<?php echo filter_var($arrs['image'], FILTER_SANITIZE_STRING);?>" />
																																					<input type="hidden" id="pcls<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING);?>newname" value="unit_" />
																																			</div>
																																	</div>
																															</div>
																													</div>
																											</div>
																									</div>
																							</div>
																					</td>
																			</tr>
                                        <tr>
                                            <td><label for=""><?php echo filter_var($label_language_values['base_price'], FILTER_SANITIZE_STRING);?></label></td>
                                            <td>
                                                <div class="col-xs-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="unit-price-currency"><?php echo filter_var($settings->get_option('ld_currency_symbol'), FILTER_SANITIZE_STRING);?></span></span>
                                                        <input type="text" name="baseprice" id="txtedtunitbaseprice<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="form-control mytxtservice_method_uniteditbase_price<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" placeholder="US Dollar" value="<?php echo filter_var($arrs['base_price'], FILTER_SANITIZE_STRING); ?>">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
																				<tr>
                                            <td><label for=""><?php echo filter_var($label_language_values['min_limit'], FILTER_SANITIZE_STRING);?></label></td>
                                            <td><div class="col-xs-12"><input type="text" name="txtminlimit" class="form-control mytxt_service_method_editminlimit<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" id="txtedtunitminlimit<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($arrs['minlimit'], FILTER_SANITIZE_STRING); ?>"/></div></td>
                                        </tr>
                                        <tr>
                                            <td><label for=""><?php echo filter_var($label_language_values['max_limit'], FILTER_SANITIZE_STRING);?></label></td>
                                            <td><div class="col-xs-12"><input type="text" name="txtmaxlimit" class="form-control mytxt_service_method_editmaxlimit<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" id="txtedtunitmaxlimit<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" value="<?php echo filter_var($arrs['maxlimit'], FILTER_SANITIZE_STRING); ?>"/></div></td>
                                        </tr>
                                        <tr>
																					<td></td>
																					<td>
																							<div class="col-xs-12"><a data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" class="btn btn-success ld-btn-width mybtnservice_method_unitupdate" ><?php echo filter_var($label_language_values['update'], FILTER_SANITIZE_STRING);?></a></div>
																					</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                        </div>
                        <div class="manage-unit-price-container<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?> col-sm-12 col-md-6 col-lg-6 col-xs-12 mt-20" style="display:<?php    if($settings->get_option('ld_calculation_policy') == "M"){echo filter_var("none", FILTER_SANITIZE_STRING);}else{echo filter_var("block", FILTER_SANITIZE_STRING);} ?>">
                            <div class="manage-unit-price-main col-sm-12 col-md-12 col-lg-12 col-xs-12">
								<h4><?php echo filter_var($label_language_values['service_unit_price_rules'], FILTER_SANITIZE_STRING);?></h4>
                                    <ul>
                                        <li class="form-group">
                                            <label class="col-sm-2 col-xs-12 np" for="addon_qty_6"><?php echo filter_var($label_language_values['base_price'], FILTER_SANITIZE_STRING);?></label>
                                            <div class="col-xs-12 col-sm-2">
                                            <input class="form-control" placeholder="1" value="1" id="" type="text" readonly="readonly" /></div>
                                            <div class="price-rules-select">
                                                <select class="form-control" id="">
                                                    <option selected="" readonly value="=">= </option>
                                                </select>
                                            </div>
                                            <div class="col-xs-12 col-sm-3">
                                                <input  class="pull-left form-control" readonly value="<?php echo filter_var($arrs['base_price'], FILTER_SANITIZE_STRING); ?>" placeholder="<?php echo filter_var($label_language_values['price'], FILTER_SANITIZE_STRING);?>" type="text" />
                                            </div>
                                        </li>
                                    </ul>
                                    <ul class="myunitspricebyqty<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
                                        <li class="form-group">
                                            <form id="mynewaddedform_units<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
                                                <label class="col-sm-2 col-xs-12 np" for="addon_qty_6"><?php echo filter_var($label_language_values['Quantity'], FILTER_SANITIZE_STRING);?></label>
                                                <div class="col-xs-12 col-sm-2">
                                                    <input required class="form-control mynewqty<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" name="mynewssqty" id="mynewaddedqty_units<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" placeholder="1" value="" type="text"/>
                                                </div>
                                                <div class="price-rules-select">
                                                    <select class="form-control mynewrule<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>">
                                                        <option selected value="E">=</option>
                                                        <option value="G"> &gt; </option>
                                                    </select>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <input name="mynewssprice" id="mynewaddedprice_units<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" required class="pull-left form-control mynewprice<?php  echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" value="" placeholder="<?php echo filter_var($label_language_values['price_per_unit'], FILTER_SANITIZE_STRING);?>" type="text" />
                                                </div>
                                                &nbsp; <a href="javascript:void(0);" data-id="<?php echo filter_var($arrs['id'], FILTER_SANITIZE_STRING); ?>" data-inspector="0" class="btn btn-circle btn-success add-addon-price-rule form-group new-manage-price-list myaddnewatyrule_units"><?php echo filter_var($label_language_values['add_new'], FILTER_SANITIZE_STRING);?></a>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                        </div>
                        </div>
                        
                    </div>
                </div>
            </li>
        </div>
    <?php  } ?>
	<div class="col-sm-12 col-md-12 col-xs-12">
    <li>
        
        <div class="panel panel-default ld-clean-services-panel ld-add-new-price-unit">
            <div class="panel-heading">
                <h4 class="panel-title">
					
                    <div class="lda-col6">
                        <span class="ld-service-title-name"></span>
                    </div>
                    <div class="pull-right lda-col6">
                        <div class="pull-right">
                            <div class="ld-show-hide pull-right">
                                <input type="checkbox" name="ld-show-hide" checked="checked" class="ld-show-hide-checkbox" id="sp0" >
                                <label class="ld-show-hide-label" for="sp0"></label>
                            </div>
                        </div>
                    </div>
                </h4>
            </div>
            <div id="" class="panel-collapse collapse in detail_sp0">
                <div class="panel-body">
                    <div class="ld-service-collapse-div col-sm-12 col-md-12 col-xs-12 np">
                        <form id="service_method_unitaddform" method="" type="" class="slide-toggle" >
                            <table class="ld-create-service-table">
                                <tbody>
                                <tr>
                                    <td><label for=""><?php echo filter_var($label_language_values['article_name'], FILTER_SANITIZE_STRING);?></label></td>
                                    <td><div class="col-xs-12"><input type="text" class="form-control mytxt_service_method_unitname" name="unitprice" id="txtunitnamess" /></div></td>
                                </tr>
                                <tr>
                                    <td><label for=""><?php echo filter_var($label_language_values['base_price'], FILTER_SANITIZE_STRING);?></label></td>
                                    <td><div class="col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="unit-price-currency"><?php echo filter_var($settings->get_option('ld_currency_symbol'), FILTER_SANITIZE_STRING);?></span></span>
                                                <input type="text" class="form-control mytxt_service_method_unitbaseprice" id="txtbasepricess" name="baseprice" placeholder="US Dollar">
                                            </div>
                                                 <label for="txtbasepricess" generated="true" class="error"></label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
									<td></td>
                                    <td>
                                       <div class="col-xs-12"> <a class="btn btn-success ld-btn-width mybtnservice_method_unitsave"  ><?php echo filter_var($label_language_values['save'], FILTER_SANITIZE_STRING);?></a></div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </li>
</div>
<?php 
}
else if (isset($_POST['deleteid'])) {
    $objservice_method_unit->id = filter_var($_POST['deleteid'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->delete_services_method_unit();
}
else if(isset($_POST['pos']) && isset($_POST['ids']))
{
    echo filter_var("yes in ", FILTER_SANITIZE_STRING);
    echo count($_POST['ids']);
    for($i=0;$i<count($_POST['ids']);$i++)
    {
        $objservice_method_unit->position=filter_var($_POST['pos'][$i], FILTER_SANITIZE_STRING);
        $objservice_method_unit->id=filter_var($_POST['ids'][$i], FILTER_SANITIZE_STRING);
        $objservice_method_unit->updateposition();
    }
}
elseif (isset($_POST['changestatus'])) {
    $objservice_method_unit->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->status = filter_var($_POST['changestatus'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->changestatus();
}
elseif (isset($_POST['changesinfotatus'])) {
    $objservice_method_unit->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->status = filter_var($_POST['changesinfotatus'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->changesinfotatus();
}
elseif (isset($_POST['operationinsert'])) {
    $objservice_method_unit->units_title = filter_var($_POST['unit_name'], FILTER_SANITIZE_STRING);
    $t = $objservice_method_unit->check_same_title();
    $cnt = mysqli_num_rows($t);
    if($cnt == 0){
        $objservice_method_unit->units_title = mysqli_real_escape_string($conn, ucwords(filter_var($_POST['unit_name'], FILTER_SANITIZE_STRING)));
        $objservice_method_unit->base_price = filter_var($_POST['base_price'], FILTER_SANITIZE_STRING);
        $objservice_method_unit->maxlimit = 1;
        $objservice_method_unit->status = 'D';
        $objservice_method_unit->add_services_method_unit();
    }
    else{
        echo filter_var("1", FILTER_SANITIZE_STRING);
    }
}
elseif (isset($_POST['operationedit'])) {
    $objservice_method_unit->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);

    $objservice_method_unit->units_title = filter_var(mysqli_real_escape_string($conn, ucwords(filter_var($_POST['units_title'], FILTER_SANITIZE_STRING))), FILTER_SANITIZE_STRING);
    $objservice_method_unit->base_price = filter_var($_POST['base_price'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->minlimit = filter_var($_POST['minlimit'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->maxlimit = filter_var($_POST['maxlimit'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->predefine_image = filter_var($_POST['predefineimage'], FILTER_SANITIZE_STRING);
    $objservice_method_unit->update_services_method_unit();
		
		/* REMOVE UNSED IMAGES FROM FOLDER */
    $used_images = $objservice_method_unit->get_used_images();
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
?>
<script type="text/javascript">
    var ajax_url = '<?php echo filter_var(AJAX_URL, FILTER_VALIDATE_URL);?>';
    var ajaxObj = {'ajax_url':'<?php echo filter_var(AJAX_URL, FILTER_VALIDATE_URL);?>'};
    var imgObj={'img_url':'<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'assets/images/';?>'};
</script>