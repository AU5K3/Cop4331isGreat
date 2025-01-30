<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		//OR Phone like ? OR Email like ?
		//$stmt = $conn->prepare("select Name from Colors where (FirstName like ? OR LastName like ?) and UserID=?");
		$stmt = $conn->prepare("select FirstName, LastName, Phone, Email, ID from Contacts where (FirstName like ? OR LastName like ? OR Phone like ? OR Email like ?)  AND UserID=?");
		$colorName = "%" . $inData["search"] . "%";
		//$colorName, $colorName,
		$stmt->bind_param("ssssi", $colorName, $colorName, $colorName, $colorName, $inData["userID"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			//$searchResults .= '"' . $row["FirstName"] . '"';
			$searchResults .= '{"firstName" : "'.$row["FirstName"].'" , "lastName" : "'.$row["LastName"].'" , "phone" : "'.$row["{Phone}"].'" , "email" : "'.$row["Email"].'"}';
			//$searchResults .= '{"FirstName":"'.$row["FirstName"].'"}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"UserID":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
