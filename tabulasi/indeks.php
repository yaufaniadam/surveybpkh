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
					<li class="active"><a href="<?php echo BASE_URL; ?>/indeks.php?id=<?php echo $survey_id; ?>">Indeks</a></li>
								  
				</ul>
				<div class="box">
					<h3>Index Lembaga</h3>
					
					<?php //$bq1 = 7; $bq2 = 7; $bq3 =8; $bq4 =6; $bq5=5; $bq6 =5; $bq7 =6; $bq8=7;$bq9=8;$bq10=7;$bq11=8;$bq12=5;$bq13=6;
				
					list($questions,$totalRows) = Indeks::getQuestions();
					$totalRows;
					$sum=0;
					foreach ($questions as $key => $q) {
						$q1	 = ($q->getValueEncoded('d30')*7)/(236*50);
						$q2	 = ($q->getValueEncoded('d31')*7)/(236*50);
						$q3	 = ($q->getValueEncoded('d32')*8)/(236*50);
						$q4	 = ($q->getValueEncoded('d33')*6)/(236*50);
						$q5	 = ($q->getValueEncoded('d34')*5)/(236*50);
						$q6	 = ($q->getValueEncoded('d35')*5)/(236*50);
						$q7	 = ($q->getValueEncoded('d36')*6)/(236*50);
						$q8	 = ($q->getValueEncoded('d37')*7)/(236*50);
						$q9	 = ($q->getValueEncoded('d38')*8)/(236*50);
						$q10 = ($q->getValueEncoded('d39')*7)/(100*50);

						$sum+= number_format($q1+$q2+$q3+$q4+$q5+$q6+$q7+$q8+$q9+$q10,5);
					}
					echo "Y_LEMBAGA_A = ". $sum."<br>";
					
					list($questions,$totalRows) = Indeks::getQuestions();
					$totalRows;
					$sum=0;
					foreach ($questions as $key => $q) {
						$q10	 = ($q->getValueEncoded('d39')*7)/(104*50);
						$q11	 = ($q->getValueEncoded('d40')*8)/(104*50);
						$q12	 = ($q->getValueEncoded('d41')*5)/(104*50);
						$q13	 = ($q->getValueEncoded('d42')*6)/(104*50);

						$sum+= number_format($q10+$q11+$q12+$q13,5);
					}
					echo "Y_LEMBAGA_B = ". $sum."<br>";


				
					list($questions,$totalRows) = Indeks::getQuestions();
					$totalRows;
					$sum=0;
					foreach ($questions as $key => $q) {
						$q14	 = ($q->getValueEncoded('d47')*7)/(152*100);
						$q15	 = ($q->getValueEncoded('d51')*8)/(152*100);
						$q16	 = ($q->getValueEncoded('d52')*7)/(152*100);
						$q17	 = ($q->getValueEncoded('d53')*8)/(152*100);
						$q18	 = ($q->getValueEncoded('d54')*8)/(152*100);


						$sum+= number_format($q14+$q15+$q16+$q17+$q18,5);
					}
					echo "X_LAYANAN = ". $sum;
					
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
