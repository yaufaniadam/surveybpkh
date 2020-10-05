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
			Hasil Survei
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Dashboard</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
			
				<?php
				// tampilkan kategori pertanyaan
					list($questionnaires, $totalRows) = Survey::getQuestionnaires( $survey_id ); 
					$i = 1;					
					foreach ( $questionnaires as $q) { ?>
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title"><?php echo $i++ .". "; echo $q->getValueEncoded('name'); ?></h3>
							</div>
							<!-- /.box-header -->
							<div class="box-body">						
								<?php // tampilkan isi pertanyaan
									list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items( $q->getValueEncoded('id')); 
									?>
									<table class="table table-striped">
										<?php $a = 1;	
											foreach ( $questionnaire_items as $q_item) { ?>
										<tr>
											
														<td style="width:80%"><?php echo $a++ .". "; echo $q_item->getValueEncoded('question_text'); ?></td>
														
														<td style="width:8%;text-align:center;">			
															Persentase
														</td>
														<td style="width:10%;text-align:center;">			
															Jumlah
														</td>
													
										</tr>
										<tr>
											<td colspan="3">
												<?php 
													//type pertanyaan
													$q_type = $q_item->getValueEncoded('question_type_id');
													//id pertanyaan
													$id_q = $q_item->getValueEncoded('id');
													?>
													
													<table class="table table-bordered">
													<?php
													if ( $q_type == 3 || $q_type == 1  ) {
														
														// tampilkan pilihan jawaban
														list($questionnaire_item_variants, $totalRows) = Survey::getQuestionnaire_item_variants(0, 15, $id_q); 
												
														foreach ( $questionnaire_item_variants as $q_item_variant){
														?>
															<tr>
																<td style="width:40%"><?php echo $q_item_variant->getValueEncoded('content') ?></td>
																<td>			
																	<div class="progress progress-sm active">
																		<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
																		<span class="sr-only">20% Complete</span>
																		</div>
																	</div>
																</td>
																<td style="width:10%; text-align:center;">			
																	<span class="badge bg-red">55%</span>
																</td>
																<td style="width:10%;text-align:center;">			
																	<span class="badge bg-blue">55</span>
																</td>
															</tr>
																
															
														<?php
														}														
													
													} else if ( $q_type == 6  ) { ?>
														<tr>
															<td style="width:40%">Teks</td>
															<td>			
																<div class="progress progress-sm active">
																	<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
																	<span class="sr-only">20% Complete</span>
																	</div>
																</div>
															</td>
															<td style="width:10%;text-align:center;">			
																<span class="badge bg-red">55%</span>
															</td>
															<td style="width:10%;text-align:center;">			
																<span class="badge bg-blue">55</span>
															</td>
														</tr>
													<?php }
													?>	
													
													</table>
																						
											</td>
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
