<?php
    // To interact, send GET request.
    // Endpoint for searching contacts based on data entered.
    // input: varchar(100) (most you should enter is 100 chars (firstname + lastname length))
    // Cookie: the currently assigned authentication cookie of the client
    // Returns the list of matched contacts belonging to the current user in JSON format.
    
    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "GET")
    {
        return returnWrongRequestMethod();
    }

    // Ensure that the necessary data has been passed
    // In this case, query can be empty, signaling to return all contacts belonging to a user
    $query = $_GET['query'];
    $auth = $auth_header;

    if (empty($auth))
    {
        return returnError($responseObj, "Error: Missing authentication.");
    }

    $responseObj = new stdClass();
    $responseObj->status = -1;

    // Authenticate the user and get their ID if possible
    $userID = isAuthenticated($auth, $conn);
    if ($userID == -1)
    {
        return returnError($responseObj, "Error: The passed authentication token is invalid.");
    } 
    else if ($userID == -2)
    {
        return returnError($responseObj, "Error: There was a failure to authenticate the user.", HTTP_INTERNAL_ERROR);
    }

    // Begin processing the query
    $responseObj->contacts = array();
    $counter = 0;

    // Query is empty, so grab all contacts that belong to the user
    $row = "";
    if (empty($query))
    {
        if ($getContacts = $conn->prepare("SELECT * FROM Contacts WHERE UserID=?"))
        {
            $getContacts->bind_param("i", $userID);
            $getContacts->execute();
            $res = $getContacts->get_result();
            while ($row = $res->fetch_assoc())
            {
                array_push($responseObj->contacts, new contact($row["ID"], $row["FirstName"], $row["LastName"], $row["Phone"], $row["Email"]));
                $counter = $counter + 1;
            }
            $getContacts->close();
        }
        else
        {
            return returnError($responseObj, "Error: Server failed to retrieve contacts.", HTTP_INTERNAL_ERROR);
        }
    }
    else
    {

    }


    $responseObj->message = ("Retrieved " . $counter . " contact" . ($counter != 1 ? "s" : "") . ".");
    return returnAsJson($responseObj);
?>