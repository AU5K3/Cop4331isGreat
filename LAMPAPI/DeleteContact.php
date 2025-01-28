<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "error" => "Invalid request method."
    ]);
    exit();
}

// Get JSON input
$inData = getRequestInfo();

// Check if input is valid
if ($inData === null) {
    error_log("Raw input received: " . file_get_contents('php://input'));
    returnWithError("Invalid JSON format or no data received");
    exit();
}

error_log("Decoded JSON: " . json_encode($inData));

// Initialize variables
$firstName = $inData["firstName"] ?? null;
$lastName = $inData["lastName"] ?? null;
$email = $inData["email"] ?? null;
$userId = $inData["UserID"] ?? null;

// Validate that required fields are present
if (empty($firstName) || empty($lastName) || empty($email) || empty($userId)) {
    returnWithError("All fields are required");
    exit();
}

error_log("Extracted values: firstName=$firstName, lastName=$lastName, email=$email, userID=$userId");

// Connect to the database
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    returnWithError("Connection failed: " . $conn->connect_error);
    exit();
}

// Prepare the DELETE statement
$stmt = $conn->prepare("DELETE FROM Contacts WHERE FirstName = ? AND LastName = ? AND Email = ? AND UserID = ?");
if ($stmt === false) {
    error_log("Failed to prepare DELETE statement: " . $conn->error);
    returnWithError("Failed to prepare DELETE statement");
    $conn->close();
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("sssi", $firstName, $lastName, $email, $userId);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        returnWithSuccess("Contact deleted successfully");
    } else {
        returnWithError("No contact found with the specified details");
    }
} else {
    error_log("Failed to execute DELETE query: " . $stmt->error);
    returnWithError("Failed to delete contact");
}

$stmt->close();
$conn->close();

// Functions for JSON responses
function getRequestInfo()
{
    $rawData = file_get_contents('php://input');
    error_log("Raw input received: " . $rawData);
    return json_decode($rawData, true);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

function returnWithSuccess($message)
{
    $retValue = '{"message":"' . $message . '","error":""}';
    sendResultInfoAsJson($retValue);
}
?>
