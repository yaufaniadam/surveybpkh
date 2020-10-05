<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Pendahuluan"; 
	wordOnline($title,8, $year);   
?>
