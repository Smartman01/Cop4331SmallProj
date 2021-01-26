<?php
    // To interact, send POST request.
    // Endpoint for verifying (or rejecting) a login attempt. Takes the following inputs:
    // user: varchar(50)
    // password: varchar(255)

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the necessary data has been passed
    $requestBody = json_decode(file_get_contents('php://input'));

    $username = $requestBody->username;
    $password = $requestBody->password;

    $queryRes = "";

    $responseObj = new stdClass();
    $responseObj->status = -1;

    if (empty($username))
    {
        return fireError($responseObj, "Error: Missing username input.");
    }
    else if (empty($password))
    {
        return fireError($responseObj, "Error: Missing password input.");
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
        return fireError($responseObj, "Error: Server failed to check whether user exists.");
    }

    // Query result is empty, so no user with input username was found.
    if (empty($queryRes))
    {
        return fireError($responseObj, "Error: User not found.");
    }

    // Check passed password compared to hashed one retrieved
    if (password_verify($password, $queryRes))
    {
        // Password is valid, log the user in
        // TODO: log the user in (e.g. start their session), probably by returning an insecure cookie in JSON
        
        // Update DateLastLoggedIn with current datetime
        if ($updateDate = $conn->prepare("UPDATE Users SET DateLastLoggedIn=? WHERE Login=?"))
        {
            $updateDate->bind_param("ss", date('Y-m-d H:i:s'), $username);
            $updateDate->execute();
            $updateDate->bind_result($queryRes);
            $updateDate->fetch();
            $updateDate->close();
        }
        else
        {
            // Don't want to cancel the login attempt here but want to log the failure
            error_log("Server failed to update DateLastLoggedIn");
        }

        // Temporary "good" status return
        return fireError($responseObj, "User successfully logged in (but not really yet).", 1);
    }
    else
    {
        return fireError($responseObh, "Error: Password did not match.");
    }
?>