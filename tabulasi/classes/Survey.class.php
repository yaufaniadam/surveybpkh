<?php    
/** 
  * Class Survey
  *
  * Class yang terkait dengan Survey
  * serta segala aktifitas pendukungnya 
  * berkaitan dengan beberapa tabel sekaligus
  *
  */           
  
require_once "DataObject.class.php";             
class Survey extends DataObject { 

  //objek-objek yang akan dipakai            
  protected $data = array(
    "id" => "",
    "survey_id" => "",
    "category_title" => "",
    "code" => "",
    "name" => "",
    "description" => "",
    "questionnaire_key" => "",
    "sort_order" => "",	
    "is_active" => "",	
	
    "entity_questionnaire_id" => "",
    "question_text" => "",
	"question_type_id" => "",
	
	"entity_questionnaire_item_id" => "",        
    "content" => "",
    "content_value" => "",
   
    "participant_id" => "",
	"start_date" => "",
	"finish_date" => "",	
	
    "first_name" => "",
    "last_name" => "",	 
		
	//answer
	"questionnaire_item_variant_id" => "",	
	"questionnaire_item_id" => "",	
	"questionnaire_id" => "",	
	"votes" => "",	
	
  ); 
  
  //get Surveys
  
  
   public static function getSurveys() {
    $conn = @parent::connect();
	$sql = "SELECT * FROM " . DB_PREFIX . "surveys_entities";
    try {
      $st = $conn-> prepare( $sql );    
     
      $st->execute();
	  $questionnaires = array();
      foreach ( $st->fetchAll() as $row ) {
        $questionnaires[] = new Survey( $row );
      }
      
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return  $questionnaires;
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  
  public static function getSurvey( $survey_id) {
    $conn = @parent::connect();
	$sql = "SELECT * FROM " . DB_PREFIX . "surveys_entities WHERE id=:survey_id";
    try {
      $st = $conn-> prepare( $sql );    
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
	  
      $st->execute();      
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new Survey( $row );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  //ambil kategori pertanyaan survey
  public static function getQuestionnaires( $survey_id) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_questionnaires sort_order WHERE survey_id =:survey_id";
    try {
      $st = $conn-> prepare( $sql );   
	  $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT ); 	  
	    
      $st->execute();
	  $questionnaires = array();
      foreach ( $st->fetchAll() as $row ) {
        $questionnaires[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $questionnaires, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  //ambil kategori pertanyaan survey
  public static function getQuestionnairy( $survey_id, $cats ) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_questionnaires sort_order WHERE survey_id =:survey_id AND category_title =:cats";
    try {
      $st = $conn-> prepare( $sql );   
	  $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT ); 	  
	  $st->bindValue( ":cats", $cats, PDO::PARAM_INT ); 	  
     
      $st->execute();
	  $questionnaires = array();
      foreach ( $st->fetchAll() as $row ) {
        $questionnaires[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $questionnaires, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  //ambil pertanyaan survey berdasarkan kategorinya
  public static function getQuestionnaire_items( $questionnaires_id ) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_questionnaire_items where entity_questionnaire_id=:questionnaires_id ORDER BY sort_order ASC";
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":questionnaires_id", $questionnaires_id, PDO::PARAM_INT ); 
     
      $st->execute();
	  $questionnaire_items = array();
      foreach ( $st->fetchAll() as $row ) {
        $questionnaire_items[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $questionnaire_items, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  
  //ambil pilihan jawaban dari setiap jenis soal multiple choice
  public static function getQuestionnaire_item_variants( $startRow, $numRows, $questionnaire_item_id ) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_questionnaire_item_variants where entity_questionnaire_item_id=:questionnaire_item_id ORDER BY sort_order ASC LIMIT :startRow, :numRows";
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
     
      $st->execute();
	  $questionnaire_item_variants = array();
      foreach ( $st->fetchAll() as $row ) {
        $questionnaire_item_variants[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $questionnaire_item_variants, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  
  // ambil data peserta yang mengikuti voting 
  public static function getSurveyParticipants( $survey_id ) {
    $conn = @parent::connect();
	$sql = "SELECT participant_id 
		FROM 		 
		" . DB_PREFIX . "surveys_entity_participants
		WHERE 
		survey_id =:survey_id AND start_date IS NOT NULL AND finish_date IS NOT NULL ORDER BY participant_id ASC

		";
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
      //$st->bindValue( ":questionnaire_id", $questionnaire_id, PDO::PARAM_INT );
     // $st->bindValue( ":month", $month, PDO::PARAM_INT );
     // $st->bindValue( ":year", $year, PDO::PARAM_INT );
     
      $st->execute();
	  $answers = array();
      foreach ( $st->fetchAll() as $row ) {
        $answers[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $answers, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 

  //hitung jumlah votes untuk setiap pilihan /variasi jawaban
  public static function countVotes( $questionnaire_item_variant_id ) {
    $conn = @parent::connect();
	$sql = "SELECT votes FROM " . DB_PREFIX . "surveys_entity_questionnaire_item_variants 
		WHERE		
		id =:questionnaire_item_variant_id
	";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":questionnaire_item_variant_id", $questionnaire_item_variant_id, PDO::PARAM_INT );     
      $st->execute();	  
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new Survey( $row );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  
 
  //hitung jumlah voters untuk setiap soal
  public static function countVotesByQuestion( $survey_id, $questionnaire_id, $questionnaire_item_id ) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_answers 
		WHERE		
		survey_id =:survey_id AND 
		questionnaire_id =:questionnaire_id AND 
		questionnaire_item_id =:questionnaire_item_id 
    
	";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_id", $questionnaire_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
     // $st->bindValue( ":month", $month, PDO::PARAM_INT );
     // $st->bindValue( ":year", $year, PDO::PARAM_INT );
     
      $st->execute();	  
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }  


   //hitung jumlah voters untuk setiap soal by Embrakasi
  public static function countVotesByQuestionEmb( $survey_id, $questionnaire_id, $questionnaire_item_id,$emb ) {
    $conn = @parent::connect();
  $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_entity_answers 
    WHERE   
    survey_id =:survey_id AND 
    questionnaire_id =:questionnaire_id AND 
    questionnaire_item_id =:questionnaire_item_id  AND
    questionnaire_item_id =:questionnaire_item_id
  ";
  
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_id", $questionnaire_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
     // $st->bindValue( ":month", $month, PDO::PARAM_INT );
     // $st->bindValue( ":year", $year, PDO::PARAM_INT );
     
      $st->execute();   
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }  
  
  //hitung jumlah voters yg mengisi untuk soal non multiple choice
  public static function countVotersByQuestionText( $survey_id, $questionnaire_id, $questionnaire_item_id ) {
    $conn = @parent::connect();
	$sql = "SELECT SQL_CALC_FOUND_ROWS answer_text FROM " . DB_PREFIX . "surveys_entity_answers 
		WHERE		
		survey_id =:survey_id AND 
		questionnaire_id =:questionnaire_id AND 
		questionnaire_item_id =:questionnaire_item_id AND
		answer_text IS NOT NULL AND
		answer_text <> '' 
	";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_id", $questionnaire_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
     
      $st->execute();	  
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }
  
  //Ambil nilai dari pertanyaan yg dipilih oleh voter
  public static function getAnsweredVoterByQuestion( $survey_id, $questionnaire_item_id, $participant_id ) {
    $conn = @parent::connect();
	$sql = "SELECT questionnaire_item_variant_id FROM " . DB_PREFIX . "surveys_entity_answers 
		WHERE		
		survey_id =:survey_id AND 
		questionnaire_item_id =:questionnaire_item_id AND 
		participant_id =:participant_id
	";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
      $st->bindValue( ":participant_id", $participant_id, PDO::PARAM_INT );
      
      $st->execute();	  
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new Survey( $row );	  

    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  	
  }
  // ambil nilai setiap pilihan 
  public static function getContent_value( $item_variant ) {
	$conn = @parent::connect();
	$sql = "SELECT content_value FROM " . DB_PREFIX . "surveys_entity_questionnaire_item_variants 
		WHERE id =:item_variant";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":item_variant", $item_variant, PDO::PARAM_INT );
      
      $st->execute();	  
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new Survey( $row );	  
	  
	} catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  	
  }
 public static function getBobot( $questionnaire_item_id ) {	
	// query 3 ambil bobot tiap soal
	$conn = @parent::connect();
	$sql = "SELECT content FROM " . DB_PREFIX . "surveys_entity_questionnaire_items 
		WHERE id =:questionnaire_item_id";
	
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":questionnaire_item_id", $questionnaire_item_id, PDO::PARAM_INT );
      
      $st->execute();	  
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new Survey( $row );	  
	  
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }

  //ambil total soal yg dikerjakan
  public static function getQuestionaresByUser( $user_id) { 
  // query 3 ambil bobot tiap soal
  $conn = @parent::connect();
  $sql = "SELECT questionnaire_item_id FROM " . DB_PREFIX . "surveys_entity_answers
    WHERE participant_id =:user_id";
  
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
      
      $st->execute();   
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];    
    
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }
  
  public static function hitungBobot($survey_id,$questionnaire_item_id,$participant_id) {	
  
	$item_variants = self::getAnsweredVoterByQuestion( $survey_id, $questionnaire_item_id, $participant_id );
	
	if ( $item_variants ) {
		$item_var = $item_variants->getValueEncoded('questionnaire_item_variant_id');
		if ( $item_var > 0 ) {
			//nilai tiap pilihan/variant
			$content_value = self::getContent_value( $item_var );	
			$content_value = $content_value->getValueEncoded('content_value');
			
			$bobot = self::getBobot( $questionnaire_item_id );
			$bobot = $bobot->getValueEncoded('content');
			
			//hasil setelah dikalikan bobot
			$hitung_bobot = $content_value * ($bobot/100);
		} else {
			return $hitung_bobot = 0;
		}
	return $hitung_bobot;
	} else {
		return $hitung_bobot =0;
	}
  } 
  public static function hitungRerata($survey_id,$questionnaire_item_id,$participant_id) {	
  
	$item_variants = self::getAnsweredVoterByQuestion( $survey_id, $questionnaire_item_id, $participant_id );	
	$item_variant = $item_variants->getValueEncoded('questionnaire_item_variant_id');
	
	//nilai tiap pilihan/variant
	$content_value = self::getContent_value( $item_variant );	
	$content_value = $content_value->getValueEncoded('content_value');
	
	$bobot = self::getBobot( $questionnaire_item_id );
	$bobot = $bobot->getValueEncoded('content');
	
	//hasil setelah dikalikan bobot
	$hitung_bobot = $content_value * ($bobot/100);
	
	return $hitung_bobot;
  } 
  
  // ambil data peserta yang mengikuti voting tapi NULL
  public static function getSurveyParticipantsNull( $survey_id ) {
    $conn = @parent::connect();
	$sql = "SELECT participant_id 
		FROM 		 
		" . DB_PREFIX . "surveys_entity_participants
		WHERE 
		survey_id =:survey_id AND start_date IS NULL AND finish_date IS NULL ORDER BY participant_id ASC

		";
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":survey_id", $survey_id, PDO::PARAM_INT );
     
      $st->execute();
	  $answers = array();
      foreach ( $st->fetchAll() as $row ) {
        $answers[] = new Survey( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $answers, $row["totalRows"] );
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  } 
  //delete particiants entitiy
   public function delete_participantentity($participant_id) {
  
    $conn = @parent::connect();
    $sql = "DELETE FROM " . DB_PREFIX . "surveys_entitiy_participants where participant_id =:participant_id";          
    try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":participant_id", $this->data["participant_id"], PDO::PARAM_INT );
		$st->execute();
      parent::disconnect( $conn );
	  
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  //delete particiants 
   public function delete_participants($participant_id) {
  
    $conn = @parent::connect();
    $sql = "DELETE FROM " . DB_PREFIX . "surveys_participants where id =:participant_id";          
    try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":participant_id", $this->data["participant_id"], PDO::PARAM_INT );
		$st->execute();
      parent::disconnect( $conn );
	  
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  //delete particiants answer
   public function delete_participants_answer($participant_id) {
  
    $conn = @parent::connect();
    $sql = "DELETE FROM " . DB_PREFIX . "entity_answers where participant_id =:participant_id";          
    try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":participant_id", $this->data["participant_id"], PDO::PARAM_INT );
		$st->execute();
      parent::disconnect( $conn );
	  
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  //semua participant yg komplit jawabannya
  public function ambilSemuaParticipants() {
    $conn = @parent::connect();
  $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . DB_PREFIX . "surveys_participants ORDER BY id ASC";
    try {
      $st = $conn-> prepare( $sql );      
      
      $st->execute();
      $participants = array();
      foreach ( $st->fetchAll() as $row ) {
        $participants[] = new Survey( $row );
      }
      //$st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return  $participants;
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }

  public function cekSurveySelesai($user_id) {
  // query 3 ambil bobot tiap soal
  $conn = @parent::connect();
  $sql = "SELECT questionnaire_item_id FROM " . DB_PREFIX . "surveys_entity_answers
    WHERE participant_id =:user_id";
  
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
      
      $st->execute();   
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];    
    
    } catch ( PDOException $e ) {
      @parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }  
  }


}