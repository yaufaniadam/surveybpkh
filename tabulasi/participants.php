<?php 
require_once(dirname(__FILE__) . '/common.inc.php' );
session_start(); 
get_header("Participants"); 
checkLogin();

include "sidebar.php";

if ($login ==1) {
	
?>
	
	<div class="content-wrapper">
	<section class="content-header">
	  <h1>
			Participants
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Participants</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Peserta Survey</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">						
						
						<?php 
							$parts = Survey::ambilSemuaParticipants();

							//print_r($parts);

							

							foreach($parts as $part) {

								if(Survey::cekSurveySelesai($part->getValueEncoded('id')) < 43) {
								echo $part->getValueEncoded('id') .'='. Survey::cekSurveySelesai($part->getValueEncoded('id')).'<br>' ;
								}
							}
 

						?>	
							
					</div>
					<!-- /.box-body -->
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
