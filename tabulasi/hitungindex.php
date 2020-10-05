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
	  <h1>
			<?php $survey = Survey::getSurvey( $survey_id );
				echo $survey->getValueEncoded('name');
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
					<li class="active"><a href="<?php echo BASE_URL; ?>/hitungindex.php?id=<?php echo $survey_id; ?>">Indeks</a></li>
								  
				</ul>
				<div class="box">
	<?php 
list($questionnaires, $totalRows) = Survey::getQuestionnairy( $survey_id, 'index' );
if ( $questionnaires ) {
foreach ( $questionnaires as $q) { 
?>

<table class="table table-striped table-bordered">
<tbody>
<tr>
	<th rowspan="3" width="92" style="text-align:center;vertical-align:middle;">No</th>
	<th class="hidden-xs" colspan="15" width="702" style="text-align:center">Unsur Penilaian</th>
	<th rowspan="3" width="84" style="text-align:center;vertical-align:middle;">Total</th>
</tr>
<!--<tr>
	<th  class="hidden-xs"  colspan="8" width="400" style="text-align:center">Kategori Pengelolaan Keuangan Haji</th>
	<th  class="hidden-xs" colspan="7" width="302" style="text-align:center">Kategori Institusi</th>
</tr>-->
<tr>
<?php
list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
	if ( $questionnaire_items ) {
	$a=1;foreach ( $questionnaire_items as $qi ) { 
		// nanti disini buat nampilkan bobotnya: getValue_content()
		?>
		<td class="hidden-xs" width="43" style="text-align:center;"><?php echo $a++; ?></td>
	<?php } 
	} // endif $questionnaire_items
	?>
</tr>


<?php
// ambil participant yang ikut survey
list($answers, $totalRows) = Survey::getSurveyParticipants( $survey_id ); 
	if ( $answers ) {
	$i = 1;
	foreach ( $answers as $a ) { ?>
	<tr>
		<td width="43" style="text-align:center;"><?php echo $i++; ?></td>
		<?php
		// ambil id soal jawaban untuk tiap voter
		list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
			if ( $questionnaire_items ) {
			$total = 0;
			foreach ( $questionnaire_items as $qa ) { ?>	

				<td  class="hidden-xs"  width="43" style="text-align:center;">
					<?php //bobot
					
					
					$b = Survey::hitungBobot($survey_id,$qa->getValueEncoded('id'),$a->getValueEncoded('participant_id'));
					if ( $b > 0  ) { $total+= $b; echo $b; }
					else { echo '0'; }
					
					?>					
				</td>
		
		<?php }
			
			//jumlahkan dulu hasil di atas:
			echo '<td width="43" style="text-align:center;">';
			echo number_format($total,2);
			echo '</td>';
			} //$questionnaire_items 
			?>
	</tr>

<?php } 
	} //endif $answer
?>
<tr>
<td width="92" style="text-align:center;font-weight:bold;">TOTAL*</td> <!-- hitng pakai function bobotByQuestionByParticipant()-->

<?php
		// ambil id soal jawaban untuk tiap voter
		list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
		if ( $questionnaire_items ) {
			$sum = 0;
			foreach ( $questionnaire_items as $qa ) {
?>
			<td  class="hidden-xs" width="43" style="text-align:center;">
				<?php 
					//AMBIL PARTICIPANT
					list($participants, $totalRows) = Survey::getSurveyParticipants( $survey_id );
					$total = 0;
					foreach ( $participants as $p ) {
						//bobot
						$b = Survey::hitungBobot($survey_id,$qa->getValueEncoded('id'),$p->getValueEncoded('participant_id'));
						if ( $b ) $total+= $b;
					
					} echo number_format($total,2); $sum+= $total;					
				?>				
			</td>

		<?php } //end foreach
			echo '<td width="43" style="text-align:center;">' . number_format( $sum, 2 ) . '</td>';
		
		} //endif $questionnaire_items
		?>
</tr>
<tr>
<td width="92" style="text-align:center;font-weight:bold;">TOTAL**</td>
<?php
		// ambil id soal jawaban untuk tiap voter
		list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
		if ( $questionnaire_items ) {
			$sum = 0;
			foreach ( $questionnaire_items as $qa ) {
?>
			<td class="hidden-xs" width="43" style="text-align:center;">
				<?php 
					//AMBIL PARTICIPANT
					list($participants, $totalRows) = Survey::getSurveyParticipants( $survey_id );
					$count = count($participants);
					$total = 0;
					foreach ( $participants as $p ) {
						//bobot
						$b = Survey::hitungRerata($survey_id,$qa->getValueEncoded('id'),$p->getValueEncoded('participant_id'));
						if ( $b > 0 ) { $total+= $b; } else { echo '0';}
					
					} $avg = @($total/$count); echo number_format($avg,2); 
					
					$sum+= $avg;					
				?>				
			</td>

		<?php } //end foreach
			echo '<td width="43" style="text-align:center;">' . number_format( $sum, 2 ) . '</td>';
		
		} //endif $questionnaire_items
		?>

</tr>

<tr>
<td width="92" style="text-align:center;font-weight:bold;">NRR TERTIMBANG</td>
<?php
		// ambil id soal jawaban untuk tiap voter
		list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
		if ( $questionnaire_items ) {
			$sum = 0;
			$jumlah_soal = count( $questionnaire_items );
			foreach ( $questionnaire_items as $qa ) {
?>
			<td  class="hidden-xs" width="43" style="text-align:center;">
				<?php 
					//AMBIL PARTICIPANT
					list($participants, $totalRows) = Survey::getSurveyParticipants( $survey_id );
					$count = count($participants);
					$total = 0;
					foreach ( $participants as $p ) {
						//bobot
						$b = Survey::hitungRerata($survey_id,$qa->getValueEncoded('id'),$p->getValueEncoded('participant_id'));
						if ( $b ) $total+= $b;
					
					} 
					$sub_total = @($total/$count);
					$nrr = @($sub_total/$jumlah_soal); echo number_format($nrr, 4); 
					
					$sum+= $nrr;					
				?>				
			</td>

		<?php } //end foreach
			echo '<td width="43" style="text-align:center;">' . number_format( $sum, 4 ) . '</td>';
		
		} //endif $questionnaire_items
		?>

</tr>
<tr>
<td colspan="17" width="751" style="font-weight:bold; background:#d9d9d9; text-align:center;">Indeks identitas</td>

</tr>
<tr>
<td colspan="17" style="font-weight:bold; text-align:center;">
    <h1 style="font-weight:bold;font-size:3em;"><?php echo $indeks = number_format((($sum*$jumlah_soal)/4)*100,2); ?></h1>
	<?php if ( $indeks > 0 && $indeks <= 25 ) {
		rendah();
	} else if ( $indeks > 25 && $indeks <= 50 ) {
		sedang();
	} else if ( $indeks > 50 && $indeks <= 75 ) {
		cukup();
	} else if ( $indeks > 75 && $indeks <= 100 ) {
		tinggi();
	}
	?>
 </td>
</tr>
</tbody>
</table>
			
<?php } //end foreach
} // endif questionnaires

?>			
				
			</div>			
			</div>			
        </div>		
	</section>
 </div>     



<?php	

} else {

	echo "<script>window.location.href =' ".BASE_URL ."/login.php';</script>";

}

get_footer(); ?>
