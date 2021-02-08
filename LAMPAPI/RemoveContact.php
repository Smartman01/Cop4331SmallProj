<?php
    // To interact, send POST or DELETE request.
    // Endpoint for removing a contact for a user.
    // ID: int (id of the contact to remove)
    // Cookie: the currently assigned authentication cookie of the client

    include "DBConnect.php";
    include "ResponseLib.php";

    // TODO: allow batch deletion of records

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "DELETE")
    {
        return returnWrongRequestMethod();
    }

    // Ensure that the necessary data has been passed
    // At least one contact field must be provided, and auth must always be given
    $requestBody = json_decode(file_get_contents('php://input'));

    $contactID = $requestBody->id;

    $auth = $auth_header;

    if (empty($auth))
    {
        return returnError($responseObj, "Error: Missing authentication.");
    }
    else if (empty($contactID))
    {
        return returnError($responseObj, "Error: ID of the contact to be removed must be provided.");
    }

    // Create the response object used to reply in JSON
    $responseObj = new stdClass();
    $responseObj->status = -1;

    // Check if the user is authenticated to perform this action
    $userID = isAuthenticated($auth, $conn);
    if ($userID == -1)
    {
        return returnError($responseObj, "Error: The passed authentication token is invalid.");
    } 
    else if ($userID == -2)
    {
        return returnError($responseObj, "Error: There was a failure to authenticate the user.", HTTP_INTERNAL_ERROR);
    }

    // Ensure that the target contact record exists and belongs to the user
    $queryRes = ""
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

    // All necessary checks (exists and belongs to auth user) have been passed
    // Delete the specified contact record
    if ($deleteContact = $conn->prepare("DELETE FROM Contacts WHERE ID=?"))
    {
        $deleteContact->bind_param("i", $contactID);
        $deleteContact->execute();
        $deleteContact->bind_result($queryRes);
        $deleteContact->fetch();
        $deleteContact->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to delete contact record.", HTTP_INTERNAL_ERROR);
    }

    // Form the successful response
    $responseObj->message = "Contact successfully removed.";

    return returnAsJson($responseObj);
?>