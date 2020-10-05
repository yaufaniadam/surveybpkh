<?php
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title = "Standar 1. Visi, Misi, Tujuan, Sasaran Serta Strategi Pencapaian";
	wordOnline($title,11, $year);
	lampiran($course_id, 11, $year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow == 8 ) {
	addComment($course_id, 11, $year); 
	}	  
?>
