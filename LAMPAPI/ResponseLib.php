<?php
    // Not an endpoint, no requests to be made to this page
    // Provides functions for managing json requests and responses

    function fireError($responseObj, $msg, int $status = -1) 
    {
        $responseObj->message = $msg;
        $responseObj->status = $status;

        echo json_encode($responseObj);
        return -1;
    }

?>