<?php 
error_reporting(1);
require_once( "classes/User.class.php" );  
require_once( "classes/Survey.class.php" );  
require_once( "classes/Indeks.class.php" ); 

function validateField( $fieldName, $missingFields ) {
  if ( in_array( $fieldName, $missingFields ) ) {
    echo 'class="error"';
  }
}
          
function setChecked( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj-> getValue( $fieldName ) == $fieldValue ) {
    echo 'checked="checked"';
  }
}
          
function setSelected( DataObject $obj, $fieldName, $fieldValue ) {
  if ( $obj-> getValue( $fieldName ) == $fieldValue ) {
    echo 'selected="selected"';
  }
} 

function checkLogin() {
	global $login;
	$login=1;
	
	/*
global $login, $current_id,	$current_role;
if(empty($_SESSION['user'])) {		
		 $login ="0";	
	} else {		
		$login 			= "1";	
		$current_id 	= $_SESSION["user"]->getValue( "user_id" );
		$current_role 	= $_SESSION["user"]->getValue( "role" );
	} */
}

function norole() {
	echo "<div class='alert alert-danger'><strong>Warning!</strong> Anda tidak memiliki hak akses pada halaman ini</div>";
}
function loginfirst() {
	echo "<script>window.location.href =' ".BASE_URL ."/login.php';</script>";
}
function selectcourse() {
	echo "<div class='alert alert-danger'><strong>Warning!</strong> Program studi belum dipilih</div>";	
} 

function get_header($page_title) {
	include 'header.php';
}
function get_header_login($page_title) {
	include 'header_login.php';
}
function get_footer() {
	include 'footer.php';
}

function rendah() {
	echo '<h2 class="indeks" style="color:red;">Rendah</h2>';
}
function sedang() {
	echo '<h2 class="indeks" style="color:orange;">Sedang</h2>';
}
function cukup() {
	echo '<h2 class="indeks" style="color:green;">Cukup</h2>';
}
function tinggi() {
	echo '<h2 class="indeks" style="color:darkgreen;">Tinggi</h2>';
}

