<?php
    // To interact, send POST request.
    // Endpoint for removing a contact for a user.
    // ID: int (id of the contact to modify)
    // FirstName, LastName, Phone, Email (will update any field passed, does not have to be all of them)
    // Cookie: the currently assigned authentication cookie of the client

    // Ensures the contact actually exists and is assigned to the user attempting modification.

    include "DBConnect.php";
    include "ResponseLib.php";
?>