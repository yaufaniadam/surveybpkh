<?php
/**
 * Surveys controller
 *
 * PUBLIC:                  	PRIVATE:
 * -----------              	------------------
 * __construct              	_prepareQuestionnaires
 * indexAction              	_checkActionAccess 
 * manageAction             	_getQuestionnaireItems
 * changeStatusAction       	_saveAnswer
 * addAction                	_updateVotesCounter
 * copyAction               	_setParticipantLogin
 * editAction               	_assignParticipantToSurvey
 * deleteAction             	_getContentByGenter
 * exportAction             	_findNextQuestionnaire
 * clearResultsAction       	_preparePageItems
 * resultsAction            	_checkActionAccessByCode
 * showAction               	_getGenderWhereClause 
 * loginAction              	_exportActionEmptyColumns
 * logoutAction             	_exportActionHeaderColumns
 * errorAction					_exportActionContentColumns
 * expiredAction				_prepareQuestionnaireCounts
 * completedAction				_prepareParticipantCounts
 * 								_prepareResultCounts
 * 								
 * 
 */

class SurveysController extends CController
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

        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active participants
            Website::setMetaTags(array('title'=>A::t('surveys', 'Surveys Management')));

            $this->_alert = '';
            $this->_alertType = '';
            
			$this->_cRequest = A::app()->getRequest();
			$this->_cSession = A::app()->getSession();

            // Fetch site settings info 
            $this->_settings = Bootstrap::init()->getSettings();
            $this->_view->dateFormat = $this->_settings->date_format;
            $this->_view->timeFormat = $this->_settings->time_format;
            $this->_view->votesMode = array('o'=>A::t('surveys', 'One Time'), 'm'=>A::t('surveys', 'Multiple Times'));
            $this->_view->accessMode = array('p'=>A::t('surveys', 'Public (anyone)'), 'r'=>A::t('surveys', 'Protected (predefined only)'));
            $this->_view->genderFormulation = array(''=>A::t('surveys', 'select'), '0'=>A::t('surveys', 'No'), '1'=>A::t('surveys', 'Yes'));
            $this->_view->accessModeFilter = array(''=>'', 'p'=>A::t('surveys', 'Public'), 'r'=>A::t('surveys', 'Protected'));

            $this->_view->tabs = SurveysComponent::prepareTab('surveys');
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('surveys/manage');
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
		
		// Prepare surveys total
		$this->_view->surveysTotal = Surveys::model()->count();
        // Prepare questionnaire counts
        $this->_view->questionnaireCounters = $this->_prepareQuestionnaireCounts();
        // Prepare participant counts
        $this->_view->participantCounters = $this->_prepareParticipantCounts();
        // Prepare results counts
        $this->_view->resultsCounters = $this->_prepareResultCounts();
		
        $this->_view->render('surveys/manage');        
    }
    
    /**
     * Change status survey questionnaire action handler
     * @param int $id
     */
    public function changeStatusAction($id)
    {
        Website::prepareBackendAction('edit', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($id);

        if(Surveys::model()->updateByPk($id, array('is_active'=>($survey->is_active == 1 ? '0' : '1')))){
			$alert = A::t('app', 'Status has been successfully changed!');
			$alertType = 'success';
		}else{
			$alert = (APPHP_MODE == 'demo') ? A::t('core', 'This operation is blocked in Demo Mode!') : A::t('app', 'Status changing error');
			$alertType = (APPHP_MODE == 'demo') ? 'warning' : 'error';
		}
		 
		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);
        
        $this->redirect('surveys/manage');        
    }

    /**
     * Add new action handler
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', 'surveys', 'surveys/manage');

        $this->_view->fieldGender = ModulesSettings::model()->param('surveys', 'field_gender');
        $sortOrder = Surveys::model()->count();
        $this->_view->sortOrder = ($sortOrder < 99) ? $sortOrder + 1 : 99;        		        
        $this->_view->render('surveys/add');
    }
    
    /**
     * Copy existing action handler
     */
    public function copyAction()
    {
        Website::prepareBackendAction('add', 'surveys', 'surveys/manage');

        // Retrieve all surveys
        $this->_view->surveys = $surveys = array(''=>A::t('surveys', 'Select'));
        $result = Surveys::model()->findAll();
        foreach($result as $key => $val){
            $surveys[$val['id']] = $val['name'];
        }

        $cRequest = A::app()->getRequest();
        $this->_view->surveyId = $cRequest->getPost('survey_id', 'int', 0);
        $this->_view->surveyName = $cRequest->getPost('survey_name', 'string', '');
        if($cRequest->getPost('act') == 'send'){
			$result = CWidget::create('CFormValidation', array(
				'fields'=>array(
                	'survey_id' =>array('title'=>A::t('surveys', 'Copy a Survey'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys($surveys))),
                	'survey_name' =>array('title'=>A::t('surveys', 'Survey Name'), 'validation'=>array('required'=>true, 'type'=>'text', 'maxLength'=>255)),
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
				}elseif(Surveys::model()->copyBy($this->_view->surveyId, $this->_view->surveyName)){
					$this->_cSession->setFlash('alert', A::t('surveys', 'The survey has been successfully copied!'));
					$this->_cSession->setFlash('alertType', 'success');
                    $this->redirect('surveys/manage');
                }else{
					$this->_alert = A::t('surveys', 'An error occurred while copied this survey!');
					$this->_alertType = 'error';                                        
                }
            }
        }
        if(!empty($this->_alert)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($this->_alertType, $this->_alert, array('button'=>true)));
        }
        
        $this->_view->surveys = $surveys;
        $this->_view->render('surveys/copy');
    }

    /**
     * Edit participants action handler
     * @param int $id 
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($id);
        
        $this->_view->fieldGender = ModulesSettings::model()->param('surveys', 'field_gender');
        $this->_view->id = $id;
        $this->_view->render('surveys/edit');
    }

    /**
     * Delete action handler
     * @param int $id
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($id);

        $alert = '';
    	$alertType = '';
		
		if($survey->delete()){				
			$alert = A::t('surveys', 'Survey successfully deleted!');
			$alertType = 'success';	
		}else{
			if(APPHP_MODE == 'demo'){
				$alert = CDatabase::init()->getErrorMessage();
				$alertType = 'warning';
		   	}else{
				$alert = A::t('surveys', 'Survey deleting error');
				$alertType = 'error';
		   	}			
		}

		$this->_cSession->setFlash('alert', $alert);
		$this->_cSession->setFlash('alertType', $alertType);

		$this->redirect('surveys/manage');
    }
    
    /**
     * Export survey action handler
     * @param int $surveyId
     * @param string $format
     */
    public function exportAction($surveyId = 0, $format = 'csv_columnar')
    {
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($surveyId);
        if($format != 'csv_columnar' && $format != 'csv_tabular') $this->redirect('surveys/manage');
        
        $filename = 'survey_'.$survey->code.'.csv';
        $outputStart = "\xEF\xBB\xBF";
        $output = '';

        if($format == 'csv_tabular'){
            // Tabular format
            
            $currentQuestionnaire = '';
            $separator = ',';
            $eol = "\r\n";            
            
            // ---------------------------------------
            // GET ALL ANSWERS
            // ---------------------------------------
			$participantAnswers = array();
			$partInd = 0;
			while($answers = SurveyAnswers::model()->getAnwers($surveyId, 'tabular', $partInd.', 1000')){
				$currentParticipant = '';
				foreach($answers as $answer){                
					if($currentParticipant != $answer['participant_id']){
						if(!isset($participantAnswers[$answer['participant_id']])){
							$participantAnswers[$answer['participant_id']] = array();	
						}
					}
					$participantAnswers[$answer['participant_id']][$answer['questionnaire_id']][$answer['question_number']][] = $answer;
					$currentParticipant = $answer['participant_id'];
				}
				$partInd += 1000;
			}
			
            // ---------------------------------------
            // GET ALL QUESTIONARIES (in current survey)
            // ---------------------------------------
            $questionaries = SurveyQuestionnaires::model()->findAll(
                array('condition'=>'survey_id = :surveyId AND '.CConfig::get('db.prefix').'surveys_entity_questionnaires.is_active = 1', 'order'=>'sort_order ASC'),
                array(':surveyId'=>$surveyId)
            );
            //CDebug::d($questionaries, 1);            
            $questionariesIds = array();
            foreach($questionaries as $key => $val){
                $questionariesIds[] = $val['id'];//.$val['name'];
            }

            // ---------------------------------------
            // GET ALL QUESTIONARIE ITEMS (questions, excepting question #17 - Text/HTML code)
            // ---------------------------------------
            $questionarieItems = SurveyQuestionnaireItems::model()->findAll(
                array('condition'=>'entity_questionnaire_id IN('.implode(',', $questionariesIds).') AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.is_active = 1 AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.question_type_id != 17', 'order'=>' FIELD(entity_questionnaire_id, '.implode(',', $questionariesIds).'), sort_order ASC')
            );
            //CDebug::d($questionarieItems, 1);
            $questionarieItemsIds = array();
            foreach($questionarieItems as $key => $val){
                $questionarieItemsIds[] = $val['id'];//.$val['name'];
            }
            
            // ---------------------------------------
            // GET ALL QUESTIONARIE ITEM VARIANTS (question variants)
            // ---------------------------------------
            $questionarieItemVariants = SurveyQuestionnaireItemVariants::model()->findAll(
                array('condition'=>'entity_questionnaire_item_id IN('.implode(',', $questionarieItemsIds).')', 'order'=>' FIELD(entity_questionnaire_item_id, '.implode(',', $questionarieItemsIds).'), id ASC')
            );
            $itemVariants = array();
            foreach($questionarieItemVariants as $variants){
                //if(!isset())
                $itemVariants[$variants['entity_questionnaire_item_id']][$variants['row_title']] = $variants['row_title'];
            }

            // ---------------------------------------
            // DRAW RESULT
            // ---------------------------------------
            // Print survey header
            $output .= A::t('surveys', 'Survey Name').': '.$survey->name.' (ID: '.$survey->id.') ';
            $output .= $eol.$eol;

            $outputHeader = '';
            $outputQuestions = '';
            
            // ---------------------------------------
            // GAMES 
            // ---------------------------------------
            $games = array(
                'game_empathy_memory.js'            =>  array('columns' => '6', 'cicles' => 8, 	'trening_cicles' => 1, 'additional_cicles' => 18),
                'game_emotional_intelligence.js'    =>  array('columns' => '6', 'cicles' => 80, 'trening_cicles' => 2, 'additional_cicles' => 0),
                'emotional-intelligence-ipt.js'		=>  array('columns' => '4', 'cicles' => 6, 	'trening_cicles' => 0, 'additional_cicles' => 0),
				'decision-making-m.js'				=>  array('columns' => '2', 'cicles' => 17, 'trening_cicles' => 0, 'additional_cicles' => 0),
				'decision-making-f.js'				=>  array('columns' => '2', 'cicles' => 17, 'trening_cicles' => 0, 'additional_cicles' => 0),
            );
            
            foreach($questionarieItems as $key => $val){                

                // Prepare questions
                $rowsColumns = '';
                $headerColumns = '';
                if(isset($itemVariants[$val['id']]) && is_array($itemVariants[$val['id']])){
                    foreach($itemVariants[$val['id']] as $varKey => $varVal){
                        if(!empty($varKey)){
                            $rowsColumns .= $val['questionnaire_key'].$val['sort_order'].'-';
                            $rowsColumns .= $varKey;
                            $rowsColumns .= $separator;                            
                            $headerColumns .= $separator;
                        }
                    }                                            
                }                
                if(!$rowsColumns){
                    // Draw question name
                    // $outputQuestions .= $val['question_text_formatted'].'.'.$separator;
                    // Draw question key and number
                    
                    // 18 - Action Script
                    if($val['question_type_id'] == 18){                        
                        $outputQuestions .= $this->_exportActionHeaderColumns($games, $val, $separator);                            
                    }else{
                        //$val['questionnaire_key'].
                        $outputQuestions .= $val['questionnaire_key'].$val['sort_order'].$separator;
                    }
                            
                }else{
                    $outputQuestions .= $rowsColumns;
                }

                // Prepare header #1                
                if($currentQuestionnaire != $val['questionnaire_name']){
                    $outputHeader .= $val['questionnaire_name'];
                }
                if(!$headerColumns){
					if($val['question_type_id'] == 18){
						// Write here separators according to the game's cicles
						$outputHeader .= $this->_exportActionEmptyColumns($games, $val, $separator);                            
					}else{
						$outputHeader .= $separator;	
					}                    
                }else{
                    $outputHeader .= $headerColumns;
                }                
                $currentQuestionnaire = $val['questionnaire_name'];                
            }            

            // Header line #1
            $output .= $separator;
            $output .= $separator;
            $output .= A::t('surveys', 'Questionnaires').': '.$separator;
            $output .= $separator;
            $output .= $outputHeader;
            $output .= $eol;

            // Header line #2
            $output .= A::t('surveys', 'Start Date').$separator;
            $output .= A::t('surveys', 'Finish Date').$separator;
            $output .= A::t('surveys', 'Participant').$separator;            
            $output .= A::t('surveys', 'Questions').': '.$separator;
            $output .= $outputQuestions;
            $output .= $eol;            

            // Draw each row - PARTICIPANT
            $surveyParticipants = SurveyParticipants::model()->findAll('survey_id = :surveyId', array(':surveyId'=>$surveyId));
            foreach($surveyParticipants as $participant){
                
                $output .= ((!CTime::isEmptyDateTime($participant['start_date'])) ? $participant['start_date'] : A::t('surveys', 'Not Started')).$separator;
                $output .= ((!CTime::isEmptyDateTime($participant['finish_date'])) ? $participant['finish_date'] : A::t('surveys', 'Not Finished')).$separator;
                $output .= $participant['participant_identity_code'].$separator;
                $output .= $separator;

                // Draw each coulmn - QUESTION
                foreach($questionarieItems as $key => $val){
                    
                    $qAnswer = isset($participantAnswers[$participant['participant_id']][$val['entity_questionnaire_id']][$val['sort_order']]) ? $participantAnswers[$participant['participant_id']][$val['entity_questionnaire_id']][$val['sort_order']] : false;

                    // Prepare questions
                    $rowsColumns = '';
                    if(isset($itemVariants[$val['id']]) && is_array($itemVariants[$val['id']])){
                        
                        // Use this for other option answers that is written in answer table as -1, -2 etc. 
                        $otherOptionRowName = -1;
                        
                        foreach($itemVariants[$val['id']] as $varKey => $varVal){
                            
                            if(!empty($varKey)){
                                // ****************************************************
                                // Answers with MULTIPLE rows (multiple selection): Twin A/ Twin B etc...
                                // ****************************************************
                                $found = false;
                                
                                //CDebug::d($qAnswer);
                                if(is_array($qAnswer)){
                                    
                                    $rowColumnsAnswers = '';
                                    foreach($qAnswer as $qaKey => $qaVal){
                                        if($qaVal['questionnaire_item_id'] == $val['id']){
                                            
                                            if($qaVal['questionnaire_item_other'] != '' && $otherOptionRowName == $qaVal['selected_variant_row_name']){
                                                
												$rowColumnsAnswers .= $this->_prepareCsvString($qaVal['questionnaire_item_other']);
                                                $otherOptionRowName--;
                                                
                                                // Collect data
                                                // 13 - Matrix Choice (multiple answers per row with other option) 
                                                if($qaVal['question_type_id'] == 13){
                                                    $rowColumnsAnswers .= ';';
                                                }else{
                                                    $rowColumnsAnswers .= $separator;
                                                    $found = true;
                                                    break;                                                
                                                }
                                                
                                            }elseif($qaVal['answer_text'] != '' && $otherOptionRowName == $qaVal['selected_variant_row_name']){
                                                
                                                // 14 - Matrix Choice (Date/Time)
                                                $rowColumnsAnswers .= $qaVal['answer_text'];
                                                $otherOptionRowName--;
                                                //$found = true;
                                                break;
                                                
                                            }elseif($qaVal['selected_variant_row_name'] == $varKey){
                                                
                                                $rowColumnsAnswers .= $qaVal['selected_variant_number'];
                                                // Collect data
                                                // 12 - Matrix Choice (multiple answers per row)
                                                // 13 - Matrix Choice (multiple answers per row with other option) 
                                                if($qaVal['question_type_id'] == 12 || $qaVal['question_type_id'] == 13){
                                                    $rowColumnsAnswers .= ';';                                                    
                                                }else{ 
                                                    $rowColumnsAnswers .= $separator;
                                                    $found = true;
                                                    break;
                                                }
                                                
                                            }
                                        }
                                    }
                                    
                                    $rowsColumns .= trim($rowColumnsAnswers, ';');    
                                    
                                    if(!$found){
                                        $rowsColumns .= $separator;
                                    }
                                }else{
                                    // Empty answer
                                    $rowsColumns .= $separator;
                                }                                
                                
                            }else{
                                // ****************************************************
                                // Answers with SINGLE row (multiple selection)
                                // ****************************************************
                                
                                //CDebug::d($qAnswer);                                
                                if(is_array($qAnswer)){                                    
                                    $rowColumnsAnswers = '';                                    
                                    foreach($qAnswer as $qaKey => $qaVal){                                        
                                        if($qaVal['questionnaire_item_other'] != ''){
                                            $rowColumnsAnswers .= $this->_prepareCsvString($qaVal['questionnaire_item_other']).';';    
                                        }elseif($qaVal['answer_text'] != ''){
                                            // Multiple textboxes ?
                                            $rowColumnsAnswers .= $qaVal['answer_text'].';';    
                                        }else{
                                            $rowColumnsAnswers .= $qaVal['selected_variant_number'].';';    
                                        }                                        
                                    }
                                    
									$rowsColumns .= $this->_prepareCsvString(trim($rowColumnsAnswers, ';')).$separator;    
                                }else{
                                    // Empty answer
                                    $rowsColumns .= $separator;
                                }                                
                            }
                        }
                    }else{
                        
                        // ****************************************************
                        // Answers with FREE text
                        // ****************************************************

                        // 18 - Action Script
                        if($val['question_type_id'] == 18){                            
							if(!empty($qAnswer)){
								$rowsColumns .= $this->_exportActionContentColumns($games, $qAnswer, $separator);                            	
							}else{
								$rowsColumns .= $this->_exportActionEmptyColumns($games, $val, $separator);
							}                            
                        }else{
                            $rowsColumns .= $this->_prepareCsvString($qAnswer[0]['answer_text']).$separator;        
                        }                        
                    }
                    
                    $output .= $rowsColumns;

                }

                $output .= $eol;        
            }
        
        }else{
            // Columnar format
            
            $answers = SurveyAnswers::model()->getAnwers($surveyId);
            
            $headersPrinted = false;
            $participantId = '';
            $questionnaireName = '';
            $questionnaireItemId = '';
            $columns = '';
            $eol = "\r\n";
            foreach($answers as $answer){
                $fieldsCount = 0;
                $questionnaireItemId = $answer['questionnaire_item_id'];
                
                // Prepare headers
                if(!$headersPrinted){
                    $output .= A::t('surveys', 'Survey Name').': '.$answer['survey_name'].$eol;
                    $columns .= A::t('surveys', 'Question Text');
                    $columns .= ','.A::t('surveys', 'Row Title');
                    $columns .= ','.A::t('surveys', 'Selected Variant');
                    $columns .= ','.A::t('surveys', 'Other Option');
                    $columns .= ','.A::t('surveys', 'Entered Text');
                    $columns .= ','.A::t('surveys', 'Action Script Data');
                    $headersPrinted = true;
                }
    
                // Prepare records
                foreach($answer as $key => $val){
                    if($key == 'participant_id_code'){
                        if($participantId != $val){
                            $participantId = $val;
                            $output .= $eol.$eol;
                            $output .= A::t('surveys', 'Participant').' ID: '.$participantId.$eol;
                            $questionnaireName = '';
                        }
                    }elseif(!in_array($key, array('survey_name', 'participant_id', 'participant_id_code', 'questionnaire_name', 'questionnaire_item_id'))){
                        if($fieldsCount++) $output .= ',';
                        if($key == 'row_title'){
                            if($val < 0){
                                // Handle question types 11 and 13
                                $output .= '"'.SurveyAnswers::model()->getRowName($questionnaireItemId, $val).'"';
                            }elseif($val === 0){
                                $output .= '';    
                            }else{
                                $output .= '"'.str_ireplace('"', ' ', $val).'"';    
                            }                        
                        }else{
                            $output .= '"'.str_ireplace('"', ' ', $val).'"';
                        }
                    }elseif($key == 'questionnaire_name'){
                        if($questionnaireName != $val){
                            $questionnaireName = $val;
                            $output .= $eol;
                            $output .= A::t('surveys', 'Questionnaire').': '.$questionnaireName.$eol;
                            $output .= $columns.$eol;                        
                        }
                    }elseif($key == 'actions_data'){
                        if($fieldsCount++) $output .= ',';
                        $output .= '"'.str_ireplace('"', "'", $val).'"';                    
                    }
                }
                $output .= $eol;
            }
        }
        
        
        if(!empty($output)) $output = $outputStart.$output;
        
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
     * Clear results action handler
     * @param int $surveyId 
     */
    public function clearResultsAction($surveyId = 0)
    {
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($surveyId);
        
        $this->_prepareQuestionnaires($surveyId);
        
		// Clear participant data for the given survey
		SurveyParticipants::model()->updateAll(array('start_date'=>NULL, 'finish_date'=>NULL, 'status'=>0, 'data_score'=>'', 'data_total_score'=>''), 'survey_id = :survey_id', array(':survey_id'=>$surveyId));
		
        // Delete records from questionnaire item variants table
		if(SurveyAnswers::model()->count('survey_id = :survey_id', array(':survey_id'=>$surveyId)) == 0){
            $message = A::t('surveys', 'No results found for this survey!');
            $messageType = 'warning';            
        }elseif(SurveyAnswers::model()->deleteAll('survey_id = :survey_id', array(':survey_id'=>$surveyId))){

			// ---------------------------------------
			// GET ALL QUESTIONARIES (in current survey)
			// ---------------------------------------
			$questionaries = SurveyQuestionnaires::model()->findAll(
				array('condition'=>'survey_id = :surveyId AND '.CConfig::get('db.prefix').'surveys_entity_questionnaires.is_active = 1', 'order'=>'sort_order ASC'),
				array(':surveyId'=>$surveyId)
			);
			$questionariesIds = array();
			foreach($questionaries as $key => $val){
				$questionariesIds[] = $val['id'];
			}
	
			// ---------------------------------------
			// GET ALL QUESTIONARIE ITEMS
			// ---------------------------------------
			$questionarieItems = SurveyQuestionnaireItems::model()->findAll(
				array('condition'=>'entity_questionnaire_id IN('.implode(',', $questionariesIds).') AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.is_active = 1 AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.question_type_id != 17', 'order'=>' FIELD(entity_questionnaire_id, '.implode(',', $questionariesIds).'), sort_order ASC')
			);
			$questionarieItemsIds = array();
			foreach($questionarieItems as $key => $val){
				$questionarieItemsIds[] = $val['id'];
			}
	
			// Clear votes in item variants
			SurveyQuestionnaireItemVariants::model()->updateAll(array('votes'=>0), 'entity_questionnaire_item_id IN('.implode(',', $questionarieItemsIds).')');

			$message = A::t('surveys', 'Survey results have been successfully cleaned!');
            $messageType = 'success';
		}else{
			if(APPHP_MODE == 'demo'){
				$message = A::t('core', 'This operation is blocked in Demo Mode!');
				$messageType = 'warning';
			}else{
				$message = A::t('surveys', 'An error occurred while cleaning results for this survey!');
				$messageType = 'error';
			}
        }
        
        if(!empty($message)){
            $this->_view->actionMessage = CWidget::create('CMessage', array($messageType, $message, array('button'=>true)));
        }        
        
        $this->_view->surveyId = $surveyId;
        $this->_view->summaryTotal = 0;
        $this->_view->summaryParticipants = 0;
        $this->_view->summaryResponses = 0;
        $this->_view->summaryCompleted = 0;
        $this->_view->summaryIncomplete = 0;
        $this->_view->summaryCompletionRate = 0;
        $this->_view->summaryAverageTime = 0;
        $this->_view->surveyName = $survey->name;
        $this->_view->render('surveys/results');        
    }

    /**
     * Results action handler
     * @param int $surveyId
     * @param int $questionnaireId
     */
    public function resultsAction($surveyId = 0, $questionnaireId = 0)
    {        
        Website::prepareBackendAction('manage', 'surveys', 'surveys/manage');
        $survey = $this->_checkActionAccess($surveyId);
        
        $this->_prepareQuestionnaires($surveyId, $questionnaireId);

        $this->_view->surveyId = $surveyId;
        
        // Calculate Responses
        $this->_view->summaryResponses = SurveyAnswers::model()->count(
            array('condition'=>'survey_id = :survey_id', 'count'=>'DISTINCT(participant_id)'),
            array(':survey_id'=>$surveyId)
        );
        $this->_view->summaryCompleted = SurveyAnswers::model()->count(
            array('condition'=>'survey_id = :survey_id', 'count'=>'DISTINCT participant_id, survey_id, questionnaire_id'),
            array(':survey_id'=>$surveyId)
        );        
        $this->_view->summaryParticipants = SurveyParticipants::model()->count(
            'survey_id = :survey_id',
            array(':survey_id'=>$surveyId)
        );
        $totalQuestionnaires = SurveyQuestionnaires::model()->count(
            'survey_id = :survey_id',
            array(':survey_id'=>$surveyId)
        );
        
        $summaryAverageTime = SurveyParticipants::model()->sum(
            'TIMESTAMPDIFF(SECOND, start_date, finish_date)',
            "survey_id = :survey_id AND finish_date != ''",
            array(':survey_id'=>$surveyId)
        );
        
        /* participants * questionaries in survey */
        $total = $this->_view->summaryParticipants * $totalQuestionnaires;
        $this->_view->summaryTotal = $total;
        $this->_view->summaryIncomplete = $total - $this->_view->summaryCompleted;
        $this->_view->summaryCompletionRate = $total ? round($this->_view->summaryCompleted / $total * 100, 2) : '0';
        $this->_view->summaryAverageTime = $this->_view->summaryCompleted ? date($this->_view->timeFormat, $summaryAverageTime) : '0';
        
        $this->_view->surveyName = $survey->name;
        $this->_view->render('surveys/results');        
    }

    /**
     * Show action handler
     * @param mixed $code
     */
    public function showAction($code = 0)
    {
        // Set frontend settings
        Website::setFrontend();
		$cRequest = A::app()->getRequest();
        $cSession = A::app()->getSession();
        
		// Redirect if not logged
        if(!CAuth::isLoggedInAs('participant')){
            $this->redirect('surveys/login'.($code ? '/code/'.$code : ''));
        }
        // Redirect if entered wrong code
        if(CAuth::isLoggedInAs('participant') && $code !== $cSession->get('surveyCode')){
            $this->redirect('surveys/error');
        }
        
        $this->_view->questionnaireItems = array();
        $this->_view->startCount = 0;
        $this->_view->questionnaireName = '';
        $this->_view->errorItem = '';
        $this->_view->isSurveyComplete = false;
        $page = $cRequest->getQuery('page', '', 'survey-welcome');
        $error = $cRequest->getQuery('error');
        $pageSession = $cSession->get('page', 'survey-welcome');
        $currentQuestionnaireId = (int)$cSession->get('currentQuestionnaireId');
        
        // Check if survey is a valid and active 
        $survey = Surveys::model()->find('is_active = 1 AND code = :code', array(':code'=>$code));
        if(!$survey){
            $this->redirect('surveys/error');
        }
        
        // Run pages loop
        if($page != $pageSession){
            // Check if page is valid 
            $this->redirect('surveys/show/code/'.$code.'/page/'.$pageSession);
            
        }elseif($cRequest->getPost('act') == 'send'){
            $validated = true;
            $saved = true;
            $errorMessage = '';
            $loggedGender = $cSession->get('loggedGender');
            
            // Get all items for current page
            $pageSize = $cSession->get('currentQuestionnaireItemsPerPage');
            $start = ($page) ? (($page - 1) * $pageSize) : 0;
            $whereByGender = $this->_getGenderWhereClause($survey->gender_formulation, $cSession->get('loggedGender'));
            if($surveyQuestionnaireItems = $this->_getQuestionnaireItems($currentQuestionnaireId, $start, $pageSize, $whereByGender)){
                // Validate all items
                foreach($surveyQuestionnaireItems as $key => $val){
                    // This item is not for current gender - continue
                    if(!SurveysComponent::validateQuestionByGender($survey->gender_formulation, $loggedGender, $val['question_text'], $val['question_text_f'])){
                        continue;
                    }
                    
                    $result = SurveysComponent::validateQuestion($val);
                    if($result['error']){
                        $validated = false;
                        $errorMessage = $result['error_message'];
                        $this->_view->errorItem = $result['error_item'];
                        break;
                    }
                }

                // Prepare data - save in session
                if($validated){
                    if(!$cSession->isExists('questionnaire_data')) $cSession->set('questionnaire_data', '');
                    $questionnaireData = $cSession->get('questionnaire_data');
					if(empty($questionnaireData)) $questionnaireData = array();
                    $tempItems = array();

                    foreach($surveyQuestionnaireItems as $key => $val){
                        
                        // This item is not for current gender - continue
                        if(!SurveysComponent::validateQuestionByGender($survey->gender_formulation, $loggedGender, $val['question_text'], $val['question_text_f'])){
                            continue;
                        }
                        
                        // Do nothing - text/html
                        //if($val['question_type_id'] == '17'){                                
                            // continue;
                        //}
                        
                        $itemData = $cRequest->getPost('item_'.$val['id']);
                        $itemOtherData = $cRequest->getPost('item_'.$val['id'].'_other');
                        $itemOtherId = $cRequest->getPost('item_'.$val['id'].'_other_id', 'int', 0);
                        $questionTypeId = isset($val['question_type_id']) ? $val['question_type_id'] : '';

                        $questionnaireData[$val['id']] = array();
                        $questionnaireData[$val['id']]['question_type_id'] = $questionTypeId;
                        if(is_array($itemData)){
                            $questionnaireData[$val['id']]['items'] = array();
                            foreach($itemData as $kKey => $vVal){
                                $questionnaireData[$val['id']]['items'][$kKey] = $vVal;
                            }   
                        }else{
                            if($questionTypeId == '6' || $questionTypeId == '8'){
                                // 6 - Single Textbox or 8 - Comment/Essay Box
                                $questionnaireData[$val['id']]['answer_text'] = $itemData;
                            }elseif($questionTypeId == '9'){
                                // 9 - Date/Time
                                $itemDataDay = $cRequest->getPost('item_'.$val['id'].'_dd');
                                $itemDataMonth = $cRequest->getPost('item_'.$val['id'].'_mm');
                                $itemDataYear = $cRequest->getPost('item_'.$val['id'].'_yy');
                                
                                if($val['date_format'] == 'DD/MM/YYYY'){
                                    $itemData .= $itemDataDay.'-'.$itemDataMonth;    
                                }else{
                                    $itemData .= $itemDataMonth.'-'.$itemDataDay;    
                                }
                                $itemData .= '-'.$itemDataYear;
                                $questionnaireData[$val['id']]['answer_text'] = trim($itemData, '-');
                            }elseif($questionTypeId == '10'){
                                // 10 - Matrix Choice (only one answer per row)	
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    $questionnaireData[$val['id']]['items'][$iKey] = $iVal;
                                }
                            }elseif($questionTypeId == '11'){
                                // 11 - Matrix Choice (only one answer per row with other option)	
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    if(!preg_match('/_other/i', $iKey)){
                                        $questionnaireData[$val['id']]['items'][$iKey] = $iVal;    
                                    }                                        
                                }                                    
                            }elseif($questionTypeId == '12'){
                                // 12 - Matrix Choice (multiple answers per row)	
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    $questionnaireData[$val['id']]['items'][$iKey] = $iVal;
                                }
                            }elseif($questionTypeId == '13'){
                                // 13 - Matrix Choice (multiple answers per row with other option)	
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    if(!preg_match('/_other/i', $iKey)){
                                        $questionnaireData[$val['id']]['items'][$iKey] = $iVal;    
                                    }                                        
                                }                                    
                            }elseif($questionTypeId == '14'){
                                // 14 - Matrix Choice (Date/Time)
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                $rowsMax = count($itemData) / 3;
                                $rowsInd = 0;
                                while(++$rowsInd <= $rowsMax){
                                    $itemDataDay = $cRequest->getPost('item_'.$val['id'].'_dd_row_'.$rowsInd);
                                    $itemDataMonth = $cRequest->getPost('item_'.$val['id'].'_mm_row_'.$rowsInd);
                                    $itemDataYear = $cRequest->getPost('item_'.$val['id'].'_yy_row_'.$rowsInd);
                                    $itemDate = '';
                                    
                                    if($val['date_format'] == 'DD/MM/YYYY'){
                                        $itemDate .= $itemDataDay.'-'.$itemDataMonth;    
                                    }else{
                                        $itemDate .= $itemDataMonth.'-'.$itemDataDay;    
                                    }
                                    $itemDate .= '-'.$itemDataYear;
                                    $questionnaireData[$val['id']]['items']['item_'.$val['id'].'_row_'.$rowsInd] = trim($itemDate, '-');
                                }
                            }elseif($questionTypeId == '15'){
                                // 15 - Rating Scale
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    $questionnaireData[$val['id']]['items'][$iKey] = $iVal;
                                }
                            }elseif($questionTypeId == '16'){
                                // 16 - Ranking
                                $itemData = $cRequest->getPostWith('item_'.$val['id']);
                                foreach($itemData as $iKey => $iVal){
                                    $questionnaireData[$val['id']]['items'][$iKey] = $iVal;
                                }                                
                            }elseif($questionTypeId == '18'){
                                // 18 - Action Script	
                                $questionnaireData[$val['id']]['actions_data'] = $cRequest->getPost('questionnaire_item_data');
                                $questionnaireData[$val['id']]['actions_data_score'] = $cRequest->getPost('questionnaire_item_score');
                                $questionnaireData[$val['id']]['actions_data_total_score'] = $cRequest->getPost('questionnaire_item_total_score');
                            }else{
                                $questionnaireData[$val['id']]['items'] = $itemData;
                            }
                        }
                        
                        if($questionTypeId == '11'){
                            $itemData = $cRequest->getPostWith('item_'.$val['id']);
                            foreach($itemData as $iKey => $iVal){
                                if(preg_match('/_other$/i', $iKey)){
                                    $questionnaireData[$val['id']]['item_other'][$iKey] = $iVal;
                                    $questionnaireData[$val['id']]['item_other_variants'][$iKey] = $cRequest->getPost($iKey.'_variant');
                                }                                        
                            }
                            if(!isset($questionnaireData[$val['id']]['items'])){                                    
                                $questionnaireData[$val['id']]['items'] = $tempItems;
                            }
                        }elseif($questionTypeId == '13'){
                            $itemData = $cRequest->getPostWith('item_'.$val['id']);
                            foreach($itemData as $iKey => $iVal){
                                if(preg_match('/_other$/i', $iKey)){
                                    $questionnaireData[$val['id']]['item_other'][$iKey] = $iVal;
                                }                                        
                            }
                            if(!isset($questionnaireData[$val['id']]['items'])){                                    
                                $questionnaireData[$val['id']]['items'] = $tempItems;
                            }
                        }else{
                            if(!empty($itemOtherId)) $questionnaireData[$val['id']]['item_other_id'] = $itemOtherId;
                            $questionnaireData[$val['id']]['item_other'] = $itemOtherData;    
                        }                            
                    }                                           
                    
                    //xxx
                    ///CDebug::display($questionnaireData, true);
                    
                    $cSession->set('questionnaire_data', $questionnaireData);
                    $validated = true;
                }                    
            }
            
            if($validated){
                if($page === 'survey-welcome'){
                    $nextPage = 'questionnaire-welcome';
                }elseif($page === 'survey-already-completed'){
					$this->logoutAction();
				}elseif($page === 'survey-finish'){
                    // Update finish time for participant
                    $cParticipant = SurveyParticipants::model()->find('survey_id = :survey_id AND participant_id = :participant_id', array(':survey_id'=>$survey->id, ':participant_id'=>CAuth::getLoggedId()));
                    if(CTime::isEmptyDateTime($cParticipant->finish_date)){
                        $cParticipant->finish_date = LocalTime::currentDateTime();
                        $cParticipant->status = 2;
                        $cParticipant->save();
                    }
                    
                    $csParticipant = SurveysParticipants::model()->findByPk(CAuth::getLoggedId());
                    // Send email notification when survey is finished
                    if(ModulesSettings::model()->param('surveys', 'send_participant_email_notification') == 1 && $csParticipant->email !== ''){
                        $emailResult = Website::sendEmailByTemplate(
                            $csParticipant->email,
                            'surveys_survey_completed',
                            A::app()->getLanguage(),
                            array(
                                '{FIRST_NAME}' => $csParticipant->first_name,
                                '{LAST_NAME}' => (($csParticipant->first_name !== '' || $csParticipant->last_name !== '') ? $csParticipant->last_name : A::t('surveys', 'Participant'))
                            )
                        );
                    }
            
                    $this->logoutAction();
                }elseif($page === 'questionnaire-welcome'){
                    $nextPage = '1';
                }elseif($page === 'questionnaire-finish'){
                    // =======================================================
                    // OLD PLACE - save prepared date in database
                    // =======================================================

                    // Set the current questionnaire Id
                    $newCurrentQuestionnaire = $this->_findNextQuestionnaire(CAuth::getLoggedId(), $survey->id);
                    if($currentQuestionnaireId == $newCurrentQuestionnaire['id']){
                        $nextPage = 'survey-finish';						
                    }else{
                        $cSession->set('currentQuestionnaireId', $newCurrentQuestionnaire['id']);
                        $cSession->set('currentQuestionnaireItemsPerPage', $newCurrentQuestionnaire['items_per_page']);
                        $nextPage = 'questionnaire-welcome';
                    }
                }else{
                    $nextPage = ($page + 1);                    
                }                    
                $cSession->set('page', $nextPage);

                $this->redirect('surveys/show/code/'.$code.'/page/'.$page);
            }else{                   
                $this->_preparePageItems($survey, $code, $page, $pageSession, $currentQuestionnaireId);
                $this->_view->actionMessage = CWidget::create(
                    'CMessage', array('error', $errorMessage, array('button'=>false))
                );
            }

        }elseif($page === 'survey-welcome' && $pageSession === 'survey-welcome'){
            // Show survey welcome page
            $this->_view->page = 'survey-welcome';
            $cSession->set('page', 'survey-welcome');                
            if($survey->welcome_message){
                $this->_view->content = $this->_getContentByGenter($survey, $survey, 'welcome_message'); 
            }else{
                $this->_view->content = A::t('surveys', 'Welcome to the survey!');
            }
			
		}elseif($page === 'survey-already-completed' && $pageSession === 'survey-already-completed'){
            // Show survey already completed page
            $this->_view->page = 'survey-already-completed';
            $cSession->set('page', 'survey-already-completed');
			
			$this->_view->content = CWidget::create('CMessage', array('warning', A::t('surveys', 'You have already completed this survey!'), array('button'=>false)));

        }elseif($page === 'survey-finish' && $pageSession === 'survey-finish'){
            // Show survey finish page
            $this->_view->page = 'survey-finish';
            $cSession->set('page', 'survey-finish');
            
            if($survey->complete_message !== ''){
                $content = $this->_getContentByGenter($survey, $survey, 'complete_message'); 
            }else{
				$content = CWidget::create('CMessage', array('success', A::t('surveys', 'You have successfully completed this survey!'), array('button'=>false)));
            }

			$cParticipant = SurveyParticipants::model()->find('survey_id = :survey_id AND participant_id = :participant_id', array(':survey_id'=>$survey->id, ':participant_id'=>CAuth::getLoggedId()));
            if($cParticipant){
				// Show score data on the survey finish page
                if($cParticipant->data_score != '' && $cParticipant->data_total_score != ''){
                    $content .= '<br>'.A::t('surveys', 'Score').': '.$cParticipant->data_score;
                    $content .= '<br>'.A::t('surveys', 'Total Score').': '.$cParticipant->data_total_score;
                    $content .= '<hr>';
                }
				
				// Check if survey was already completed in the past
				if($cParticipant->status == 2 && !CTime::isEmptyDateTime($cParticipant->finish_date)){
					$content = CWidget::create('CMessage', array('warning', A::t('surveys', 'You have already completed this survey!'), array('button'=>false)));
					$this->_view->page = 'survey-already-completed';
					$cSession->set('page', 'survey-already-completed');
				}
            }
            
            $this->_view->content = $content;
            $this->_view->isSurveyComplete = true;
            
        }elseif($page === 'questionnaire-welcome' && $pageSession === 'questionnaire-welcome'){
            // Show questionnaire welcome page
            $this->_view->page = 'questionnaire-welcome';
            $cSession->set('page', 'questionnaire-welcome');
            if($surveyQuestionnaire = SurveyQuestionnaires::model()->findByPk($currentQuestionnaireId)){
                if($surveyQuestionnaire->start_message !== ''){
                    $this->_view->questionnaireName = $surveyQuestionnaire->name;                    
                    $this->_view->content = $this->_getContentByGenter($survey, $surveyQuestionnaire, 'start_message'); 
                }else{
                    // No questionnaire welcome page exists - skip to 1st question
                    $cSession->set('page', '1');
                    $this->redirect('surveys/show/code/'.$code.'/page/1');
                    // A::t('surveys', 'This is a new questionnaire {questionnaire}! Click Continue to start.', array('{questionnaire}'=>$surveyQuestionnaire->name));
                }
            }else{
                // No pending questionnaires exist - skip to the end of survey
                $cSession->set('page', 'survey-finish');
                $this->redirect('surveys/show/code/'.$code.'/page/survey-finish');
            }

        }elseif($page === 'questionnaire-finish' && $pageSession === 'questionnaire-finish'){

            if($questionnaireData = $cSession->get('questionnaire_data')){    
                // =======================================================
                // START - save prepared date in database
                // =======================================================
                
                ///yyy
                ///CDebug::display($questionnaireData, true);
                
                $result = true;
                foreach($questionnaireData as $key => $val){
                    $surveyAnswers = new SurveyAnswers();
                    $items = isset($val['items']) ? $val['items'] : '0';
                    $itemOther = isset($val['item_other']) ? $val['item_other'] : '';
                    $itemOtherVariants = isset($val['item_other_variants']) ? $val['item_other_variants'] : '';
                    $itemOtherId = isset($val['item_other_id']) ? $val['item_other_id'] : '';
                    $questionTypeId = isset($val['question_type_id']) ? $val['question_type_id'] : '';
                    $actionsData = isset($val['actions_data']) ? $val['actions_data'] : '';
                    $actionsDataScore = isset($val['actions_data_score']) ? $val['actions_data_score'] : '';
                    $actionsDataTotalScore = isset($val['actions_data_total_score']) ? $val['actions_data_total_score'] : '';
                    
                    $rowCount = 0;
                    $itemsCount = count($items);
                    if(is_array($items)){
                        
                        // Check items
                        foreach($items as $kKey => $kVal){
                            $surveyAnswers->clearPkValue();
                            $surveyAnswers->participant_id = CAuth::getLoggedId();
                            $surveyAnswers->survey_id = $survey->id;
                            $surveyAnswers->questionnaire_id = $currentQuestionnaireId;
                            $surveyAnswers->questionnaire_item_id = $key;
                            $surveyAnswers->questionnaire_item_other = '';                                    
                            if($questionTypeId == 7){
                                if(!$kVal) continue;
                                $surveyAnswers->questionnaire_item_variant_id = $kKey;
                                $surveyAnswers->answer_text = $kVal;                                        
                                $result = $surveyAnswers->save();
                            }elseif($questionTypeId == 11){
                                if(isset($val['item_other'][$kKey.'_other']) && $val['item_other'][$kKey.'_other'] != ''){
                                    continue;
                                }                                        
                                $surveyAnswers->questionnaire_item_variant_id = $kVal;
                                $surveyAnswers->answer_text = '';
                                $result = $surveyAnswers->save();
                            }elseif($questionTypeId == 12 || $questionTypeId == 13){
                                foreach($kVal as $kkKey => $kkVal){                                            
                                    $surveyAnswers->clearPkValue();
                                    $surveyAnswers->questionnaire_item_id = str_ireplace(array('item_', '_row_1', '_row_2'), '', $kKey);
                                    $surveyAnswers->questionnaire_item_variant_id = $kkVal;
                                    $surveyAnswers->answer_text = '';
                                    $result = $surveyAnswers->save();
                                    
                                    // Update votes counter in item variants table
                                    $this->_updateVotesCounter($surveyAnswers->questionnaire_item_variant_id);
                                }
                            }elseif($questionTypeId == 14){
                                $rowCount--;
                                $surveyAnswers->questionnaire_item_id = str_ireplace(array('item_', '_row_1', '_row_2'), '', $kKey);
                                $surveyAnswers->questionnaire_item_variant_id = $rowCount;
                                $surveyAnswers->answer_text = $kVal;
                                $result = $surveyAnswers->save();
                            }elseif($questionTypeId == 15){
                                $surveyAnswers->questionnaire_item_variant_id = $kVal;
                                $surveyAnswers->answer_text = '';
                                $result = $surveyAnswers->save();
                            }elseif($questionTypeId == 16){
                                if(!empty($kVal)){
                                    $surveyAnswers->questionnaire_item_variant_id = $kVal;
                                    $surveyAnswers->answer_text = '';                                
                                    $result = $surveyAnswers->save();
                                }
                            }else{
                                $surveyAnswers->questionnaire_item_variant_id = $kVal;    
                                $surveyAnswers->answer_text = isset($val['answer_text']) ? $val['answer_text'] : '';
                                $result = $surveyAnswers->save();
                            }
                            
                            ///CDebug::display($surveyAnswers->getErrorMessage());
                            ///CDebug::display($surveyAnswers, false);                                    
                            // Update votes counter in item variants table
                            if(!in_array($questionTypeId, array('12', '13'))){                                    
                                $this->_updateVotesCounter($surveyAnswers->questionnaire_item_variant_id);
                            }                                    
                            
                            if(!$result) break;
                        }
                        
                        // Check other options
                        if(!empty($itemOther)){
                            if(is_array($itemOther)){
                                // question type ID - 11 and 13?
                                $rowCount = 0; /* must be negative, otherwise error - unique fields set */
                                foreach($itemOther as $oKey => $oVal){
                                    $rowCount--;
                                    if(!empty($oVal)){
                                        // Update votes counter
                                        $otherVariantId = isset($itemOtherVariants[$oKey]) ? $itemOtherVariants[$oKey] : '';
                                        $this->_updateVotesCounter($otherVariantId);
                                        // Save answer
                                        $result = $this->_saveAnswer($surveyAnswers, $key, $val, $survey->id, $currentQuestionnaireId, $rowCount, $oVal);
                                    }
                                }
                            }else{
                                // Questions with other option
                                $result = $this->_saveAnswer($surveyAnswers, $key, $val, $survey->id, $currentQuestionnaireId, $itemOtherId, $itemOther);
                            }
                        }                                
                    }else{
                        $result = $this->_saveAnswer($surveyAnswers, $key, $val, $survey->id, $currentQuestionnaireId, (!empty($itemOther) ? $itemOtherId : ''), $itemOther, $actionsData, $actionsDataScore, $actionsDataTotalScore);
                    }
                    if(!$result) break;
                }
                
                ///exit;
                
                if(!$result){
                    SurveyAnswers::model()->deleteAll(
                        'participant_id = :participant_id AND survey_id = :survey_id AND questionnaire_id = :questionnaire_id',
                        array(':participant_id'=>CAuth::getLoggedId(), ':survey_id'=>$survey->id, ':questionnaire_id'=>$currentQuestionnaireId)
                    );
                    ///echo SurveyAnswers::model()->getErrorMessage(); exit;
                    ///$this->redirect('surveys/show/code/'.$code.'/page/questionnaire-finish/error/1/');
                    $error = '1';
                }
    
                $cSession->remove('questionnaire_data');
                // =======================================================
                // END - save prepared date in database (start)
                // =======================================================                
            }
            
            // Show questionnaire finish page
            $surveyQuestionnaire = SurveyQuestionnaires::model()->findByPk($currentQuestionnaireId);
            $this->_view->questionnaireName = $surveyQuestionnaire->name;
            $this->_view->page = 'questionnaire-finish';
            $cSession->set('page', 'questionnaire-finish');
            if($error == '1'){
				if(APPHP_MODE == 'demo'){
					$alert = CDatabase::init()->getErrorMessage();
					$alertType = 'warning';
				}else{
					$alert = A::t('surveys', 'An error occurred while saving data for this questionnaire!');
					$alertType = 'error';
				}			
                $this->_view->content = CWidget::create('CMessage', array($alertType, $alert, array('button'=>false)));
            }else{
                $finish_message = $this->_getContentByGenter($survey, $surveyQuestionnaire, 'finish_message'); 
                $this->_view->content = ($finish_message !== '') ? $finish_message : A::t('surveys', 'You have successfully completed questionnaire {questionnaire}! Click Continue to proceed.', array('{questionnaire}'=>$surveyQuestionnaire->name));
            }        
        }else{                
            $this->_preparePageItems($survey, $code, $page, $pageSession, $currentQuestionnaireId);                
        }
        

        // Set meta tags according to active language
        Website::setMetaTags(array('title'=>$survey->name.' | '.A::t('surveys', 'Page').' '.$page));
        $this->_view->surveyName = $survey->name;
        $this->_view->genderFormulation = $survey->gender_formulation;
        $this->_view->code = $code;
        $this->_view->render('surveys/show');
    }
    
    /**
     * Participant login action handler
     * @param string $code
     * @param string $param
     */
    public function loginAction($code = 0, $param = '')
    {
        // Set frontend settings
        Website::setFrontend();
		$cRequest = A::app()->getRequest();
        $cSession = A::app()->getSession();
        $message = '';
        $messageType = '';
		// Clean code from spaces
		$code = trim($code);

        // Check if survey is a valid and active 
        $survey = Surveys::model()->find('code = :code', array(':code'=>$code));

        // Redirect if already logged
        if(CAuth::isLoggedInAs('participant')){
            $this->redirect('surveys/show/code/'.($survey ? $code : ''));
        }
        
        $this->_view->fieldIdentityCode = $fieldIdentityCode = ModulesSettings::model()->param('surveys', 'field_identity_code');
        $this->_view->fieldPassword = $fieldPassword = ModulesSettings::model()->param('surveys', 'field_password');
        $this->_view->fieldFirstName = $fieldFirstName = ModulesSettings::model()->param('surveys', 'field_first_name');
        $this->_view->fieldLastName = $fieldLastName = ModulesSettings::model()->param('surveys', 'field_last_name');
        $this->_view->fieldEmail = $fieldEmail = ModulesSettings::model()->param('surveys', 'field_email');
        $this->_view->fieldGender = $fieldGender = ModulesSettings::model()->param('surveys', 'field_gender');

        $this->_view->step = $step = $cRequest->getPost('step', 'int', 1);
        $this->_view->identityCode = $identityCode = $cRequest->getPost('identity_code');
        $this->_view->password = $password = $cRequest->getPost('password');
        $this->_view->firstName = $firstName = $cRequest->getPost('first_name');
        $this->_view->lastName = $lastName = $cRequest->getPost('last_name');
        $this->_view->email = $email = $cRequest->getPost('email');
        $this->_view->gender = $gender = $cRequest->getPost('gender');
        $this->_view->agree = $agree = $cRequest->getPost('agree');
        
        $this->_view->showFirstName = false;
        $this->_view->showLastName = false;
        $this->_view->showEmail = false;
        $this->_view->showGender = false;
		$this->_view->showTermsAndConditions = ModulesSettings::model()->param('surveys', 'show_terms_and_conditions');
        
        // Check if survey is a valid and active 
        if($survey){
            if(!$survey->is_active){
                $this->redirect('surveys/expired');  
            }elseif($survey->is_active && !CTime::isEmptyDate($survey->expires_at) && $survey->expires_at < LocalTime::currentDate()){
                Surveys::model()->updateByPk((int)$survey->id, array('is_active'=>0));
                $this->redirect('surveys/expired');
            }elseif($survey->access_mode == 'p'){
				if(CAuth::isLoggedInAsAdmin()){
					A::app()->getSession()->setFlash('message', 'access-deined-error');
					$this->redirect('surveys/error');
				}else{
					// Handle particapnt's access according to idetification type					
					$identificationType = ModulesSettings::model()->param('surveys', 'participant_identification_type');

					// Check participant if survey has public access
					$participantId = 0;
					if($identificationType == 'ip_address'){
						$ipAddress = A::app()->getRequest()->getUserHostAddress();
						$participant = SurveysParticipants::model()->find('ip_address = :ip_address', array(':ip_address'=>$ipAddress));
					}else{
						$cookieCode = A::app()->getCookie()->get('surveyParticipantId');
						$participant = !empty($cookieCode) ? SurveysParticipants::model()->find('cookie_code = :cookie_code', array(':cookie_code'=>$cookieCode)) : null;
					}
						
					if($participant){
						if($identificationType == 'ip_address'){
							$surveyParticipant = SurveyParticipants::model()->find(
								CConfig::get('db.prefix').'surveys_participants.ip_address = :ip_address AND
								'.CConfig::get('db.prefix').'surveys_participants.is_active = 1 AND 
								'.CConfig::get('db.prefix').'surveys_entity_participants.survey_id = :survey_id',                            
								array(':ip_address'=>$ipAddress, ':survey_id'=>$survey->id)
							);
						}else{
							$surveyParticipant = SurveyParticipants::model()->find(
								CConfig::get('db.prefix').'surveys_participants.cookie_code = :cookie_code AND
								'.CConfig::get('db.prefix').'surveys_participants.is_active = 1 AND 
								'.CConfig::get('db.prefix').'surveys_entity_participants.survey_id = :survey_id',                            
								array(':cookie_code'=>$cookieCode, ':survey_id'=>$survey->id)
							);
						}
						
						if($surveyParticipant){
							if($surveyParticipant->is_active){
								$participantId = $surveyParticipant->participant_id;
								$this->_setParticipantLogin($code, $survey->access_mode, $participantId, '', '', '', '');
							}else{
								A::app()->getSession()->setFlash('message', 'participants-blocked');
								$this->redirect('surveys/error');                                
							}
						}else{
							// Assign new paticipant to survey
							$this->_assignParticipantToSurvey($code, $survey->id, $survey->access_mode, $participant->getPrimaryKey());
						}
					}else{                        
						$participant = new SurveysParticipants();
						if($identificationType == 'ip_address'){
							$participant->ip_address = $ipAddress;
						}else{
							$cookieCode = CHash::getRandomString(20);
							$participant->cookie_code = $cookieCode;
							A::app()->getCookie()->set('surveyParticipantId', $cookieCode);
						}						
						$participant->gender = '';
						$participant->is_active = 1;
						
						if($participant->save()){                            
							// Assign new paticipant to survey
							$this->_assignParticipantToSurvey($code, $survey->id, $survey->access_mode, $participant->getPrimaryKey());
						}else{
							A::app()->getSession()->setFlash('message', 'initial-record-error');
							$this->redirect('surveys/error');
						}
					}
					
					// Set the current questionnaire Id
					if($nextQuestionnaire = $this->_findNextQuestionnaire($participantId, $survey->id)){
						$cSession->set('currentQuestionnaireId', $nextQuestionnaire['id']);
						$cSession->set('currentQuestionnaireItemsPerPage', $nextQuestionnaire['items_per_page']);
						// Update starting time for participant
						if(!empty($surveyParticipant)){
							$cParticipant = SurveyParticipants::model()->find('survey_id = :survey_id AND participant_id = :participant_id', array(':survey_id'=>$survey->id, ':participant_id'=>$surveyParticipant->participant_id));
							if(CTime::isEmptyDateTime($cParticipant->start_date)){
								$cParticipant->start_date = LocalTime::currentDateTime();
								$cParticipant->status = 1;
								$cParticipant->save();
							}
						}
						
						$this->redirect('surveys/show/code/'.$code);
					}else{
						$this->redirect('surveys/completed');
					}
				}
            }
        }else{
            $this->redirect('surveys/error');
        }
		
		// Check if participant tryies to login wit hdirect link
		$enableLoginByUrl = ModulesSettings::model()->param('surveys', 'enable_login_by_url');
		$checkAdditionalFields = true;
		if($enableLoginByUrl && !empty($param) && !CAuth::isLoggedIn()){
			$result = CHash::decrypt($param, CConfig::get('installationKey'));
			parse_str($result, $resultParsed);
			
			$identityCode = !empty($resultParsed['entity_code']) ? $resultParsed['entity_code'] : null;
			$password = !empty($resultParsed['pswd']) ? $resultParsed['pswd'] : null;
			
			$checkAdditionalFields = false;
			$cRequest->setPost('act', 'send');
		}
        
        if($cRequest->getPost('act') == 'send'){
            if(empty($identityCode)){
                $message = A::t('surveys', 'Identity code cannot be empty!');
                $messageType = 'error';
                $this->_view->errorField = 'identity_code';
            }elseif($fieldPassword == 'allow' && empty($password)){
                $message = A::t('surveys', 'Password cannot be empty!');
                $messageType = 'error';
                $this->_view->errorField = 'password';                    
            }elseif($checkAdditionalFields && $this->_view->showTermsAndConditions && empty($agree)){
                $message = A::t('surveys', 'You have to agree you accept our Terms and Conditions!');
                $messageType = 'error';
                $this->_view->errorField = 'agree';                    
            }
            
            if(!$message){
                $surveyParticipant = SurveyParticipants::model()->find(
                    CConfig::get('db.prefix').'surveys_participants.identity_code = :identity_code AND
                    '.CConfig::get('db.prefix').'surveys_participants.is_active = 1 AND
                    '.CConfig::get('db.prefix').'surveys_entity_participants.survey_id = :survey_id AND
                    '.CConfig::get('db.prefix').'surveys_entity_participants.is_active = 1',
                    array(
                        ':identity_code'=>$identityCode,
                        ':survey_id'=>$survey->id
                    )
                );
                if($surveyParticipant){                        
                    if($surveyParticipant->participant_first_name == '') $this->_view->showFirstName = true;
                    if($surveyParticipant->participant_last_name == '') $this->_view->showLastName = true;
                    if($surveyParticipant->participant_email == '') $this->_view->showEmail = true;
                    if($surveyParticipant->participant_gender == '') $this->_view->showGender = true;

                    if($step == '2' && $this->_view->showFirstName && $fieldFirstName == 'allow-required' && empty($firstName)){
                        $message = A::t('surveys', 'First Name cannot be empty!');
                        $messageType = 'error';
                        $this->_view->errorField = 'first_name';                    
                    }elseif($step == '2' && $this->_view->showLastName && $fieldLastName == 'allow-required' && empty($lastName)){
                        $message = A::t('surveys', 'Last Name cannot be empty!');
                        $messageType = 'error';
                        $this->_view->errorField = 'last_name';
                    }elseif($step == '2' && $this->_view->showEmail && $fieldEmail == 'allow-required' && empty($email)){
                        $message = A::t('surveys', 'Email cannot be empty!');
                        $messageType = 'error';
                        $this->_view->errorField = 'email';
                    }elseif($step == '2' && $this->_view->showEmail && !empty($email) && !CValidator::isEmail($email)){
                        $message = A::t('surveys', 'Please enter a valid email address!');
                        $messageType = 'error';
                        $this->_view->errorField = 'email';                        
                    }elseif($step == '2' && $this->_view->showGender && $fieldGender == 'allow-required' && empty($gender)){
                        $message = A::t('surveys', 'Please select your gender!');
                        $messageType = 'error';
                        $this->_view->errorField = 'gender';
                    }
                    
                    if(!$message){
                        if($step == '1' && $checkAdditionalFields &&
                           (($fieldFirstName !== 'no' && $surveyParticipant->participant_first_name == '') ||
                            ($fieldLastName !== 'no' && $surveyParticipant->participant_last_name == '') ||
                            ($fieldEmail !== 'no' && $surveyParticipant->participant_email == '') ||
                            ($fieldGender !== 'no' && $surveyParticipant->participant_gender == '')
                            )){
                            $message = A::t('surveys', 'Please enter other fields!');
                            $messageType = 'error';
                            $this->_view->step = 2;                            
                        }elseif($fieldPassword == 'no' || ($fieldPassword == 'allow' && $surveyParticipant->participant_password === $password)){
                            $this->_setParticipantLogin(
                                $code,
                                $survey->access_mode,
                                $surveyParticipant->participant_id,
                                $surveyParticipant->participant_last_name,
                                $surveyParticipant->participant_first_name,
                                $surveyParticipant->participant_email,
                                $surveyParticipant->participant_gender
                            );
                            
                            // Set the current questionnaire Id
                            if($nextQuestionnaire = $this->_findNextQuestionnaire($surveyParticipant->participant_id, $survey->id)){
                                $cSession->set('currentQuestionnaireId', $nextQuestionnaire['id']);
                                $cSession->set('currentQuestionnaireItemsPerPage', $nextQuestionnaire['items_per_page']);
                                
                                // Update participant with new data 
                                $participant = SurveysParticipants::model()->findByPk($surveyParticipant->participant_id);
                                if($fieldFirstName != 'no' && $firstName != '' && $participant->first_name == '') $participant->first_name = $firstName;
                                if($fieldLastName != 'no' && $lastName != '' && $participant->last_name == '') $participant->last_name = $lastName;
                                if($fieldEmail != 'no' && $email != '' && $participant->email == '') $participant->email = $email;
                                if($fieldGender != 'no' && $gender != '' && $participant->gender == '') $participant->gender = $gender;
                                $participant->save();
                                
                                // Update starting time for participant
                                $cParticipant = SurveyParticipants::model()->find('survey_id = :survey_id AND participant_id = :participant_id', array(':survey_id'=>$survey->id, ':participant_id'=>$surveyParticipant->participant_id));
                                if(CTime::isEmptyDateTime($cParticipant->start_date)){
                                    $cParticipant->start_date = LocalTime::currentDateTime();
                                    $cParticipant->status = 1;
                                    $cParticipant->save();
                                }
								
								// This participant already completed this survey
								if($cParticipant->status == 2 && !CTime::isEmptyDateTime($cParticipant->finish_date)){
									$cSession->set('page', 'survey-already-completed');
									$this->redirect('surveys/show/code/'.$code.'/page/survey-already-completed');
								}else{
									$this->redirect('surveys/show/code/'.$code);
								}
                            }else{
                                $this->redirect('surveys/completed');
                            }
                        }else{
                            $message = A::t('surveys', 'Wrong password for registered participants, please try again!');
                            $messageType = 'error';
                            $this->_view->errorField = 'password';
                        }
                    }
                }else{
                    $message = A::t('surveys', 'Participant with such identity code does not exist!');
                    $messageType = 'error';
                    $this->_view->errorField = 'identity_code';
                }                
            }
        }

        if(!empty($message)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($messageType, $message, array('button'=>false))
            );
        }

        $this->_view->code = $code;
		$this->_view->hasTestData = Modules::model()->param('surveys', 'has_test_data');
		$this->_view->loginMessage = $survey->login_message;
		
        $this->_view->render('surveys/login');
    }

    /**
     * Participant logout action handler
     * @param mixed $code
     */
    public function logoutAction($code = 0)
    {
        $surveyCode = A::app()->getSession()->get('surveyCode');
        $surveyAccessMode = A::app()->getSession()->get('surveyAccessMode');
        A::app()->getSession()->endSession();
		
        // Clear cache
        if(CConfig::get('cache.enable')) CFile::emptyDirectory('protected/tmp/cache/');
        if($surveyCode){
            if($surveyAccessMode == 'p'){
                $this->redirect('surveys/completed');
            }else{
                $this->redirect('surveys/login/code/'.$surveyCode);                        
            }            
        }elseif($this->_checkActionAccessByCode($code)){ 
            $this->redirect('surveys/login/code/'.htmlentities($code));
        }else{
            $this->redirect(Website::getDefaultPage());
        }
    } 
    
    /**
     * Error action handler
     */
    public function errorAction()
    {
        $alert = '';
        if(A::app()->getSession()->hasFlash('message')){
            $alert = A::app()->getSession()->getFlash('message');
        }

        if($alert == 'participants-blocked'){
            $this->_view->errorTitle = A::t('surveys', 'Error Page');
            $this->_view->errorHeader = A::t('surveys', 'Error Page');
            $this->_view->errorText = A::t('surveys', 'Participants from this IP address are blocked!');            
        }elseif($alert == 'initial-record-error'){
            $this->_view->errorTitle = A::t('surveys', 'Error Page');
            $this->_view->errorHeader = A::t('surveys', 'Error Page');
            $this->_view->errorText = A::t('surveys', 'An error occurred while creating initial record for participant!');
		}elseif($alert == 'access-deined-error'){
            $this->_view->errorTitle = A::t('surveys', 'Error Page');
            $this->_view->errorHeader = A::t('surveys', 'Error Page');
            $this->_view->errorText = A::t('surveys', 'You cannot see this survey because you are logged in as admin!');
        }else{
            $this->_view->errorTitle = A::t('surveys', '404 Page');
            $this->_view->errorHeader = A::t('surveys', '404 Error Title');
            $this->_view->errorText = A::t('surveys', '404 Error Description'); 
        }
        $this->_view->render('surveys/error');
    }

    /**
     * Expired action handler
     */
    public function expiredAction()
    {
        $this->_view->expiredTitle = A::t('surveys', 'Survey is expired');
        $this->_view->expiredHeader = A::t('surveys', 'Survey is expired');
        $this->_view->expiredText = A::t('surveys', 'This survey has expired'); 
        $this->_view->render('surveys/expired');
    }

    /**
     * Completed action handler
     */
    public function completedAction()
    {
        $this->_view->completedTitle = A::t('surveys', 'This survey has been completed');
        $this->_view->completedHeader = A::t('surveys', 'Survey is completed');
        $this->_view->completedText = A::t('surveys', 'This survey has been completed'); 
        $this->_view->render('surveys/completed');
    }

    /**
     * Completed action handler
     */
    public function showScoreAction()
    {
		if(!CAuth::isLoggedInAs('participant')){
			$this->redirect('surveys/login');
		}

		$result = array(
			'score'			=>	0,
			'total_score'	=>	0,
		);
		
		if($survey = Surveys::model()->find('code = :code', array(':code'=>A::app()->getSession()->get('surveyCode')))){
			$participant = SurveyParticipants::model()->find(
				'participant_id = :participant_id AND survey_id = :survey_id',
				array(':participant_id'=>CAuth::getLoggedId(), ':survey_id'=>$survey->id)
			);
			
			if($participant){
				$result = array(
					'score'			=>	$participant->data_score,
					'total_score'	=>	$participant->data_total_score,
				);			
			}
		}
		//echo CAuth::getLoggedId();
		//echo $result['data_score'];
		//echo $result['data_total_score'];

		echo json_encode($result);
		exit;
	}

    /**
     * Prepare questionnaires
     * @param int $surveyId
     * @param int $questionnaireId
     * @return void
     */
    private function _prepareQuestionnaires($surveyId = 0, $questionnaireId = 0)
    {        
        $questionnaires = array();
        $result = SurveyQuestionnaires::model()->findAll(array('condition'=>'survey_id = :survey_id', 'order'=>'sort_order ASC'), array(':survey_id'=>$surveyId));
        $questionnaires[0] = A::t('surveys', 'select');
        foreach($result as $key => $val){
            $questionnaires[$val['id']] = $val['name'];
        }
        $this->_view->questionnaires = $questionnaires;

        $this->_view->questionnaireId = $questionnaireId;
        $this->_view->questionnaireLink = 'surveyQuestionnaires/results/surveyId/'.$surveyId.'/questionnaireId/'.$questionnaireId;
        $this->_view->questionnaireName = "questionnaire->name";
        $this->_view->questionnaireItems = SurveyQuestionnaireItems::model()->findAll(
            array(
                'condition'=>'entity_questionnaire_id = :entity_questionnaire_id',
                'order'=>'sort_order ASC'
            ),
            array(':entity_questionnaire_id'=>$questionnaireId)
        );
    }
    
    /**
     * Check if passed record ID is valid
     * @param int $id
     */
    private function _checkActionAccess($id = 0)
    {        
        $model = Surveys::model()->findByPk($id);
        if(!$model){
            $this->redirect('surveys/manage');
        }
        return $model;
    }
    
    /**
     * Check if passed record code is valid
     * @param int $code
     */
    private function _checkActionAccessByCode($code = 0)
    {        
        $model = Surveys::model()->exists('code = :code', array(':code'=>$code));
        if(!$model){
            return false;
        }
        return true;
    }    
    
    /**
     * Finds and returns Id of the next questionnaire
     * @param int $participantId
     * @param int $surveyId
     * @return int
     */
    private function _findNextQuestionnaire($participantId, $surveyId)
    {
        // Check what are the finished questionnaires
        $surveyTableName = CConfig::get('db.prefix').SurveyAnswers::model()->getTableName();
        $surveyAnswers = CDatabase::init()->select('
            SELECT questionnaire_id
            FROM '.$surveyTableName.'
            WHERE participant_id = :participant_id AND survey_id = :survey_id
            GROUP BY questionnaire_id',
            array(
                ':participant_id'=>$participantId,
                ':survey_id'=>$surveyId
            )
        );
        $completeQuestionnaires = '-1';
        foreach($surveyAnswers as $key => $val){
            $completeQuestionnaires .= ','.$val['questionnaire_id'];
        }
        // Find the pending questionnaires
        $pendingQuestionnaires = SurveyQuestionnaires::model()->findAll(
            array(
                'condition'=>'survey_id = :survey_id AND
                            '.CConfig::get('db.prefix').'surveys_entity_questionnaires.is_active = 1 AND
                            '.CConfig::get('db.prefix').'surveys_entity_questionnaires.id NOT IN('.$completeQuestionnaires.')',
                'order'=>'sort_order ASC'
            ),
            array(
                ':survey_id'=>$surveyId
            )
        );
        
        return array(
            'id' => (isset($pendingQuestionnaires[0]['id']) ? $pendingQuestionnaires[0]['id'] : 0),
            'items_per_page' => (isset($pendingQuestionnaires[0]['items_per_page']) ? $pendingQuestionnaires[0]['items_per_page'] : 1)
        );        
    }
    
    /**
     * Prepare page items 
     * @param object $survey
     * @param string $code
     * @param string $page
     * @param string $pageSession
     * @param int $currentQuestionnaireId
     * @return void
     */
    private function _preparePageItems($survey, $code, $page, $pageSession, $currentQuestionnaireId)
    {
        $cSession = A::app()->getSession();
        $pageSize = $cSession->get('currentQuestionnaireItemsPerPage');
        
        $whereByGender = $this->_getGenderWhereClause($survey->gender_formulation, $cSession->get('loggedGender'));
        $totalItems = SurveyQuestionnaireItems::model()->count(
            'entity_questionnaire_id = :entity_questionnaire_id AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.is_active = 1 '.$whereByGender,
            array(':entity_questionnaire_id'=>$currentQuestionnaireId)
        );                
        $start = ($page) ? (($page - 1) * $pageSize) : 0;
        
        $surveyQuestionnaireItems = $this->_getQuestionnaireItems($currentQuestionnaireId, $start, $pageSize, $whereByGender);
        if(empty($surveyQuestionnaireItems)){
            $cSession->set('page', 'questionnaire-finish');
            $this->redirect('surveys/show/code/'.$code.'/page/questionnaire-finish');
        }
        
        // Show standard page
        $this->_view->startCount = $start;
        $this->_view->loggedGender = $cSession->get('loggedGender');
        $this->_view->questionnaireItems = $surveyQuestionnaireItems;
        $this->_view->page = $pageSession;
        $this->_view->questionnaireName = isset($surveyQuestionnaireItems[0]['questionnaire_name']) ? $surveyQuestionnaireItems[0]['questionnaire_name'] : '';
        $this->_view->content = '';        
    }
    
    /**
     * Get questionnaire items
     * @param int $currentQuestionnaireId
     * @param int $start
     * @param int $pageSize
     * @param string $whereByGender
     * @return array
     */
    private function _getQuestionnaireItems($currentQuestionnaireId, $start, $pageSize, $whereByGender = '')
    {
        if($start < 0) return array();
        
        return SurveyQuestionnaireItems::model()->findAll(
            array(
                'condition'=>'entity_questionnaire_id = :entity_questionnaire_id AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.is_active = 1'.$whereByGender,
                'order'=>'sort_order ASC',
                'limit'=>$start.', '.$pageSize
            ),
            array(':entity_questionnaire_id'=>$currentQuestionnaireId)
        );
    }
    
    /**
     * Saves data to the answers table
     * @param object $surveyAnswers
     * @param string $key
     * @param array $val
     * @param int $surveyId,
     * @param int $currentQuestionnaireId
     * @param mixed $variantId
     * @param string $itemOther
     * @para mixed $actionsData
     * @para mixed $actionsDataScore
     * @para mixed $actionsDataTotalScore
     */
    private function _saveAnswer($surveyAnswers, $key, $val, $surveyId, $currentQuestionnaireId, $variantId, $itemOther, $actionsData = '', $actionsDataScore = '', $actionsDataTotalScore = '')
    {
        $surveyAnswers->clearPkValue();
        $surveyAnswers->participant_id = CAuth::getLoggedId();
        $surveyAnswers->survey_id = $surveyId;
        $surveyAnswers->questionnaire_id = $currentQuestionnaireId;
        $surveyAnswers->questionnaire_item_id = $key;
        if($variantId !== ''){
            $surveyAnswers->questionnaire_item_variant_id = $variantId;            
        }else{
            $surveyAnswers->questionnaire_item_variant_id = (isset($val['items']) && !is_array($val['items'])) ? (int)$val['items'] : '0';
        }
        $surveyAnswers->questionnaire_item_other = $itemOther;
        $surveyAnswers->answer_text = isset($val['answer_text']) ? $val['answer_text'] : '';
        $surveyAnswers->actions_data = $actionsData;
        $result = $surveyAnswers->save();
        
        if(!empty($actionsDataScore) && !empty($actionsDataTotalScore)){
            SurveyParticipants::model()->updateAll(
                array('data_score'=>$actionsDataScore, 'data_total_score'=>$actionsDataTotalScore),
                'survey_id = :survey_id AND participant_id = :participant_id',
                array(':survey_id'=>$surveyId, ':participant_id'=>CAuth::getLoggedId())
            );
        }
        
        // Update votes counter in item variants table
        $this->_updateVotesCounter($surveyAnswers->questionnaire_item_variant_id);
       
        return $result;
    }
    
    /**
     * Updates votes counter in item variants table
     * @param mixed $variantId
     * @return bool
     */
    private function _updateVotesCounter($variantId = '')
    {
        $result = false;
        if($variantId !== ''){
            $result = SurveyQuestionnaireItemVariants::model()->updateVotesCounter((int)$variantId);
        }
        return $result;
    }
 
    /**
     * Sets participant login
     * @param string $code
     * @param char $accessMode
     * @param int $participantId
     * @param string $lastName
     * @param string $firstName
     * @param string $email
     * @param char $gender
     */
    private function _setParticipantLogin($code, $accessMode, $participantId, $lastName, $firstName, $email, $gender)
    {
        $cSession = A::app()->getSession();
        $cSession->set('loggedIn', true);
        $cSession->set('loggedId', $participantId);
        $cSession->set('loggedName', $lastName.' '.$firstName);
        $cSession->set('loggedEmail', $email);
        $cSession->set('loggedGender', $gender);
        $cSession->set('loggedRole', 'participant');
        $cSession->set('surveyCode', $code);
        $cSession->set('surveyAccessMode', $accessMode);
    }
    
    /**
     * Assigns participant to survey
     * @param string $code
     * @param int $surveyId
     * @param char $accessMode
     * @param int $participantId
     */
    private function _assignParticipantToSurvey($code, $surveyId, $accessMode, $participantId)
    {
        $surveyParticipant = new SurveyParticipants();
        $surveyParticipant->survey_id = $surveyId;
        $surveyParticipant->participant_id = $participantId;
        $surveyParticipant->start_date = LocalTime::currentDateTime();
        $surveyParticipant->status = 1;
        $surveyParticipant->is_active = 1;
        if($surveyParticipant->save()){
            $this->_setParticipantLogin($code, $accessMode, $participantId, '', '', '', '');
        }else{
            A::app()->getSession()->setFlash('message', 'initial-record-error');
            $this->redirect('surveys/error');                                
        }
    }

    /**
     * Returns content according to gender formulation
     * @param object $survey
     * @param object $object
     * @param string $field
     * @return string
     */
    private function _getContentByGenter($survey, $object, $field)
    {
        if(!$survey->gender_formulation){
            $fieldName = $field;           
        }else{
            $fieldName = (A::app()->getSession()->get('loggedGender') == 'm') ? $field : $field.'_f';           
        }        
    
        return $object->$fieldName;
    }
    
    /**
     * Prepares where clause by gender of logged participant
     * @param int $genderFormulation
     * @param string $loggedGender
     * @return string
     */
    private function _getGenderWhereClause($genderFormulation = 0, $loggedGender = '')
    {
        $whereClause = '';
        
        if($genderFormulation){            
            if($loggedGender == 'm'){
                $whereClause = ' AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.question_text != ""';
            }elseif($loggedGender == 'f'){
                $whereClause = ' AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.question_text_f != ""';
            }
        }
        
        return $whereClause;        
    }
    
    /**
     * Prepares string for insertion in CSV file cell
     * @param string $string
     * @return string
     */
    private function _prepareCsvString($string)
    {
        return str_ireplace(array(",", "\r\n"), ' ', $string);
    }
    
	/**
     * Draws action script empty header columns
     * @param array $games
     * @param array $val
     * @param string $separator
     * @return string
     */
    private function _exportActionEmptyColumns($games, $val, $separator = ',')
    {
        if(empty($games)) return '';

        $gameName = basename($val['file_path']);
		$cicles = isset($games[$gameName]['cicles']) ? $games[$gameName]['cicles'] : 0;
		$trening_cicles = isset($games[$gameName]['trening_cicles']) ? $games[$gameName]['trening_cicles'] : 0;
		$columns = isset($games[$gameName]['columns']) ? $games[$gameName]['columns'] : 0;
		$additionalCicles = isset($games[$gameName]['additional_cicles']) ? $games[$gameName]['additional_cicles'] : 0;
		
		$totalTreningCicles = $trening_cicles * $columns + $additionalCicles;
		$totalStandardCicles = $cicles * $columns + $cicles * $additionalCicles;

		return str_repeat($separator, $totalTreningCicles + $totalStandardCicles);
	}

    /**
     * Draws action script header columns
     * @param array $games
     * @param array $val
     * @param string $separator
     * @return string
     */
    private function _exportActionHeaderColumns($games, $val, $separator = ',')
    {
        if(empty($games) || empty($val)) return '';
        
        $gameName = basename($val['file_path']);
        $gameColumns = '';

        switch($gameName){
            
            // GAME EMPATHY MEMORY
            case 'game_empathy_memory.js':

                $gameColumns .= 'CorrectAnswerTraining'.$separator;
                $gameColumns .= 'ParticipantChoiceTraining'.$separator;
                $gameColumns .= 'TrueFalseTraining'.$separator;
                $gameColumns .= 'ReactionTimeTraining'.$separator;

				for($j = 0; $j < 10; $j++){
					$jnd = ($j+1);
					$gameColumns .= 'SquarePositionTraining'.$jnd.$separator;
					$gameColumns .= 'ParticipantClickTraining'.$jnd.$separator;
				}
				
                for($i = 0; $i < $games[$gameName]['cicles']; $i++){
                    $ind = ($i+1);
                    $gameColumns .= 'CorrectAnswer'.$ind.$separator;
                    $gameColumns .= 'ParticipantChoice'.$ind.$separator;
                    $gameColumns .= 'TrueFalse'.$ind.$separator;
                    $gameColumns .= 'ReactionTime'.$ind.$separator;
					
					for($j = 0; $j < 10; $j++){
						$jnd = ($j+1);
						$gameColumns .= 'SquarePosition'.$ind.'-'.$jnd.$separator;
						$gameColumns .= 'ParticipantClick'.$ind.'-'.$jnd.$separator;
					}
                }
                break;

            // DOTPROB (GAME EMOTIONAL INTELLIGENCE)
            case 'game_emotional_intelligence.js':                

                for($i = 0; $i < 2; $i++){
                    $ind = ($i+1);
                    $gameColumns .= 'ConditionTraining'.$ind.$separator;
                    $gameColumns .= 'ArrowDirectionTraining'.$ind.$separator;
                    $gameColumns .= 'WhereArrEmoTraining'.$ind.$separator;
                    $gameColumns .= 'WhereArrSpatialTraining'.$ind.$separator;
                    $gameColumns .= 'IscorrectTraining'.$ind.$separator;
                    $gameColumns .= 'ReactionTimeTraining'.$ind.$separator;
                }

                for($i = 0; $i < $games[$gameName]['cicles']; $i++){
                    $ind = ($i+1);
                    $gameColumns .= 'Condition'.$ind.$separator;
                    $gameColumns .= 'ArrowDirection'.$ind.$separator;
                    $gameColumns .= 'WhereArrEmo'.$ind.$separator;
                    $gameColumns .= 'WhereArrSpatial'.$ind.$separator;
                    $gameColumns .= 'Iscorrect'.$ind.$separator;
                    $gameColumns .= 'ReactionTime'.$ind.$separator;
                }
                break;

            // IPT (GAME EMOTIONAL INTELLIGENCE)
            case 'emotional-intelligence-ipt.js':                

                for($i = 0; $i < $games[$gameName]['cicles']; $i++){
                    $ind = ($i+1);
                    $gameColumns .= 'CorrectChoice'.$ind.$separator;
                    $gameColumns .= 'ParticipantChoice'.$ind.$separator;
                    $gameColumns .= 'TrueFalse'.$ind.$separator;
                    $gameColumns .= 'ReactionTime'.$ind.$separator;
                }
                break;
                
            // DECISION MAKING M
            case 'decision-making-m.js':
			case 'decision-making-f.js':

                for($i = 0; $i < $games[$gameName]['cicles']; $i++){
                    $ind = ($i+1);
                    $gameColumns .= 'ParticipantChoice'.$ind.$separator;
                    $gameColumns .= 'ReactionTime'.$ind.$separator;
                }
                break;
			
            default:
                break;
        }
        
        $resultColumns = $gameColumns;
        
        return $resultColumns;        
    }

    /**
     * Draws action script content columns
     * @param array $games
     * @param array $qAnswer
     * @param string $separator      
     * @return string
     */
    private function _exportActionContentColumns($games, $qAnswer, $separator = ',') 
    {
        $gameName = basename($qAnswer[0]['file_path']);                            
        $actionsData = json_decode($qAnswer[0]['actions_data']);
        $gameColumns = '';        
        $resultColumns = '';
        $countGameColumns = 0;
        
        //CDebug::d($qAnswer);       
        //CDebug::d($actionsData);
        
        if(is_array($actionsData)){                                    
            switch($gameName){
                    
                // GAME EMPATHY MEMORY
				// ---------------------------------------
                case 'game_empathy_memory.js':
                    
                    // CorrectAnswer
                    //      Correct answer for that round - depending on its location (its randomized)
                    // ParticipantChoice
                    //      Which answer did participant choose
                    // TrueFalse     
                    //      Was participant answer correct (true) or not (false)
                    // ReactionTime
                    //      How much time passed until pressing the answer?
                    // SquarePosition
                    //      Position of the square in the matrix from 1-9, each number represent a position, like in a phone
                    // ParticipantClick
                    //       1 - if participant clicked on the square, 0 if he didn't
					
                    // Traning (cicle 1)
                    $rightAnswer = isset($actionsData[0]->actionLog[2]->rightAnswer[0]) ? $actionsData[0]->actionLog[2]->rightAnswer[0] : '';
                    $userAnswer = isset($actionsData[0]->actionLog[2]->userAnswer) ? $actionsData[0]->actionLog[2]->userAnswer : '';
                    $tsElapsed = isset($actionsData[0]->actionLog[2]->tsElapsed) ? $actionsData[0]->actionLog[2]->tsElapsed : '';
                    $squarePosition = isset($actionsData[3]->actionLog[3]->SquarePosition) ? $actionsData[3]->actionLog[3]->SquarePosition : '';
                    $clickPosition = isset($actionsData[3]->actionLog[3]->ClickPosition) ? $actionsData[3]->actionLog[3]->ClickPosition : '';
					
                    $gameColumns .= ($rightAnswer == 'answer1' ? 'LEFT' : 'RIGHT').$separator;
                    $gameColumns .= ($userAnswer == 'answer1' ? 'LEFT' : 'RIGHT').$separator;
                    $gameColumns .= (($rightAnswer == $userAnswer) ? 'TRUE' : 'FALSE').$separator;
                    $gameColumns .= $tsElapsed.$separator;
                  
					// SquarePosition
					// ParticipantClick
					for($i=0; $i<10; $i++){
						$gameColumns .= $squarePosition.$separator;
						$gameColumns .= (($squarePosition == $clickPosition) ? '1' : '0').$separator;
					}
						
                    // Real Mode (8 cicles)
                    foreach($actionsData as $adKey => $acVal){
                        
                        // Skip traning mode
                        if($adKey < 1) continue;

                        if(isset($acVal->actionLog[2]->question)){
                            // CorrectAnswer
                            // ParticipantChoice
                            // TrueFalse
                            // ReactionTime
                            $rightAnswer = isset($acVal->actionLog[2]->rightAnswer[0]) ? $acVal->actionLog[2]->rightAnswer[0] : '';
                            $userAnswer = isset($acVal->actionLog[2]->userAnswer) ? $acVal->actionLog[2]->userAnswer : '';
                            $tsElapsed = isset($acVal->actionLog[2]->tsElapsed) ? $acVal->actionLog[2]->tsElapsed : '';
                            
                            $gameColumns .= ($rightAnswer == 'answer1' ? 'LEFT' : 'RIGHT').$separator;
                            $gameColumns .= ($userAnswer == 'answer1' ? 'LEFT' : 'RIGHT').$separator;
                            $gameColumns .= (($rightAnswer == $userAnswer) ? 'TRUE' : 'FALSE').$separator;
                            $gameColumns .= $tsElapsed.$separator;
                        }

                        if(isset($acVal->actionLog[3]->SquarePosition)){						
							// SquarePosition
							// ParticipantClick
							for($i=0; $i<10; $i++){								
								$ind = $i+3;								
								$squarePosition = isset($acVal->actionLog[$ind]->SquarePosition) ? $acVal->actionLog[$ind]->SquarePosition : '';
								$clickPosition = isset($acVal->actionLog[$ind]->ClickPosition) ? $acVal->actionLog[$ind]->ClickPosition : '';								
								$gameColumns .= $squarePosition.$separator;
								$gameColumns .= (($squarePosition == $clickPosition) ? '1' : '0').$separator;
							}
						}						
                    }
					
                    $resultColumns .= $gameColumns;
                    
                    break;                
                

                // DOTPROB (GAME EMOTIONAL INTELLIGENCE)
				// ---------------------------------------
                case 'game_emotional_intelligence.js':
                        
                    // Condition:
                    //      What is the condition: 
                    //      NH=One picture Neutral, One picture Happy
                    //      NT=One Picture Neutral, One picture threat
                    //      TH=One picture threat, one picture happy
                    //      NN=both pictures neutral 
                    // ArrowDirection:
                    //      What is the direction of the arrow: Left or right                        
                    // Where is the arrow located in terms of emotional condition:
                    //      T-in the place of a threat picture
                    //      H-in the place of a happy picture
                    //      N-in the pklace of a neutral picture
                    // Iscorrect:
                    //      Was the participant's answer right: 1-yes, 2-no
                    // WhereArrSpatial:
                    //      Where was the arrow spatialy on the screen:
                    //      U=up
                    //      D=down
                    // ReactionTime:
                    //      Reaction time from the appearance of the arrow until pressing


                    $prop_zero = 0;
                    $prop_one = 1;
                

                    // Traning (cicle 1)
                    $fImage = isset($actionsData[1]->actionLog[0]->items->$prop_zero->src) ? $actionsData[1]->actionLog[0]->items->$prop_zero->src : '';
                    $sImage = isset($actionsData[1]->actionLog[0]->items->$prop_one->src) ? $actionsData[1]->actionLog[0]->items->$prop_one->src : '';
                    $firstImage = (!empty($fImage) ? substr($fImage, strlen($fImage)-5, $fImage-4) : ''); 
                    $secondImage = (!empty($sImage) ? substr($sImage, strlen($sImage)-5, $sImage-4) : ''); 
                    $isCorrect = (isset($actionsData[1]->actionLog[1]->buttonPressed) && ($actionsData[1]->actionLog[1]->buttonPressed == 'arrowRight' && $actionsData[1]->actionLog[1]->arrow == 'right') || ($actionsData[1]->actionLog[1]->buttonPressed == 'arrowLeft' && $actionsData[1]->actionLog[1]->arrow == 'left')) ? true : false;
                    $reactionTime = (isset($actionsData[1]->actionLog[1]->tsElapsed)) ? $actionsData[1]->actionLog[1]->tsElapsed : '';
                    
					$whereArrEmo = '';
                    if(isset($actionsData[1]->actionLog[1]->pos)){
                        if($actionsData[1]->actionLog[1]->pos == 1 && $firstImage == 't' || $actionsData[1]->actionLog[1]->pos == 2 && $secondImage == 't'){
                            $whereArrEmo = 'T';
                        }elseif($actionsData[1]->actionLog[1]->pos == 1 && $firstImage == 'h' || $actionsData[1]->actionLog[1]->pos == 2 && $secondImage == 'h'){
                            $whereArrEmo = 'H';
                        }elseif($actionsData[1]->actionLog[1]->pos == 1 && $firstImage == 'n' || $actionsData[1]->actionLog[1]->pos == 2 && $secondImage == 'n'){
                            $whereArrEmo = 'N';
                        }                                
                    }
					
                    $gameColumns .= strtoupper($firstImage.$secondImage).$separator;                    		/* Condition */
                    $gameColumns .= ($actionsData[1]->actionLog[1]->arrow == 'left' ? 'L' : 'R').$separator;  	/* ArrowDirection */
                    $gameColumns .= $whereArrEmo.$separator;                                            		/* WhereArrEmo */
                    $gameColumns .= ($actionsData[1]->actionLog[1]->pos == 1 ? 'U' : 'D').$separator;         	/* WhereArrSpatial */
                    $gameColumns .= ($isCorrect ? 1 : 2).$separator;                                    		/* Iscorrect */
                    $gameColumns .= $reactionTime.$separator;                                           		/* ReactionTime */
					

                    // Traning (cicle 2)
                    $fImage = isset($actionsData[1]->actionLog[2]->items->$prop_zero->src) ? $actionsData[1]->actionLog[2]->items->$prop_zero->src : '';
                    $sImage = isset($actionsData[1]->actionLog[2]->items->$prop_one->src) ? $actionsData[1]->actionLog[2]->items->$prop_one->src : '';
                    $firstImage = (!empty($fImage) ? substr($fImage, strlen($fImage)-5, $fImage-4) : ''); 
                    $secondImage = (!empty($sImage) ? substr($sImage, strlen($sImage)-5, $sImage-4) : ''); 
                    $isCorrect = (isset($actionsData[1]->actionLog[3]->buttonPressed) && ($actionsData[1]->actionLog[3]->buttonPressed == 'arrowRight' && $actionsData[1]->actionLog[3]->arrow == 'right') || ($actionsData[1]->actionLog[3]->buttonPressed == 'arrowLeft' && $actionsData[1]->actionLog[3]->arrow == 'left')) ? true : false;
                    $reactionTime = (isset($actionsData[1]->actionLog[3]->tsElapsed)) ? $actionsData[1]->actionLog[3]->tsElapsed : '';
                    
					$whereArrEmo = '';
                    if(isset($actionsData[1]->actionLog[3]->pos)){
                        if($actionsData[1]->actionLog[3]->pos == 1 && $firstImage == 't' || $actionsData[1]->actionLog[3]->pos == 2 && $secondImage == 't'){
                            $whereArrEmo = 'T';
                        }elseif($actionsData[1]->actionLog[3]->pos == 1 && $firstImage == 'h' || $actionsData[1]->actionLog[3]->pos == 2 && $secondImage == 'h'){
                            $whereArrEmo = 'H';
                        }elseif($actionsData[1]->actionLog[3]->pos == 1 && $firstImage == 'n' || $actionsData[1]->actionLog[3]->pos == 2 && $secondImage == 'n'){
                            $whereArrEmo = 'N';
                        }                                
                    }
					
                    $gameColumns .= strtoupper($firstImage.$secondImage).$separator;                    		/* Condition */
                    $gameColumns .= ($actionsData[1]->actionLog[3]->arrow == 'left' ? 'L' : 'R').$separator;  	/* ArrowDirection */
                    $gameColumns .= $whereArrEmo.$separator;                                            		/* WhereArrEmo */
                    $gameColumns .= ($actionsData[1]->actionLog[3]->pos == 1 ? 'U' : 'D').$separator;         	/* WhereArrSpatial */
                    $gameColumns .= ($isCorrect ? 1 : 2).$separator;                                    		/* Iscorrect */
                    $gameColumns .= $reactionTime.$separator;                                           		/* ReactionTime */
					
                    // Real Mode (80 cicles)
					if(is_array($actionsData[2]->actionLog)){						

						foreach($actionsData[2]->actionLog as $adKey => $acVal){
								
							$firstImage = isset($acVal->person1) ? substr($acVal->person1, strlen($acVal->person1)-5, $acVal->person1-4) : ''; 
							$secondImage = isset($acVal->person2) ? substr($acVal->person2, strlen($acVal->person2)-5, $acVal->person2-4) : '';
							$condition = strtoupper($firstImage.$secondImage);
							$arrowDirection = isset($acVal->arrow) ? ($acVal->arrow == 'left' ? 'L' : 'R') : '';
							$whereArrSpatial = isset($acVal->pos) ? ($acVal->pos == 1 ? 'U' : 'D') : '';
							$isCorrect = (isset($acVal->buttonPressed) && ($acVal->buttonPressed == 'arrowRight' && $acVal->arrow == 'right') || ($acVal->buttonPressed == 'arrowLeft' && $acVal->arrow == 'left')) ? 1 : 2;
							$reactionTime = isset($acVal->tsElapsed) ? $acVal->tsElapsed : '0';
							
							$whereArrEmo = '';
							if(isset($acVal->pos)){
								if($acVal->pos == 1 && $firstImage == 't' || $acVal->pos == 2 && $secondImage == 't'){
									$whereArrEmo = 'T';
								}elseif($acVal->pos == 1 && $firstImage == 'h' || $acVal->pos == 2 && $secondImage == 'h'){
									$whereArrEmo = 'H';
								}elseif($acVal->pos == 1 && $firstImage == 'n' || $acVal->pos == 2 && $secondImage == 'n'){
									$whereArrEmo = 'N';
								}                                
							}

							$gameColumns .= $condition.$separator;          /* Condition */
							$gameColumns .= $arrowDirection.$separator;     /* ArrowDirection */
							$gameColumns .= $whereArrEmo.$separator;        /* WhereArrEmo */
							$gameColumns .= $whereArrSpatial.$separator;    /* WhereArrSpatial */
							$gameColumns .= $isCorrect.$separator;          /* Iscorrect */
							$gameColumns .= $reactionTime.$separator;       /* ReactionTime */
						}
					}
                                            
                    $resultColumns .= $gameColumns;

                    break;                                


                // IPT (GAME EMOTIONAL INTELLIGENCE)
				// ---------------------------------------
                case 'emotional-intelligence-ipt.js':
                    
                    // CorrectAnswer
                    //      Correct answer for that round 
                    // ParticipantChoice
                    //      Which answer did participant choose
                    // TrueFalse     
                    //      Was participant answer correct (true) or not (false)
                    // ReactionTime
                    //      How much time passed until pressing the answer?
                    
                    // Real Mode (6 cicles)
                    foreach($actionsData as $adKey => $acVal){

                        $rightAnswer = isset($acVal->actionLog[0]->rightAnswer[0]) ? $acVal->actionLog[0]->rightAnswer[0] : '';
                        $userAnswer = isset($acVal->actionLog[0]->userAnswer) ? $acVal->actionLog[0]->userAnswer : '';
                        $tsElapsed = isset($acVal->actionLog[0]->tsElapsed) ? $acVal->actionLog[0]->tsElapsed : '';
                       
                        $gameColumns .= preg_replace('/[^0-9]/i', '', $rightAnswer).$separator;        /* CorrectAnswer */
                        $gameColumns .= preg_replace('/[^0-9]/i', '', $userAnswer).$separator;         /* ParticipantChoice */ 
                        $gameColumns .= ($rightAnswer == $userAnswer ? 'TRUE' : 'FALSE').$separator;   /* TrueFalse */
                        $gameColumns .= $tsElapsed.$separator;                                         /* ReactionTime */
                    }
                    
                    $resultColumns .= $gameColumns;
                    
                    break;                


                // DECISION MAKING M
				// ---------------------------------------
                case 'decision-making-m.js':
				case 'decision-making-f.js':
					
                    // ParticipantChoice
                    //      Which answer did participant choose
                    // ReactionTime
                    //      How much time passed until pressing the answer?
					
                    // Real Mode (17 cicles)
					$actionsData = $actionsData[0]->actionLog;					
                    foreach($actionsData as $adKey => $acVal){
						
						if(isset($acVal->offerSelected->offerText) && isset($acVal->tsElapsed)){
							$userAnswer = isset($acVal->offerSelected->offerText) ? $acVal->offerSelected->offerText : '';
							$tsElapsed = isset($acVal->tsElapsed) ? $acVal->tsElapsed : '';

							$gameColumns .= substr($userAnswer, 0, 1).$separator;         /* ParticipantChoice */ 
							$gameColumns .= $tsElapsed.$separator;                        /* ReactionTime */
						}
                    }
                    
                    $resultColumns .= $gameColumns;

                    break;
					
					
                default:
                    break;
            }           
		}
        
        return $resultColumns;
    }
	
    /**
     * Prepares array of total counters of questionnaires for each survey
     * @return array
     */
    private function _prepareQuestionnaireCounts()
    {
		$table = SurveyQuestionnaires::model()->getTableName(true);
        $result = SurveyQuestionnaires::model()->count(array(			
			'condition'	=> $table.'.is_active = 1',
			'select'	=> $table.'.survey_id',
			'count'		=> '*',
			'groupBy'	=> 'survey_id',
			'allRows'	=> true
		));
		
        $questionnaireCounters = array();
        foreach($result as $key => $model){
            $questionnaireCounters[$model['survey_id']] = $model['cnt'];
        }

        return $questionnaireCounters;
    }	

    /**
     * Prepares array of total counters of participants for each survey
     * @return array
     */
    private function _prepareParticipantCounts()
    {
		$table = SurveyParticipants::model()->getTableName(true);
        $result = SurveyParticipants::model()->count(array(
			'condition'	=> $table.'.is_active = 1',
			'select'	=> $table.'.survey_id',
			'count'		=> '*',
			'groupBy'	=> 'survey_id',
			'allRows'=>true
		));
		
        $participantCounters = array();
        foreach($result as $key => $model){
            $participantCounters[$model['survey_id']] = $model['cnt'];
        }

        return $participantCounters;
    }	

    /**
     * Prepares array of total counters of results for each survey
     * @return array
     */
    private function _prepareResultCounts()
    {
		$table = SurveyParticipants::model()->getTableName(true);
        $result = SurveyParticipants::model()->count(array(
			'condition'	=> $table.'.is_active = 1 AND '.$table.'.status = 2',
			'select'	=> $table.'.survey_id',
			'count'		=> '*',
			'groupBy'	=> 'survey_id',
			'allRows'=>true
		));
		
        $resultCounters = array();
        foreach($result as $key => $model){
            $resultCounters[$model['survey_id']] = $model['cnt'];
        }

        return $resultCounters;
    }
	
}
