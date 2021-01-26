<?php
    // To interact, send POST request.
    // Endpoint for a user to create a contact.
    // FirstName: varchar(50)
    // LastName: varchar(50)
    // Phone: varchar(50) (as a sidenote can probably shorten this, as phone numbers don't tend to be this long)
    // Email: varchar(50)
    // Cookie: the currently assigned authentication cookie of the client

    // Ensure that the exactly specified contact does not already exist


    include "DBConnect.php";
    include "ResponseLib.php";
?>