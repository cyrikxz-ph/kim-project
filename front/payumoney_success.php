<?php 
session_start();
include(dirname(dirname(__FILE__)).'/header.php');
include(dirname(dirname(__FILE__)).'/config.php');
include(dirname(dirname(__FILE__)).'/objects/class_connection.php');
include(dirname(dirname(__FILE__)).'/objects/class_setting.php');

$database= new laundry_db();
$conn=$database->connect();
$database->conn=$conn;
$settings=new laundry_setting();
$settings->conn=$conn;

$status=filter_var($_POST["status"], FILTER_SANITIZE_STRING);
$firstname=filter_var($_POST["firstname"], FILTER_SANITIZE_STRING);
$amount=filter_var($_POST["amount"], FILTER_SANITIZE_STRING);
$txnid=filter_var($_POST["txnid"], FILTER_SANITIZE_STRING);
$_SESSION['ld_details']['paumoney_transaction_id'] = filter_var($_POST["txnid"], FILTER_SANITIZE_STRING);
$posted_hash=filter_var($_POST["hash"], FILTER_SANITIZE_STRING);
$key=filter_var($_POST["key"], FILTER_SANITIZE_STRING);
$productinfo=filter_var($_POST["productinfo"], FILTER_SANITIZE_STRING);
$email=filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$salt=$settings->get_option('ld_payumoney_salt');

If (isset($_POST["additionalCharges"])) {
	$additionalCharges=filter_var($_POST["additionalCharges"], FILTER_SANITIZE_STRING);
	$retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}else {	  
	$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);
if ($hash != $posted_hash) {
	echo "Invalid Transaction. Please try again";
}else{
	?>
	<script>window.location = '<?php echo FRONT_URL; ?>booking_complete.php'; </script>
	<?php 
}
?>