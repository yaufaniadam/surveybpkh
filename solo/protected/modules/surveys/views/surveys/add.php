<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Surveys Management').' - '.A::t('surveys', 'Add Survey').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Add Survey'))
    );    

    $rowInd = ($fieldGender == 'no' ? '8' : '9');
	A::app()->getClientScript()->registerScriptFile('assets/modules/surveys/js/surveys.js');
	A::app()->getClientScript()->registerScript(
		'gender-formulation',
		'genderFormulation_OnChange("frmSurveyAdd_row_","'.$rowInd.'",$("#frmSurveyAdd_gender_formulation").val());',
		2
	);
?>

<h1><?= A::t('surveys', 'Surveys Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
    <div class="sub-title"><?= A::t('surveys', 'Add New Survey'); ?></div>    
    <div class="content">
    <?php
		echo CWidget::create('CDataForm', array(
			'model'=>'Surveys',
			'operationType'=>'add',
			'action'=>'surveys/add',
			'successUrl'=>'surveys/manage',
			'cancelUrl'=>'surveys/manage',
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmSurveyAdd',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
			'fieldSetType'=>'frameset',
			'fields'=>array(
                'separator_general' =>array(
                    'separatorInfo'=>array('legend'=>A::t('surveys', 'General')),
                    'name'               => array('type'=>'textbox', 'title'=>A::t('surveys', 'Name'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>100), 'htmlOptions'=>array('maxlength'=>'100', 'class'=>'middle')),
                    'description'        => array('type'=>'textarea', 'title'=>A::t('surveys', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1024), 'htmlOptions'=>array('maxLength'=>'1024', 'class'=>'middle')),
                    'created_at'         => array('type'=>'data', 'default'=>LocalTime::currentDate()),
                    'expires_at'         => array('type'=>'datetime', 'title'=>A::t('surveys', 'Expires At'), 'validation'=>array('required'=>false, 'type'=>'date', 'maxLength'=>10), 'htmlOptions'=>array('maxlength'=>'10', 'style'=>'width:100px'), 'default'=>'', 'definedValues'=>array(), 'appendCode'=>' / '.A::t('surveys', 'leave blank if not expired'), 'minDate'=>'-1', 'maxDate'=>''),
                    'access_mode'        => array('type'=>'select', 'title'=>A::t('surveys', 'Access Mode'), 'tooltip'=>A::t('surveys', 'What type of participants can take this survey?'), 'default'=>'p', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($accessMode)), 'data'=>$accessMode, 'htmlOptions'=>array()),
                    'votes_mode'         => array('type'=>'select', 'title'=>A::t('surveys', 'Votes Mode'), 'tooltip'=>A::t('surveys', 'How many times participants can complete this survey?'), 'default'=>'o', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($votesMode)), 'data'=>$votesMode, 'htmlOptions'=>array()),
                    'gender_formulation' => array('type'=>'select', 'title'=>A::t('surveys', 'Gender Formulation'), 'tooltip'=>A::t('surveys', 'Whether to allow the questions formulation by gender?'), 'default'=>'0', 'validation'=>array('required'=>($fieldGender == 'allow-required' ? true : false), 'type'=>'set', 'source'=>array_keys($genderFormulation)), 'data'=>$genderFormulation, 'htmlOptions'=>array('onchange'=>'genderFormulation_OnChange(\'frmSurveyAdd_row_\',\''.$rowInd.'\',this.value)'), 'disabled'=>($fieldGender == 'no' ? true : false)),
                    'items_per_page'     => array('type'=>'textbox', 'title'=>A::t('surveys', 'Items Per Page'), 'tooltip'=>A::t('surveys', 'Default value that will be used in this survey questionnaires'), 'default'=>'5', 'validation'=>array('required'=>true, 'type'=>'range', 'minValue'=>'1', 'maxValue'=>'99'), 'htmlOptions'=>array('maxlength'=>'2', 'class'=>'small')),
                    'sort_order'         => array('type'=>'textbox', 'title'=>A::t('surveys', 'Sort Order'), 'tooltip'=>'', 'default'=>$sortOrder, 'validation'=>array('required'=>true, 'type'=>'numeric'), 'htmlOptions'=>array('maxlength'=>'3', 'class'=>'small')),
                    'is_active'          => array('type'=>'checkbox', 'title'=>A::t('surveys', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom'),
                ),            
                'separator_messages' =>array(
                    'separatorInfo'=>array('legend'=>A::t('surveys', 'Start and Finish Pages')),
                    'login_message'    	 => array('type'=>'textarea', 'title'=>A::t('surveys', 'Login Page Text'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge')),
					'welcome_message'    => array('type'=>'textarea', 'title'=>A::t('surveys', 'Welcome Page Text').' <br>('.A::t('surveys', 'Male Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge')),
                    'welcome_message_f'  => array('type'=>'textarea', 'title'=>A::t('surveys', 'Welcome Page Text').' <br>('.A::t('surveys', 'Female Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge')),
                    'complete_message'   => array('type'=>'textarea', 'title'=>A::t('surveys', 'Complete Page Text').' <br>('.A::t('surveys', 'Male Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge')),
                    'complete_message_f' => array('type'=>'textarea', 'title'=>A::t('surveys', 'Complete Page Text').' <br>('.A::t('surveys', 'Female Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xlarge')),
                ),            
            ),
			'buttons'=>array(
                'submit' => array('type'=>'submit', 'value'=>A::t('surveys', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
            'buttonsPosition'	=> 'both',
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('surveys', 'Survey')),
			'return'			=> true,
		));
    ?>    
    </div>
</div>