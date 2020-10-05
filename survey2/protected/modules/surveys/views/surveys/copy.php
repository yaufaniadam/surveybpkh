<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_pageTitle = A::t('surveys', 'Surveys Management').' - '.A::t('surveys', 'Copy Existing Survey').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Copy Survey'))
    );    
?>

<h1><?= A::t('surveys', 'Surveys Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
    <div class="sub-title"><?= A::t('surveys', 'Copy Existing Survey'); ?></div>    
    <div class="content">
    <?php
        echo $actionMessage;

        echo CWidget::create('CFormView', array(
            'action'=>'surveys/copy',
            'cancelUrl'=>'surveys/manage',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmSurveyCopy',
                'enctype'=>'multipart/form-data',
                'autoGenerateId'=>false
            ),
            'requiredFieldsAlert'=>true,
            'fieldSetType'=>'frameset|tabs',
            'fields'=>array(
                'act'=>array('type'=>'hidden', 'value'=>'send'),
                'survey_id'=>array('type'=>'select', 'title'=>A::t('surveys', 'Copy a Survey'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>$surveyId, 'data'=>$surveys, 'htmlOptions'=>array('onchange'=>"surveyId_OnChange(this)", 'style'=>'max-width:210px')),
                'survey_name'=>array('type'=>'textbox', 'title'=>A::t('surveys', 'Survey Name'), 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>$surveyName, 'htmlOptions'=>array('id'=>'survey_name', 'maxLength'=>'100')),
            ),
            'checkboxes'=>array(),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>A::t('surveys', 'Copy'), 'htmlOptions'=>array('name'=>'')),
                'cancel'=>array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white', 'onclick'=>"$(location).attr('href','surveys/manage');")),
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
		'surveys-select',
		'function surveyId_OnChange(el){
            var selectedText = $(el).find("option:selected").text(),
				selectedVal = $(el).find("option:selected").val(),
				newName = "";
            if(selectedVal != "" && selectedText != ""){
                newName = "'.A::t('surveys', 'Copy of a survey').'";  
                if(selectedText.length < 50) newName += ": "+selectedText;
                $("#survey_name").val(newName);
            }else{
                $("#survey_name").val("");    
            }            
		};',
		0
	); 
?>