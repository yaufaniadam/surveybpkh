<?php
/**
 * Surveys model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations
 * model (static)			_customFields
 * copyBy                  	_beforeSave
 *             				_afterSave
 *                         	_afterDelete
 *
 */

class Surveys extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'surveys_entities';
    
    /** @var string */    
    private $_tableSurveyQuestionnaires = 'surveys_entity_questionnaires';
    /** @var string */    
    private $_tableSurveyQuestionnaireItems = 'surveys_entity_questionnaire_items';
    /** @var string */    
    private $_tableSurveyQuestionnaireItemVariants = 'surveys_entity_questionnaire_item_variants';
    /** @var string */    
    private $_tableSurveyParticipants = 'surveys_entity_participants';
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
	 * This method copies survey data
	 * @param int $id
	 * @param string $name
	 */
	public function copyBy($id = 0, $name = '')
	{
        // Copy survey entity
        //-------------------
        if(ModulesSettings::model()->param('surveys', 'survey_code_type') == 'random'){
            $code = "'".CHash::getRandomString(10, array('type'=>'alphanumeric', 'case'=>'upper'))."'";
        }else{
            $code = "(SELECT MAX(id)+1 FROM ".CConfig::get('db.prefix').$this->_table.")";
        }
        $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_table."
            (id, code, name, description, welcome_message, welcome_message_f, complete_message, complete_message_f, created_at, expires_at, access_mode, gender_formulation, items_per_page, sort_order, is_active)
            (
                SELECT NULL, ".$code.", :name, description, welcome_message, welcome_message_f, complete_message, complete_message_f, created_at, expires_at, access_mode, gender_formulation, items_per_page, sort_order, is_active
                FROM ".CConfig::get('db.prefix').$this->_table."
                WHERE id = :id
				ORDER BY id
            )"; 
        $this->_db->customExec($sql, array(':name'=>$name, ':id'=>(int)$id));
        $newSurveyId = $this->_db->lastId();
        

        // Copy survey participants
        //-------------------
        $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyParticipants."
            (id, survey_id, participant_id, start_date, finish_date, status, is_active)
            (
                SELECT NULL, :survey_id, participant_id, start_date, finish_date, status, is_active
                FROM ".CConfig::get('db.prefix').$this->_tableSurveyParticipants."
                WHERE survey_id = :id
				ORDER BY id
            )";
        $this->_db->customExec($sql, array(':survey_id'=>(int)$newSurveyId, ':id'=>(int)$id));

        
        // Copy survey questionnaires
        //-------------------
        $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires."
            (id, survey_id, category_title, name, description, questionnaire_key, start_message, start_message_f, finish_message, finish_message_f, items_per_page, sort_order, is_active)
            (
                SELECT NULL, :survey_id, category_title, name, description, questionnaire_key, start_message, start_message_f, finish_message, finish_message_f, items_per_page, sort_order, is_active
                FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires."
                WHERE survey_id = :id
				ORDER BY id
            )"; 
        $this->_db->customExec($sql, array(':survey_id'=>(int)$newSurveyId, ':id'=>(int)$id));
        
       
        // Prepare list of questionnaires IDs
        //-------------------
        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires." WHERE survey_id = :survey_id ORDER BY id";
        $result = $this->_db->select($sql, array(':survey_id'=>(int)$id));
        $resultTotal = count($result);

        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires." WHERE survey_id = :survey_id ORDER BY id";
        $resultNew = $this->_db->select($sql, array(':survey_id'=>(int)$newSurveyId));
        $resultNewTotal = count($resultNew);

		$questionnaireIds = '-1';
		$questionnaireNewIds = '-1';

        if(is_array($result)){
            // Copy questionnaire items
            for($i=0; $i < $resultTotal; $i++){        
                $sql = "INSERT INTO ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems."
                    (id, entity_questionnaire_id, question_text, question_text_f, help_text, question_type_id, date_format, file_path, validation_type, content, alignment_type, is_required, sort_order, is_active)
                    (
                        SELECT NULL, :eq_new_id, question_text, question_text_f, help_text, question_type_id, date_format, file_path, validation_type, content, alignment_type, is_required, sort_order, is_active
                        FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems."
                        WHERE entity_questionnaire_id = :eq_id
						ORDER BY id
                    )";
                $this->_db->customExec($sql, array(':eq_new_id'=>(int)$resultNew[$i]['id'], ':eq_id'=>(int)$result[$i]['id']));

				$questionnaireIds .= ','.(int)$result[$i]['id'];
				$questionnaireNewIds .= ','.(int)$resultNew[$i]['id'];
            }
        }

        // Prepare list of questionnaires items IDs
        //-------------------
        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems." WHERE entity_questionnaire_id IN (".$questionnaireIds.") ORDER BY entity_questionnaire_id ASC, sort_order ASC";
        $result = $this->_db->select($sql, array());
        $resultTotal = count($result);
		
        $sql = "SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems." WHERE entity_questionnaire_id IN (".$questionnaireNewIds.") ORDER BY entity_questionnaire_id ASC, sort_order ASC";
        $resultNew = $this->_db->select($sql, array());
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
        return array();
    }
    
    /**
     * Used to define custom fields
     * This method should be overridden
     * Usage: 'CONCAT(first_name, " ", last_name)' => 'fullname'
     */
    protected function _customFields()
    {
        return array();
    }    

	/**
	 * This method is invoked before saving a record
	 * @param int $id
	 */
	protected function _beforeSave($id = 0)
	{
        if(CTime::isEmptyDate($this->created_at)) $this->created_at = null;
        if(CTime::isEmptyDate($this->expires_at)){
			$this->expires_at = null;
		}else{
            if($this->expires_at < LocalTime::currentDate()){
                $this->is_active = 0;
            }            
            if($this->expires_at <= $this->created_at){
                $this->_error = true;
                $this->_errorMessage = A::t('surveys', 'The date of survey expiration must be later than the date of creation!');
                return false;
            }
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
        if($this->isNewRecord()){
            if(ModulesSettings::model()->param('surveys', 'survey_code_type') == 'random'){
                $code = CHash::getRandomString(10, array('type'=>'alphanumeric', 'case'=>'upper'));
            }else{
                $code = $pk;
            }
            
            $this->_db->update($this->_table, array('code'=>$code), 'id = :id', array(':id'=>(int)$pk));
        }        
	}    

	/**
	 * This method is invoked after deleting a record successfully
	 * @param int $id
	 */
	protected function _afterDelete($id = 0)
	{
        $this->_isError = false;
        
        // Prepare list of questionnaires IDs
        $result = $this->_db->select("SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaires." WHERE survey_id = :survey_id", array(':survey_id'=>(int)$id));
        $inClauseQuestionnaire = '';
        if(is_array($result)) foreach($result as $key => $val) $inClauseQuestionnaire .= ','.$val['id'];

        // Prepare list of questionnaire items IDs
        $result = $this->_db->select("SELECT id FROM ".CConfig::get('db.prefix').$this->_tableSurveyQuestionnaireItems." WHERE entity_questionnaire_id IN (-1".$inClauseQuestionnaire.")");
        $inClauseQuestionnaireItem = '';
        if(is_array($result)) foreach($result as $key => $val) $inClauseQuestionnaireItem .= ','.$val['id'];

        // Delete records from questionnaire item variants table
        if(false === $this->_db->delete($this->_tableSurveyQuestionnaireItemVariants, 'entity_questionnaire_item_id IN (-1'.$inClauseQuestionnaireItem.')')){
            $this->_isError = true;
        }                    

        // Delete records from questionnaire items table
        if(false === $this->_db->delete($this->_tableSurveyQuestionnaireItems, 'entity_questionnaire_id IN (-1'.$inClauseQuestionnaire.')')){
            $this->_isError = true;
        }

        // Delete records from entity questionnaires table
        if(false === $this->_db->delete($this->_tableSurveyQuestionnaires, 'survey_id = :survey_id', array(':survey_id'=>(int)$id))){
            $this->_isError = true;
        }

        // Delete records from entity participants table
        if(false === $this->_db->delete($this->_tableSurveyParticipants, 'survey_id = :survey_id', array(':survey_id'=>(int)$id))){
            $this->_isError = true;
        }
        
        // Delete records from entity answers table
        if(false === $this->_db->delete($this->_tableSurveyAnswers, 'survey_id = :survey_id', array(':survey_id'=>(int)$id))){
            $this->_isError = true;
        }            
    }

}
