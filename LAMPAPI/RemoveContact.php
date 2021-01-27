<?php
    // To interact, send POST or DELETE request.
    // Endpoint for removing a contact for a user.
    // ID: int (id of the contact to remove)
    // Cookie: the currently assigned authentication cookie of the client

    // Ensures the contact actually exists and is assigned to the user attempting removal.

    include "DBConnect.php";
    include "ResponseLib.php";

    // Ensure that the proper request method is used
    if ($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "DELETE")
    {
        return returnWrongRequestMethod();
    }
?>