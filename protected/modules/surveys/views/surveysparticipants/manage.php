<?php
    $this->_activeMenu = 'surveysParticipants/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Participants Management')),
    );
    
    A::app()->getClientScript()->registerCssFile('assets/modules/surveys/css/surveys.css');
?>

<h1><?= A::t('surveys', 'Participants Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>

    <div class="content">
    <?php 
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('participants', 'add')){
            echo '<a href="surveysParticipants/add" class="add-new">'.A::t('surveys', 'Add New').'</a>';
            echo '<a href="surveysParticipants/export" class="export-data align-right ml10"><b class="icon-down">&nbsp;</b> '.A::t('surveys', 'Export').'</a>';
        }
        
        $filters = array();        
        if($fieldFirstName !== 'no' || $fieldLastName !== 'no') $filters['first_name,last_name'] = array('title'=>A::t('surveys', 'Name'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'120px', 'maxLength'=>'100');
        if($fieldEmail !== 'no') $filters['email'] = array('title'=>A::t('surveys', 'Email'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'130px', 'maxLength'=>'100');
        $filters['identity_code'] = array('title'=>A::t('surveys', 'Identity Code'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'130px', 'maxLength'=>'20');
        $filters['is_active'] = array('title'=>A::t('surveys', 'Active'), 'type'=>'enum', 'operator'=>'=', 'width'=>'', 'source'=>array(''=>'', '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes')), 'emptyOption'=>true, 'emptyValue'=>'');

        $fields = array();
        $fields['identity_code'] = array('title'=>A::t('surveys', 'Identity Code'), 'type'=>'label', 'align'=>'', 'width'=>'130px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');
        if($fieldFirstName !== 'no' || $fieldLastName !== 'no') $fields['full_name'] = array('title'=>A::t('surveys', 'Name'), 'type'=>'label', 'align'=>'', 'width'=>'170px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');
        if($fieldEmail !== 'no') $fields['email'] = array('title'=>A::t('surveys', 'Email'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');
        if($fieldGender !== 'no') $fields['gender'] = array('title'=>A::t('surveys', 'Gender'), 'type'=>'enum', 'class'=>'center', 'headerClass'=>'center', 'source'=>$genders, 'width'=>'100px');
        $fields['is_active'] = array('title'=>A::t('surveys', 'Active'), 'type'=>'link', 'align'=>'', 'width'=>'120px', 'class'=>'center', 'headerClass'=>'center', 'isSortable'=>true, 'linkUrl'=>'surveysParticipants/changeStatus/id/{id}', 'linkText'=>'', 'definedValues'=>array('0'=>'<span class="badge-red">'.A::t('surveys', 'No').'</span>', '1'=>'<span class="badge-green">'.A::t('surveys', 'Yes').'</span>'), 'htmlOptions'=>array('class'=>'tooltip-link', 'title'=>A::t('surveys', 'Click to change status')));
        $fields['id'] = array('title'=>A::t('surveys', 'ID'), 'type'=>'label', 'align'=>'center', 'width'=>'40px', 'class'=>'center', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'');

        echo CWidget::create('CGridView', array(
            'model'=>'SurveysParticipants',
            'actionPath'=>'surveysParticipants/manage',
            'condition'=>'',
            'defaultOrder'=>array(),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>$filters,
            'fields'=>$fields,
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('participants', 'edit'),
                    'link'=>'surveysParticipants/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('surveys', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('participants', 'delete'),
                    'link'=>'surveysParticipants/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('surveys', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
