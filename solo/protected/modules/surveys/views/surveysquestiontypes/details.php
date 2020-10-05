<?php
    $this->_activeMenu = 'surveysQuestionTypes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Questions Types Management'), 'url'=>'surveysQuestionTypes/manage'),
        array('label'=>A::t('surveys', 'Question Type Details')),
    );    

    A::app()->getClientScript()->registerCssFile('assets/modules/surveys/css/surveys.css');
?>

<h1><?= A::t('surveys', 'Questions Types Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

    <div class="sub-title"><?= A::t('surveys', 'Question Type Details'); ?></div>    
    <div class="content">        
    <?php
        echo CWidget::create('CFormView', array(
            'action'=>'',
            'cancelUrl'=>'',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmQuestionType',
                //'enctype'=>'multipart/form-data',
                'autoGenerateId'=>false
            ),
            'requiredFieldsAlert'=>false,
            'fields'=>array(
                'name' => array('type'=>'label', 'title'=>A::t('surveys', 'Name'), 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>$name, 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('style'=>'width:550px')),
                'description' => array('type'=>'label', 'title'=>A::t('surveys', 'Description'), 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>$description, 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array('style'=>'width:550px')),
                'code_example' => array('type'=>'html', 'title'=>A::t('surveys', 'Code Example'), 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>$codeExample, 'appendCode'=>'<div style="clear:both"></div>'),
                'html_example' => array('type'=>'html', 'title'=>A::t('surveys', 'HTML Example'), 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>$htmlExample, 'appendCode'=>'<div style="clear:both"></div>'),
            ),
            'buttons'=>array(
                'cancel' => array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('class'=>'button white', 'onclick'=>"$(location).attr('href','surveysQuestionTypes/manage');")),
            ),
            'buttonsPosition'=>'bottom',
            'events'=>array(),
            'return'=>true,
        ));
    ?>    
    </div>
</div>
