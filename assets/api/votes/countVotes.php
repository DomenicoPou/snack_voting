<?php
// Headers for simple a fetch REST API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Includes
include_once '../config/database.php';
include_once '../objects/votes.php';
 
// Instantiate Database
$database = new Database();
$db = $database->getConnection();
 
// Initialise the vote class
$votes = new Votes($db);
 
// Count the votes for each snack
$stmt = $votes->countVotes();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if ($num > 0) {
	
	// Create the arrays
	$vote_arr = array();
	$vote_arr["results"] = array();
	
	// Foreach row extract the values and push it to the overall result array
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		extract($row);
		
		$vote_item = array(
		    "name" => $name,
		    "total" => $total
		);
		
		array_push($vote_arr["results"], $vote_item);
	}
	
	// Return the array
	echo json_encode($vote_arr);
}

// If there isn't any votes. Warn the user
else{
    echo json_encode(
        array("message" => "No Votes")
    );
}
?>