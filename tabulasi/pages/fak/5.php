<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title=" Standar 5. Kurikulum, Pembelajaran, dan Suasana Akademik";
	wordOnline($title,15, $year);
	lampiran($course_id, 15, $year);

	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 15, $year); 
	}		
?>
