<?php
    // Not an endpoint, no requests to be made to this page
    // Simply provides a single configurtion and connection for DB interactions

    $DB_Config = parse_ini_file("/var/www/DBConfig.config"));
    
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
            return -1;
        }

        if (empty($queryRes))
        {
            return -1;
        }

        return $queryRes;
    }
?>