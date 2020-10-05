<?php
/**
 * SurveysParticipants controller
 *
 * PUBLIC:                  PRIVATE:
 * -----------              ------------------
 * __construct              _checkActionAccess
 * indexAction              
 * exportAction
 * manageAction
 * changeStatusAction
 * addAction
 * editAction
 * deleteAction
 *
 */

class SurveysParticipantsController extends CController
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
                $this->redirect('index/index');
            }
        }

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active participants
            Website::setMetaTags(array('title'=>A::t('surveys', 'Participants Management')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            
			$this->_cRequest = A::app()->getRequest();
			$this->_cSession = A::app()->getSession();

            $this->_view->fieldIdentityCode = ModulesSettings::model()->param('surveys', 'field_identity_code');
            $this->_view->fieldPassword = ModulesSettings::model()->param('surveys', 'field_password');
            $this->_view->fieldFirstName = ModulesSettings::model()->param('surveys', 'field_first_name');
            $this->_view->fieldLastName = ModulesSettings::model()->param('surveys', 'field_last_name');
            $this->_view->fieldEmail = ModulesSettings::model()->param('surveys', 'field_email');
            $this->_view->fieldGender = ModulesSettings::model()->param('surveys', 'field_gender');
                        
            $this->_view->tabs = SurveysComponent::prepareTab('participants');
        }
        
        $this->_view->genders = array(''=>'', 'f'=>A::t('surveys', 'Female'), 'm'=>A::t('surveys', 'Male'));
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveysParticipants/manage');
    }
    
    /**
     * Manage action handler
     */
    public function exportAction()
    {
        Website::prepareBackendAction('manage', 'participants', 'surveysParticipants/manage');

        $filename = 'participants.csv';
        $output = "\xEF\xBB\xBF";
        
        $participants = SurveysParticipants::model()->findAll();
        $headersCount = 0;
        foreach($participants as $participant){
            $fieldsCount = 0;
            
            // Prepare headers
            if(!$headersCount){
                foreach($participant as $key => $val){
                    if($headersCount++) $output .= ',';
                    $output .= ucwords(str_replace('_', ' ', $key));
                }
                $output .= "\n";
            }
            
            // Prepare records
            foreach($participant as $key => $val){
                if($fieldsCount++) $output .= ',';
                $output .= '"'.$val.'"';
            }
            $output .= "\n";
        }
        
        try{
            A::app()->getRequest()->downloadFile($filename, $output, 'application/csv');            
        }catch (Exception $e){
            $this->_view->errorTitle = A::t('surveys', 'Export Error');
            $this->_view->errorHeader = A::t('surveys', 'Export Error');
            $this->_view->errorText = CWidget::create(
                'CMessage', array('error', A::t('surveys', 'An error occurred while exporting operation! Please try again later.'))
            );
            $this->_view->render('surveysParticipants/export');
        }        
    }

    /**
     * Manage action handler
     */
    public function manageAction()
    {
        Website::prepareBackendAction('manage', 'participants', 'surveysParticipants/manage');

		if($this->_cSession->hasFlash('alert')){
            $alert = $this->_cSession->getFlash('alert');
            $alertType = $this->_cSession->getFlash('alertType');
			
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($alertType, $alert, array('button'=>true))
            );
		}

        $this->_view->render('surveysParticipants/manage');        
    }

    /**
     * Change status surveys participants action handler
     * @param int $id
     */
    public function changeStatusAction($id)
    {
        Website::prepareBackendAction('edit', 'participants', 'surveysParticipants/manage');
        $participants = $this->_checkActionAccess($id);

		if(SurveysParticipants::model()->updateByPk($id, array('is_active'=>($participants->is_active == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
		 
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

        $this->redirect('surveysParticipants/manage');        
    }

    /**
     * Add new action handler
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'participants', 'surveysParticipants/manage');

        $this->_view->render('surveysParticipants/add');
    }

    /**
     * Edit participants action handler
     * @param int $id 
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'participants', 'surveysParticipants/manage');
        $participants = $this->_checkActionAccess($id);
        
        $this->_view->id = $id;
        $this->_view->render('surveysParticipants/edit');
    }

    /**
     * Delete action handler
     * @param int $id  
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'participants', 'surveysParticipants/manage');
        $participants = $this->_checkActionAccess($id);

        $alert = '';
        $alertType = '';
		$isDebug = false;
    
		// ---------------------------------------
		// 0. Delete participant from databse
		// ---------------------------------------
		if($isDebug){
			echo '<br><br>Start of Debug<br>------------';
			echo '<br><br>0. [OK] Delete participant from databse';
		}

		// Get all surveys of defined participant
		$allSurveys = SurveyParticipants::model()->findAll(CConfig::get('db.prefix').'surveys_entity_participants.participant_id = :participant_id', array('i:participant_id'=>$id));
		
		if(count($allSurveys) > 0){
			$alert = A::t('surveys', 'You cannot delete this participant account because it presents in one or more active surveys! Before delete this participant from all surveys.');
			$alertType = 'error';
		}else{			
			if($participants->delete()){
				if(!$participants->getError()){
	
					// ---------------------------------------
					// 1. Delete participant from all surveys
					// ---------------------------------------
					$deleteResult = SurveyParticipants::model()->deleteAll('participant_id = :participant_id', array('i:participant_id'=>$id));
					if($isDebug){
						echo '<br><br>1. [OK] Delete participant from all surveys';
					}
	
					// ---------------------------------------
					// 2. Clear participant results from survey
					// ---------------------------------------
					if(!empty($allSurveys)){
						foreach($allSurveys as $key => $survey){
							SurveysComponent::clearParticipantResults($survey['survey_id'], $id, $isDebug);
						}
					}
	
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
		}
		
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);
		
		$this->redirect('surveysParticipants/manage');
    }

    /**
     * Check if passed record ID is valid
     * @param int $id
     */
    private function _checkActionAccess($id = 0)
    {        
        $model = SurveysParticipants::model()->findByPk($id);
        if(!$model){
            $this->redirect('surveysParticipants/manage');
        }
        return $model;
    }    
  
}