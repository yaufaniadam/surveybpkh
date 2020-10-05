<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Survey Questionnaires Management').' - '.A::t('surveys', 'Edit Questionnaire').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Questionnaires Management'), 'url'=>'surveyQuestionnaires/manage/surveyId/'.$surveyId),
        array('label'=>A::t('surveys', 'Edit Questionnaire')),
    );    
?>

<h1><?= A::t('surveys', 'Survey Questionnaires Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <?= A::t('surveys', 'Edit Questionnaire') ?>
    </div>
    <div class="content">
    <?php   
        $actionPath = 'surveyQuestionnaires/';		

        $fields = array();        

        $fields['separator_general'] = array();
        $fields['separator_general']['separatorInfo']  = array('legend'=>A::t('surveys', 'General'));
        $fields['separator_general']['survey_id']      = array('type'=>'data', 'default'=>$surveyId, 'htmlOptions'=>array());
        $fields['separator_general']['name']           = array('type'=>'textbox', 'title'=>A::t('surveys', 'Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>'255', 'class'=>'middle'));
        $fields['separator_general']['description']    = array('type'=>'textarea', 'title'=>A::t('surveys', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1024), 'htmlOptions'=>array('maxLength'=>'1024', 'class'=>'middle'));
        $fields['separator_general']['category_title'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Category Title'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>'255'));
        $fields['separator_general']['questionnaire_key'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Key'), 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>10), 'htmlOptions'=>array('maxlength'=>'10', 'class'=>'medium'));
        $fields['separator_general']['items_per_page'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Items Per Page'), 'tooltip'=>'', 'default'=>1, 'validation'=>array('required'=>true, 'type'=>'range', 'minValue'=>'1', 'maxValue'=>'99'), 'htmlOptions'=>array('maxlength'=>'2', 'class'=>'small'));
        $fields['separator_general']['sort_order']     = array('type'=>'textbox', 'title'=>A::t('surveys', 'Sort Order'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'numeric'), 'htmlOptions'=>array('maxlength'=>'3', 'class'=>'small'));
        $fields['separator_general']['is_active']      = array('type'=>'checkbox', 'title'=>A::t('surveys', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        
        $fields['separator_messages'] = array();
        $fields['separator_messages']['separatorInfo'] = array('legend'=>A::t('surveys', 'Start and Finish Pages'));
        if(!$surveyGenderFormulation):
            $fields['separator_messages']['start_message']  = array('type'=>'textarea', 'title'=>A::t('surveys', 'Start Page Text'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
            $fields['separator_messages']['finish_message'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Finish Page Text'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
        else:
            $fields['separator_messages']['start_message']  = array('type'=>'textarea', 'title'=>A::t('surveys', 'Start Page Text').' <br>('.A::t('surveys', 'Male Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
            $fields['separator_messages']['start_message_f']  = array('type'=>'textarea', 'title'=>A::t('surveys', 'Start Page Text').' <br>('.A::t('surveys', 'Female Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
            $fields['separator_messages']['finish_message'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Finish Page Text').' <br>('.A::t('surveys', 'Male Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
            $fields['separator_messages']['finish_message_f'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Finish Page Text').' <br>('.A::t('surveys', 'Female Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge'));
        endif;

		echo CWidget::create('CDataForm', array(
			'model'=>'SurveyQuestionnaires',
            'primaryKey'=>$id,        
			'operationType'=>'edit',
            'actionPath'=>$actionPath.'edit/surveyId/'.$surveyId,
			'successUrl'=>$actionPath.'manage/surveyId/'.$surveyId,
			'cancelUrl'=>$actionPath.'manage/surveyId/'.$surveyId,
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmQuestionnaireEdit',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
            'buttonsPosition'=>'both',
			'fieldSetType'=>'frameset',
            'fields'=>$fields,
			'buttons'=>array(
                'submit' => array('type'=>'submit', 'value'=>A::t('surveys', 'Update & Close'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('surveys', 'Questionnaire')),
			'return'			=> true,
		));
    ?>    
    </div>
</div>