<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 3. Mahasiswa dan Lulusan";
	wordOnline($title,13, $year);
	lampiran($course_id, 13, $year);

	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 13, $year); 
	}		
?>
