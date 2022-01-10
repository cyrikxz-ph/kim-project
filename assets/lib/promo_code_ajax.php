<?php  

	include(dirname(dirname(dirname(__FILE__))).'/config.php');
	include(dirname(dirname(dirname(__FILE__))).'/objects/class_connection.php');
	include(dirname(dirname(dirname(__FILE__))).'/objects/class_promo_code.php');
	
	$database=new laundry_db();
	$conn=$database->connect();
	$database->conn=$conn;
	$promo=new laundry_promo_code();
	$promo->conn=$conn;
	
	$alldata=$promo->readall_service();
	
/* Code for Add */
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='add_promo_code'){
    $promo->coupon_code = filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING);
    $t = $promo->check_same_title();
    $cnt = mysqli_num_rows($t);
    if($cnt == 0) {
        $promo->coupon_code = filter_var($_POST['coupon_code'], FILTER_SANITIZE_STRING);
        $promo->coupon_type = filter_var($_POST['coupon_type'], FILTER_SANITIZE_STRING);
        $promo->value = filter_var($_POST['value'], FILTER_SANITIZE_STRING);
        $promo->limit_use = filter_var($_POST['limit'], FILTER_SANITIZE_STRING);
        $promo->expiry_date = filter_var($_POST['expiry_date'], FILTER_SANITIZE_STRING);
        $promo->add_promo_code();
    }
    else{
        echo filter_var("1", FILTER_SANITIZE_STRING);
    }
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='edit_promo_code'){
	    $promo->id=filter_var($_POST['recordid'], FILTER_SANITIZE_STRING);
        $promo->coupon_code=filter_var($_POST['edit_coupon_code'], FILTER_SANITIZE_STRING);
        $promo->coupon_type=filter_var($_POST['edit_coupon_type'], FILTER_SANITIZE_STRING);
        $promo->value=filter_var($_POST['edit_value'], FILTER_SANITIZE_STRING);
        $promo->limit_use=filter_var($_POST['edit_limit'], FILTER_SANITIZE_STRING);
        $promo->expiry_date=filter_var($_POST['edit_expiry_date'], FILTER_SANITIZE_STRING);
        $savedata=$promo->update_promo_code();
        if($savedata){
            echo filter_var("Data Updated", FILTER_SANITIZE_STRING);
        }else{
            echo filter_var("Data Not Updated", FILTER_SANITIZE_STRING);
        }
}
if(isset($_POST['action']) && filter_var($_POST['action'], FILTER_SANITIZE_STRING)=='delete_record'){
	
	$promo->id=filter_var($_POST['recordid'], FILTER_SANITIZE_STRING);
	$delete=$promo->delete_promo_code();
	if($delete){
		echo filter_var("Record Deleted", FILTER_SANITIZE_STRING);
	}else{
		echo filter_var("Record Not Deleted", FILTER_SANITIZE_STRING);
	}
}