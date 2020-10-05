<?php
/**
 * SurveyQuestionnaires model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations                 
 * model (static)			_customFields	
 * copyBy                  	_afterDelete
 *
 */

class SurveyQuestionnaires extends CActiveRecord
{
	
    /** @var string */    
    protected $_table = 'surveys_entity_questionnaires';
    
    /** @var string */
    private $_tableSurveys = 'surveys_entities';
    /** @var string */    
    private $_tableSurveyQuestionnaires = 'surveys_entity_questionnaires';
    /** @var string */
    private $_tableSurveyQuestionnaireItems = 'surveys_entity_questionnaire_items';
    /** @var string */
    private $_tableSurveyQuestionnaireItemVariants = 'surveys_entity_questionnaire_item_variants';
    /** @var string */    
    private $_tableSurveyAnswers = 'surveys_entity_answers';
    

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
	 * This method copies questionnaire data
	 * @param int $questionnaireId
	 * @param string $name
	 */
	public function copyBy($questionnaireId = 0, $name = '')
	{
		
        // Copy survey questionnaire
        //-------------------
        $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires."
            (id, survey_id, category_title, name, description, questionnaire_key, start_message, start_message_f, finish_message, finish_message_f, items_per_page, sort_order, is_active)
            (
                SELECT NULL, survey_id, category_title, :name, description, questionnaire_key, start_message, start_message_f, finish_message, finish_message_f, items_per_page, sort_order, is_active
                FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires."
                WHERE id = :questionnaire_id
				ORDER BY id
            )";
        $this->_db->customExec($sql, array(':questionnaire_id'=>(int)$questionnaireId, ':name'=>$name));
		$newQuestionnaireId = $this->_db->lastId();
			
		// Copy questionnaire items
		//-------------------
		$sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems."
			(id, entity_questionnaire_id, question_text, question_text_f, help_text, question_type_id, date_format, file_path, validation_type, content, alignment_type, is_required, sort_order, is_active)
			(
				SELECT NULL, :eq_new_id, question_text, question_text_f, help_text, question_type_id, date_format, file_path, validation_type, content, alignment_type, is_required, sort_order, is_active
				FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems."
				WHERE entity_questionnaire_id = :eq_id
				ORDER BY id
			)";
		$this->_db->customExec($sql, array(':eq_new_id'=>$newQuestionnaireId, ':eq_id'=>(int)$questionnaireId));

        // Prepare list of questionnaires items IDs
        //-------------------
        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems." WHERE entity_questionnaire_id = :eq_id ORDER BY id";
        $result = $this->_db->select($sql, array(':eq_id'=>(int)$questionnaireId));
        $resultTotal = count($result);

        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems." WHERE entity_questionnaire_id = :eq_new_id ORDER BY id";
        $resultNew = $this->_db->select($sql, array(':eq_new_id'=>(int)$newQuestionnaireId));
        $resultNewTotal = count($resultNew);
		
        // Copy questionnaire item variants
		//-------------------
        if(is_array($result)){
            for($i=0; $i < $resultTotal; $i++){        
                $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItemVariants."
                    (id, entity_questionnaire_item_id, question_type_id, row_title, content, content_value, votes, sort_order)
                    (
                        SELECT NULL, :eq_new_id, question_type_id, row_title, content, content_value, 0, sort_order
                        FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItemVariants."
                        WHERE entity_questionnaire_item_id = :eq_id
						ORDER BY id
                    )";
                $this->_db->customExec($sql, array(':eq_new_id'=>(int)$resultNew[$i]['id'], ':eq_id'=>(int)$result[$i]['id']));
            }
        }
        
        return true;
    }

  	/**
     * Defines relations between different tables in database and current $_table
	 */
    protected function _relations()
    {
        return array(
            'survey_id' => array(
                self::BELONGS_TO,
                $this->_tableSurveys,
                'id',
                'joinType'=>self::INNER_JOIN,
                'condition'=>'',
                'fields'=>array(
                    'gender_formulation'=>'gender_formulation',
                    'name'=>'survey_name'
                )
            ),
        );
    }
    
    protected function _customFields()
    {
        return array();
    }    

	/**
	 * This method is invoked after deleting a record successfully
	 * @param string $pk
	 * You may override this method
	 */
	protected function _afterDelete($pk = '')
	{
        // Prepare list of questionnaire items IDs
        $result = $this->_db->select('SELECT id FROM '.CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems.' WHERE entity_questionnaire_id = :entity_questionnaire_id', array(':entity_questionnaire_id'=>(int)$pk));
        $inClauseQuestionnaireItem = '';
        if(is_array($result)) foreach($result as $key => $val) $inClauseQuestionnaireItem .= ','.$val['id'];

        // Delete records from questionnaire item variants table
		if(false === $this->_db->delete($this->_tableSurveyQuestionnaireItemVariants, 'entity_questionnaire_item_id IN (-1'.$inClauseQuestionnaireItem.')')){
			$this->_isError = true;
		}        

        // Delete related table 
        $this->_db->delete($this->_tableSurveyQuestionnaireItems, 'entity_questionnaire_id = :entity_questionnaire_id', array(':entity_questionnaire_id'=>$pk));

		// Delete records from entity answers table
		if(false === $this->_db->delete($this->_tableSurveyAnswers, 'questionnaire_id = :questionnaire_id', array(':questionnaire_id'=>(int)$pk))){
			$this->_isError = true;
		}
	}
}
