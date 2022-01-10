<?php  

include(dirname(dirname(dirname(__FILE__)))."/config.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_connection.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_adminprofile.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_setting.php");
include(dirname(dirname(dirname(__FILE__)))."/objects/class_dayweek_avail.php");
include(dirname(dirname(dirname(__FILE__)))."/header.php");
$con = new laundry_db();
$conn = $con->connect();
$objadminprofile = new laundry_adminprofile();
$objadminprofile->conn = $conn;
$settings = new laundry_setting();
$settings->conn = $conn;
    $timeavailability= new laundry_dayweek_avail();
    $timeavailability->conn = $conn;
if(isset($_POST['updateinfo'])){
    $objadminprofile->fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    $objadminprofile->address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $objadminprofile->city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $objadminprofile->zip = filter_var($_POST['zip'], FILTER_SANITIZE_STRING);
    $objadminprofile->state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
    $objadminprofile->country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
    $objadminprofile->phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $objadminprofile->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    if($objadminprofile->update_profile()){
        echo filter_var("Info Updated", FILTER_SANITIZE_STRING);
    }
    else {
        echo filter_var("Not Updated", FILTER_SANITIZE_STRING);
    }
}
elseif(isset($_POST['updateinfowithpass'])){
    $objadminprofile->fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    $objadminprofile->address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $objadminprofile->city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $objadminprofile->zip = filter_var($_POST['zip'], FILTER_SANITIZE_STRING);
    $objadminprofile->state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
    $objadminprofile->country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
    $objadminprofile->phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $objadminprofile->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $objadminprofile->password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    if($objadminprofile->update_profile_withpass()){
        echo filter_var("Info Updated", FILTER_SANITIZE_STRING);
    }
    else {
        echo filter_var("Not Updated", FILTER_SANITIZE_STRING);
    }
}
elseif(isset($_POST['updatepass'])){
    $objadminprofile->fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
	$objadminprofile->email = filter_var($_POST['adminemail'], FILTER_SANITIZE_EMAIL);
    $objadminprofile->address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $objadminprofile->city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $objadminprofile->zip = filter_var($_POST['zip'], FILTER_SANITIZE_STRING);
    $objadminprofile->state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
    $objadminprofile->country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
    $objadminprofile->phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $objadminprofile->id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
    $op=md5(filter_var($_POST['oldpassword'], FILTER_SANITIZE_STRING));
    $dp=filter_var($_POST['dboldpassword'], FILTER_SANITIZE_STRING);
    $np=filter_var($_POST['newpassword'], FILTER_SANITIZE_STRING);
    $rp=filter_var($_POST['retypepassword'], FILTER_SANITIZE_STRING);
    $operation = 1;
   if (filter_var($_POST['oldpassword'], FILTER_SANITIZE_STRING) != "") {
        if ($op != $dp) {
            $operation = 2;
            echo filter_var("sorry", FILTER_SANITIZE_STRING);
        }
        else {
            $operation = 3;
            if ($np == $rp) {
                $objadminprofile->password=md5($rp);
                $update=$objadminprofile->update_profile();
                if($update){
                    $_SESSION['ld_adminid']=filter_var($_POST['id'], FILTER_SANITIZE_STRING);
                }
            }
            else{
                echo filter_var("Please Retype Correct Password...", FILTER_SANITIZE_STRING);
            }
        }
    }
    if ($operation == 1) {
        $objadminprofile->password=$dp;
        $update=$objadminprofile->update_profile();
        if($update){
            $_SESSION['ld_adminid']=filter_var($_POST['id'], FILTER_SANITIZE_STRING);
        }
    }
}else if(isset($_POST['check_for_option'])){
    $check_for_products  = "select * from ld_services,ld_service_units";
    $hh = mysqli_query($conn,$check_for_products);
    $t = $timeavailability->get_timeavailability_check();
    $last = "";
    if($settings->get_option('ld_company_address')=="" ||
        $settings->get_option('ld_company_city')=="" ||
        $settings->get_option('ld_company_state')=="" ||
        $settings->get_option('ld_company_name')=="" ||
        $settings->get_option('ld_company_email')=="" ||
        $settings->get_option('ld_company_zip_code')=="" ||
        $settings->get_option('ld_company_country')=="" ||
        mysqli_num_rows($hh)=="" ||
        mysqli_num_rows($t)==""){
        $last = "Please Fill all the Company Informations and add some Services and Addons.";
    }
    if($last != ""){
        echo filter_var($last, FILTER_SANITIZE_STRING);
    }
}
?>