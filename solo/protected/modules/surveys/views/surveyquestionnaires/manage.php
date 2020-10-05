<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Questionnaires Management')),
    );
    
    A::app()->getClientScript()->registerCssFile('assets/modules/surveys/css/surveys.css');
?>

<h1><?= A::t('surveys', 'Survey Questionnaires Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
    </div>
    <div class="content">
    <?php 
        echo $actionMessage;

        $actionPath = 'surveyQuestionnaires/';		
        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('survey_questionnaires', 'add')):
            echo '<a href="'.$actionPath.'add/surveyId/'.$surveyId.'" class="add-new">'.A::t('surveys', 'Add New Questionnaire').'</a>';
            echo '<a href="surveyQuestionnaires/copy/surveyId/'.$surveyId.'" class="copy-existing"><b class="icon-copy">&nbsp;</b> '.A::t('surveys', 'Copy').'</a>';
        endif;
        
        echo CWidget::create('CGridView', array(
            'model'=>'SurveyQuestionnaires',
            'actionPath'=>$actionPath.'manage/surveyId/'.$surveyId,
            'condition'=>CConfig::get('db.prefix').'surveys_entity_questionnaires.survey_id = '.(int)$surveyId,
            'defaultOrder'=>array('sort_order'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>30),
            'sorting'=>true,
            'filters'=>array(
                'name' 			=> array('title'=>A::t('surveys', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'100'),
                'category_title' => array('title'=>A::t('surveys', 'Category'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'120px', 'maxLength'=>'100'),
                'is_active' 	=> array('title'=>A::t('surveys', 'Active'), 'type'=>'enum', 'table'=>'surveys_entity_questionnaires', 'operator'=>'=', 'width'=>'', 'source'=>array(''=>'', '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>''),
            ),
            'fields'=>array(
                'name'  => array('title'=>A::t('surveys', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'questionnaire_key' => array('title'=>A::t('surveys', 'Key'), 'type'=>'label', 'align'=>'', 'width'=>'115px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>'', 'format'=>''),
                'category_title' => array('title'=>A::t('surveys', 'Category'), 'type'=>'label', 'align'=>'', 'width'=>'110px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(''=>'- '.A::t('surveys', 'not defined').' -'), 'format'=>''),
                'link_questions' => array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyQuestionnaireItems/manage/questionnaireId/{id}', 'linkText'=>A::t('surveys', 'Questions'), 'htmlOptions'=>array(), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
                'sort_order' => array('title'=>A::t('surveys', 'Sort Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'90px'),
                'is_active' => array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'90px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyQuestionnaires/changeStatus/surveyId/'.(int)$surveyId.'/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change status'))),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('survey_questionnaires', 'edit'),
		            'link'=>$actionPath.'edit/surveyId/'.$surveyId.'/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('surveys', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('survey_questionnaires', 'delete'),
                    'link'=>$actionPath.'delete/surveyId/'.$surveyId.'/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('surveys', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
