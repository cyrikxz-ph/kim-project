<?php 
if (extension_loaded('zip')) {
	include(dirname(dirname(dirname(__FILE__))). "/config.php");
	include(dirname(dirname(dirname(__FILE__))). "/objects/class_connection.php");
	include(dirname(dirname(dirname(__FILE__))). '/objects/class_setting.php'); 
	$cvars = new laundry_myvariable();
	$host = trim($cvars->hostnames);
	$un = trim($cvars->username);
	$ps = trim($cvars->passwords); 
	$db = trim($cvars->database);

	$con = new laundry_db();
	$conn = $con->connect();

	$settings = new laundry_setting();
	$settings->conn = $conn;

	/* path where the updated files are saved */
	$server_path = str_rot13("uggc://fxlzbbaynof.pbz/pyrnagb/");
 
	 /* download zip */
	if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING) == "auto_updater")
	{
		
	}
}else{
    echo filter_var("Not installed - ZipArchive is required for importing content. Please contact your server administrator and ask them to enable it.", FILTER_SANITIZE_STRING);
}
?>