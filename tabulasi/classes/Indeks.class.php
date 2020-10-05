<?php
             
require_once "DataObject.class.php";
             
class Indeks extends DataObject {
             
  protected $data = array(
    "id_ans" => "",
    "d30" => "",
    "d31" => "",
    "d32" => "",
    "d33" => "",
    "d34" => "",
    "d35" => "",
    "d36" => "",
    "d37" => "",
    "d38" => "",
    "d39" => "",
    "d40" => "",
    "d41" => "",
    "d42" => "",
    "d47" => "",
    "d51" => "",
    "d52" => "",
    "d53" => "",
    "d54" => "",


  );
             
  public static function getQuestions() {
    $conn = @parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM temporary";
             
    try {
      $st = $conn-> prepare( $sql );
;
      $st->execute();
	    $questions = array();
      foreach ( $st->fetchAll() as $row ) {
        $questions[] = new Indeks( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $questions, $row["totalRows"] );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
  
          
 

}             
?>