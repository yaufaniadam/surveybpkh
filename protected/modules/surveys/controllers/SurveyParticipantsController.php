<?php
/**
 * SurveyParticipants controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _checkSurveyAccess
 * indexAction              _checkActionAccess
 * manageAction					
 * changeStatusAction
 * addAction
 * editAction
 * deleteAction
 * clearResultAction
 * 
 */

class SurveyParticipantsController extends CController
{
	
    private $_alert;
    private $_alertType;

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
            Website::setMetaTags(array('title'=>A::t('surveys', 'Survey Participants Management')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

			$this->_cRequest = A::app()->getRequest();
			$this->_cSession = A::app()->getSession();

            // Fetch site settings info 
            $this->_settings = Bootstrap::init()->getSettings();
            $this->_view->dateTimeFormat = $this->_settings->datetime_format;
            $this->_view->statuses = array('0'=>A::t('surveys', 'Not Started'), '1'=>A::t('surveys', 'In Work'), '2'=>A::t('surveys', 'Finished'));
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveyParticipants/manage');
    }

    /**
     * Manage action handler
     * @param int $surveyId
     */
    public function manageAction($surveyId = 0)
    {
        Website::prepareBackendAction('manage', 'survey_participants', 'surveyParticipants/manage');
        $this->_checkSurveyAccess($surveyId);

		if($this->_cSession->hasFlash('alert')){
            $alert = $this->_cSession->getFlash('alert');
            $alertType = $this->_cSession->getFlash('alertType');
			
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
		}

		// Prepare logging by URL params
		$this->_view->loginByUrl = ModulesSettings::model()->param('surveys', 'enable_login_by_url');
        $this->_view->tabs = SurveysComponent::prepareTab('surveyparticipants', $this->_view->surveyId);
		
        $this->_view->render('surveyParticipants/manage');        
    }
    
    /**
     * Change status survey participants action handler
     * @param int $surveyId
     * @param int $id
     */
    public function changeStatusAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'survey_participants', 'surveyParticipants/manage');
		$this->_checkSurveyAccess($surveyId);
        $sParticipant = $this->_checkActionAccess($id);

		if(SurveyParticipants::model()->updateByPk($id, array('is_active'=>($sParticipant->is_active == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
		 
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->redirect('surveyParticipants/manage/surveyId/'.$surveyId);        
    }

    /**
     * Add new survey participants action handler
     * @param int $surveyId
     */
    public function addAction($surveyId = 0)
    {
        Website::prepareBackendAction('add', 'survey_participants', 'surveyParticipants/manage');
		$this->_checkSurveyAccess($surveyId);

        // Retrieve all participants
        $this->_view->participants = array('-1'=>A::t('surveys', 'Select All'));
        
        $participants = SurveyParticipants::model()->getVacantParticipants($surveyId);
        $currentParticipants = SurveyParticipants::model()->count('survey_id = :survey_id', array(':survey_id'=>$surveyId));
        $totalParticipants = SurveysParticipants::model()->count('is_active = 1');
        
        $cRequest = A::app()->getRequest();
        $participantId = $cRequest->getPost('participant_id');
        if($cRequest->getPost('act') == 'send'){
			$result = CWidget::create('CFormValidation', array(
				'fields'=>array(
                	'participant_id' =>array('title'=>A::t('surveys', 'Select Participant'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($participants))),
				),
		    ));
		    if($result['error']){
		    	$this->_alert = $result['errorMessage'];
		    	$this->_view->errorField = $result['errorField'];
		    	$this->_alertType = 'validation';
		    }else{
				if(APPHP_MODE == 'demo'){
					$this->_alert = A::t('core', 'This operation is blocked in Demo Mode!');
					$this->_alertType = 'warning';
				}else{
                    if(count($participants) == 1 && !$totalParticipants){
                        $this->_alert = A::t('surveys', 'There are still no participants to be selected!');
                        $this->_alertType = 'warning';                        
                    }elseif($participantId == '-1'){
                        if($currentParticipants < $totalParticipants){
                            // Add all participants
                            if(SurveyParticipants::model()->addAllParticipants($surveyId, $currentParticipants)){
								A::app()->getSession()->setFlash('alert', A::t('surveys', A::t('surveys', 'New participants have been successfully added to the survey!')));
								A::app()->getSession()->setFlash('alertType', 'success');
                                $this->redirect('surveyParticipants/manage/surveyId/'.$surveyId);
                            }else{
                                $this->_alert = A::t('surveys', 'An error occurred while adding a participant to the survey!');
                                $this->_alertType = 'error';                            
                            }
                        }else{
                            $this->_alert = A::t('surveys', 'All available participants are already added to this survey!');
                            $this->_alertType = 'warning';                                                        
                        }
                    }else{
                        $surveyParticipant = new SurveyParticipants();
                        $surveyParticipant->survey_id = $surveyId;
                        $surveyParticipant->participant_id = $participantId;
                        $surveyParticipant->is_active = 1;
                        if($surveyParticipant->save()){
							A::app()->getSession()->setFlash('alert', A::t('surveys', 'New participant has been successfully added to the survey!'));
							A::app()->getSession()->setFlash('alertType', 'success');
                            $this->redirect('surveyParticipants/manage/surveyId/'.$surveyId);
                        }else{
                            $this->_alert = A::t('surveys', 'An error occurred while adding a participant to the survey!');
                            $this->_alertType = 'error';
                        }
                    }
                }
            }
        }
        if(!empty($this->_alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($this->_alertType, $this->_alert, array('button'=>true)));
        }
        
        $this->_view->participantId = $participantId; 
        $this->_view->participants = $participants;
        $this->_view->tabs = SurveysComponent::prepareTab('surveyparticipants', $this->_view->surveyId);
        $this->_view->render('surveyParticipants/add');
    }
    
    /**
     * Edit survey participants action handler
     * @param int $surveyId
     * @param int $id
     */
    public function editAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('edit', 'survey_participants', 'surveyParticipants/manage');
		$this->_checkSurveyAccess($surveyId);
        $participant = $this->_checkActionAccess($id);
		
        $this->_view->fieldGender = ModulesSettings::model()->param('surveys', 'field_gender');
		$this->_view->id = $id;
        $this->_view->tabs = SurveysComponent::prepareTab('surveyparticipants', $this->_view->surveyId);

		// Prepare logging by URL params
		$this->_view->loginByUrl = ModulesSettings::model()->param('surveys', 'enable_login_by_url');
		$this->_view->loginUrl = $this->_view->loginByUrl ? A::app()->getRequest()->getBaseUrl().'surveys/login/code/'.$this->_view->surveyCode.'/param/'.SurveysComponent::generateLoginByUrlLink($participant->participant_identity_code, $participant->participant_password) : '';

        $this->_view->render('surveyParticipants/edit');
    }    

    /**
     * Delete action handler
     * @param int $surveyId
     * @param int $id  
     */
    public function deleteAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('delete', 'survey_participants', 'surveyParticipants/manage');
		$this->_checkSurveyAccess($surveyId);
        $sParticipant = $this->_checkActionAccess($id);

        $alert = '';
        $alertType = '';
		$isDebug = false;
    
		if($isDebug){
			echo '<br><br>Start of Debug<br>------------';
			echo '<br><br>1. [OK] Delete participant from the given survey';
		}

		// ---------------------------------------
		// 1. Delete participant from survey
		// ---------------------------------------
		if($sParticipant->delete()){
            if(!$sParticipant->getError()){
				// ---------------------------------------
				// 2. Clear participant results from survey
				// ---------------------------------------
				SurveysComponent::clearParticipantResults($surveyId, $id, $isDebug);

				if($isDebug){
					echo '<br><br>------------';
					echo '<br>End of Debug';
					exit();
				}

				$alert = A::t('app', 'Delete Success Message');
				$alertType = 'success';				
            }else{
                $alert = A::t('app', 'Delete Warning Message');
                $alertType = 'warning';
			}
		}else{
            if(APPHP_MODE == 'demo'){
                $alert = CDatabase::init()->getErrorMessage();
                $alertType = 'warning';
            }else{
                $alert = A::t('app', 'Delete Error Message');
                $alertType = 'error';
            }
		}
		
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);
		
		$this->redirect('surveyParticipants/manage/surveyId/'.$surveyId);
    }
	
