<?php
/***
	SNACKS
	snacks contains the list of fruits that students can vote on.
	
	-snacks
		-id	int(11)
		-name	varchar(255)
*/
class Snacks {
 
	// Database Configure
	private $conn;
	private $table_name = "snacks";
	
	// Snack Properties
	public $id;
	public $name;
	
	// Constructor with $db as database connection
	public function __construct($db){
		$this->conn = $db;
	}
	
	/**
		Return all the required information used for snacks.
	*/
	function read() {
	
		// Query to return all the snack within the database ordered by total votes desc
		$query = "SELECT a.id AS `id`, a.name AS `name`, COUNT(*) AS `total` FROM " . $this->table_name . " a 
		LEFT JOIN votes b ON a.id = b.snackID
		GROUP BY a.id
		ORDER BY total DESC";
		
		// Load and prepare the query 
		$dbstmt = $this->conn->prepare($query);
		
		// Execute query
		$dbstmt->execute();
		
		return $dbstmt;
	}
}