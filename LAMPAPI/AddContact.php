<?php
    // To interact, send POST request.
    // Endpoint for a user to create a contact.
    // FirstName: varchar(50)
    // LastName: varchar(50)
    // Phone: varchar(50) (as a sidenote can probably shorten this, as phone numbers don't tend to be this long)
    // Email: varchar(50)
    // Cookie: the currently assigned authentication cookie of the client

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST")
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
    $auth = $requestBody->auth;

    if (empty($auth))
    {
        return returnError($responseObj, "Error: Missing authentication.");
    }
    else if (empty($firstName) && empty($lastName) && empty($phone) && empty($email))
    {
        return returnError($responseObj, "Error: At least one input must be provided.");
    }

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

    // Ensure that the contact record being made does not already exist and belong to auth user
    $queryRes = "";
    if ($getContact = $conn->prepare("SELECT ID FROM Contacts WHERE (FirstName, LastName, Phone, Email, UserID) IN ((?,?,?,?,?))"))
    {
        $getContact->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userID);
        $getContact->execute();
        $getContact->bind_result($queryRes);
        $getContact->fetch();
        $getContact->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to check whether contact record exists.", HTTP_INTERNAL_ERROR);
    }

    if (!empty($queryRes))
    {
        return returnError($responseObj, "Error: This exact contact already exists.");
    }

    // All necessary checks have been passed, so create the new contact
    if ($createContact = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?,?,?,?,?)"))
    {
        $createContact->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userID);
        $createContact->execute();
        $createContact->bind_result($queryRes);
        $createContact->fetch();
        $createContact->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to create contact record.", HTTP_INTERNAL_ERROR);
    }

    $response = "Contact successfully created.";

    return returnAsJson($responseObj, $response);
?>