<?php
	/***
		STUDENTS
		are the targeted users for this app. As the application is ment to be used
		to determin what healthy snack theyprefer to eat.
		
		-students
			-studentID	varchar(255)
			-fullname	varchar(255)
			-password	text
	*/
class Students {

	// Database Configurations
	private $conn;
	private $table_name = "students";
	
	// Student Properties
	public $id;
	public $studentID;
	public $fullname;
	public $password;
	
	// Constructor
	public function __construct($db){
		$this->conn = $db;
	}
	
	/**
		Register students within the databse
	*/
	function register(){
	
		// Simple Insert with the required information
		$query = "INSERT INTO " . $this->table_name . "
			SET studentID = :studentID,
			fullname = :fullname,
			password = :password";
		
		// Prepare the query
		$dbstmt = $this->conn->prepare($query);
		
		// Replace the paramaters
		$dbstmt->bindParam(':studentID', $this->studentID);
		$dbstmt->bindParam(':fullname', $this->fullname);
		
		// Encrypt the password with BCRYPT and replace the query with it
		$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
		$dbstmt->bindParam(':password', $password_hash);
		
		// Execute the query and check if it went well
		if ($dbstmt->execute()) {
			return true;
		}
		
		return false;
	}
	
	
	
	/**
		Check if given email exist in the database
	*/
	function studentIDExists() {
	
		$query = "SELECT * 
			FROM " . $this->table_name . "
			WHERE studentID = :studentID
			LIMIT 0,1";
		
		$dbstmt = $this->conn->prepare($query);
		
		$this->studentID = htmlspecialchars(strip_tags($this->studentID));
		
		$dbstmt->bindParam(":studentID", $this->studentID);
		
		$dbstmt->execute();
		
		// Count the number of rows given within the query
		$num = $dbstmt->rowCount();
		$this->num = $dbstmt;
		
		// If email exists, assign values to object properties for easy access and use for php sessions
		if($num > 0){
		
			// Get row details and values
			$row = $dbstmt->fetch(PDO::FETCH_ASSOC);
			
			// Assign values to students properties
			$this->studentID = $row['studentID'];
			$this->fullname = $row['fullname'];
			$this->password = $row['password'];
			
			// Return the values to be used within the students session
			return $row;
		}
		
		// return null if email does not exist in the database
		return null;
	}
}
