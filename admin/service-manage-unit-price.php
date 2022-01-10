<?php  
include(dirname(__FILE__).'/header.php');
include(dirname(dirname(__FILE__)) . "/objects/class_services_methods_units.php");
include(dirname(__FILE__).'/user_session_check.php');
$con = new laundry_db();
$conn = $con->connect();
$objservice_m_unit = new laundry_services_methods_units();
$objservice_m_unit->conn = $conn;
?>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<link rel="stylesheet" href="<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>assets/css/bootstrap-toggle.min.css" type="text/css" media="all">
<script src="<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL); ?>assets/js/bootstrap-toggle.min.js" type="text/javascript" ></script>
<div id="lda-clean-services-panel" class="panel tab-content">
	<div class="panel-body">
		<div class="ld-clean-service-details tab-content col-md-12 col-sm-12 col-lg-12 col-xs-12">
			
			<div class="ld-clean-service-top-header">
				<span class="ld-clean-service-service-name pull-left mymethodtitleforunit"></span>
				
				<div class="pull-right lda-unit-button-top">
					<table>
						<tbody>
							<tr>
								<td>
										
										<div id="service-front-view" class="modal fade">
												<div class="modal-dialog modal-sm modal-md ">
														<div class="modal-content">
																<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
																		<h4 class="modal-title"><?php echo filter_var($label_language_values['method_units_front_view'], FILTER_SANITIZE_STRING);	?></h4>
																		<h4 class="modal-titletester"></h4>
																</div>
																<div class="modal-body mymodalbody">
																</div>
																<div class="modal-footer cb">
																		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo filter_var($label_language_values['close'], FILTER_SANITIZE_STRING);	?></button>
																</div>
														</div>
												</div>
										</div>
								</td>
								<td>
									<button id="ld-add-new-price-unit" class="btn btn-success" value="add new service"><i class="fa fa-plus"></i><?php echo filter_var($label_language_values['add_unit'], FILTER_SANITIZE_STRING);	?></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
						
			</div>
			<div id="hr"></div>
			<div class="tab-pane active">
				<div class="tab-content ld-clean-services-right-details">
					<div class="tab-pane active col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div id="accordion" class="panel-group">
						<ul class="nav nav-tab nav-stacked myservice_method_unitload" id="sortable-services-unit" > 
							</ul>
						</div>	
					</div>
				</div>
			</div>
			
		</div>
			
	</div>
	
	
</div>
<?php  
	include(dirname(__FILE__).'/footer.php');
?>
<script type="text/javascript">
    var ajax_url = '<?php echo filter_var(AJAX_URL, FILTER_VALIDATE_URL);	?>';
    var link_url = '<?php echo filter_var(SITE_URL, FILTER_VALIDATE_URL).'admin/';	?>';
</script>