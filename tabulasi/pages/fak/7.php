<?php
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 7. Penelitian, Pelayanan/Pengabdian Kepada Masyarakat";
	wordOnline($title,17, $year);
	lampiran($course_id, 17, $year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow == 8 ) {
	addComment($course_id, 17, $year); 
	}	 
?>
