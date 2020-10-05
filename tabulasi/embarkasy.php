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
			Embarkasy
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Embarkasy</li>
	  </ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Embarkasy</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">						
						
						<ul>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=81">Aceh</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=82">Medan</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=83">Batam</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=84">Padang</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=85">Palembang</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=86">Solo</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=87">Jakarta (Pondo Gede)</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=88">Surabaya</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=89">Banjarmasin</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=90">Jakarta (Bekasi)</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=91">Balikpapan</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=92">Makassar</a></li>
							<li><a href="<?php echo BASE_URL; ?>/byembarkasi.php?id=2&emb_id=93">Lombok</a></li>	

						</ul>

							
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
