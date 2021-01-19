<?php
    // To interact, send POST request.
    // Endpoint for creating a user (and verifying that input information is safe).
    // FirstName: varchar(50)
    // LastName: varchar(50)
    // user: varchar(50)
    // password: varchar(50)

    // Upon successfully creating a user will update DateCreated and DateLastLoggedIn with current datetime.
    // Additionally, should maybe forward the user to a login page or log them in.

    include "DBConnect.php";

    // Ensure that the necessary data has been passed
    // TODO: fill in $_POST names or modify for the way the front end passes the information
    // TODO: check with frontend about how they want to handle errors (e.g. error codes & messages)
    $firstName = $_POST[''];
    $lastName = $_POST[''];
    $username = $_POST[''];
    $password = $_POST[''];

    $responseObj->status = -1;

    if (empty($firstName))
    {
        return fireError($responseObj, "Error: Missing first name input.");
    }
    else if (empty($lastName))
    {
        $responseObj->message = "Error: Missing last name input.";
        echo json_encode($responseObj);
        return
    }
?>