    /**
     * Clear survey results for specific participant
     * @param int $surveyId
     * @param int $id
     */
    public function clearResultAction($surveyId = 0, $id = 0)
    {
        Website::prepareBackendAction('delete', 'survey_participants', 'surveyParticipants/manage');
		$this->_checkSurveyAccess($surveyId);
        $sParticipant = $this->_checkActionAccess($id);
		
        $alert = '';
        $alertType = '';
		$isDebug = false;
		
		// Handle only surveys with registered access mode
		if($this->_view->accessMode == 'r'){
			$condition = 'survey_id = :survey_id AND participant_id = :participant_id';
			$conditionParams = array('i:survey_id'=>$surveyId, 'i:participant_id'=>$id);
	
			// ---------------------------------------
			// 1. Clear participant data for the given survey
			// ---------------------------------------
			SurveyParticipants::model()->updateAll(array('start_date'=>NULL, 'finish_date'=>NULL, 'status'=>0, 'data_score'=>'', 'data_total_score'=>''), $condition, $conditionParams);
			if($isDebug){
				echo '<br><br>Start of Debug<br>------------';
				echo '<br><br>1. [OK] Clear participant data for the given survey';
			}
			
			// ---------------------------------------
			// 2. Clear participant results from survey
			// ---------------------------------------
			SurveysComponent::clearParticipantResults($surveyId, $id, $isDebug);
		   
			if($isDebug){
				echo '<br><br>------------';
				echo '<br>End of Debug';
				exit();
			}

			if(!$this->_cSession->isExists('alert')){
				$this->_cSession->setFlash('alert', A::t('surveys', 'Survey results have been successfully cleaned!'));
				$this->_cSession->setFlash('alertType', 'success');
			}
		}
		
        $this->redirect('surveyParticipants/manage/surveyId/'.$surveyId);        
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
		$this->_view->surveyCode = $survey->code;
		$this->_view->accessMode = $survey->access_mode;
        $this->_view->surveyLink = 'surveyParticipants/manage/surveyId/'.$surveyId;
	}
 
    /**
     * Check if passed record ID is valid
     * @param int $id
     * @return model
     */
    private function _checkActionAccess($id = 0)
    {        
        $model = SurveyParticipants::model()->findByPk($id);
        if(!$model){
            $this->redirect('surveyParticipants/manage');
        }
        return $model;
    }

}
