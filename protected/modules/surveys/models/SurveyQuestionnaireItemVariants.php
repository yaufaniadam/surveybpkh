<?php
/**
 * SurveyQuestionnaireItemVariants model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct
 * model (static)           
 * updateVotesCounter
 *
 */

class SurveyQuestionnaireItemVariants extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'surveys_entity_questionnaire_item_variants';
    /** @var string */    
    private $_tableSurveysQustionaries = 'surveys_entity_questionnaires';


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
     * Update votes counter
     * @param int $id
     * @return bool
     */
    public function updateVotesCounter($id)
    {
        $sql = "UPDATE `".CConfig::get('db.prefix').$this->_table."` SET votes = votes + 1 WHERE id = :id";
        return $this->_db->customExec($sql, array(':id'=>(int)$id));       
    }
       
}