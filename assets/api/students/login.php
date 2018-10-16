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
$data = json_decode(file_get_contents("php://input"), TRUE);
 
// Set the student values and check if they actually exist
$student->studentID = $data['studentID'];
$studentID_exists = $student->studentIDExists();
 
// If the student exists and password is correct
if($studentID_exists && password_verify($data['password'], $student->password)){
	
	http_response_code(200);
	
	// Display to the user that the Login was Succesful
	echo json_encode(array("message" => "Login Succesful.", "studentID" => $studentID_exists['studentID'], "fullname" => $studentID_exists['fullname']));
} else {
	
	
	http_response_code(200);
	
	// Display to the user that the Login Failed
	echo json_encode(array("message" => "Login Failed."));
}
?>