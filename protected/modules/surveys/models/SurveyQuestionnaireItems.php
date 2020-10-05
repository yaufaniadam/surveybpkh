<?php
/**
 * SurveyQuestionnaireItems model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations                 	_insertItemVariants
 * model (static)           _customFields              	_removeItemVariants
 *                         	_beforeSave
 *                         	_afterSave
 *                         	_afterDelete
 *
 */

class SurveyQuestionnaireItems extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'surveys_entity_questionnaire_items';
    
    /** @var string */    
    private $_tableSurveyQustionaries = 'surveys_entity_questionnaires';
    /** @var string */    
    private $_tableSurveyQuestionnaireItemVariants = 'surveys_entity_questionnaire_item_variants';
    /** @var string */    
    private $_tabelSurveyQuestionTypes = 'surveys_question_types';
    /** @var string */    
    private $_tableSurveyAnswers = 'surveys_entity_answers';
    
    /** @var array */    
    private $_multipleAnswers = array('1', '2', '3', '4', '5', '7', '10', '11', '12', '13', '14', '15', '16');


    /**
	 * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
	/**
	 * Returns the static model of the specified AR class
	 */
   	public static function model()
   	{
		return parent::model(__CLASS__);
   	}

  	/**
     * Defines relations between different tables in database and current $_table
	 */
    protected function _relations()
    {
		return array(
            'entity_questionnaire_id' => array(
                self::BELONGS_TO,
                $this->_tableSurveyQustionaries,
                'id',
                'joinType'=>self::INNER_JOIN,
                'condition'=>'',
                'fields'=>array('name'=>'questionnaire_name', 'questionnaire_key'=>'questionnaire_key'),
            ),
            'question_type_id' => array(
                self::HAS_ONE,
                $this->_tabelSurveyQuestionTypes,
                'id',
                'joinType'=>self::INNER_JOIN,
                'condition'=>'',
                'fields'=>array('name'=>'question_type'),
            ),
		);
    }
    
    /**
     * Used to define custom fields
     * This method should be overridden
     * Usage: 'CONCAT(last_name, " ", first_name)' => 'fullname'
     */
    protected function _customFields()
    {
        // "question_type_formatted" is used instead of "question_type"
        return array(
            'IF(question_text != "", question_text, question_text_f)'=>'question_text_formatted',
            'CASE WHEN question_text != "" AND question_text_f != "" THEN "m/f" WHEN question_text != "" AND question_text_f = "" THEN "m" WHEN question_text = "" AND question_text_f != "" THEN "f" ELSE "" END '=>'question_formulation_formatted',
            'REPLACE('.CConfig::get('db.prefix').$this->_tabelSurveyQuestionTypes.'.name, "(", "<br>(")'=>'question_type_formatted'
        );
    }
    
	/**
	 * This method is invoked before saving a record (after validation, if any)
	 * You may override this method
	 * @param string $pk
	 * @return boolean
	 */
	protected function _beforeSave($pk = '')
	{
        // $pk - key used for saving operation
        $cRequest = A::app()->getRequest();
        $questionTypeId = $cRequest->getPost('question_type_id');
        $variantText = $cRequest->getPost('variant_text');
        
        
        // Date/Time or Matrix Choice (Date/Time)
        if(!in_array($questionTypeId, array('9', '14'))){
            $this->_columns['date_format'] = '';
        }
        // Single Textbox or Multiple Textboxes
        if(!in_array($questionTypeId, array('6', '7'))){
            $this->_columns['validation_type'] = '';
        }        
        
        if(in_array($questionTypeId, $this->_multipleAnswers) && empty($variantText)){
			$this->_error = true;
            $this->_errorMessage = A::t('surveys', 'The field multiple answers cannot be empty for this type of question!');
            return false;
        }

		return true;
	}

	/**
	 * This method is invoked after saving a record successfully
	 * @param string $pk
	 * You may override this method
	 */
	protected function _afterSave($pk = '')
	{
        $cRequest = A::app()->getRequest();
        $questionTypeId = $cRequest->getPost('question_type_id');
        $questionTypeOriginal = $cRequest->getPost('question_type_original');
        $variantText = $cRequest->getPost('variant_text');
        $variantTextOriginal = $cRequest->getPost('variant_text_original');
        
        if($variantText == $variantTextOriginal && $questionTypeId == $questionTypeOriginal){
            return true;
        }
        
        if(in_array($questionTypeId, array('1', '2', '3', '4', '5', '7'))){
            // 1 - Multiple Choice (only one answer)
            // 2 - Multiple Choice (only one answer with other option)            
            // 3 - Multiple Choice (multiple answers)
            // 4 - Multiple Choice (multiple answers with other option)            
            // 5 - Dropdown (only one answer)
            // 7 - Multiple Textboxes
            $variantTextParts = explode("\r\n", $variantText);
            if(!$this->isNewRecord()){
                $this->_removeItemVariants($pk);
            }
            $this->_insertItemVariants($pk, $questionTypeId, $variantTextParts);
        }elseif($questionTypeId == '6' || $questionTypeId == '8'){
            // 6 - Single Textbox	
            // 8 - Comment/Essay Box	
            $this->_removeItemVariants($pk);
        }elseif(in_array($questionTypeId, array('10', '11', '12', '13', '14', '15', '16'))){
            // 10 - Matrix Choice (only one answer per row)
            // 11 - Matrix Choice (only one answer per row with other option)
            // 12 - Matrix Choice (multiple answers per row)
            // 13 - Matrix Choice (multiple answers per row with other option)
            // 14 - Matrix Choice (Date/Time)
            // 15 - Rating Scale            
            // 16 - Ranking
            $variantTextRows = explode('===', $variantText);
            if(!$this->isNewRecord()) $this->_removeItemVariants($pk);
            foreach($variantTextRows as $key => $val){
                $variantTextParts = explode("\r\n", $val);
                //if($this->isNewRecord()){}
                $this->_insertItemVariants($pk, $questionTypeId, $variantTextParts);
            }
        }
	}    
    
	/**
	 * This method is invoked after deleting a record successfully
	 * @param string $pk
	 * You may override this method
	 */
	protected function _afterDelete($pk = '')
	{
        // $pk - key used for deleting operation
		$this->_isError = false;

		// Delete records from entity questionnaires table
		if(false === $this->_db->delete($this->_tableSurveyQuestionnaireItemVariants, 'entity_questionnaire_item_id = :entity_questionnaire_item_id', array(':entity_questionnaire_item_id'=>(int)$pk))){
			$this->_isError = true;
		}

		// Delete records from entity answers table
		if(false === $this->_db->delete($this->_tableSurveyAnswers, 'questionnaire_item_id = :questionnaire_item_id', array(':questionnaire_item_id'=>(int)$pk))){
			$this->_isError = true;
		}
	}

    /**
     * Inserts item variants by given data
     * @param int $pk
     * @param int $questionTypeId
     * @param array $variantTextParts
     */
    private function _insertItemVariants($pk, $questionTypeId, $variantTextParts)
    {
        $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItemVariants."
                (id, entity_questionnaire_item_id, question_type_id, row_title, content, content_value, votes, sort_order) VALUES ";
        $count = 0;
        $rowTitle = '';
        foreach($variantTextParts as $key){
            if(empty($key) || $key === '===') continue;
            if(in_array($questionTypeId, array('10', '11', '12', '13', '14', '15', '16')) && preg_match('/\[/i', $key)){
                // 10 - Matrix Choice (only one answer per row)
                // 11 - Matrix Choice (only one answer per row with other option)
                // 12 - Matrix Choice (multiple answers per row)
                // 13 - Matrix Choice (multiple answers per row with other option)
                // 14 - Matrix Choice (Date/Time)
                // 15 - Rating Scale
                // 16 - Ranking
                $rowTitle = str_ireplace(array('[row','[', ']'), '', $key);
                continue;
            }
            
            // prepare content and content value
            $keyParts = explode('|', $key);
            if(count($keyParts) > 1){
                $contentValue = isset($keyParts[0]) ? $keyParts[0] : '';
                $content = isset($keyParts[1]) ? $keyParts[1] : $key;            
            }else{
                $contentValue = '';
                $content = isset($keyParts[0]) ? $keyParts[0] : '';
            }
            
            if($count++) $sql .= ',';
            $sql .= "(NULL, :eqi_id, :qt_id, '".CString::quote($rowTitle)."', '".CString::quote($content)."', '".$contentValue."', 0, ".(int)$count.")";
        }
        return $this->_db->customExec($sql, array(
            ':eqi_id'=>(int)$pk,
            ':qt_id'=>(int)$questionTypeId
        ));
    }

    /**
     * Inserts item variants by given data
     * @param int $id
     */
    private function _removeItemVariants($id)
    {
        return $this->_db->delete(
            $this->_tableSurveyQuestionnaireItemVariants,
            'entity_questionnaire_item_id = :entity_questionnaire_item_id',
            array(':entity_questionnaire_item_id'=>(int)$id)
        );

		// delete records from entity answers table
		if(false === $this->_db->delete($this->_tableSurveyAnswers, 'questionnaire_item_id = :questionnaire_item_id', array(':questionnaire_item_id'=>(int)$pk))){
			$this->_isError = true;
		}
    }
}
