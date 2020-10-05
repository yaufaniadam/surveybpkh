<?php
    $this->_activeMenu = 'surveysParticipants/manage';
    $this->_pageTitle = A::t('surveys', 'Participants Management').' - '.A::t('surveys', 'Add Participant').' | '.CConfig::get('name');
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Participants Management'), 'url'=>'surveysParticipants/manage'),
        array('label'=>A::t('surveys', 'Add Participant'))
    );    
?>

<h1><?= A::t('surveys', 'Participants Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
    <div class="sub-title"><?= A::t('surveys', 'Add New Participant'); ?></div>    
    <div class="content">
    <?php   
        $fields = array();
        if($fieldIdentityCode == 'allow') $fields['identity_code'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Identity Code'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'identityCode', 'minLength'=>4, 'maxLength'=>20, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>'20'));
        if($fieldPassword == 'allow') $fields['password'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Password'), 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6, 'maxLength'=>10, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'10'));
        if($fieldFirstName !== 'no') $fields['first_name'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'First Name'), 'default'=>'', 'validation'=>array('required'=>($fieldFirstName == 'allow-required' ? true : false), 'type'=>'any', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        if($fieldLastName !== 'no') $fields['last_name'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Last Name'), 'default'=>'', 'validation'=>array('required'=>($fieldFirstName == 'allow-required' ? true : false), 'type'=>'any', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32'));
        if($fieldEmail !== 'no') $fields['email'] = array('type'=>'textbox', 'title'=>A::t('surveys', 'Email'), 'default'=>'', 'validation'=>array('required'=>($fieldEmail == 'allow-required' ? true : false), 'type'=>'email', 'maxLength'=>100, 'unique'=>false), 'htmlOptions'=>array('maxlength'=>'100', 'class'=>'email', 'autocomplete'=>'off'));
        if($fieldGender !== 'no') $fields['gender'] = array('type'=>'select', 'title'=>A::t('surveys', 'Gender'), 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array_keys($genders)), 'data'=>$genders, 'htmlOptions'=>array());
        $fields['ip_address'] = array('type'=>'data', 'default'=>A::app()->getRequest()->getUserHostAddress());
        $fields['is_active'] = array('type'=>'checkbox', 'title'=>A::t('surveys', 'Active'), 'default'=>'1', 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'viewType'=>'custom');
            
        echo CWidget::create('CDataForm', array(
			'model'=>'SurveysParticipants',
			'operationType'=>'add',
			'action'=>'surveysParticipants/add',
			'successUrl'=>'surveysParticipants/manage',
			'cancelUrl'=>'surveysParticipants/manage',
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmParticipantAdd',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
			'fieldSetType'=>'frameset',
			'fields'=>$fields,
			'buttons'=>array(
                'submit' => array('type'=>'submit', 'value'=>A::t('surveys', 'Create'), 'htmlOptions'=>array('name'=>'')),
                'cancel' => array('type'=>'button', 'value'=>A::t('surveys', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'	=> 'core',
			'alerts'			=> array('type'=>'flash', 'itemName'=>A::t('surveys', 'Participant')),
			'return'			=> true,
		));
    ?>    
    </div>
</div>
    