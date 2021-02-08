<?php
    // Not an endpoint, no requests to be made to this page
    // Provides functions for managing json requests and responses

    // Constants for readible code in endpoints that include this
    define("HTTP_BAD_REQUEST", 400);
    define("HTTP_INTERNAL_ERROR", 500);

    // Set the header of any endpoints that include this to have the correct Content-Type
    header('Content-type: application/json');

    // Obtain the current auth header if it exists
    $auth_header = $_SERVER['HTTP_AUTH'];

    // Define a contact class to form responses easier
    class contact
    {
        public int $id;
        public $firstName;
        public $lastName;
        public $phone;
        public $email;

        function __construct(int $id, $firstName, $lastName, $phone, $email)
        {
            $this->id = $id;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->phone = $phone;
            $this->email = $email;
        }
    }

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
        header("HTTP/1.1 405 Method Not Allowed");

        $response = new stdClass();
        $response->status = -1;
        $response->message = "Error: The wrong request method was used for this endpoint.";

        echo json_encode($response);

        return -1;
    }

    function returnAsJson($responseObj, int $status = 1)
    {
        $responseObj->status = $status;

        echo json_encode($responseObj);

        return 1;
    }

?>