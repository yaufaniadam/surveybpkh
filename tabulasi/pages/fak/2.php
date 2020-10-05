<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title=" Standar 2. Tata Pamong, Kepemimpinan, Sistem Pengelolaan Dan Penjaminan Mutu";
	wordOnline($title,12, $year);
	lampiran($course_id, 12, $year);

	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 12, $year); 
	}		
?>
