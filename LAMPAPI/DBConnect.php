<?php
    // Not an endpoint, no requests to be made to this page
    // Simply provides a single configurtion and connection for DB interactions

    $DB_Config = parse_ini_file("/var/www/DBConfig.config");
    
    if ($DB_Config != false)
    {
        $conn = new mysqli($DB_Config["IP"], $DB_Config["Username"], $DB_Config["Password"], $DB_Config["DBName"]);

        if ($conn->connect_error)
        {
            die("DB Connection failed: " . $conn->connect_error);
        }
    }


    // Ensure that $auth correlates to a real user and is valid
    // $auth consists of a username and a timestamp, which is separated by the sequence "$/$"
    // Returns -1 if the authorization is not valid, -2 if there was a server error, or userID otherwise
    function isAuthenticated($auth, $conn)
    {
        $queryRes = "";
        $auth = explode("$/$", $auth);
        if ($getAuthUser = $conn->prepare("SELECT ID FROM Users WHERE (Login, DateLastLoggedIn) IN ((?,?))"))
        {
            $getAuthUser->bind_param("ss", $auth[0], $auth[1]);
            $getAuthUser->execute();
            $getAuthUser->bind_result($queryRes);
            $getAuthUser->fetch();
            $getAuthUser->close();
        }
        else
        {
            return -2;
        }

        if (empty($queryRes))
        {
            return -1;
        }

        return $queryRes;
    }

    // Check whether or not an exactly specified record exists or not
    // Returns -1 if it does not, -2 if there was a server error, the ID of the record otherwise
    function contactExists($firstName, $lastName, $phone, $email, $userID)
    {
        // Some type-mixing going on with $returnVal
        $returnVal = "";
        if ($getContact = $conn->prepare("SELECT ID FROM Contacts WHERE (FirstName, LastName, Phone, Email, UserID) IN ((?,?,?,?,?))"))
        {
            $getContact->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userID);
            $getContact->execute();
            $getContact->bind_result($returnVal);
            $getContact->fetch();
            $getContact->close();
        }
        else
        {
            $returnVal = -2;
        }

        if (empty($returnVal))
        {
            $returnVal = -1;
        }

        return $returnVal;
    }
?>