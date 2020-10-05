<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Participants Management')),
    );
	
	$functionName = function($record, $params){
		$href = A::app()->getRequest()->getBaseUrl().'surveys/login/code/'.$params['surveyCode'].'/param/'.SurveysComponent::generateLoginByUrlLink($record['participant_identity_code'], $record['participant_password']);
		return '<a href="'.$href.'" target="_blank">'.A::t('surveys', 'Login Link').'</a>';
	}
?>

<h1><?= A::t('surveys', 'Survey Participants Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
    </div>
    <div class="content">
    <?php 
        echo $actionMessage;

        $actionPath = 'surveyParticipants/';		
        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('survey_participants', 'add')){
            echo '<a href="'.$actionPath.'add/surveyId/'.$surveyId.'" class="add-new">'.A::t('surveys', 'Add Existing Participant').'</a>';
        }
        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('participants', 'add')){
            echo '&nbsp;&nbsp;&nbsp;[ <a href="surveysParticipants/manage">'.A::t('surveys', 'Create Participant').'</a> ]';
        }
        
        echo CWidget::create('CGridView', array(
            'model'=>'SurveyParticipants',
            'actionPath'=>$actionPath.'manage/surveyId/'.$surveyId,
            'condition'=>CConfig::get('db.prefix').'surveys_entity_participants.survey_id = '.(int)$surveyId,
            'defaultOrder'=>array('id'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'last_name,first_name' 	=> array('title'=>A::t('surveys', 'Participant Name'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'surveys_participants', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'100'),
                'identity_code' 		=> array('title'=>A::t('surveys', 'Identity Code'), 'type'=>'textbox', 'table'=>CConfig::get('db.prefix').'surveys_participants', 'operator'=>'like%', 'width'=>'90px', 'maxLength'=>'100'),
				'status' 				=> array('title'=>A::t('surveys', 'Status'), 'type'=>'enum', 'table'=>'', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$statuses, 'emptyOption'=>true, 'emptyValue'=>'', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
                'is_active' 			=> array('title'=>A::t('surveys', 'Active'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'surveys_entity_participants', 'operator'=>'=', 'width'=>'', 'source'=>array(''=>'', '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'participant_identity_code' => array('title'=>A::t('surveys', 'Identity Code'), 'type'=>'label', 'align'=>'', 'width'=>'110px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'participant_name' 		=> array('title'=>A::t('surveys', 'Participant Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'start_date'  			=> array('title'=>A::t('surveys', 'Start Date'), 'type'=>'datetime', 'align'=>'', 'width'=>'160px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('surveys', 'not yet')), 'format'=>$dateTimeFormat),
                'finish_date' 			=> array('title'=>A::t('surveys', 'Finish Date'), 'type'=>'datetime', 'align'=>'', 'width'=>'150px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(null=>A::t('surveys', 'not yet')), 'format'=>$dateTimeFormat),
				///'ip_address' 			=> array('title'=>A::t('surveys', 'IP Address'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'status'      			=> array('title'=>A::t('surveys', 'Status'), 'type'=>'enum', 'class'=>'center', 'headerClass'=>'center', 'source'=>$statuses, 'width'=>'100px'),
				'link_direct_login'   	=> array('title'=>'', 'type'=>'html', 'align'=>'', 'width'=>'110px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'prependCode'=>'[ ', 'appendCode'=>' ]', 'disabled'=>(!$loginByUrl ? true : false), 'callback'=>array('function'=>$functionName, 'params'=>array('surveyCode'=>$surveyCode))),
                'link_clear_results'   	=> array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'110px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyParticipants/clearResult/surveyId/'.$surveyId.'/id/{id}', 'linkText'=>A::t('surveys', 'Clear Results'), 'definedValues'=>array(), 'htmlOptions'=>array('class'=>'prompt-delete', 'data-prompt-message'=>A::t('surveys', 'Are you sure you want to clear results for this participant?')), 'disabled'=>($accessMode == 'p' ? true : false), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'is_active'   			=> array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyParticipants/changeStatus/surveyId/'.$surveyId.'/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change status'))),
            ),
            'actions'=>array(
				'edit'   => array(
					'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('survey_participants', 'edit'),								
					'link'=>$actionPath.'edit/surveyId/'.$surveyId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('surveys', 'Edit this record')
				),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('survey_participants', 'delete'),
                    'link'=>$actionPath.'delete/surveyId/'.$surveyId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('surveys', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
