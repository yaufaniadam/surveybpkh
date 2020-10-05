<?php
/**
 * SurveysHomepage controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              
 * indexAction              
 * 
 */

class SurveysHomepageController extends CController
{
	
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Block access if the module is not installed
        if(!Modules::model()->isInstalled('surveys')){
            if(CAuth::isLoggedInAsAdmin()){
                $this->redirect('modules/index');
            }else{
                $this->redirect(Website::getDefaultPage());
            }
        }

		// Set Frontend
		Website::setFrontend();

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';
    }

    /**
     * Controller default Frontend action handler
     */
    public function indexAction()
    {
		$this->_view->hasTestData = Modules::model()->param('surveys', 'has_test_data');		
		$this->_view->render('surveyshomepage/index');
    }

}
