<?php
    // To interact, send POST request.
    // Endpoint for creating a user (and verifying that input information is safe).
    // FirstName: varchar(50)
    // LastName: varchar(50)
    // user: varchar(50)
    // password: varchar(255)

    // Upon successfully creating a user will update DateCreated and DateLastLoggedIn with current datetime.
    // Additionally, should maybe forward the user to a login page or log them in.

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the necessary data has been passed
    // TODO: check with frontend about how they want to handle errors (e.g. error codes & messages)
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $queryRes = "";

    // Used for testing prints info gotten from html (from Carl)
    // echo "$firstName $lastName $username $password"

    $responseObj = new stdClass();
    $responseObj->status = -1;
    
    if (empty($firstName))
    {
        return fireError($responseObj, "Error: Missing first name input.");
    }
    else if (empty($lastName))
    {
        return fireError($responseObj, "Error: Missing last name input.");
    }
    else if (empty($username))
    {
        return fireError($responseObj, "Error: Missing username input.");
    }
    else if (empty($password))
    {
        return fireError($responseObj, "Error: Missing password input.");
    }

    // Truncate the input to the maximum length allowed in the database
    // Figure this should happen so little that throwing an error for this is unimportant
    $firstName = substr($firstName, 0, 50);
    $lastName = substr($lastName, 0, 50);
    $username = strtolower(substr($username, 0, 50));

    // Ensure that the current username is not already taken
    if ($getUser = $conn->prepare("SELECT ID FROM Users WHERE Login=?"))
    {
        $getUser->bind_param("s", $username);
        $getUser->execute();
        $getUser->bind_result($queryRes);
        $getUser->fetch();
        $getUser->close();
    }
    else
    {
        return fireError($responseObj, "Error: Server failed to check whether username is in use.");
    }
    
    if (!empty($queryRes))
    {
        return fireError($responseObj, "Error: Username already in use.");
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Create the user and insert it into the database
    if ($createUser = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)"))
    {
        $createUser->bind_param("ssss", $firstName, $lastName, $username, $password);
        $createUser->execute();
        $createUser->bind_result($queryRes);
        $createUser->fetch();
        $createUser->close();
    }
    else
    {
        return fireError($responseObj, "Error: Server failed to create the user.");
    }

    echo "$queryRes";
?>