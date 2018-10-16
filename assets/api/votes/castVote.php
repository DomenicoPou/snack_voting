<?php
// Required Headers for POST REST API
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// Inclusions
include_once '../config/database.php';
include_once '../objects/votes.php';
 
// Connect to database
$database = new Database();
$db = $database->getConnection();
 
// Instantiate Vote Object
$votes= new Votes($db);

// Grab the contents of the fetch POST data, if existing
$data = file_get_contents("php://input");
$data = json_decode($data, true);

// Set the vote object values
$votes->studentID = $data['studentID'];
$votes->snackID = $data['snackID'];

// Cast the vote
$results = $votes->castVote();

// Depending on the results, send the user a relevant message

if ($results != null) {
	
	http_response_code(200);
	
	// Include the name of the voted snack
	echo json_encode(array("message" => "User vote was accepted.", "vote" => $results));
} else {

	http_response_code(400);
	echo json_encode(array("message" => "Unable to vote."));
}
?>