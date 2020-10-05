<?php header('content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="en">
<head>    
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>" />
    <meta name="generator" content="<?= CConfig::get('name').' v'.CConfig::get('version'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- don't move it -->
    <base href="<?= A::app()->getRequest()->getBaseUrl(); ?>" />
    <title><?= CHtml::encode($this->_pageTitle); ?></title>    
	<link rel="shortcut icon" href="images/apphp.ico" />    

    <?= CHtml::cssFile('templates/default/css/bootstrap.css'); ?>
    <?= CHtml::cssFile('templates/default/css/bootstrap-responsive.css'); ?>
    <?= CHtml::cssFile('templates/default/css/style.css'); ?>
    <?php if(A::app()->getLanguage('direction') == 'rtl') echo CHtml::cssFile('templates/default/css/style.rtl.css'); ?>
    
    <!-- jquery files -->
	<?//= CHtml::scriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'); ?>
	<?//= CHtml::scriptFile('http://code.jquery.com/ui/1.10.2/jquery-ui.js'); ?>
    <?= CHtml::scriptFile('assets/vendors/jquery/jquery.js'); ?>

    <!-- tooltip files -->
    <?= CHtml::scriptFile('assets/vendors/tooltip/jquery.tipTip.min.js'); ?>
    <?= CHtml::cssFile('assets/vendors/tooltip/tipTip.css'); ?>

    <!-- site js main files -->
    <?= CHtml::scriptFile('templates/default/js/main.js'); ?>

</head>
<body>
    
    <?php include('header.php'); ?>

    <div class="container survey-content">
        <div class="row">
            <div class="span12">            
                <?= A::app()->view->getContent(); ?>            
            </div>
        </div>
      
        <?php include('footer.php'); ?>
    </div>

    <?php //echo CHtml::scriptFile('templates/default/js/jquery.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-transition.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-alert.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-modal.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-dropdown.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-scrollspy.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-tab.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-tooltip.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-popover.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-button.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-collapse.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-carousel.js'); ?>
    <?php //echo CHtml::scriptFile('templates/default/js/bootstrap-typeahead.js'); ?>
    
</body>
</html>