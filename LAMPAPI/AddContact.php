<?php

// session_start();  // Start the session to access the UserID

// // for debugging purposes
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// // Function to get input data from the request body
// function getRequestInfo()
// {
//     $json = file_get_contents('php://input');
//     $data = json_decode($json, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         returnWithError("Invalid JSON payload: " . json_last_error_msg());
//         exit();
//     }

//     return $data;
// }

// // Function to send JSON response
// function sendResultInfoAsJson($obj)
// {
//     header('Content-type: application/json');
//     echo json_encode($obj);
// }

// // Function to return error message as JSON
// function returnWithError($err)
// {
//     $retValue = array("error" => $err);
//     sendResultInfoAsJson($retValue);
// }
// 	// $inData = getRequestInfo();
	
// 	// $color = $inData["contacts"];
// 	// $userId = $inData["userId"];

// 	// $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
// 	// if ($conn->connect_error) 
// 	// {
// 	// 	returnWithError( $conn->connect_error );
// 	// } 
// 	// else
// 	// {
// 	// 	$stmt = $conn->prepare("INSERT into Contacts (UserId,Name) VALUES(?,?)");
// 	// 	$stmt->bind_param("ss", $userId, $color);
// 	// 	$stmt->execute();
// 	// 	$stmt->close();
// 	// 	$conn->close();
// 	// 	returnWithError("");
// 	// }

// 	// function getRequestInfo()
// 	// {
// 	// 	return json_decode(file_get_contents('php://input'), true);
// 	// }

// 	// function sendResultInfoAsJson( $obj )
// 	// {
// 	// 	header('Content-type: application/json');
// 	// 	echo $obj;
// 	// }
	
// 	// function returnWithError( $err )
// 	// {
// 	// 	$retValue = '{"error":"' . $err . '"}';
// 	// 	sendResultInfoAsJson( $retValue );
// 	// }

// 	if (!isset($_SESSION['userID'])) {
// 		$_SESSION['userID'] = 1;
// 		// returnWithError("User is not logged in.");
// 		// exit();
// 	}
	
// 	$userId = $_SESSION['userID'];  // uses ssession start
	
// 	$inData = getRequestInfo();
	
	
// 	if (!isset($inData["FirstName"]) || !isset($inData["LastName"]) || !isset($inData["Email"])|| !isset($inData["Phone"])|| !isset($inData["userID"])) {
// 		returnWithError("Missing required fields.");
// 		exit();
// 	}
	
// 	// set variables
// 	$firstName = $inData["FirstName"];
// 	$lastName = $inData["LastName"];
// 	$phone = $inData["Phone"];
// 	$email = $inData["Email"];
// 	$userId = $inData["userID"];
	
// 	// Database connection
// 	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
// 	if ($conn->connect_error) 
// 	{
// 		returnWithError("Database connection failed: " . $conn->connect_error);
// 		exit();
// 	}
	
// 	// Insert into CONTACTS table
// 	$stmt = $conn->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, userID) VALUES (?, ?, ?, ?, ?)");
// 	if ($stmt === false) {
// 		returnWithError("Failed to prepare INSERT statement for Contacts: " . $conn->error);
// 		$conn->close();
// 		exit();
// 	}
// 	$stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $inData["userId"]);
// 	$stmt->execute();
	
// 	// any errors
// 	if ($stmt->error) {
// 		returnWithError("Failed to execute INSERT statement for Contacts: " . $stmt->error);
// 		$stmt->close();
// 		$conn->close();
// 		exit();
// 	}
	
// 	// Get the ID of the newly inserted contact
// 	$contactID = $stmt->insert_id;  // This retrieves the auto-incremented ID from the CONTACTS table
// 	$stmt->close();  // Close the first statement
	
// 	// Insert into USERCONTACTS table linking the new contact with the user from MAINUSERS
// 	$stmt = $conn->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, userID) VALUES(?,?,?,?,?)");
// 	if ($stmt === false) {
// 		returnWithError("Failed to prepare INSERT statement for Contacts: " . $conn->error);
// 		$conn->close();
// 		exit();
// 	}
// 	$stmt->bind_param("ssssi", $firstname, $lastname, $phone, $email, $inData["userId"]);
// 	$stmt->execute();
	
// 	// eerrors
// 	if ($stmt->error) {
// 		returnWithError("Failed to execute INSERT statement for Contacts: " . $stmt->error);
// 		$stmt->close();
// 		$conn->close();
// 		exit();
// 	}
	
// 	// stops connections
// 	$stmt->close();
// 	$conn->close();
	
// 	// successful contact added
// 	$response = array("message" => "Contact added successfully!");
// 	sendResultInfoAsJson($response);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// This is a security note: Only POST requests are allowed to ensure safe handling of data.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode([
		"error" => "Invalid request method. Please use POST."
	]);
	exit();
}

$inData = getRequestInfo();

// This step is to check if the JSON or data is valid
if ($inData === null) {
	error_log("Raw input received: " . file_get_contents('php://input'));
	returnWithError("Invalid JSON format or no data received");
	exit();
}

error_log("Decoded JSON: " . json_encode($inData));

// This is to plug the data from JSON
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];
$phone = $inData["phone"];
$email = $inData["email"];
$userId = $inData["UserID"];

// This step is to check if there's anything missing
// if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($userId)) {
// 	returnWithError("All fields are required");
// 	exit();
// }

error_log("Extracted values: firstName=$firstName, lastName=$lastName, phone=$phone, email=$email, UserID=$userId");

// This step is to connect with the database
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error) {
	error_log("Connection failed: " . $conn->connect_error);
	returnWithError("Connection failed: " . $conn->connect_error);
	exit();
}

// This step is to add the contact
$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);

if ($stmt->execute()) {
	returnWithSuccess("Contact added successfully");
} else {
	error_log("Failed to execute query: " . $stmt->error);
	returnWithError("Failed to add contact");
}

$stmt->close();
$conn->close();

// This step is extracting JSON data from request
function getRequestInfo()
{
	$rawData = file_get_contents('php://input');
	error_log("Raw input received: " . $rawData);
	return json_decode($rawData, true);
}

// This step is to returing the result in JSON
function sendResultInfoAsJson($obj)
{
	header('Content-type: application/json');
	echo $obj;
}

// This is error function
function returnWithError($err)
{
	$retValue = '{"error":"' . $err . '"}';
	sendResultInfoAsJson($retValue);
}

// This step means success
function returnWithSuccess($message)
{
	$retValue = '{"message":"' . $message . '","error":""}';
	sendResultInfoAsJson($retValue);
}
	
?>