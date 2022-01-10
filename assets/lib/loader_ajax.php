<?php 
/* Getting file name */
$filename = $_FILES['file']['name'];
/* Location */
$location = "../images/gif-loader/".$filename;
/* Upload file */
if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
    echo filter_var($location, FILTER_SANITIZE_STRING);
}else{
    echo 0;
}