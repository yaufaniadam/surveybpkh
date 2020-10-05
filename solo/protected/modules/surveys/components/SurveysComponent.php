<?php
/**
 * Surveys component for drawing surveys
 *
 * PUBLIC (static):         	PRIVATE (static):
 * -----------              	------------------
 * prepareTab               	_getRowsCount
 * drawQuestion             	_drawOtherOption
 * validateQuestion         	_getDrawingElements
 * clearParticipantResults      _drawTableRows
 * generateLoginByUrlLink       _drawDateTime
 * 
 */

class SurveysComponent extends CComponent
{
    const NL = "\n";

    /**
     * Prepares surveys module tabs
     * @param string $activeTab
     * @param int $surveyQuestId
     */
    public static function prepareTab($activeTab = 'surveys', $surveyQuestId = '')
    {
        return CWidget::create('CTabs', array(
            'tabsWrapper'=>array('tag'=>'div', 'class'=>'title'),
            'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs'),
            'contentWrapper'=>array(),
            'contentMessage'=>'',
            'tabs'=>array(
                A::t('surveys', 'Settings') => array('href'=>'modules/settings/code/surveys', 'id'=>'tabSettings', 'content'=>'', 'active'=>false, 'htmlOptions'=>array('class'=>'modules-settings-tab')),
                A::t('surveys', 'Surveys')  => array('href'=>'surveys/index', 'id'=>'tabSurveys', 'content'=>'', 'active'=> (in_array($activeTab, array('surveys', 'surveyquestionnaires', 'surveyparticipants', 'surveyquestionnaireitems')))),
                A::t('surveys', 'Participants') => array('href'=>'surveysParticipants/index', 'id'=>'tabSurveysParticipants', 'content'=>'', 'active'=>($activeTab == 'participants')),
                A::t('surveys', 'Questions Types') => array('href'=>'surveysQuestionTypes/index', 'id'=>'tabQuestionTypes', 'content'=>'', 'active'=>($activeTab == 'questionstypes')),
            ),
            'events'=>array(
                
            ),
            'return'=>true,
        ));
    }
    
    /**
     * Draws question by type
     * @param array $question
     */
    public static function drawQuestion($question = array())
    {
        $output = '';
        
        $questionType = isset($question['question_type_id']) ? $question['question_type_id'] : '';
        $questionnaireItemId = isset($question['id']) ? $question['id'] : '';
        $questionnaireFilePath = isset($question['file_path']) ? $question['file_path'] : '';
        $questionnaireDateFormat = isset($question['date_format']) ? $question['date_format'] : '';
        $questionnaireAlignmentType = isset($question['alignment_type']) ? $question['alignment_type'] : '';
        $validationType = isset($question['validation_type']) ? $question['validation_type'] : '';
        $validationCssClass = ($validationType == 'numeric' ? ' css-validation-numeric' : ' css-validation-text');

        // Prepare UL class
        if($questionnaireAlignmentType == 'v') $class = ' class="vertical"';
        elseif($questionnaireAlignmentType == 'h') $class = ' class="horizontal"';
        else $class = '';
        
        $content = isset($question['content']) ? $question['content'] : '';
        $helpText = isset($question['help_text']) ? $question['help_text'] : '';
        
        $variants = SurveyQuestionnaireItemVariants::model()->findAll(
            array('condition'=>'entity_questionnaire_item_id = :entity_questionnaire_item_id', 'order'=>'id ASC'),
            array(':entity_questionnaire_item_id'=>$questionnaireItemId)
        );
        if(!is_array($variants)) $variants = array();
        $cRequest = A::app()->getRequest();
        $otherText = A::t('surveys', 'other');
        $otherOption = '';
        $otherId = '';
        $postResult = $cRequest->getPost('item_'.$questionnaireItemId);
        
        if($questionType == '1' || $questionType == '2'){
            // 1 - Multiple Choice (only one answer)
            // 2 - Multiple Choice (only one answer with other option)
            $otherOption = $cRequest->getPost('item_'.$questionnaireItemId.'_other');            
            $output .= '<ul'.$class.'>'.self::NL;
            foreach($variants as $key => $val){
                if($questionType == '2' && preg_match('/#/i', $val['content'])){
                    $otherText = str_replace('#', '', $val['content']);
                    $otherId = $val['id'];
                    break;
                }
                $id = '_'.$questionnaireItemId.'_'.$val['id'];
                if($questionType == '2'){
                    $checked = (!$otherOption && $postResult == $val['id']) ? ' checked="checked"' : '';
                }else{
                    $checked = ($postResult == $val['id']) ? ' checked="checked"' : '';    
                }                
                $output .= '<li><input type="radio"'.$checked.' value="'.$val['id'].'" id="'.$id.'" name="item_'.$questionnaireItemId.'" class="css-radiobutton" onclick="normalOptionOnCheck(\'item_'.$questionnaireItemId.'\')" /><label for="'.$id.'" class="css-radiobutton-label">'.$val['content'].'</label></li>'.self::NL;
            }
            if($questionType == '2'){
                $output .= '<li>'.$otherText.': ';
                if(!empty($otherId)) $output .= '<input type="hidden" value="'.$otherId.'" name="item_'.$questionnaireItemId.'_other_id" />';
                $output .= '<input type="text" class="other_option" onclick="otherOptionOnCheck(\'item_'.$questionnaireItemId.'\')" value="'.CHtml::encode($otherOption).'" name="item_'.$questionnaireItemId.'_other" maxlength="255" /></li>'.self::NL;
            }
            $output .= '</ul>'.self::NL;
            $output .= '<div class="clear-both"></div>'.self::NL;
           
        }elseif($questionType == '3' || $questionType == '4'){
            // 3 - Multiple Choice (multiple answers)
            // 4 - Multiple Choice (multiple answers with other option)
            $otherOption = $cRequest->getPost('item_'.$questionnaireItemId.'_other');
            $output .= '<ul'.$class.'>'.self::NL;
            foreach($variants as $key => $val){
                if($questionType == '4' && preg_match('/#/i', $val['content'])){
                    $otherText = str_replace('#', '', $val['content']);
                    $otherId = $val['id'];
                    break;
                }
                $id = '_'.$questionnaireItemId.'_'.$val['id'];                
                $checked = (is_array($postResult) && in_array($val['id'], $postResult)) ? ' checked="checked"' : '';
                $output .= '<li><input type="checkbox"'.$checked.' value="'.$val['id'].'" id="'.$id.'" name="item_'.$questionnaireItemId.'[]" class="css-checkbox" /><label for="'.$id.'" class="css-checkbox-label">'.$val['content'].'</label></li>'.self::NL;
            }
            if($questionType == '4'){
                $output .= '<li>'.$otherText.': ';
                if(!empty($otherId)) $output .= '<input type="hidden" value="'.$otherId.'" name="item_'.$questionnaireItemId.'_other_id" />';
                $output .= '<input type="text" class="other_option" value="'.CHtml::encode($otherOption).'" name="item_'.$questionnaireItemId.'_other" maxlength="255" /></li>'.self::NL;
            }
            $output .= '</ul>'.self::NL;
            $output .= '<div class="clear-both"></div>'.self::NL;

        }elseif($questionType == '5'){
            // 5 - Dropdown (only one answer)
            $output .= '<select name="item_'.$questionnaireItemId.'" class="css-dropdown">'.self::NL;
            $output .= '<option value="">'.A::t('surveys', 'select').'</option>'.self::NL;
            foreach($variants as $key => $val){
                $selected = ($postResult == $val['id']) ? ' selected="selected"' : '';    
                $output .= '<option'.$selected.' value="'.$val['id'].'">'.$val['content'].'</option>'.self::NL;
            }
            $output .= '</select>'.self::NL;
            
        }elseif($questionType == '6'){
            // 6 - Single Textbox
            $output .= '<input type="text" name="item_'.$questionnaireItemId.'" class="css-textbox'.$validationCssClass.'" value="'.CHtml::encode($postResult).'" />'.self::NL;

        }elseif($questionType == '7'){
            // 7 - Multiple Textboxes
            $postData = $postResult;
            $output .= '<table class="tbl-multiple-textboxes tbl-answers">'.self::NL;
            foreach($variants as $key => $val){
                $id = '_'.$questionnaireItemId.'_'.$val['id'];
                $postValue = isset($postData[$val['id']]) ? $postData[$val['id']] : '';
                $output .= '<tr>'.self::NL;
                $output .= '<td>'.$val['content'].':</td>'.self::NL;
                $output .= '<td><input type="text" id="'.$id.'" name="item_'.$questionnaireItemId.'['.$val['id'].']" class="css-textbox'.$validationCssClass.'" value="'.CHtml::encode($postValue).'" /></td>'.self::NL;
                $output .= '</tr>'.self::NL;
            }
            $output .= '</table>'.self::NL;

        }elseif($questionType == '8'){
            // 8 - Comment/Essay Box
            $output .= '<textarea name="item_'.$questionnaireItemId.'" class="css-textarea">'.CHtml::encode($postResult).'</textarea>'.self::NL;

        }elseif($questionType == '9'){
            // 9 - Date/Time            
            $output .= self::_drawDateTime($questionnaireItemId, $questionnaireDateFormat);
           
        }elseif($questionType == '10' || $questionType == '11'){
            // 10 - Matrix Choice (only one answer per row)
            // 11 - Matrix Choice (only one answer per row with other option)	
            
            // Extract $table, $thead, $tr, $th, $td
            extract(self::_getDrawingElements($questionnaireAlignmentType));

            $output .= '<'.$table.' class="tbl-answers">'.self::NL;
            $output .= self::_drawTableHeader($questionnaireAlignmentType, $variants, $questionType, '11');
            $output .= self::_drawTableRows($questionnaireAlignmentType, $variants, $questionType, '11', $questionnaireItemId);            
            $output .= '</'.$table.'>'.self::NL;

        }elseif($questionType == '12' || $questionType == '13'){
            // 12 - Matrix Choice (multiple answers per row)	
            // 13 - Matrix Choice (multiple answers per row with other option)	

            // Extract $table, $thead, $tr, $th, $td
            extract(self::_getDrawingElements($questionnaireAlignmentType));

            $output .= '<'.$table.' class="tbl-answers">'.self::NL;
            $output .= self::_drawTableHeader($questionnaireAlignmentType, $variants, $questionType, '13');
            $output .= self::_drawTableRows($questionnaireAlignmentType, $variants, $questionType, '13', $questionnaireItemId);            
            $output .= '</'.$table.'>'.self::NL;

        }elseif($questionType == '14'){
            // 14 - Matrix Choice (Date/Time)	

            // Extract $table, $thead, $tr, $th, $td
            extract(self::_getDrawingElements($questionnaireAlignmentType));

            $output .= '<'.$table.' class="tbl-answers">'.self::NL;            
            $output .= self::_drawTableHeader($questionnaireAlignmentType, $variants, $questionType);            
            // Content rows
            $rowTitle = '';
            $rowName = 0;
            $otherOptionFound = false;
            $output .= '<'.$tr.'>'.self::NL;
            foreach($variants as $key => $val){
                if($rowTitle != $val['row_title']){
                    if($rowTitle != ''){
                        $otherOptionFound = false;
                        $output .= '</'.$tr.'><'.$tr.'>'.self::NL;
                    }
                    $output .= '<'.$td.'>'.$val['row_title'].'</'.$td.'>'.self::NL;
                    $rowName++;
                }
                $id = $val['id'];
                $postResult = $cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rowName);
                $checked = (is_array($postResult) && in_array($val['id'], $postResult)) ? ' checked="checked"' : '';
                $output .= '<'.$td.'>';
                $output .= self::_drawDateTime($questionnaireItemId, $questionnaireDateFormat, '_row_'.$rowName);
                $output .= '</'.$td.'>'.self::NL;
                $rowTitle = $val['row_title'];
            }
            $output .= '</'.$tr.'>'.self::NL;
            $output .= '</'.$table.'>'.self::NL;

            
        }elseif($questionType == '15'){
            // 15 - Rating Scale
            
            // Extract $table, $thead, $tr, $th, $td
            extract(self::_getDrawingElements($questionnaireAlignmentType));
            
            $output .= '<'.$table.' class="tbl-answers">'.self::NL;            
            $output .= self::_drawTableHeader($questionnaireAlignmentType, $variants, $questionType);            
            $output .= self::_drawTableRows($questionnaireAlignmentType, $variants, $questionType, '', $questionnaireItemId);            
            $output .= '</'.$table.'>'.self::NL;           
            
        }elseif($questionType == '16'){
            // 16 - Ranking

            $output .= '<table class="tbl-answers">'.self::NL;
            $rowTitle = '';
            $rowName = 0;
            foreach($variants as $key => $val){                
                if($rowTitle == '' || $rowTitle !== $val['row_title']){
                    $rowName++;
                    if($rowTitle !== ''){
                        $output .= '</select>'.self::NL;
                        $output .= '</td>'.self::NL;
                        $output .= '</tr>'.self::NL;
                    }
                    $output .= '<tr>'.self::NL;
                    $output .= '<td>'.$val['row_title'].'</td>'.self::NL;
                    $output .= '<td><select name="item_'.$questionnaireItemId.'_row_'.$rowName.'" class="css-dropdown">';
                    $output .= '<option value=""></option>'.self::NL;
                }                
                $postResult = $cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rowName);
                $output .= '<option'.(($postResult == $val['id']) ? ' selected="selected"' : '').' value="'.$val['id'].'">'.$val['content'].'</option>';
                $rowTitle = $val['row_title'];    
            }
            $output .= '</select>'.self::NL;
            $output .= '</td>'.self::NL;
            $output .= '</tr>'.self::NL;
            $output .= '</table>'.self::NL;
            

        }elseif($questionType == '17'){
            // 17 - Text/HTML Code
            $output .= '<div class="css-story">'.self::NL;
            $output .= $content;
            $output .= '</div>'.self::NL;

        }elseif($questionType == '18'){
            // 18 - Action Script        
            
            $output .= '<div class="wrapper-oneiro-game"><canvas id="gamespace" width="900" height="550"></canvas></div>'.self::NL;

            A::app()->getClientScript()->registerScript(
                'game',
                '$.getJSON("'.$questionnaireFilePath.'", function( data ) {
                    game = new NS_Oneiro.GameQuiz1(data)
                    game.init();
                    game.preloadAssets(game.run);
                });',
                3
            );                    

            A::app()->getClientScript()->registerScriptFile('assets/vendors/games/createjs-2013.12.12.min.js');
            A::app()->getClientScript()->registerScriptFile('assets/vendors/games/Game.js');
            A::app()->getClientScript()->registerScriptFile('assets/vendors/games/GameActions.js');            
        }
        
        if(in_array($questionType, array('1', '2', '10', '11', '15'))){
            A::app()->getClientScript()->registerScript(
                'multiple-choice-one',
                'function normalOptionOnCheck(el){                    
                    $("input[name="+el+"_other]").val("");
                }
                function otherOptionOnCheck(el){
                    $("input:radio[name="+el+"]").attr("checked", false);
                };',
                1
            );
        }
 
        return $output;       
    }
    
    
    /**
     * Validates question by type
     * @param array $params
     */
    public static function validateQuestion($params = array())
    {
        $output = array('error'=>0, 'error_message'=>'', 'error_item'=>'');
        $errorMessage = A::t('surveys', 'This is a required question! Please check one option from the listed below.');
        $error = false;
        
        $questionnaireItemId = isset($params['id']) ? $params['id'] : 0;
        $questionType = isset($params['question_type_id']) ? $params['question_type_id'] : ''; 
        $itemRequired = isset($params['is_required']) ? $params['is_required'] : 0;
        $validationType = isset($params['validation_type']) ? $params['validation_type'] : '';
        $dateFormat = isset($params['date_format']) ? $params['date_format'] : '';
        
        $cRequest = A::app()->getRequest();
        $postResult = $cRequest->getPost('item_'.$questionnaireItemId);

        if($itemRequired && in_array($questionType, array('1', '2', '3', '4'))){
            // 1 - Multiple Choice (only one answer)
            // 2 - Multiple Choice (only one answer with other option)
            // 3 - Multiple Choice (multiple answers)
            // 4 - Multiple Choice (multiple answers with other option)            
            if(in_array($questionType, array('1', '3')) &&
               !$cRequest->isPostExists('item_'.$questionnaireItemId)){
                $error = true;
            }
            if(in_array($questionType, array('2', '4')) &&
               !$cRequest->isPostExists('item_'.$questionnaireItemId) && 
               !$cRequest->getPost('item_'.$questionnaireItemId.'_other')){
                $error = true;
            }

        }elseif($itemRequired && $questionType == '5'){
            // 5 - Dropdown (only one answer)            
            if($postResult === ''){
                $error = true;
            }
            
        }elseif($itemRequired && $questionType == '6'){
            // 6 - Single Textbox
            if($itemRequired && $postResult === ''){
                $error = true;
            }elseif($postResult != '' && $validationType == 'numeric' && !CValidator::isNumeric($postResult, 1)){
                $errorMessage = A::t('surveys', 'The field must be a valid numeric value! Please re-enter.');
                $error = true;
            }                

        }elseif($questionType == '7'){
            // 7 - Multiple Textboxes (all textboxes must be checked)
            $error = false;
            $textboxes = $postResult;
            foreach($textboxes as $key => $val){
                if($itemRequired && $val === ''){
                    $errorMessage = A::t('surveys', 'This is a required question! Please please fill all the fields.');
                    $error = true;
                    break;
                }elseif($val != '' && $validationType == 'numeric' && !CValidator::isNumeric($val, 1)){
                    $errorMessage = A::t('surveys', 'The field must be a valid numeric value! Please re-enter.');
                    $error = true;
                    break;
                }                
            }

        }elseif($itemRequired && $questionType == '8'){
            // 8 - Comment/Essay Box
            if($postResult === ''){
                $error = true;
            }
            
        }elseif($questionType == '9'){
            // 9 - Date/Time
            $postResultDay = str_pad($cRequest->getPost('item_'.$questionnaireItemId.'_dd'), 2, '0', STR_PAD_LEFT);
            $postResultMonth = str_pad($cRequest->getPost('item_'.$questionnaireItemId.'_mm'), 2, '0', STR_PAD_LEFT);
            $postResultYear = $cRequest->getPost('item_'.$questionnaireItemId.'_yy');
            $postDate = $postResultYear.$postResultMonth.$postResultDay;
            
            $errorMessage = A::t('surveys', 'The field must be a valid date in format {format}! Please re-enter.', array('{format}'=>$dateFormat));
            if($itemRequired && strlen($postDate) < 8){
                $error = true;
            }elseif(trim($postDate, 0) !== ''){
                if(!CValidator::isInteger($postResultYear) || !CValidator::isInteger($postResultMonth) || !CValidator::isInteger($postResultDay)){
                    $error = true;
                }elseif(!CValidator::isDate($postResultYear.'-'.$postResultMonth.'-'.$postResultDay)){
                    $error = true;
                }                
            }

        }elseif($itemRequired && ($questionType == '10' || $questionType == '11')){
            // 10 - Matrix Choice (only one answer per row)
            // 11 - Matrix Choice (only one answer per row with other option)	

            // Find how many rows in this item
            $rows = self::_getRowsCount($questionnaireItemId);
            // Check row-by-row
            while($rows > 0){
                if($questionType == '10' &&
                   !$cRequest->isPostExists('item_'.$questionnaireItemId.'_row_'.$rows)){
                    $error = true;
                    break;
                }
                if($questionType == '11' &&
                   !$cRequest->isPostExists('item_'.$questionnaireItemId.'_row_'.$rows) &&
                   !$cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rows.'_other')){
                    $error = true;
                    break;
                }
                $rows--;
            }                

        }elseif($itemRequired && ($questionType == '12' || $questionType == '13')){
            // 12 - Matrix Choice (multiple answers per row)	
            // 13 - Matrix Choice (multiple answers per row with other option)	

            // Find how many rows in this item
            $rows = self::_getRowsCount($questionnaireItemId);
            // Check row-by-row
            while($rows > 0){
                if($questionType == '12' &&
                   !$cRequest->isPostExists('item_'.$questionnaireItemId.'_row_'.$rows)){
                    $error = true;
                    break;
                }
                if($questionType == '13' &&
                   !$cRequest->isPostExists('item_'.$questionnaireItemId.'_row_'.$rows) &&
                   !$cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rows.'_other')){
                    $error = true;
                    break;
                }                
                $rows--;
            }
            
        }elseif($questionType == '14'){
            // 14 - Matrix Choice (Date/Time)	

            // Find how many rows in this item
            $rows = self::_getRowsCount($questionnaireItemId);
            // Check row-by-row
            while($rows > 0){
                $postResultDay = $cRequest->getPost('item_'.$questionnaireItemId.'_dd_row_'.$rows);
                $postResultMonth = $cRequest->getPost('item_'.$questionnaireItemId.'_mm_row_'.$rows);
                $postResultYear = $cRequest->getPost('item_'.$questionnaireItemId.'_yy_row_'.$rows);
                $postDate = $postResultYear.$postResultMonth.$postResultDay;
                
                $errorMessage = A::t('surveys', 'The field must be a valid date in format {format}! Please re-enter.', array('{format}'=>$dateFormat));
                if($itemRequired && strlen($postDate) < 8){
                    $error = true;
                }elseif($postDate !== ''){
                    if(!CValidator::isInteger($postResultYear) || !CValidator::isInteger($postResultMonth) || !CValidator::isInteger($postResultDay)){
                        $error = true;
                    }elseif(!CValidator::isDate($postResultYear.'-'.$postResultMonth.'-'.$postResultDay)){
                        $error = true;
                    }                
                }
                $rows--;
            }

        }elseif($itemRequired && $questionType == '15'){
            // 15 - Rating Scale

            // Find how many rows in this item
            $rows = self::_getRowsCount($questionnaireItemId);
            // Check row-by-row
            while($rows > 0){
                if(!$cRequest->isPostExists('item_'.$questionnaireItemId.'_row_'.$rows) &&
                   !$cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rows.'_other')){
                    $error = true;
                    break;
                }                
                $rows--;
            }

            //if(!$cRequest->isPostExists('item_'.$questionnaireItemId)){
            //    $error = true;
            //}        

        }elseif($itemRequired && $questionType == '16'){
            // 16 - Ranking

            // Find how many rows in this item
            $rows = self::_getRowsCount($questionnaireItemId);
            // Check row-by-row
            while($rows > 0){
                if(!$cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rows)){
                    $error = true;
                    break;
                }
                $rows--;
            }                
        }elseif($itemRequired && $questionType == '17'){
            // 17 - Text/HTML Code            
        }elseif($itemRequired && $questionType == '18'){
            // 18 - Action Script            
        }

        if($error){
            $output['error'] = 1;
            $output['error_message'] = $errorMessage;
            $output['error_item'] = $questionnaireItemId;
        }

        return $output;        
    }
    
    /**
     * Validates question by gender
     * @param bool genderFormulation
     * @param char $loggedGender
     * @param string $questionTextMale
     * @param string $questionTextFemale
     */
    public static function validateQuestionByGender($genderFormulation = '', $loggedGender = '', $questionTextMale = '', $questionTextFemale = '')
    {
        $result = true;
        
        if($genderFormulation){
            // If this item is not for current gender - continue
            if($loggedGender == 'm' && empty($questionTextMale) || $loggedGender == 'f' && empty($questionTextFemale)){
                $result = false;
            }            
        } else {
            // Always return true, because gender is not relevent
        }
        
        return $result;
    }
    
    /**
     * Clear participant results
     * @param int $surveyId
     * @param int $id
     * @param bool $isDebug
     * @return void
     */
    public static function clearParticipantResults($surveyId = 0, $id = 0, $isDebug = false)
    {
        $alert = '';
        $alertType = '';

		$condition = 'survey_id = :survey_id AND participant_id = :participant_id';
		$conditionParams = array('i:survey_id'=>$surveyId, 'i:participant_id'=>$id);

		// ---------------------------------------
		// 2. Get all participant answers for the given survey
		// ---------------------------------------
		$answers = SurveyAnswers::model()->findAll($condition, $conditionParams);
		if($isDebug) echo '<br><br>2. [OK] Get all participant answers for the given survey';
		//CDebug::d($answers,1);
		if(count($answers) == 0){
            $alert = A::t('surveys', 'No results found for this participant!');
            $alertType = 'warning';            
        }else{
			// ---------------------------------------
			// 3. Delete all participant answers for the given survey
			// ---------------------------------------
			if(SurveyAnswers::model()->deleteAll($condition, $conditionParams)){
				if($isDebug) echo '<br><br>3. [OK] Delete all participant answers for the given survey';
	
				// ---------------------------------------
				// 4. Get all questionaries (in the given survey)
				// ---------------------------------------
				$questionaries = SurveyQuestionnaires::model()->findAll(
					array('condition'=>'survey_id = :surveyId AND '.CConfig::get('db.prefix').'surveys_entity_questionnaires.is_active = 1', 'order'=>'sort_order ASC'),
					array(':surveyId'=>$surveyId)
				);
				$questionariesIds = array();
				foreach($questionaries as $key => $val){
					$questionariesIds[] = $val['id'];
				}
				if($isDebug){
					echo '<br><br>4. [OK] Get all questionaries (in the given survey)';
					CDebug::d($questionariesIds);					
				}
		
				// ---------------------------------------
				// 5. Get all questionarie items (in the given survey/questionaries)
				// ---------------------------------------
				$questionarieItems = SurveyQuestionnaireItems::model()->findAll(
					array('condition'=>'entity_questionnaire_id IN('.implode(',', $questionariesIds).') AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.is_active = 1 AND '.CConfig::get('db.prefix').'surveys_entity_questionnaire_items.question_type_id != 17', 'order'=>' FIELD(entity_questionnaire_id, '.implode(',', $questionariesIds).'), sort_order ASC')
				);
				$questionarieItemsIds = array();
				foreach($questionarieItems as $key => $val){
					$questionarieItemsIds[] = $val['id'];
				}
				if($isDebug){
					echo '<br><br>5. [OK] Get all questionarie items (in the given survey/questionaries)';
					CDebug::d($questionarieItemsIds);
				}
				
				// ---------------------------------------
				// 6. Find all variants
				// ---------------------------------------
				$variants = SurveyQuestionnaireItemVariants::model()->findAll('entity_questionnaire_item_id IN('.implode(',', $questionarieItemsIds).')');
				if($isDebug){
					echo '<br><br>6. [OK] Find all variants';
					//CDebug::d($variants);
				}

				// ---------------------------------------
				// 7. Reduce votes in item variants
				// ---------------------------------------
				if($isDebug){
					echo '<br><br>7. [OK] Reduce votes in item variants';
					//CDebug::d($variants);
				}
				foreach($variants as $vKey => $variant){
					foreach($answers as $aKey => $answer){
						if($variant['id'] == $answer['questionnaire_item_variant_id']){
							if($variants[$vKey]['votes']){
								if($isDebug){
									echo '<br>Variant ID: '.$variant['id'].' | was: '.$variants[$vKey]['votes'].' done: '.($variants[$vKey]['votes']-1);
								}
								$variants[$vKey]['votes']--;
							}
							break;
						}
					}
				}
				
				// TODO: OPTIMIZE - do 1 query
				// ---------------------------------------
				// 8. Update votes count
				// ---------------------------------------
				if($isDebug){
					echo '<br><br>8. [OK] Update votes count';
					//CDebug::d($variants);
				}
				$updateSql = 'UPDATE '.CConfig::get('db.prefix').'surveys_entity_questionnaire_item_variants SET votes = CASE ';
				foreach($variants as $key => $variant){
					$updateSql .= 'WHEN id = '.$variant['id'].' THEN '.$variant['votes'].' ';
				}
				$updateSql .= 'END';				
				$updateResult = CDatabase::init()->customExec($updateSql);
				if($isDebug){
					echo '<br>Update records: '.CDebug::d($updateResult);
				}
				
				$alert = A::t('surveys', 'Survey results have been successfully cleaned!');
				$alertType = 'success';
			}else{
				if(APPHP_MODE == 'demo'){
					$alert = A::t('core', 'This operation is blocked in Demo Mode!');
					$alertType = 'warning';
				}else{
					$alert = A::t('surveys', 'An error occurred while cleaning results for this participant!');
					$alertType = 'error';
				}
			}
		}
		
        if(!empty($alert)){
			A::app()->getRequest()->setFlash('alert', $alert);
			A::app()->getRequest()->setFlash('alertType', $alertType);
        }        
	}
	
	/**
	 * Generates link for login by URL
	 * @param static $identityCode
	 * @param string $password
	 * @return string
	 */
	public static function generateLoginByUrlLink($identityCode, $password)
	{
		$value = 'entity_code='.$identityCode.'&pswd='.$password;
		return $result = CHash::encrypt($value, CConfig::get('installationKey'));
	}	

    /**
     * Validates question by gender
     * @param int $questionnaireItemId 
     */
    private static function _getRowsCount($questionnaireItemId = 0)
    {
        $titles = array();
        $variants = SurveyQuestionnaireItemVariants::model()->findAll('entity_questionnaire_item_id = :entity_questionnaire_item_id', array(':entity_questionnaire_item_id'=>$questionnaireItemId));
        if(is_array($variants)){
            foreach($variants as $key => $val){
                array_push($titles, $val['row_title']);
            }
        }
        return count(array_unique($titles));
    }    

    /**
     * Draws other option
     * @param int $questionnaireItemId
     * @param string $rowName
     * @param string $otherOption
     * @param string $otherOptionText
     * @param int $id
     * @param char $questionnaireAlignmentType
     */
    private static function _drawOtherOption($questionnaireItemId = 0, $rowName = '', $otherOption = '', $otherOptionText = '', $questionnaireAlignmentType = '', $id = '')
    {
        $output = '';
        if($questionnaireAlignmentType == 'v'){
            $td = 'li';
        }else{
            $td = 'td';
            $otherOptionText = '';
        }

        $otherOption = A::app()->getRequest()->getPost('item_'.$questionnaireItemId.'_row_'.$rowName.'_other');
        $output .= '<'.$td.'>';
        $output .= $otherOptionText;
        $output .= '<input type="text" class="other_option" onclick="otherOptionOnCheck(\'item_'.$questionnaireItemId.'_row_'.$rowName.'\')" value="'.CHtml::encode($otherOption).'" name="item_'.$questionnaireItemId.'_row_'.$rowName.'_other" maxlength="255" />';
        // Save this variant id to not loose it before insertion in database        
        $output .= '<input type="hidden" value="'.(int)$id.'" name="item_'.$questionnaireItemId.'_row_'.$rowName.'_other_variant" />';
        $output .= '</'.$td.'>'.self::NL;
        return $output;
    }
    
    /**
     * Returns array of drawing element
     * @param char $alignmentType
     * @return array()
     */
    private static function _getDrawingElements($alignmentType = '')
    {
        if($alignmentType == 'v'){
            $result = array('table'=>'div', 'thead'=>'div', 'tr'=>'ul', 'th'=>'li', 'td'=>'li');
        }else{
            $result = array('table'=>'table', 'thead'=>'thead', 'tr'=>'tr', 'th'=>'th', 'td'=>'td');
        }
        return $result;        
    }
    
    /**
     * Returns array of drawing element
     * @param char $alignmentType
     * @param array $variants
     * param string $questionType
     * param string $questionTypeOther
     * @return HTML
     */
    private static function _drawTableHeader($alignmentType = '', $variants = '', $questionType = '', $questionTypeOther = '')
    {
        // Extract $table, $thead, $tr, $th, $td
        extract(self::_getDrawingElements($alignmentType));
        $otherText = A::t('surveys', 'other');

        $output = '';
        $outputInner = '';
        if($alignmentType !== 'v'){
            $output .= '<'.$thead.'>'.self::NL;
            $output .= '<'.$tr.'>'.self::NL;
            $rowTitle = '';
            foreach($variants as $key => $val){
                if($questionType == $questionTypeOther && preg_match('/#/i', $val['content'])){
                    $otherText = str_replace('#', '', $val['content']);                    
                    break;
                }elseif($rowTitle == '' || $rowTitle == $val['row_title']){
                    $outputInner .= '<'.$th.'>';
                    if($val['content_value'] !== ''){
                        $outputInner .= $val['content_value'];
                        if($val['content'] !== '') $outputInner .= '. ';
                    }
                    $outputInner .= $val['content'];
                    $outputInner .= '</'.$th.'>'.self::NL;
                    $rowTitle = $val['row_title'];    
                }
            }
            if($questionType == $questionTypeOther){
                $outputInner .= '<'.$th.'>'.$otherText.'</'.$th.'>'.self::NL;
            }
            $output .= ($questionType == '15' && $rowTitle == '') ? '' : '<'.$th.'>&nbsp;</'.$th.'>'.self::NL;
            $output .= $outputInner;    
            
            $output .= '</'.$tr.'>'.self::NL;
            $output .= '</'.$thead.'>'.self::NL;
        }
        
        return $output;        
    }
 
    /**
     * Returns drawing element
     * @param char $alignmentType
     * @param array $variants
     * param string $questionType
     * param string $questionTypeOther
     * @param int $questionnaireItemId
     * @return HTML
     */
    private static function _drawTableRows($alignmentType = '', $variants = '', $questionType = '', $questionTypeOther = '', $questionnaireItemId = '')
    {
        // Extract $table, $thead, $tr, $th, $td
        extract(self::_getDrawingElements($alignmentType));
        $cRequest = A::app()->getRequest();

        // Content rows
        $rowTitle = '';
        $rowName = 0;
        $otherOption = '';
        $otherOptionFound = false;
        $otherOptionText = A::t('surveys', 'other');
        $output = '<'.$tr.'>'.self::NL;
        
        foreach($variants as $key => $val){
            if($rowTitle != $val['row_title']){
                if($rowTitle != ''){
                    if($questionType == $questionTypeOther && !$otherOptionFound){
                        $output .= self::_drawOtherOption($questionnaireItemId, $rowName, $otherOption, $otherOptionText, $alignmentType);
                    }
                    $otherOptionFound = false;
                    $output .= '</'.$tr.'><'.$tr.'>'.self::NL;
                }
                $output .= '<'.$td.'>'.$val['row_title'].'</'.$td.'>'.self::NL;
                $rowName++;
            }
            // For single row
            if($questionType == '15' && $rowTitle == '') $rowName = 1;

            $id = $val['id'];
            if(in_array($questionType, array('10', '11', '15'))){
                $checked = ($cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rowName) == $val['id']) ? ' checked="checked"' : '';
            }elseif(in_array($questionType, array('12', '13'))){
                $postResult = $cRequest->getPost('item_'.$questionnaireItemId.'_row_'.$rowName);
                $checked = (is_array($postResult) && in_array($val['id'], $postResult)) ? ' checked="checked"' : '';
            }
            
            if($questionType == $questionTypeOther && preg_match('/#/i', $val['content'])){
                $otherOptionText = str_replace('#', '', $val['content']);
                $output .= self::_drawOtherOption($questionnaireItemId, $rowName, $otherOption, $otherOptionText, $alignmentType, $val['id']);
                $otherOptionFound = true;
            }else{
                $output .= '<'.$td.'>';
                $contentText = ($val['content'] !== '') ? $val['content'] : '&nbsp;';
                if(in_array($questionType, array('10', '11', '15'))){
                    $output .= '<input type="radio"'.$checked.' class="tbl-css-radiobutton" onclick="normalOptionOnCheck(\'item_'.$questionnaireItemId.'_row_'.$rowName.'\')" value="'.$val['id'].'" name="item_'.$questionnaireItemId.'_row_'.$rowName.'" id="'.$id.'" />';
                    if($alignmentType == 'v'){
                        $output .= '<label class="tbl-css-radiobutton-label radiobutton-vertical" for="'.$id.'">'.$contentText.'</label>';
                    }else{
                        $output .= '<label for="'.$id.'" class="tbl-css-radiobutton-label">&nbsp;</label>';
                    }
                }elseif(in_array($questionType, array('12', '13'))){
                    $output .= '<input type="checkbox"'.$checked.' value="'.$val['id'].'" id="'.$id.'" name="item_'.$questionnaireItemId.'_row_'.$rowName.'[]" class="tbl-css-checkbox" />';
                    if($alignmentType == 'v'){
                        $output .= '<label class="tbl-css-checkbox-label checkbox-vertical" for="'.$id.'">'.$contentText.'</label>';
                    }else{
                        $output .= '<label for="'.$id.'" class="tbl-css-checkbox-label">&nbsp;</label>';
                    }
                }
                $output .= '</'.$td.'>'.self::NL;
            }                
            $rowTitle = $val['row_title'];
        }
        
        if($questionType == $questionTypeOther && !$otherOptionFound){
            $output .= self::_drawOtherOption($questionnaireItemId, $rowName, $otherOption, $otherOptionText, $alignmentType);
        }                        
        $output .= '</'.$tr.'>'.self::NL;
        return $output;
    }
    
    /**
     * Returns drawing element
     * @param int $questionnaireItemId
     * @param string $questionnaireDateFormat
     * @param string $rowName
     * @return HTML
     */
    private static function _drawDateTime($questionnaireItemId = 0, $questionnaireDateFormat = '', $rowName = '')
    {
        $output = '';
        $cRequest = A::app()->getRequest();
        $postResultDay = $cRequest->getPost('item_'.$questionnaireItemId.'_dd'.$rowName);
        $postResultMonth = $cRequest->getPost('item_'.$questionnaireItemId.'_mm'.$rowName);
        $postResultYear = $cRequest->getPost('item_'.$questionnaireItemId.'_yy'.$rowName);
        
        $output .= '<div class="css-date">'.self::NL;
        if($questionnaireDateFormat == 'DD/MM/YYYY'){
            $output .= '<input name="item_'.$questionnaireItemId.'_dd'.$rowName.'" type="text" class="css-date-day" value="'.CHtml::encode($postResultDay).'" maxlength="2" placeholder="'.A::t('surveys', 'day').'" /> / '.self::NL;
            $output .= '<input name="item_'.$questionnaireItemId.'_mm'.$rowName.'" type="text" class="css-date-month" value="'.CHtml::encode($postResultMonth).'" maxlength="2" placeholder="'.A::t('surveys', 'month').'" /> / '.self::NL;
        }else{
            $output .= '<input name="item_'.$questionnaireItemId.'_mm'.$rowName.'" type="text" class="css-date-month" value="'.CHtml::encode($postResultMonth).'" maxlength="2" placeholder="'.A::t('surveys', 'month').'" /> / '.self::NL;
            $output .= '<input name="item_'.$questionnaireItemId.'_dd'.$rowName.'" type="text" class="css-date-day" value="'.CHtml::encode($postResultDay).'" maxlength="2" placeholder="'.A::t('surveys', 'day').'" /> / '.self::NL;
        }
        $output .= '<input name="item_'.$questionnaireItemId.'_yy'.$rowName.'" type="text" class="css-date-year" value="'.CHtml::encode($postResultYear).'" maxlength="4" placeholder="'.A::t('surveys', 'year').'" />'.self::NL;
        $output .= '</div>'.self::NL;

       return $output;
    }
 
   
}