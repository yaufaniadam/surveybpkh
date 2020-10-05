<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Questionnaires Management'), 'url'=>'surveyQuestionnaires/manage/surveyId/'.$surveyId),
        array('label'=>A::t('surveys', 'Questionnaire Items Management')),
    );    
?>

<h1><?= A::t('surveys', 'Questionnaire Items Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab previous" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <span class="sub-tab-divider">&raquo;</span>
        <a class="sub-tab active" href="<?= $questionnaireLink; ?>"><?= strip_tags($questionnaireName); ?></a>
    </div>
    <div class="content">
    <?php 
        echo $actionMessage;
        
        $fields = array();
        $fields['index_id'] = array('title'=>'#', 'type'=>'index', 'width'=>'20px', 'class'=>'index-column right', 'headerClass'=>'index-column right', 'isSortable'=>false);
        $fields['question_text_formatted'] = array('title'=>A::t('surveys', 'Question'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');
        $fields['question_type_formatted'] = array('title'=>A::t('surveys', 'Type'), 'type'=>'html', 'align'=>'', 'width'=>'240px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');
        if($surveyGenderFormulation):
            $formulationDefinedValues = array(''=>'', 'm'=>A::t('surveys', 'Male'), 'f'=>A::t('surveys', 'Female'), 'm/f'=>A::t('surveys', 'Male/Female'));
            $fields['question_formulation_formatted'] = array('title'=>A::t('surveys', 'Formulation'), 'type'=>'label', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>$formulationDefinedValues, 'format'=>'');
        endif;
        $fields['sort_order'] = array('title'=>A::t('surveys', 'Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'80px');
        $fields['is_required'] = array('title'=>A::t('surveys', 'Required'), 'type'=>'link', 'align'=>'', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'surveyQuestionnaireItems/changeStatus/questionnaireId/'.$questionnaireId.'/id/{id}/status/requirement', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change requirement status')));        
        $fields['is_active'] = array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'surveyQuestionnaireItems/changeStatus/questionnaireId/'.$questionnaireId.'/id/{id}/status/activity', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change activity status')));        
        
        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('survey_questions', 'add')){
            echo '<a href="surveyQuestionnaireItems/add/questionnaireId/'.$questionnaireId.'" class="add-new">'.A::t('surveys', 'Add New').'</a>';
        }
        
        echo CWidget::create('CGridView', array(
            'model'=>'SurveyQuestionnaireItems',
            'actionPath'=>'surveyQuestionnaireItems/manage/questionnaireId/'.$questionnaireId,
            'condition'=>CConfig::get('db.prefix').'surveys_entity_questionnaire_items.entity_questionnaire_id = '.(int)$questionnaireId,
            'defaultOrder'=>array(CConfig::get('db.prefix').'surveys_entity_questionnaire_items.sort_order'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>100),
            'sorting'=>true,
            'filters'=>array(
                'question_text' => array('title'=>A::t('surveys', 'Question'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'120px', 'maxLength'=>'100'),
                'is_active'     => array('title'=>A::t('surveys', 'Active'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'surveys_entity_questionnaire_items', 'operator'=>'=', 'width'=>'', 'source'=>array(''=>'', '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
                'is_required'   => array('title'=>A::t('surveys', 'Required'), 'type'=>'enum', 'table'=>CConfig::get('db.prefix').'surveys_entity_questionnaire_items', 'operator'=>'=', 'width'=>'', 'source'=>array(''=>'', '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>$fields,
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('survey_questions', 'edit'),
                    'link'=>'surveyQuestionnaireItems/edit/questionnaireId/'.$questionnaireId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('surveys', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('participants', 'delete'),
                    'link'=>'surveyQuestionnaireItems/delete/questionnaireId/'.$questionnaireId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('surveys', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
