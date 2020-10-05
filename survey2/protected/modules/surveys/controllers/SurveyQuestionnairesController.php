<?php
/**
 * SurveyQuestionnaires controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _checkSurveyAccess
 * indexAction              _checkActionAccess
 * manageAction
 * changeStatusAction
 * addAction
 * copyAction
 * editAction
 * deleteAction
 *
 */

class SurveyQuestionnairesController extends CController
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
            Website::setMetaTags(array('title'=>A::t('surveys', 'Questionnaires Management')));

			$this->_cRequest = A::app()->getRequest();
			$this->_cSession = A::app()->getSession();

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';            
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveyQuestionnaires/manage');
    }

    /**
     * Manage action handler
     * @param int $surveyId
     */
    public function manageAction($surveyId = 0)
    {
        Website::prepareBackendAction('manage', 'survey_questionnaires', 'surveyQuestionnaires/manage');
        $this->_checkSurveyAccess($surveyId);
        
		if($this->_cSession->hasFlash('alert')){
            $alert = $this->_cSession->getFlash('alert');
            $alertType = $this->_cSession->getFlash('alertType');
			
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
		}
        
        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaires', $this->_view->surveyId);		
        $this->_view->render('surveyQuestionnaires/manage');        
    }

    /**
     * Change status survey questionnaire action handler
     * @param int $surveyId
     * @param int $id
     */
    public function changeStatusAction($surveyId = 0, $id)
    {
        Website::prepareBackendAction('edit', 'survey_questionnaires', 'surveyQuestionnaires/manage');
        $this->_checkSurveyAccess($surveyId);
        $questionnaire = $this->_checkActionAccess($id);
        
        if(SurveyQuestionnaires::model()->updateByPk($id, array('is_active'=>($questionnaire->is_active == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
		 
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);
        
        $this->redirect('surveyQuestionnaires/manage/surveyId/'.(int)$surveyId);        
    }
    
    /**
     * Add new survey questionnaire action handler
     * @param int $surveyId
     */
    public function addAction($surveyId = 0)
    {
        Website::prepareBackendAction('add', 'survey_questionnaires', 'surveyQuestionnaires/manage');
		$this->_checkSurveyAccess($surveyId);

        $this->_view->maxRecords = SurveyQuestionnaires::model()->max('sort_order', 'survey_id = :survey_id', array(':survey_id'=>$surveyId));
        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaires', $this->_view->surveyId);		
        $this->_view->render('surveyQuestionnaires/add');        
    }

    /**
     * Copy existing action handler
     * @param int $surveyId 
     */
    public function copyAction($surveyId = 0)
    {
        Website::prepareBackendAction('add', 'survey_questionnaires', 'surveyQuestionnaires/manage');
        $this->_checkSurveyAccess($surveyId);
		
		//CDebug::d($_POST, 1);

        // Retrieve all questionnaires
        $this->_view->questionnaires = $questionnaires = array(''=>A::t('surveys', 'Select'));
        $result = SurveyQuestionnaires::model()->findAll('survey_id = :surveyId', array(':surveyId'=>$surveyId));
        foreach($result as $key => $val){
            $questionnaires[$val['id']] = $val['name'];
        }

        $cRequest = A::app()->getRequest();
        $this->_view->questionnaireId = $cRequest->getPost('questionnaire_id', 'int', 0);
        $this->_view->questionnaireName = $cRequest->getPost('questionnaire_name', 'string', '');
        if($cRequest->getPost('act') == 'send'){
			$result = CWidget::create('CFormValidation', array(
				'fields'=>array(
                	'questionnaire_id' =>array('title'=>A::t('surveys', 'Copy a Questionnaire'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($questionnaires))),
                	'questionnaire_name' =>array('title'=>A::t('surveys', 'Questionnaire Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>255)),
				),
		    ));
		    if($result['error']){
		    	$this->_msg = $result['errorMessage'];
		    	$this->_view->errorField = $result['errorField'];
		    	$this->_errorType = 'validation';
		    }else{                
				if(APPHP_MODE == 'demo'){
					$this->_msg = A::t('core', 'This operation is blocked in Demo Mode!');
					$this->_errorType = 'warning';
				}elseif(SurveyQuestionnaires::model()->copyBy($this->_view->questionnaireId, $this->_view->questionnaireName)){
					$this->_cSession->setFlash('alert', A::t('surveys', 'The questionnaire has been successfully copied!'));
					$this->_cSession->setFlash('alertType', 'success');
                    $this->redirect('surveyQuestionnaires/manage/surveyId/'.$this->_view->surveyId);
                }else{
					$this->_msg = A::t('surveys', 'An error occurred while copied this questionnaire!');
					$this->_errorType = 'error';                                        
                }
            }
        }
        if(!empty($this->_msg)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($this->_errorType, $this->_msg, array('button'=>true)));
        }
        
        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaires', $this->_view->surveyId);		
        $this->_view->questionnaires = $questionnaires;
        $this->_view->render('surveyQuestionnaires/copy');
    }

    /**
     * Edit survey questionnaire action handler
     * @param int $surveyId 
     * @param int $id 
     */
    public function editAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'survey_questionnaires', 'surveyQuestionnaires/manage');
        $this->_checkSurveyAccess($surveyId);
        $questionnaire = $this->_checkActionAccess($id); 
        
        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaires', $this->_view->surveyId);		
        $this->_view->id = $id;
        $this->_view->render('surveyQuestionnaires/edit');        
    }

    /**
     * Delete survey questionnaire action handler
     * @param int $surveyId 
     * @param int $id 
     */
    public function deleteAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('delete', 'survey_questionnaires', 'surveyQuestionnaires/manage');
        $this->_checkSurveyAccess($surveyId);
        $questionnaire = $this->_checkActionAccess($id); 
        
        $alert = '';
    	$alertType = '';
    
		if($questionnaire->delete()){				
			$alert = A::t('surveys', 'Questionnaire successfully deleted!');
			$alertType = 'success';	
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
		   	}else{
				$alert = A::t('surveys', 'Questionnaire deleting error');
				$alertType = 'error';
		   	}			
		}

		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaires', $surveyId);		
		$this->_view->render('surveyQuestionnaires/manage');        
    }

	/**
	 * Check if passed survey ID is valid
	 * @param int $surveyId
	 * @param string $action
	 */
	private function _checkSurveyAccess($surveyId = 0, $action = '')
	{
		$survey = Surveys::model()->findByPk((int)$surveyId);
		if(empty($survey)){
			$this->redirect('surveys/manage');
		}
		$this->_view->surveyId = $surveyId;
		$this->_view->surveyName = $survey->name;
        $this->_view->itemsPerPage = $survey->items_per_page;
        $this->_view->surveyLink = 'surveyQuestionnaires/manage/surveyId/'.$surveyId;
        if(ModulesSettings::model()->param('surveys', 'field_gender') == 'no'){
            $this->_view->surveyGenderFormulation = false;    
        }else{
            $this->_view->surveyGenderFormulation = $survey->gender_formulation;
        }        
	}
 
    /**
     * Check if passed record ID is valid
     * @param int $id
     */
    private function _checkActionAccess($id = 0)
    {        
        $model = SurveyQuestionnaires::model()->findByPk($id);
        if(!$model){
            $this->redirect('surveyQuestionnaires/manage');
        }
        return $model;
    }
    
}