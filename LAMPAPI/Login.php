<?php
    // To interact, send POST request.
    // Endpoint for verifying (or rejecting) a login attempt. Takes the following inputs:
    // user: varchar(50)
    // password: varchar(255)

    // Upon successful login will update the DateCreated field with the current datetime.

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the necessary data has been passed
    $username = $_POST['username'];
    $password = $_POST['password'];

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
?>