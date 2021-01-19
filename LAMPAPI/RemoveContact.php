<?php
    // To interact, send POST request.
    // Endpoint for removing a contact for a user.
    // UserID: int (the id of the current user)
    // ??: maybe all of the information that comprises a contact.
    // ID: int (id of the contact to remove, if that is actually applicable for this case)

    // Ensures the contact actually exists and is assigned to the user attempting removal.

    include "DBConnect.php";
    include "ResponseLib.php";
?>