<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 6. Pembiayaan, Prasarana, Sarana, dan Sistem Informasi";
	wordOnline($title,6, $year);
	lampiran($course_id, 6, $year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 6, $year); 
	}	
?>
