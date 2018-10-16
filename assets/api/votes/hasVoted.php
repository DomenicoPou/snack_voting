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

// Initialise the vote class
$votes = new Votes($db);
 
// Get the fetch POST data
$data = json_decode(file_get_contents("php://input"), TRUE);
 
// Set the student ID that we are searching
$votes->studentID = $data['studentID'];
$vote_exists = $votes->studentVoteExists();

if($vote_exists != null){

	http_response_code(200);
	
	// Tell the user that they have voted. Return the vote data to be displayed.
	echo json_encode(array("message" => "User has voted.", "vote" => $vote_exists));
} else {
	
	http_response_code(200);
	
	// They haven't voted, send them their ID, just for verification purposes.
	echo json_encode(array("message" => "User hasn't voted.", "studentID" => $data['studentID']));
}
?>