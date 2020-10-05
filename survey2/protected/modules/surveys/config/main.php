<?php

return array(
    // Module classes
    'classes' => array(
        'Surveys',
		'SurveysHomepage',
        'SurveysParticipants',
        'SurveysQuestionTypes',
        'SurveyAnswers',
        'SurveyQuestionnaires',
        'SurveyQuestionnaireItems',
        'SurveyQuestionnaireItemVariants',
        'SurveyParticipants',
    ),
    // Management links
    'managementLinks' => array(
        A::t('surveys', 'Surveys') => 'surveys/manage',
        A::t('surveys', 'Participants') => 'surveysParticipants/index',
        A::t('surveys', 'Questions Types') => 'surveysQuestionTypes/manage',
    ),    
);