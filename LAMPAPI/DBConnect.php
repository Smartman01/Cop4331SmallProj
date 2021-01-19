<?php
    // Not an endpoint, no requests to be made to this page
    // Simply provides a single configurtion and connection for DB interactions

    $DB_Config = parse_ini_file("DBConfig.config");
    
    if ($DB_Config != false)
    {
        $conn = new mysqli($DB_Config["IP"], $DB_Config["Username"], $DB_Config["Password"]);

        if ($conn->connect_error)
        {
            die("DB Connection failed: " . $conn->connect_error);
        }
    }
?>