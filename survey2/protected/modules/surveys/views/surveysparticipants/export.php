<?php
    $this->_activeMenu = 'surveysParticipants/manage';
    $this->_pageTitle = $errorTitle.' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Participants Management')),
    );    
?>

<h1><?= A::t('surveys', 'Participants Management'); ?></h1>

<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title">
        <?= $errorHeader; ?>        
        <a href="surveysParticipants/manage" class="back-link"><?= A::t('surveys', 'Back'); ?></a>
    </div>    

    <div class="content">
        <p style="text-align:center;"><?= $errorText; ?></p>
    </div>
    
</div>