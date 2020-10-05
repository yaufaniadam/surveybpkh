<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title = "Standar 7. Penelitian, Pelayanan/pengabdian Kepada Masyarakat, dan Kerjasama";
	wordOnline($title,7, $year );
	lampiran($course_id, 7, $year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 7, $year); 
	}		
?>
