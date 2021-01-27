<?php
    // To interact, send GET request.
    // Endpoint for searching contacts based on data entered.
    // input: varchar(100) (most you should enter is 100 chars (firstname + lastname length))
    // Cookie: the currently assigned authentication cookie of the client
    // Returns the list of matched contacts belonging to the current user in JSON format.
    
    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "GET")
    {
        return returnWrongRequestMethod();
    }
?>