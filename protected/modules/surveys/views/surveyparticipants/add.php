<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Survey Participants Management').' - '.A::t('surveys', 'Add Participant').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Participants Management'), 'url'=>'surveyParticipants/manage/surveyId/'.$surveyId),
        array('label'=>A::t('surveys', 'Edit Participant')),
    );    
?>

<h1><?= A::t('surveys', 'Survey Participants Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <?= A::t('surveys', 'Add Participant') ?>
    </div>
    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CFormView', array(
            'action'=>'surveyParticipants/add/surveyId/'.$surveyId,
            'cancelUrl'=>'surveyParticipants/manage/surveyId/'.$surveyId,
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmSurveyParticipantAdd',
                'enctype'=>'multipart/form-data',
                'autoGenerateId'=>false
            ),
            'requiredFieldsAlert'=>true,
            'fieldSetType'=>'frameset|tabs',
            'fields'=>array(
                'act'=>array('type'=>'hidden', 'value'=>'send'),
                'participant_id'=>array('type'=>'select', 'title'=>A::t('surveys', 'Select Participant'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>$participantId, 'data'=>$participants, 'emptyOption'=>true, 'emptyValue'=>A::t('app', '-- select --'), 'htmlOptions'=>array()),
            ),
            'checkboxes'=>array(),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>A::t('surveys', 'Add'), 'htmlOptions'=>array('name'=>'')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','surveyParticipants/manage/surveyId/".$surveyId."');")),
            ),
            'buttonsPosition'	=> 'bottom',
            'events'			=> array(),
            'return'			=> true,
        ));
    ?>
    </div>
</div>
        