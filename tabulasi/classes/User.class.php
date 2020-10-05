<?php
             
require_once "DataObject.class.php";
             
class User extends DataObject {
             
  protected $data = array(
    "id" => "",
    "username" => "",
    "password" => "",
	"salt" => "",
	"email" => "",
    "first_name" => "",  
	"role" => ""
  );
             
  public static function getUsers( $startRow, $numRows, $where, $order ) {
    $conn = @parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users $where ORDER BY 
$order LIMIT :startRow, :numRows";
             
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":startRow", $startRow, PDO::PARAM_INT );
      $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
      $st->execute();
	  $users = array();
      foreach ( $st->fetchAll() as $row ) {
        $users[] = new User( $row );
      }
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return array( $users, $row["totalRows"] );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
    

  public static function getUser( $user_id ) {
    $conn = @@parent::connect();
    $sql = "SELECT * FROM users WHERE user_id =:user_id";
             
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
      $st->execute();
      $row = $st->fetch();
      @parent::disconnect( $conn );
      if ( $row ) return new User( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
  
  public static function getUserByUsername( $username ) {
    $conn = @@parent::connect();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE username =:username";
             
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":username", $username, PDO::PARAM_INT );
      $st->execute();
	  
      $st = $conn->query( "SELECT found_rows() AS totalRows" );
      $row = $st->fetch();
      @parent::disconnect( $conn );
      return $row["totalRows"];	 
	  
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
 
  public static function getUserByRole( $role ) {
    $conn = @parent::connect();
    $sql = "SELECT * FROM users WHERE role =:role order by first_name ASC";            
             
     try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":role", $role, PDO::PARAM_INT );    
      $st->execute();
	  $usr = array();
      foreach ( $st->fetchAll() as $row ) {
        $usr[] = new User( $row );
      }      
      parent::disconnect( $conn );
      return array( $usr );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
  public static function getUserByCourses( $course_id ) {
    $conn = @parent::connect();
    $sql = "SELECT * FROM users WHERE course_id =:course_id order by user_id ASC";            
             
     try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":course_id", $course_id, PDO::PARAM_INT );    
      $st->execute();
	  $usr = array();
      foreach ( $st->fetchAll() as $row ) {
        $usr[] = new User( $row );
      }      
      parent::disconnect( $conn );
      return array( $usr );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e-> getMessage() );
    }
  }
          
  public static function getByEmail( $email) {
    $conn = @parent::connect();
    $sql = "SELECT * FROM users WHERE email =:email";
          
    try {  
	$st = $conn->prepare( $sql );
      $st->bindValue( ":email", $email, PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new User( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
             
  
  public function insert() {
  
  global $lastId;
  
    $conn = @parent::connect();
    $sql = "INSERT INTO users (
				username,
				password,
				email,
				first_name,				
				role,
				course_id
            ) VALUES (
				:username,
				password(:password),
				:email,
				:first_name,				
				:role,
				:course_id
            )";
          
    try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
		$st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR ); 
		$st->bindValue( ":email", $this->data["email"], PDO::PARAM_STR ); 
		$st->bindValue( ":first_name", $this->data["first_name"], PDO::PARAM_STR ); 		
		$st->bindValue( ":role", $this->data["role"], PDO::PARAM_STR ); 
		$st->bindValue( ":course_id", $this->data["course_id"], PDO::PARAM_INT ); 
		$st->execute();
		
		
		
      parent::disconnect( $conn );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  public function update() {
  
    $conn = @parent::connect();
    $sql = "UPDATE users SET 
		username=:username, first_name=:first_name, course_id=:course_id, role=:role WHERE user_id=:user_id";          
    try {
		$st = $conn->prepare( $sql );
		$st->bindValue( ":user_id", $this->data["user_id"], PDO::PARAM_INT );
		$st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
		$st->bindValue( ":first_name", $this->data["first_name"], PDO::PARAM_STR );
		$st->bindValue( ":course_id", $this->data["course_id"], PDO::PARAM_INT );
		$st->bindValue( ":role", $this->data["role"], PDO::PARAM_INT );
		$st->execute();
      parent::disconnect( $conn );
	  
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  }
  public function authenticate() {
    $conn = @parent::connect();
    $sql = "SELECT * FROM bk_admins WHERE username = :username AND password = md5(:password)";
          
    try {
      $st = $conn-> prepare( $sql );
      $st->bindValue( ":username", $this->data["username"], PDO::PARAM_STR );
      $st->bindValue( ":password", $this->data["password"], PDO::PARAM_STR );
      $st->execute();
      $row = $st->fetch();
      parent::disconnect( $conn );
      if ( $row ) return new User( $row );
    } catch ( PDOException $e ) {
      parent::disconnect( $conn );
      die( "Query failed: " . $e->getMessage() );
    }
  } 

}             
?>