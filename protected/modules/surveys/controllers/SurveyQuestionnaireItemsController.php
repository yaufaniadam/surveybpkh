<?php
/**
 * SurveyQuestionnaireItems controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _checkQuestionnaireAccess
 * indexAction              _checkQuestionnaireItemAccess 
 * manageAction
 * changeStatusAction
 * addAction
 * editAction
 * deleteAction
 *
 */

class SurveyQuestionnaireItemsController extends CController
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

        // Block access to this controller for not-logged users
        CAuth::handleLogin(Website::getDefaultPage());

        // Set meta tags according to active participants
        Website::setMetaTags(array('title'=>A::t('surveys', 'Questionnaire Items Management')));
        // Set backend mode
        Website::setBackend();

		$this->_cRequest = A::app()->getRequest();
		$this->_cSession = A::app()->getSession();

        $this->_view->actionMessage = '';
        $this->_view->tabs = SurveysComponent::prepareTab('surveyquestionnaireitems');		
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveyQuestionnaireItems/manage/');
    }
    
    /**
     * Manage action handler
     * @param int $questionnaireId
     * @param string $alert 
     */
    public function manageAction($questionnaireId = 0, $alert = '')
    {
        Website::prepareBackendAction('manage', 'survey_questions', 'surveyQuestionnaires/manage');
        $this->_checkQuestionnaireAccess($questionnaireId);
        
		if($this->_cSession->hasFlash('alert')){
            $alert = $this->_cSession->getFlash('alert');
            $alertType = $this->_cSession->getFlash('alertType');
			
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
		}

        $this->_view->render('surveyQuestionnaireItems/manage');        
    }
    
    /**
     * Change status survey questionnaire action handler (requirement or activity)
     * @param int $questionnaireId 
     * @param int $id
     * @param int $status
     */
    public function changeStatusAction($questionnaireId = 0, $id = 0, $status = '')
    {
        Website::prepareBackendAction('edit', 'survey_questions', 'surveyQuestionnaires/manage');
        $this->_checkQuestionnaireAccess($questionnaireId);
        $qItem = $this->_checkQuestionnaireItemAccess($id, $questionnaireId);
        $status = ($status == 'requirement') ? 'is_required' : 'is_active';

		if(SurveyQuestionnaireItems::model()->updateByPk($id, array($status=>($qItem->$status == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
		 
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->redirect('SurveyQuestionnaireItems/manage/questionnaireId/'.$questionnaireId);        
    }

    /**
     * Add questionnaire items action handler
     * @param int $questionnaireId
     */
    public function addAction($questionnaireId = 0)
    {
        Website::prepareBackendAction('add', 'survey_questions', 'surveyQuestionnaires/manage');
        $this->_checkQuestionnaireAccess($questionnaireId);
        
        $this->_view->selectedValue = A::app()->getRequest()->getPost('question_type_id');
        $this->_view->maxRecords = SurveyQuestionnaireItems::model()->max('sort_order', 'entity_questionnaire_id = :entity_questionnaire_id', array(':entity_questionnaire_id'=>$questionnaireId));
        $this->_view->render('surveyQuestionnaireItems/add');
    }
    
    /**
     * Edit questionnaire items action handler
     * @param int $questionnaireId
     * @param int $id
     */
    public function editAction($questionnaireId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'survey_questions', 'surveyQuestionnaires/manage');
        $this->_checkQuestionnaireAccess($questionnaireId);
        $qItem = $this->_checkQuestionnaireItemAccess($id, $questionnaireId);
        
        $this->_view->id = $id;
        $this->_view->selectedValue = $qItem->question_type_id;
        $this->_view->selectedValueNew = $qItem->question_type_id;
        if(A::app()->getRequest()->isPostRequest()) $this->_view->selectedValueNew = A::app()->getRequest()->getPost('question_type_id');
        $result = SurveyQuestionnaireItemVariants::model()->findAll(array(
            'condition'=>'entity_questionnaire_item_id = :entity_questionnaire_item_id',
            'order'=>'id ASC'),
            array('entity_questionnaire_item_id'=>$id));
        $variantText = '';
        if(is_array($result)){
            $rowTitle = '';
            foreach($result as $key => $val){
                // 10 - Matrix Choice (only one answer per row)
                // 11 - Matrix Choice (only one answer per row with other option)
                // 12 - Matrix Choice (multiple answers per row)
                // 13 - Matrix Choice (multiple answers per row with other option)
                // 14 - Matrix Choice (Date/Time)
                // 15 - Rating Scale
                // 16 - Ranking                
                if(in_array($val['question_type_id'], array('10', '11', '12', '13', '14', '15', '16'))){
                    if($rowTitle != $val['row_title']){
                        if($rowTitle != '') $variantText .= "\r\n===";
                        $variantText .= "\r\n[".$val['row_title'].']';
                        $rowTitle = $val['row_title'];
                    }
                }
                $contentValue = ($val['content_value'] !== '') ? $val['content_value'].'|' : '';
                $variantText .= (!empty($variantText) ? "\r\n" : '').$contentValue.$val['content'];
            }
        }
        $this->_view->variantText = $variantText;        
        $this->_view->render('surveyQuestionnaireItems/edit');
    }
    
    /**
     * Delete questionnaire items action handler
     * @param int $surveyId 
     * @param int $id 
     */
    public function deleteAction($questionnaireId = 0, $id = 0)
    {
        Website::prepareBackendAction('delete', 'survey_questions', 'surveyQuestionnaires/manage');
        $this->_checkQuestionnaireAccess($questionnaireId);
        $qItem = $this->_checkQuestionnaireItemAccess($id, $questionnaireId);
        
        $alert = '';
    	$alertType = '';
    
		if($qItem->delete()){				
			$alert = A::t('surveys', 'Questionnaire item has been successfully deleted!');
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

		$this->redirect('surveyQuestionnaireItems/manage/questionnaireId/'.$questionnaireId);
    }

	/**
	 * Check if passed questionnaire ID is valid
	 * @param int $questionnaireId
	 * @param string $action
	 */
	private function _checkQuestionnaireAccess($questionnaireId = 0, $action = '')
	{
		$surveyQuestionnaire = SurveyQuestionnaires::model()->findByPk((int)$questionnaireId);
    		if(empty($surveyQuestionnaire)){
			$this->redirect('surveys/manage');
		}
        
        // Prepare question types
        $result = SurveysQuestionTypes::model()->findAll('is_active = 1');
        $questionTypes = array(''=>A::t('surveys', 'Select'));
        foreach($result as $key => $val){
            $questionTypes[$val['id']] = $val['name'];
        }
        $this->_view->questionTypes = $questionTypes;
        
        $this->_view->questionnaireId = $questionnaireId;
        $this->_view->surveyId = $surveyQuestionnaire->survey_id;
        $this->_view->surveyLink = 'surveyQuestionnaires/manage/surveyId/'.$surveyQuestionnaire->survey_id;
        $this->_view->surveyName = $surveyQuestionnaire->survey_name;
        $this->_view->questionnaireLink = 'surveyQuestionnaireItems/manage/questionnaireId/'.$questionnaireId;
        $this->_view->questionnaireName = $surveyQuestionnaire->name;
        if(ModulesSettings::model()->param('surveys', 'field_gender') == 'no'){
            $this->_view->surveyGenderFormulation = false;    
        }else{
            $this->_view->surveyGenderFormulation = $surveyQuestionnaire->gender_formulation;
        }        
        $this->_view->dateFormat = array('DD/MM/YYYY'=>'DD/MM/YYYY', 'MM/DD/YYYY'=>'MM/DD/YYYY');
        $this->_view->validationType = array('text'=>A::t('surveys', 'Text'), 'numeric'=>A::t('surveys', 'Numeric'));
        $this->_view->alignmentType = array(''=>'', 'h'=>A::t('surveys', 'Horizontal'), 'v'=>A::t('surveys', 'Vertical'));
	}
 
	/**
	 * Check if passed questionnaire item ID is valid
	 * @param int $id
	 * @param int $questionnaireId
	 */
	private function _checkQuestionnaireItemAccess($id = 0, $questionnaireId = 0)
	{
		$seqi = SurveyQuestionnaireItems::model()->findByPk((int)$id);
		if(!$seqi){
			$this->redirect('surveyQuestionnaireItems/manage/questionnaireId/'.$questionnaireId);
		}
        return $seqi;
    }    

}