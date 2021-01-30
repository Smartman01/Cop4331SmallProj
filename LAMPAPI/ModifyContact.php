<?php
    // To interact, send POST or PATCH request.
    // Endpoint for removing a contact for a user.
    // ID: int (id of the contact to modify)
    // FirstName, LastName, Phone, Email (will update any field passed, does not have to be all of them)
    // Cookie: the currently assigned authentication cookie of the client

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "PATCH")
    {
        return returnWrongRequestMethod();
    }
?>