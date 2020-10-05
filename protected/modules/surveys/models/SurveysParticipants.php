<?php
/**
 * SurveysParticipants model 
 *
 * PUBLIC:                 	PROTECTED:                  PRIVATE:
 * ---------------         	---------------            	---------------
 * __construct             	_relations                 
 * model (static)			_customFields
 *
 */

class SurveysParticipants extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'surveys_participants';


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
        $fieldFirstName = ModulesSettings::model()->param('surveys', 'field_first_name');
        $fieldLastName = ModulesSettings::model()->param('surveys', 'field_last_name');
        if($fieldFirstName != 'no' && $fieldLastName != 'no'){
            return array('CONCAT(last_name, " ", first_name)' => 'full_name');
        }elseif($fieldFirstName == 'no' && $fieldLastName !== 'no'){
            return array('last_name' => 'full_name');
        }elseif($fieldFirstName !== 'no' && $fieldLastName == 'no'){
            return array('first_name' => 'full_name');
        }else{
            return array();
        }        
        
    }    
    
}
