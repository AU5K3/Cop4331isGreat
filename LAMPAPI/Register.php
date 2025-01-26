<?php

    // for debugging purposes
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $inData = getRequestInfo();

    // check if all necessary fields were scent
    if (empty($inData["login"]) || empty($inData["login"]) || empty($inData["login"]) || empty($inData["login"])) {
        returnWithError("Missing or empty required field(s).");
        exit();
    }

    // connect to database
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
    if( $conn->connect_error )
    {
        returnWithError("Database connection failed: " . $conn->connect_error ); // failed to connect
        exit()
    }

    // insert user into Users
    $stmt = $conn->prepare("INSERT into Users (firstName, lastName, login, password) VALUES(?,?,?,?)");

    $login = $inData["login"];
    $password = $inData["password"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
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
        echo $obj;
    }

    // function to return error message as JSON
    function returnWithError( $err )
    {
        $retValue = array("error" => $err);
        sendResultInfoAsJson( $retValue );
    }

?>

