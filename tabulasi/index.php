<?php 
require_once(dirname(__FILE__) . '/common.inc.php' );
session_start(); 
get_header("Beranda"); 
checkLogin();

include "sidebar.php";

if ($login ==1) {
	
	include 'welcome.php';

} else {

	echo "<script>window.location.href =' ".BASE_URL ."/login.php';</script>";

}

get_footer(); ?>
