<?php
/**
 * SurveysQuestionTypes model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations                 
 * model (static)           _customFields
 *
 */

class SurveysQuestionTypes extends CActiveRecord
{
    
    /** @var string */    
    protected $_table = 'surveys_question_types';
    

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
        return array();
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
