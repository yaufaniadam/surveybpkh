<?php 
session_start();
require_once(dirname(__FILE__) . '/common.inc.php' );
$_SESSION["user"] = "";
get_header('Logout'); 
session_destroy();
?>

<script type="text/javascript">
<!--
window.location = "<?php echo BASE_URL; ?>"
//-->
</script>

<?php
exit;
?>


<?php get_footer(); ?>