<?php
    $this->_activeMenu = 'surveys/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management')),
    );
    
    A::app()->getClientScript()->registerCssFile('assets/modules/surveys/css/surveys.css');
?>

<h1><?= A::t('surveys', 'Surveys Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php 
        echo $actionMessage;	

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('surveys', 'add')):
            echo '<a href="surveys/add" class="add-new">'.A::t('surveys', 'Add New').'</a> ';
            if($surveysTotal > 0):
				echo '<a href="surveys/copy" class="copy-existing"><b class="icon-copy">&nbsp;</b> '.A::t('surveys', 'Copy').'</a>';
			endif;
        endif;
		
        echo CWidget::create('CGridView', array(
            'model'=>'Surveys',
            'actionPath'=>'surveys/manage',
            'condition'=>'',
            'defaultOrder'=>array('sort_order'=>'ASC'),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'access_mode' 	=> array('title'=>A::t('surveys', 'Access Mode'), 'type'=>'enum', 'operator'=>'=', 'default'=>'', 'width'=>'', 'source'=>$accessModeFilter, 'emptyOption'=>true, 'emptyValue'=>''),
                'expires_at' 	=> array('title'=>A::t('surveys', 'Expires At'), 'type'=>'datetime', 'operator'=>'=', 'default'=>'', 'width'=>'80px', 'maxLength'=>'', 'format'=>$dateFormat),
            ),
            'fields'=>array(
                'name' 					=> array('title'=>A::t('surveys', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'created_at' 			=> array('title'=>A::t('surveys', 'Created At'), 'type'=>'datetime', 'class'=>'left', 'headerClass'=>'left', 'width'=>'110px', 'definedValues'=>array(null=>A::t('surveys', 'unknown')), 'format'=>$dateFormat),
                'expires_at' 			=> array('title'=>A::t('surveys', 'Expires At'), 'type'=>'datetime', 'class'=>'left', 'headerClass'=>'left', 'width'=>'110px', 'definedValues'=>array(null=>A::t('surveys', 'never')), 'format'=>$dateFormat),
                'code' 					=> array('title'=>A::t('surveys', 'Code'), 'type'=>'label', 'align'=>'', 'width'=>'100px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'link_questionnaires' 	=> array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyQuestionnaires/manage/surveyId/{id}', 'linkText'=>A::t('surveys', 'Questionnaires'), 'htmlOptions'=>array(), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
				'id'    				=> array('title'=>'', 'type'=>'enum', 'sourceField'=>'id', 'operator'=>'=', 'default'=>'', 'width'=>'40px', 'source'=>$questionnaireCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'link_participants' 	=> array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'85px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveyParticipants/manage/surveyId/{id}', 'linkText'=>A::t('surveys', 'Participants'), 'htmlOptions'=>array(), 'prependCode'=>'[ ', 'appendCode'=>' ]'),
				'id_participant'		=> array('title'=>'', 'type'=>'enum', 'sourceField'=>'id', 'operator'=>'=', 'default'=>'', 'width'=>'40px', 'source'=>$participantCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'link_results' 			=> array('title'=>'', 'type'=>'link', 'align'=>'', 'width'=>'60px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveys/results/surveyId/{id}', 'linkText'=>A::t('surveys', 'Results'), 'htmlOptions'=>array(), 'prependCode'=>'[ ', 'appendCode'=>' ]'),                
				'id_results'			=> array('title'=>'', 'type'=>'enum', 'sourceField'=>'id', 'operator'=>'=', 'default'=>'', 'width'=>'40px', 'source'=>$resultsCounters, 'definedValues'=>array(''=>'<span class="label-zerogray">0</span>'), 'isSortable'=>true, 'class'=>'left', 'prependCode'=>'<span class="label-lightgray">', 'appendCode'=>'</span>'),
                'link_preview' 			=> array('title'=>'', 'type'=>'link', 'align'=>'left', 'width'=>'80px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveys/show/code/{code}', 'linkText'=>A::t('surveys', 'Preview'), 'htmlOptions'=>array('target'=>'_new'), 'prependCode'=>'[ ', 'appendCode'=>' <img src="templates/backend/images/external_link.gif" alt="external link"> ]'),
                'sort_order' 			=> array('title'=>A::t('surveys', 'Order'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'60px'),
                'is_active' 			=> array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'70px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>false, 'linkUrl'=>'surveys/changeStatus/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change status'))),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('surveys', 'edit'),
                    'link'=>'surveys/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('surveys', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('surveys', 'delete'),
                    'link'=>'surveys/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('surveys', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
