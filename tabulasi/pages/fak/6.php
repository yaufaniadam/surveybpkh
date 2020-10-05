<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 6. Pembiayaan, Sarana dan Prasarana serta Sistem Informasi";
	wordOnline($title,16, $year); 
	lampiran($course_id, 16, $year);

	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 16, $year); 
	}		
?>
