<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Survey Questionnaires Management').' - '.A::t('surveys', 'Copy Existing Questionnaire').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Copy Questionnaire'))
    );    
?>

<h1><?= A::t('surveys', 'Survey Questionnaires Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
	<div class="sub-title">
        <a class="sub-tab active" href="<?= $surveyLink; ?>"><?= strip_tags($surveyName); ?></a>
        <?= A::t('surveys', 'Copy Existing Questionnaire'); ?>
    </div>
    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CFormView', array(
            'action'=>'surveyQuestionnaires/copy/surveyId/'.$surveyId,
            'cancelUrl'=>'surveyQuestionnaires/manage/surveyId/'.$surveyId,
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmSurveyQuestionnaireCopy',
                'enctype'=>'multipart/form-data',
                'autoGenerateId'=>false
            ),
            'requiredFieldsAlert'=>true,
            'fieldSetType'=>'frameset|tabs',
            'fields'=>array(
                'act'=>array('type'=>'hidden', 'value'=>'send'),
                'questionnaire_id'=>array('type'=>'select', 'title'=>A::t('surveys', 'Copy a Questionnaire'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>$questionnaireId, 'data'=>$questionnaires, 'htmlOptions'=>array('onchange'=>"questionnaireId_OnChange(this)", 'style'=>'max-width:210px')),
                'questionnaire_name'=>array('type'=>'textbox', 'title'=>A::t('surveys', 'Questionnaire Name'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>$questionnaireName, 'htmlOptions'=>array('id'=>'questionnaire_name', 'maxLength'=>'100', 'class'=>'large')),
            ),
            'checkboxes'=>array(),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>A::t('surveys', 'Copy'), 'htmlOptions'=>array('name'=>'')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','surveyQuestionnaires/manage/surveyId/".$surveyId."');")),
            ),
            'buttonsPosition'=>'bottom',
            'events'=>array(),
            'return'=>true,
        ));
    ?>
    </div>
</div>

<?php
	A::app()->getClientScript()->registerScript(
		'questionnaires-select',
		'function questionnaireId_OnChange(el){
            var selectedText = $(el).find("option:selected").text(),
				selectedVal = $(el).find("option:selected").val(),
				newName = "";
            if(selectedVal != "" && selectedText != ""){
                newName = "'.A::t('surveys', 'Copy of a questionnaire').'";  
                if(selectedText.length < 50) newName += ": "+selectedText;
                $("#questionnaire_name").val(newName);
            }else{
                $("#questionnaire_name").val("");    
            }            
		};',
		0
	); 
?>