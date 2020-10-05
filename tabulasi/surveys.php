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
			Survei
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Survei</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
			
				
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Survei Tersedia</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">						
						
						<?php
							// tampilkan peserta survey dari sebuah survey
							$survey_id = 2;
							
							$surveys = Survey::getSurveys(); 
							echo "<ol type='1'>";
								foreach ( $surveys as $s) {
									echo '<li><a href="' .BASE_URL. '/survey.php?id=' .$s->getValueEncoded('id'). '" />' . $s->getValueEncoded('name').'</a></li>';
								}
								
							echo "</ol>";
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
