<?php 
//checkLogin();
require_once(dirname(__FILE__) . '/common.inc.php' );
session_start(); 
get_header_login('Login Member'); 

?>
<script type="text/javascript" src="js/jquery.validate.js"></script>

<div class="login-box">

<div class="login-logo">
    <a href="<?php echo BASE_URL; ?>/login.php"><img src="<?php echo BASE_URL; ?>/assets/dist/img/logologin.png" alt="Logo UMY" width="250" height=""></a>
  </div>

<?php         
if ( isset( $_POST["action"] ) and $_POST["action"] == "login" ) {
  processForm();
} else {
  displayForm( array(), array(), new User( array() ) );
}
          
function displayForm( $errorMessages, $missingFields, $user ) {
 
  if ( $errorMessages ) {
    foreach ( $errorMessages as $errorMessage ) {
	?>	
		<div class="callout callout-danger">
            <p><?php echo $errorMessage; ?></p>
        </div>
     <?php
    }
  } else {
?> 
     
 <?php } ?> 
<?php global $login; if($login==0) { ?>
 
				<?php 				
				if (empty($_SERVER['HTTP_REFERER'])) {					
						$referer = BASE_URL;
					} else {
						if($_SERVER['HTTP_REFERER']== BASE_URL .'/logout.php' || $_SERVER['HTTP_REFERER']== BASE_URL .'/login.php') {
							$referer = BASE_URL;
						} else {
							$referer = $_SERVER['HTTP_REFERER'];
						}
					}
				
				?>

  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Login dahulu untuk mengakses.</p>

  <form action="login.php" method="post"  > 
					<div class="login" > 		 
						<input type="hidden" name="action" value="login" />           
						<input type="hidden" name="referer" value="<?php echo $referer; ?>" />          
						<div class="form-group">
							<input class="form-control" type="text" name="username" id="username" value="<?php echo $user->getValueEncoded("username") ?>" placeholder="Username"/> 
						</div>
						<div class="form-group">
							<input class="form-control" type="password" name="password" id="password" value="<?php echo $user->getValueEncoded("password") ?>" placeholder="Password"/> 
						  </div>
						<div class="form-group">
							<input type="submit" name="submitButton" id="submitButton" value="Login" class="btn btn-success btn-block btn-flat"/> 
						 </div>					
					</div> 					   
				</form>  

    <a href="<?php echo BASE_URL; ?>/admin/lupa.php">Lupa Password?</a><br>

  </div>
  <!-- /.login-box-body -->

				
		
<?php } else { echo ' <div class="alert alert-warning" role="alert">Anda sudah login. <a href="logout.php">Logout</a></div>';} ?>
    
 <?php

}
      
function processForm() {
  $requiredFields = array( "username", "password" );
  $missingFields = array();
  $errorMessages = array();
          
  $user = new User( array(
    "username" =>  $_POST["username"],
    "password" =>  $_POST["password"],
  ) );
          
  foreach ( $requiredFields as $requiredField ) {
    if ( !$user->getValue( $requiredField ) ) {
      $missingFields[] = $requiredField;
    }
  }
          
  if ( $missingFields ) {
    $errorMessages[] = '<p class="error">  Lengkapi form di bawah ini terlebih dahulu. </p>';
  } elseif ( !$loggedInUser = $user->authenticate() ) {
    $errorMessages[] = '<p class="error"> Maaf, cek kembali kombinasi username dan password Anda! </p>';
  }
          
  if ( $errorMessages ) {
    displayForm( $errorMessages, $missingFields, $user );
  } else {
    $_SESSION["user"] = $loggedInUser;
    displayThanks();
  }
}
          
function displayThanks() {

//$role = $_SESSION["user"]->getValue( "role" );
//if($role == 5) {
	$goto = BASE_URL.'/index.php';
/*} else if($role == 4) {
	$goto = BASE_URL.'/index.php?b=4';
} else if($role == 3) {
	$goto = BASE_URL.'/index.php?b=3';
} else if($role == 2) {
	$goto = BASE_URL.'/index.php?b=2';
} else {
	$goto = BASE_URL.'/index.php?b=1';
}
*/
?>

<script type="text/javascript">
<!--

window.location = "<?php echo $goto; ?>"
//-->
</script>

<?php }

?>
</div>
<!-- /.login-box -->

</body>
</html>
