<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Questionnaire Items Management').' - '.A::t('surveys', 'Edit Questionnaire Item').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Questionnaires Management'), 'url'=>'surveyQuestionnaires/manage/surveyId/'.$surveyId),
        array('label'=>A::t('surveys', 'Questionnaire Items Management')),
    );    

    $rowInd = ($surveyGenderFormulation) ? '4' : '3';
	A::app()->getClientScript()->registerScriptFile('assets/modules/surveys/js/surveys.js');
	A::app()->getClientScript()->registerScript(
		'questions-set',
		'questionTypeId_OnChange("frmQuestionnaireEditItem_row_","'.$rowInd.'","'.$selectedValue.'");',
		2
	);

	A::app()->getClientScript()->registerCss(
		'add-qitem',
		'.chosen-single,.chosen-drop{
			min-width:140px;
		}
		.code-example{
			float:right;
			width:30%;
			padding:10px;
			border: 1px solid #ddd;
			background-color:#fffff0;
		}'
	);
?>

<h1><?= A::t('surveys', 'Questionnaire Items Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab previous" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <span class="sub-tab-divider">&raquo;</span>
        <a class="sub-tab active" href="<?= $questionnaireLink; ?>"><?= strip_tags($questionnaireName); ?></a>
        <?= A::t('surveys', 'Edit Questionnaire Item') ?>
    </div>
    <div class="content">
    <?php   
        $actionPath = 'surveyQuestionnaireItems/';		

        $fields = array();
		$fields['entity_questionnaire_id'] = array('type'=>'data', 'default'=>$questionnaireId);
        if(!$surveyGenderFormulation):
            $fields['question_text'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Question Text'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>512), 'htmlOptions'=>array('maxLength'=>'512', 'class'=>'middle'));
        else:
            $fields['question_text'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Question').' ('.A::t('surveys', 'Male Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>512), 'htmlOptions'=>array('maxLength'=>'512', 'class'=>'middle'));
            $fields['question_text_f'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Question').' ('.A::t('surveys', 'Female Formula').')', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>512), 'htmlOptions'=>array('maxLength'=>'512', 'class'=>'middle'));            
        endif;
        $fields['help_text'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Description'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1024), 'htmlOptions'=>array('maxLength'=>'1024', 'class'=>'small'));

        $fields['question_type_id'] = array('type'=>'select', 'title'=>A::t('surveys', 'Question Type'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($questionTypes)), 'data'=>$questionTypes, 'htmlOptions'=>array('onchange'=>'questionTypeId_OnChange(\'frmQuestionnaireEditItem_row_\',\''.$rowInd.'\',this.value)'));
        $fields['question_type_original'] = array('type'=>'hidden', 'defaultEditMode'=>$selectedValue);            
        $fields['variant_text'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Multiple Answers'), 'tooltip'=>A::t('surveys', 'Multiple Answers Tooltip'), 'defaultEditMode'=>$variantText, 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>1024), 'htmlOptions'=>array('maxLength'=>'1024', 'class'=>'xxlarge'), 'appendCode'=>'<div data-caption="'.A::te('surveys', 'Example of Answers').':"></div>');
        $fields['variant_text_original'] = array('type'=>'hidden', 'defaultEditMode'=>$variantText);            
        $fields['date_format'] = array('type'=>'select', 'title'=>A::t('surveys', 'Date Format'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>$dateFormat), 'data'=>$dateFormat, 'htmlOptions'=>array());
        $fields['file_path'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'File Path'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxlength'=>'255', 'class'=>'large'));
        $fields['validation_type'] = array('type'=>'select', 'title'=>A::t('surveys', 'Validation Type'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($validationType)), 'data'=>$validationType, 'htmlOptions'=>array());
        $fields['content'] = array('type'=>'textarea', 'title'=>A::t('surveys', 'Content'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>4096), 'htmlOptions'=>array('maxLength'=>'4096', 'class'=>'xxlarge'));
        $fields['alignment_type'] = array('type'=>'select', 'title'=>A::t('surveys', 'Alignment Type'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($alignmentType)), 'data'=>$alignmentType, 'htmlOptions'=>array());
        $fields['sort_order'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Sort Order'), 'tooltip'=>'', 'default'=>0, 'validation'=>array('required'=>true, 'type'=>'numeric'), 'htmlOptions'=>array('maxlength'=>'3', 'class'=>'small'));
        $fields['is_required'] = array('type'=>'checkbox', 'title'=>A::t('surveys', 'Required'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
        $fields['is_active'] = array('type'=>'checkbox', 'title'=>A::t('surveys', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');

		echo CWidget::create('CDataForm', array(
			'model'=>'SurveyQuestionnaireItems',
            'primaryKey'=>$id,        
			'operationType'=>'edit',
            'actionPath'=>$actionPath.'edit/questionnaireId/'.$questionnaireId.'/id/'.$id,
			'successUrl'=>$actionPath.'manage/questionnaireId/'.$questionnaireId,
			'cancelUrl'=>$actionPath.'manage/questionnaireId/'.$questionnaireId,
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmQuestionnaireEditItem',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
            'buttonsPosition'=>'both',
			'fieldSetType'=>'frameset',
			'fields'=>$fields,
			'buttons'=>array(
                'submitUpdateClose'=>array('type'=>'submit', 'value'=>A::t('surveys', 'Update & Close'), 'htmlOptions'=>array('name'=>'btnUpdateClose')),
                //'submitUpdate'=>array('type'=>'submit', 'value'=>A::t('surveys', 'Update'), 'htmlOptions'=>array('name'=>'btnUpdate')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('surveys', 'Questionnaire Item')),
			'return'			=> true,
        ));
    ?>
    </div>
</div>
