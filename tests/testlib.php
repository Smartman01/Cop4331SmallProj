<?php
    // Library containing functions and variables used for the testing framework
    define('baseURL', 'https://contactmanager.rocks');
    define('APIDir', '/LAMPAPI');

    // Obtain "secrets" from "secure" file
    $Login_Config = parse_ini_file("/var/www/DBConfig.config");

    // function to send a dynamic request to any endpoint and return the response
    // the auth value would be obtained from running this function with an approrpriate request on Login.php
    function sendEndpointRequest($endpoint, $method, $request, $auth = "")
    {
        if ($method == 'POST')
        {
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/json\r\n" . "Accept: application/json\r\n" . (!empty($auth) ? "Auth: " . $auth . "\r\n" : ''),
                    'content' => $request,
                    'ignore_errors' => true
                )
            );
        }
        else if ($method == 'GET')
        {
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => (!empty($auth) ? "Auth: " . $auth . "\r\n" : ''),
                    'ignore_errors' => true
                )
            );
        }

        $epURL = baseURL . APIDir . $endpoint . (($method == 'GET') ? $request : '');

        $context = stream_context_create($options);
        $result = file_get_contents($epURL, false, $context);
        
        return $result;
    }

    function echoResults($result, $testName, $query, $response)
    {
        // html elements to format as a string
        $success = '<span style="color:green">✓ </span>';
        $failure = '<span style="color:red">✗ </span>';
        // order of variables: result symbol, name of test
        $testResult = '<p onclick="showResults(\'%s\',\'%s\')">%s%s</p>';

        echo sprintf($testResult, str_replace('"', '\'', addslashes($query)), str_replace('"', '\'', addslashes($response)), $result ? $success : $failure, $testName);
    }

    // Expected outputs in string form
    $expectedResults = [
        "Login1" => '{"status":1,...}', // This one has variable return values, so only care about the status
        "Login2" => '{"status":-1,"message":"Error: Username or password failed to match."}',
        "Login3" => '{"status":-1,"message":"Error: Username or password failed to match."}',
        "Login4" => '{"status":-1,"message":"Error: Missing username input."}',

        "Register1" => '{"status":-1,"message":"Error: Username already in use."}',
        "Register2" => '{"status":-1,"message":"Error: Missing username input."}',
        "Register3" => '{"status":-1,"message":"Error: Missing password input."}',

        "Add1" => '{"status":-1,"message":"Error: Missing authentication."}',
        "Add2" => '{"status":-1,"message":"Error: The passed authentication token is invalid."}',
        "Add3" => '{"status":-1,"message":"Error: This exact contact already exists."}',
        "Add4" => '{"status":-1,"message":"Error: At least one input must be provided."}',
        "Add5" => '{"status":1,"message":"Contact successfully created.","contact":{"id":%d,"firstName":"Test","lastName":"Test","phone":"Test","email":"Test"}}',

        // The expected responses tend to get lengthy from here on
        "Modify1" => '{"status":-1,"message":"Error: Missing authentication."}',
        "Modify2" => '{"status":-1,"message":"Error: The passed authentication token is invalid."}',
        "Modify3" => '{"status":1,"message":"Contact successfully updated.","contact":{"id":%d,"firstName":"Testy","lastName":"Testy","phone":"Testy","email":"Testy"}}',
        "Modify4" => '{"status":-1,"message":"Error: This desired updated contact already exists (id:%d)."}',
        "Modify5" => '{"status":1,"message":"Contact successfully updated.","contact":{"id":%d,"firstName":"Tester","lastName":"Testy","phone":"Testy","email":"Tester"}}',
        "Modify6" => '{"status":-1,"message":"Error: At least one modification must be provided."}',
        "Modify7" => '{"status":-1,"message":"Error: ID of the contact to be modified must be provided."}',
        "Modify8" => '{"status":-1,"message":"Error: The specified contact ID either does not exist or does not belong to this user."}',
        "Modify9" => '{"status":-1,"message":"Error: The specified contact ID either does not exist or does not belong to this user."}',

        "Remove1" => '{"status":-1,"message":"Error: Missing authentication."}',
        "Remove2" => '{"status":-1,"message":"Error: The passed authentication token is invalid."}',
        "Remove3" => '{"status":-1,"message":"Error: The specified contact ID either does not exist or does not belong to this user."}',
        "Remove4" => '{"status":-1,"message":"Error: The specified contact ID either does not exist or does not belong to this user."}',
        "Remove5" => '{"status":1,"message":"Contact successfully removed."}',

        "Search1" => '{"status":-1,"message":"Error: Missing authentication."}',
        "Search2" => '{"status":-1,"message":"Error: The passed authentication token is invalid."}',
        "Search3" => 'Retrieved 4 contacts.', // the entire response would be massive, only want the amount it found
        "Search4" => 'Retrieved 1 contact.', // Same as above also goes for all below
        "Search5" => 'Retrieved 1 contact.',
        "Search6" => 'Retrieved 0 contacts.',
        "Search7" => 'Retrieved 1 contact.',
        "Search8" => 'Retrieved 1 contact.',
        "Search9" => 'Retrieved 2 contacts.',
    ];

?>