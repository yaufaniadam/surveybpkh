<?php
/**
 * SurveysQuestionTypes controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _checkQuestionTypeAccess
 * indexAction
 * detailsAction
 * changeStatusAction
 * 
 */

class SurveysQuestionTypesController extends CController
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

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active participants
            Website::setMetaTags(array('title'=>A::t('surveys', 'Surveys Questions Type Management')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            $this->_msg = '';
            $this->_errorType = '';
            
			$this->_cRequest = A::app()->getRequest();
			$this->_cSession = A::app()->getSession();

            $this->_view->tabs = SurveysComponent::prepareTab('questionstypes');
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveysQuestionTypes/manage');
    }
    
    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');

		if($this->_cSession->hasFlash('alert')){
            $alert = $this->_cSession->getFlash('alert');
            $alertType = $this->_cSession->getFlash('alertType');
			
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
		}

        $this->_view->render('surveysQuestionTypes/manage');        
    }

    /**
     * Details action handler
     * @param int $id 
     */
    public function detailsAction($id = '')
    {
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');
        $qType = $this->_checkQuestionTypeAccess($id);
        
        $this->_view->name = $qType->name;
        $this->_view->description = $qType->description;
        $this->_view->htmlExample = $qType->html_example;
        $this->_view->codeExample = $qType->code_example;
        $this->_view->render('surveysQuestionTypes/details');        
    }
    
    /**
     * Change status questions type action handler (activity)
     * @param int $id
     */
    public function changeStatusAction($id = 0)
    {
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');
        $qType = $this->_checkQuestionTypeAccess($id);

        if(SurveysQuestionTypes::model()->updateByPk($id, array('is_active'=>($qType->is_active == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
        
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->redirect('surveysQuestionTypes/manage');        
    }

	/**
	 * Check if passed question type ID is valid
	 * @param int $id
	 */
	private function _checkQuestionTypeAccess($id = 0)
	{
		$model = SurveysQuestionTypes::model()->findByPk((int)$id);
		if(!$model){
			$this->redirect('surveysQuestionTypes/manage');
		}
        return $model;
    }    
        
}
