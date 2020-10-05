<!DOCTYPE html>
<?php 
checkLogin();
global $login,$current_role;
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Aplikasi Survei Badan Pengelola Keuangan Haji RI - <?php echo $page_title; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> 
  
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/dist/css/AdminLTE.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/dist/css/skins/skin-green-light.css">
	
  <!-- jQuery 2.2.3 -->
  <script src="<?php echo BASE_URL; ?>/assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
</head>

<body class="hold-transition skin-green-light sidebar-collapse sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>/?year=<?php echo $year; ?>" class="logo">
      <span class="logo-mini"> <img src="<?php echo BASE_URL; ?>/assets/dist/img/logo-ori-sm.png" alt="Logo UMY"></span>
      <span class="logo-lg"><img src="<?php echo BASE_URL; ?>/assets/dist/img/logo-ori.png" alt="Logo UMY" width="200" height=""></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
	  
	  
	 
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
			<li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>
  </header>
