<?php
    // To interact, send POST or PATCH request.
    // Endpoint for removing a contact for a user.
    // ID: int (id of the contact to modify)
    // FirstName, LastName, Phone, Email (will update any field passed, does not have to be all of them)
    // Cookie: the currently assigned authentication cookie of the client

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "PATCH")
    {
        return returnWrongRequestMethod();
    }

    // Ensure that the necessary data has been passed
    // At least one contact field must be provided, and auth must always be given
    $requestBody = json_decode(file_get_contents('php://input'));

    $firstName = $requestBody->firstName;
    $lastName = $requestBody->lastName;
    $phone = $requestBody->phone;
    $email = $requestBody->email;
    $contactID = $requestBody->ID;
    $auth = $requestBody->auth;

    if (empty($auth))
    {
        return returnError($responseObj, "Error: Missing authentication.");
    }
    else if (empty($contactID))
    {
        return returnError($responseObj, "Error: ID of the contact to be modified must be provided.");
    }
    else if (empty($firstName) && empty($lastName) && empty($phone) && empty($email))
    {
        return returnError($responseObj, "Error: At least one modification must be provided.");
    }

    // Create the response object used to reply in JSON
    $responseObj = new stdClass();
    $responseObj->status = -1;

    // Truncate the input to the maximum length allowed in the database
    $firstName = substr($firstName, 0, 50);
    $lastName = substr($lastName, 0, 50);
    $phone = substr($phone, 0, 50);
    $email = substr($email, 0, 50);

    // Check if the user is authenticated to perform this action
    $userID = isAuthenticated($auth, $conn);
    if ($userID == -1)
    {
        return returnError($responseObj, "Error: There was a failure to authenticate the user.", HTTP_INTERNAL_ERROR);
    }

    // Ensure that the target contact record exists and belongs to the user
    $queryRes = "";
    if ($getContact = $conn->prepare("SELECT ID FROM Contacts WHERE (ID, UserID) IN ((?,?))"))
    {
        $getContact->bind_param("ii", $contactID, $userID);
        $getContact->execute();
        $getContact->bind_result($queryRes);
        $getContact->fetch();
        $getContact->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to check whether contact record exists.", HTTP_INTERNAL_ERROR);
    }

    if (empty($queryRes))
    {
        return returnError($responseObj, "Error: The specified contact ID either does not exist or does not belong to this user.");
    }

    // 
?>