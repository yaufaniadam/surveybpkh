<?php
/**
 * SurveyAnswers model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations                 
 * model (static)           _customFields
 * getAnwers
 * getRowName
 *
 */

class SurveyAnswers extends CActiveRecord
{
	
    /** @var string */    
    protected $_table = 'surveys_entity_answers';
    /** @var string */    
    protected $_tableSurveys = 'surveys_entities';
    /** @var string */    
    protected $_tableSurveyParticipants = 'surveys_entity_participants';       
    /** @var string */    
    protected $_tableQuestionnaires = 'surveys_entity_questionnaires';
    /** @var string */    
    protected $_tableQuestionnaireItems = 'surveys_entity_questionnaire_items';
    /** @var string */    
    protected $_tableQuestionnaireItemVariants = 'surveys_entity_questionnaire_item_variants';    
    /** @var string */    
    protected $_tableParticipants = 'surveys_participants';
    

        
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
     * Returns all records for answers
     * @param int $surveyId
     * @param string $type
     * @param string $limit
     */
    public function getAnwers($surveyId = 0, $type = 'columnar', $limit = '')
    {
        if($type == 'tabular'){
            $selectFields = '
                    a.questionnaire_id,
                    a.participant_id,
                    a.answer_text,
                    a.actions_data,
                    a.questionnaire_item_other,
                    a.questionnaire_item_id,
                    qi.file_path,

                    s.id as survey_id,
                    s.name as survey_name,
                    q.name as questionnaire_name,

                    IF(qi.question_text != "", qi.question_text, qi.question_text_f) as question_text,
                    qi.sort_order as question_number,
                    qi.question_type_id,
                    
                    IF(qiv.id IS NULL, "", CONCAT(qiv.content_value, IF(qiv.content_value != "" AND qiv.content != "", "|", ""), qiv.content)) as selected_variant,
                    qiv.sort_order as selected_variant_number,
                    IF(qiv.row_title !="", qiv.row_title, a.questionnaire_item_variant_id) as selected_variant_row_name,

                    CONCAT(p.identity_code, " (ID: ", a.participant_id, ")") as participant_id_code
                ';

            $orderBy = '
                a.participant_id ASC,
                q.sort_order ASC,
                qi.sort_order ASC,
                qiv.sort_order ASC';

        }else{
            $selectFields = '
                s.name as survey_name,
                a.participant_id,
                CONCAT(a.participant_id, " (", p.identity_code, ")") as participant_id_code,
                q.name as questionnaire_name,
                IF(qi.question_text != "", qi.question_text, qi.question_text_f) as question_text,
                IF(qiv.id IS NULL, a.questionnaire_item_variant_id, qiv.row_title) as row_title,
                IF(qiv.id IS NULL, "", CONCAT(qiv.content_value, IF(qiv.content_value != "" AND qiv.content != "", "|", ""), qiv.content)) as selected_variant,
                a.questionnaire_item_other,
                a.answer_text,
                a.actions_data,
                a.questionnaire_item_id';
            
            $orderBy = '
                a.participant_id ASC,
                q.sort_order ASC,
                qi.sort_order ASC,
                qiv.sort_order ASC';
        }
        
        $sql = 'SELECT
                '.$selectFields.'    
            FROM '.CConfig::get('db.prefix').$this->_table.' a
                INNER JOIN '.CConfig::get('db.prefix').$this->_tableSurveys.' s ON a.survey_id = s.id
                INNER JOIN '.CConfig::get('db.prefix').$this->_tableParticipants.' p ON a.participant_id = p.id
                INNER JOIN '.CConfig::get('db.prefix').$this->_tableQuestionnaires.' q ON a.questionnaire_id = q.id
                INNER JOIN '.CConfig::get('db.prefix').$this->_tableQuestionnaireItems.' qi ON a.questionnaire_item_id = qi.id
                LEFT OUTER JOIN '.CConfig::get('db.prefix').$this->_tableQuestionnaireItemVariants.' qiv ON a.questionnaire_item_variant_id = qiv.id
            WHERE
                a.survey_id = :survey_id AND
                qi.question_type_id	!= 17 
            ORDER BY
                '.$orderBy.'
			'.(!empty($limit) ? 'LIMIT '.$limit : '');
        
        $result = $this->_db->select($sql, array(':survey_id'=>$surveyId));
        return $result;
    }
    
    /**
     * Returns row name for answer types 11 and 13
     * @param int $questionnaireItemId
     * @param int $rowInd
     */
    public function getRowName($questionnaireItemId = 0, $rowInd = 0)
    {
        $result = '';
        $sql = 'SELECT DISTINCT row_title 
                FROM '.CConfig::get('db.prefix').$this->_tableQuestionnaireItemVariants.'
                WHERE entity_questionnaire_item_id = :entity_questionnaire_item_id
                ORDER BY id ASC';
                
        $result = $this->_db->select($sql, array(':entity_questionnaire_item_id'=>$questionnaireItemId));
        
        $index = 0;
        foreach($result as $key => $val){
            if(++$index == abs($rowInd)){
                $result = $val['row_title'];
                break;
            }
        }
        
        return $result;
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
                'fields'=>array('name'=>'survey_name')
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
        return array();
    }
    
}
    