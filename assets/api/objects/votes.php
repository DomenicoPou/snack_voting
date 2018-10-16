<?php
	/***
		VOTES
		is the database that contains all votes created by
		
		-votes
			-id		int(11)
			-studentID 	varchar(255)
			-snackID	int(11)
	*/
class Votes {
	
	// Database Configuration
	private $conn;
	private $table_name = "votes";
	
	// Vote Properties
	public $id;
	public $studentID;
	public $snackID;
	
	// Constructor
	public function __construct($db) {
		$this->conn = $db;
	}
	
	/**
		Count all the votes grouping towards each snack, with their names attached
	*/
	function countVotes() {
		$query = "SELECT c.name AS `name` , COUNT(*) AS `total` FROM " . $this->table_name . " a " .
		"LEFT JOIN snacks c ON a.snackID = c.id GROUP BY c.name";
		
		// Prepare Query
		$dbstmt = $this->conn->prepare($query);
		
		// Execute Query
		$dbstmt->execute();
		
		// Return the database statement
		return $dbstmt;
	}
	
	/**
		Check if the student has voted 
	*/
	function studentVoteExists() {
		
		// Query with the student ID as the only param
		$query = "SELECT c.name AS `name` FROM " . $this->table_name . " a " .
			"LEFT JOIN snacks c ON a.snackID = c.id	WHERE a.studentID = :studentID";
		
		$dbstmt = $this->conn->prepare($query);
		
		// Replace the param with the students ID
		$dbstmt->bindParam(":studentID", $this->studentID);
		
		$dbstmt->execute();
		
		// Count the number of rows
		$num = $dbstmt->rowCount();
		$this->num = $dbstmt;
		
		// If the vote exists return the students voted snack/fruit
		if($num > 0){
			// Get the rows details to obtain the healthy snacks name
			$row = $dbstmt->fetch(PDO::FETCH_ASSOC);
			return $row['name'];
		}
		
		// Return null if the student hasn't voted
		return null;
	}
	
	/**
		Allows the students to cast votes, on their favorite healthy snack
	*/
	function castVote() {
	
		// Set Query with the vote casting params
		$query = "INSERT INTO " . $this->table_name . "
			SET studentID = :studentID,
			snackID = :snackID";
		
		$dbstmt = $this->conn->prepare($query);
		
		// Bind the params
		$dbstmt->bindParam(':studentID', $this->studentID);
		$dbstmt->bindParam(':snackID', $this->snackID);
		
		// Execute the query, also check if query was succesful
		if ($dbstmt->execute()) {
		
			// Return the snack name of the students vote. Or null if it was unsuccesful.
			return $this->studentVoteExists();
		}
		
		// Reutrn null if the query was not succesful
		return null;
	}
}