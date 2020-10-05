<?php
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 1. Visi, Misi, Tujuan dan Sasaran, serta Strategi Pencapaian";
	wordOnline($title,1,$year);
	lampiran($course_id, 1,$year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow == 8 ) {
	addComment($course_id, 1, $year); 
	}	
?>
