<?php
    // To interact, send POST request.
    // Endpoint for a user to create a contact.
    // FirstName: varchar(50)
    // LastName: varchar(50)
    // Phone: varchar(50) (as a sidenote can probably shorten this, as phone numbers don't tend to be this long)
    // Email: varchar(50)
    // Cookie: the currently assigned authentication cookie of the client

    // Ensure that the exactly specified contact does not already exist

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
?>