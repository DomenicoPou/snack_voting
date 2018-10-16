<?php
// Required Headers for POST REST API
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// Inclusions
include_once '../config/database.php';
include_once '../objects/students.php';
 
// Connect to database
$database = new Database();
$db = $database->getConnection();
 
// Create a student object
$student = new Students($db);

// Grab the contents of the fetch POST data, if existing
$data = file_get_contents("php://input");
$data = json_decode($data, true);

// Set the values of the student we are registering
$student->studentID = $data['studentID'];
$student->fullname = $data['fullname'];
$student->password = $data['password'];

// Check if the student exists or not
$studentID_exists = $student->studentIDExists();
if ($studentID_exists) {

	// The request was still succesful, Note: Although the result was unsuccesful, I still want my page to know nothing went wrong.
	http_response_code(200);
	
	// Display to the user that the student already exists
	echo json_encode(array("message" => "StudentID already exists."));
	exit;
}


// Create the user
if ($student->register()) {
	
	http_response_code(200);
	
	// display message: User was created.
	echo json_encode(array("message" => "User was created."));
} else {
	
	http_response_code(200);
	
	// display message: Unable to create user.
	echo json_encode(array("message" => "Unable to create user."));
}
?>