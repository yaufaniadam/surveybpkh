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
				//echo $survey->getValueEncoded('name');
			?>
			<?php echo $survey->getValueEncoded('description'); ?>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Survey</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box">
					
					<!-- /.box-header -->
					<div class="box-body">
						<ul>
							<li> <a href="<?php echo BASE_URL; ?>/rekapitulasi.php?id=<?php echo $survey_id; ?>" >Rekapitulasi</a></li>
							<li><a href="<?php echo BASE_URL; ?>/indeks.php?id=<?php echo $survey_id; ?>" >Indeks</a></li>
									  
					</ul>					
					</div>
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
