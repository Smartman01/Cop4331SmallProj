<?php
    // To interact, send POST request.
    // Endpoint for creating a user (and verifying that input information is safe).
    // FirstName: varchar(50)
    // LastName: varchar(50)
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
    // TODO: firstName and lastName are not required, so have variations of the query for these cases
    $requestBody = json_decode(file_get_contents('php://input'));

    $firstName = $requestBody->firstName;
    $lastName = $requestBody->lastName;
    $username = $requestBody->username;
    $password = $requestBody->password;

    $queryRes = "";

    // Used for testing prints info gotten from html (from Carl)
    // echo "$firstName $lastName $username $password"

    $responseObj = new stdClass();
    $responseObj->status = -1;
    
    if (empty($firstName))
    {
        return returnError($responseObj, "Error: Missing first name input.");
    }
    else if (empty($lastName))
    {
        return returnError($responseObj, "Error: Missing last name input.");
    }
    else if (empty($username))
    {
        return returnError($responseObj, "Error: Missing username input.");
    }
    else if (empty($password))
    {
        return returnError($responseObj, "Error: Missing password input.");
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
        return returnError($responseObj, "Error: Server failed to check whether username is in use.", HTTP_INTERNAL_ERROR);
    }
    
    if (!empty($queryRes))
    {
        return returnError($responseObj, "Error: Username already in use.");
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
        return returnError($responseObj, "Error: Server failed to create the user.", HTTP_INTERNAL_ERROR);
    }

    // Generate authentication cookie and send to the client
    // In order to get the DateLastLoggedIn have to check what the DB has
    if ($getLoginTime = $conn->prepare("SELECT DateLastLoggedIn FROM Users WHERE Login=?"))
    {
        $getLoginTime->bind_param("s", $username);
        $getLoginTime->execute();
        $getLoginTime->bind_result($queryRes);
        $getLoginTime->fetch();
        $getLoginTime->close();
    }
    else
    {
        return returnError($responseObj, "Error: Server failed to log in the user, but aaccount has been created.", HTTP_INTERNAL_ERROR);
    }

    $authCookie = $username . "$/$" . $queryRes;

    $response = new stdClass();
    $response->cookie = $authCookie;


    return returnAsJson($responseObj, $response);
?>