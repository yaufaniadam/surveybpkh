<?php 
require_once(dirname(__FILE__) . '/common.inc.php' );
session_start(); 
get_header("Participants"); 
checkLogin();

$survey_id = isset( $_GET["id"] ) ? (int)$_GET["id"] : 0;


include "sidebar.php";

if ($login ==1) {
	
?>
	
<div class="content-wrapper">
	<section class="content-header">
	  <h1> Bobot Pertanyaan
			<?php $survey = Survey::getSurvey( $survey_id );
				
			?>
			<small><?php echo $survey->getValueEncoded('description'); ?></small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Survey</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li><a href="<?php echo BASE_URL; ?>/rekapitulasi.php?id=<?php echo $survey_id; ?>">Rekapitulasi</a></li>
					<li><a href="<?php echo BASE_URL; ?>/kepuasan.php?id=<?php echo $survey_id; ?>">Indeks Identitas</a></li>
					<li class="active"><a href="<?php echo BASE_URL; ?>/bobot.php?id=<?php echo $survey_id; ?>">Bobot Pertanyaan</a></li>			  
				</ul>	
				
<?php
	// tampilkan kategori pertanyaan
	list($questionnaires, $totalRows) = Survey::getQuestionnairy( $survey_id, 'kepuasan' ); 
	foreach ( $questionnaires as $q) { ?>
		<div class="box">
			
			<!-- /.box-header -->
			<div class="box-body no-padding">	
				
			<?php // tampilkan isi pertanyaan
				list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
			?>
				<table class="table table-striped table-bordered">
				<tr>
						<th style="width:20px;text-align:center;">No.</th>
						<th style="width:90%;text-align:center;">Pertanyaan</th>
						<th style="width:50px;text-align:center;">Bobot</th>						
					</tr>
				<?php $a = 1;	
				foreach ( $questionnaire_items as $q_item) { 
				?>
					<tr>
						<td style="width:20px;text-align:center;"><?php echo $a++ .". "; ?></td>
						<td style="width:80%"><?php echo $q_item->getValueEncoded('question_text'); ?></td>
						<td style="width:50px;text-align:center;"><?php echo $q_item->getValueEncoded('content'); ?></td>						
					</tr>
				<?php } ?>
				</table>	
			</div>
			<!-- /.box-body -->
		</div>
<?php } ?>			
				
			</div>			
        </div>		
	</section>
 </div>     



<?php	

} else {

	echo "<script>window.location.href =' ".BASE_URL ."/login.php';</script>";

}

get_footer(); ?>
