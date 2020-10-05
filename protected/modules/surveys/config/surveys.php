<?php

return array(
    // module components
    'components' => array(
        'SurveysComponent' => array('enable' => true, 'class' => 'SurveysComponent'),
    ),

	// Default Backend url (optional, if defined - will be used as application default settings)
	'backendDefaultUrl' => 'surveys/manage',

    // Default settings (optional, if defined - will be used as application default settings)
	//'defaultErrorController' => 'Error',
    'defaultController' => 'SurveysHomepage',
    'defaultAction' => 'index',
);