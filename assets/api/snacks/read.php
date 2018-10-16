<?php
// Headers for simple a fetch REST API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Includes
include_once '../config/database.php';
include_once '../objects/snacks.php';
 
// Instantiate Database
$database = new Database();
$db = $database->getConnection();
 
// Initialise the snack class
$snacks = new Snacks($db);
 
// Read the snack data and count all the rows, just incase no snacks exist
$stmt = $snacks->read();
$num = $stmt->rowCount();
 
if($num > 0){
	
	// Instantiate arrays
	$snack_arr = array();
	$snack_arr["results"] = array();
	
	// Foreach row push the relevant data we need
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	
		// Extract Row
		extract($row);
		
		// Set up array
		$snack_item = array(
		    "id" => $id,
		    "name" => $name,
		    "total" => $total
		);
		
		// Push in overall array
		array_push($snack_arr["results"], $snack_item);
	}
	
	// Return the array
	echo json_encode($snack_arr);
}
 
else{

	// If no snacks exists then warn the user.
	echo json_encode(
		array("message" => "No snacks being voted for")
	);
}
?>