<?php
    $this->_activeMenu = 'surveysQuestionTypes/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Questions Types Management')),
    );    
?>

<h1><?= A::t('surveys', 'Questions Types Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php 
        echo $actionMessage;

        echo CWidget::create('CGridView', array(
            'model'=>'SurveysQuestionTypes',
            'actionPath'=>'surveysQuestionTypes/manage',
            'condition'=>'',
            'defaultOrder'=>array(),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>30),
            'sorting'=>true,
            'filters'=>array(),
            'fields'=>array(
                'id'        => array('title'=>A::t('surveys', 'ID'), 'type'=>'label', 'align'=>'', 'width'=>'40px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''), 
                'name'      => array('title'=>A::t('surveys', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'is_active' => array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'surveysQuestionTypes/changeStatus/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change activity status'))),
            ),
            'actions'=>array(
                'details' => array(
                    'disabled'=>false,
                    'link'=>'surveysQuestionTypes/details/id/{id}', 'imagePath'=>'templates/backend/images/details.png', 'title'=>A::t('surveys', 'See more details')
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
        