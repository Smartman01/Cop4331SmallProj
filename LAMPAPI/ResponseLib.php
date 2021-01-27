<?php
    // Not an endpoint, no requests to be made to this page
    // Provides functions for managing json requests and responses

    // Constants for readible code in endpoints that include this
    define("HTTP_BAD_REQUEST", 400);
    define("HTTP_INTERNAL_ERROR", 500);

    // Set the header of any endpoints that include this to have the correct Content-Type
    header('Content-type: application/json');

    function returnError($responseObj, $msg, int $responseCode = HTTP_BAD_REQUEST, int $status = -1) 
    {
        // Set the header to indicate the request failed or was invalid
        if ($responseCode == HTTP_BAD_REQUEST)
        {
            header("HTTP/1.1 400 Bad Request");
        }
        else if ($responseCode == HTTP_INTERNAL_ERROR)
        {
            header("HTTP/1.1 500 Internal Server Error");
        }
        else
        {
            header("HTTP/1.1 " . $responseCode);
        }

        $responseObj->message = $msg;
        $responseObj->status = $status;

        echo json_encode($responseObj);

        return -1;
    }

    function returnWrongRequestMethod()
    {
        header("HTTP/1.1 400 Bad Request");

        $response = new stdClass();
        $response->status = -1;
        $response->message = "Error: The wrong request method was used for this endpoint.";

        echo json_encode($response);

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