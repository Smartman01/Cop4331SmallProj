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
                    'header' => 'Content-type: application/json\r\n' . 'Accept: application/json\r\n',
                    'content' => $request,
                    'ignore_errors' => true
                )
            );
        }

        $context = stream_context_create($options);
        $result = file_get_contents(baseURL . APIDir . $endpoint, false, $context);
        
        return $result;
    }

    function echoResults($result, $testName)
    {
        // html elements to format as a string
        $success = '<span style="color:green">✓ </span>';
        $failure = '<span style="color:red">✗ </span>';
        // order of variables: result symbol, name of test
        $testResult = '<p>%s%s</p>';

        echo sprintf($testResult, $result ? $success : $failure, $testName);
    }

    // Expected outputs in string form
    $expectedResults = [
        "Login1" => '{"status":1,...}', // This one has variable return values, so only care about the status
        "Login2" => '{"status":-1,"message":"Error: Username or password failed to match."}',
        "Login3" => '{"status":-1,"message":"Error: Username or password failed to match."}',
        "Login4" => '{"status":-1,"message":"Error: Missing username input."}',
    ];

?>