<?php
    // To interact, send POST request.
    // Endpoint for verifying (or rejecting) a login attempt. Takes the following inputs:
    // user: varchar(50)
    // password: varchar(255)

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST")
    {
        return returnWrongRequestMethod();
    }

    // Ensure that the necessary data has been passed
    $requestBody = json_decode(file_get_contents('php://input'));

    $username = $requestBody->username;
    $password = $requestBody->password;

    $queryRes = "";

    // Disallow the sequence "$/$" in the username for future auth purposes
    $username = str_replace("$/$", "$\\/$", $username);

    $responseObj = new stdClass();
    $responseObj->status = -1;

    if (empty($username))
    {
        return returnError($responseObj, "Error: Missing username input.");
    }
    else if (empty($password))
    {
        return returnError($responseObj, "Error: Missing password input.");
    }

    // Truncate the username to the maximum length allowed in the database
    $username = strtolower(substr($username, 0, 50));

    // Check to see whether or not this user exists
    if ($getUserPass = $conn->prepare("SELECT password FROM Users WHERE Login=?"))
    {
        $getUserPass->bind_param("s", $username);
        $getUserPass->execute();
        $getUserPass->bind_result($queryRes);
        $getUserPass->fetch();
        $getUserPass->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to check whether user exists.", HTTP_INTERNAL_ERROR);
    }

    // Query result is empty, so no user with input username was found.
    if (empty($queryRes))
    {
        return returnError($responseObj, "Error: User not found.");
    }

    // Check passed password compared to hashed one retrieved
    if (password_verify($password, $queryRes))
    {
        // Password is valid, log the user in
        
        // Update DateLastLoggedIn with current datetime
        $currentDate = date('Y-m-d H:i:s');
        if ($updateDate = $conn->prepare("UPDATE Users SET DateLastLoggedIn=? WHERE Login=?"))
        {
            $updateDate->bind_param("ss", $currentDate, $username);
            $updateDate->execute();
            $updateDate->bind_result($queryRes);
            $updateDate->fetch();
            $updateDate->close();
        }
        else
        {
            // Do not want to cancel the login attempt here but want to log the failure
            error_log("Server failed to update DateLastLoggedIn");
        }

        // Generate a "secure" authentication cookie so the server can validate CRUD operations
        // Combines the username with the login time to later be checked
        $authCookie = $username . "$/$" . $currentDate;

        // Send the data necessary to log the user in
        $responseObj->message = "Successfully logged in user.";
        $response = new stdClass();
        $response->cookie = $authCookie;
        $responseObj->response = $response;

        return returnAsJson($responseObj);
    }
    else
    {
        return returnError($responseObh, "Error: Password did not match.");
    }
?>