<?php
    // To interact, send POST request.
    // Endpoint for removing a contact for a user.
    // UserID: int (the id of the current user)
    // ID: int (id of the contact to modify, if that is actually applicable for this case)
    // FirstName, LastName, Phone, Email (will update any field passed)

    // Ensures the contact actually exists and is assigned to the user attempting removal.

    include "DBConnect.php";
?>