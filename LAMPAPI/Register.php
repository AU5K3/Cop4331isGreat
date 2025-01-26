<?php

    // for debugging purposes
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $inData = getRequestInfo();

    // check if all necessary fields were set
    if (!isset($inData["login"]) || !isset($inData["login"]) || !isset($inData["login"]) || !isset($inData["login"])) {
        returnWithError("Missing required field(s).");
        exit();
    }

    // connect to database
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
    if( $conn->connect_error )
    {
        returnWithError("Database connection failed: " . $conn->connect_error ); // failed to connect
        exit();
    }

    // insert user into Users
    $stmt = $conn->prepare("INSERT into Users (firstName, lastName, login, password) VALUES(?,?,?,?)");

    $login = $inData["login"];
    $password = $inData["password"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];

    // check if any field is empty
    if (strlen($inData["login"]) == 0 || strlen($inData["login"]) == 0 || strlen($inData["login"]) == 0 || strlen($inData["login"] == 0)) {
        returnWithError("Empty required field(s).");
        exit();
    }
    $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);

    $stmt->execute();

    // check for errors
	if ($stmt->error) {
		returnWithError("Failed to execute INSERT statement for Users: " . $stmt->error);
		$stmt->close();
		$conn->close();
		exit();
	}

    // stops connections
    $stmt->close();
    $conn->close();

    // user was succesfully registered!
    $response = array("message" => "User registered successfully!");
    sendResultInfoAsJson($response);

    // function to get input data from request body
    function getRequestInfo()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            returnWithError("Invalid JSON payload: " . json_last_error_msg());
            exit();
        }
    
        return $data;
    }

    // function to send JSON response
    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json');
        echo json_encode($obj);
    }

    // function to return error message as JSON
    function returnWithError( $err )
    {
        $retValue = array("error" => $err);
        sendResultInfoAsJson( $retValue );
    }

?>

