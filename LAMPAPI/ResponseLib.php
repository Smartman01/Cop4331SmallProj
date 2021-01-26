<?php
    // Not an endpoint, no requests to be made to this page
    // Provides functions for managing json requests and responses
    header('Content-type: application/json');

    function fireError($responseObj, $msg, int $status = -1) 
    {
        header("HTTP/1.1 400 Bad Request");
        $responseObj->message = $msg;
        $responseObj->status = $status;

        echo json_encode($responseObj);
        return -1;
    }

    function returnAsJson($responseObj, $content, int $status = 1)
    {
        $responseObj->response = $content;
        $responseObj->status = $status;

        echo json_encode($responseObj);
        return 1;
    }

?>