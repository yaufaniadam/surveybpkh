<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Survey Participants Management').' - '.A::t('surveys', 'Add Participant').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Participants Management'), 'url'=>'surveyParticipants/manage/surveyId/'.$surveyId),
        array('label'=>A::t('surveys', 'Add Participant')),
    );    
?>

<h1><?= A::t('surveys', 'Survey Participants Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <?= A::t('surveys', 'Edit Participant') ?>
    </div>
    <div class="content">
    <?php
        echo $actionMessage;

		echo CWidget::create('CDataForm', array(
			'model'=>'SurveyParticipants',
			'primaryKey'=>$id,
			'operationType'=>'edit',
			'action'=>'surveyParticipants/edit/surveyId/'.$surveyId.'/id/'.$id,
			'successUrl'=>'surveyParticipants/manage/surveyId/'.$surveyId,
			'cancelUrl'=>'surveyParticipants/manage/surveyId/'.$surveyId,
			'passParameters'=>true,
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmSurveyParticipantEdit',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
			'fields'=>array(
                'participant_name' 			=> array('type'=>'label', 'title'=>A::t('surveys', 'Name')),
                'participant_gender' 		=> array('type'=>'label', 'title'=>A::t('surveys', 'Gender'), 'disabled'=>($fieldGender == 'no' ? true : false), 'definedValues'=>array('f'=>A::t('surveys', 'Female'), 'm'=>A::t('surveys', 'Male'))),
                'participant_email' 		=> array('type'=>'label', 'title'=>A::t('surveys', 'Email')),
				'participant_login_link' 	=> array('type'=>'link', 'title'=>A::t('surveys', 'Login Link'), 'tooltip'=>'', 'linkUrl'=>$loginUrl, 'linkText'=>A::t('surveys', 'Login Link'), 'htmlOptions'=>array('target'=>'_blank'), 'prependCode'=>'[ ', 'appendCode'=>' ]', 'disabled'=>(!$loginByUrl ? true : false)),
				'ip_address' 				=> array('type'=>'label', 'title'=>A::t('surveys', 'Created From IP Address')),
				'cookie_code' 				=> array('type'=>'label', 'title'=>A::t('surveys', 'Cookie Code')),
				'is_active' 				=> array('type'=>'checkbox', 'title'=>A::t('surveys', 'Active'), 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
			),
			'buttons'=>array(
                'submitUpdateClose' => array('type'=>'submit', 'value'=>A::t('surveys', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                'submitUpdate' => array('type'=>'submit', 'value'=>A::t('surveys', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel' => array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('surveys', 'Survey Participant')),
			'return'			=> true,
		));
    ?>        
    </div>    
</div>
            