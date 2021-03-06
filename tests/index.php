<!DOCTYPE html>
<html>
    <head>
        <title>contact manager API tests</title>
        <meta content property="og:title">
    	<meta content="#fa19b6" name="theme-color">
    	<meta charset="UTF-8">
    	<meta content="width=device-width, initial-scale=1" name="viewport">
        <style>
    		body {
    			max-width: 400px;
    			margin: 40px auto;
    			line-height: 1;
    			padding: 0 10px;
    			background: #151414;
    			color: #EFFFFF;
    			font-family: 'Mona';
    			font-size: 20px;
    		}
    		a {
    			text-decoration: none;
    		}
    		h1 {
    			font-family: sans-serif;
    		}
			h2 {
				color: #87cece;
				font-family: sans-serif;
			}
			aside {
				color: #f3f3c7;
			}
    	</style>
		<script type="text/javascript">
			var counter = 0;

			// basic function for displaying results of a query when wanted
			function showResults(query, response)
			{
				var resultBox = document.getElementById("results");
				var queryBox = document.getElementById("query");
				var responseBox = document.getElementById("response");

				resultBox.removeAttribute("hidden");
				queryBox.innerHTML = query;
				responseBox.innerHTML = response;
			}

			// i am an expert at wasting time
			function funAPIDemoTitle(element)
			{
				if (counter > 0)
				{
					return;
				}
				element = document.getElementsByTagName("h1")[0];
				var interval = setInterval(function() {
					counter++;

					element.style.textShadow = (-1000 + 5*counter) + 'px' + ' 0px' + ' cyan, ' + (1000 - 5*counter) + 'px' + ' 0px' + ' magenta';

					if (counter > 400)
					{
						clearInterval(interval);
						element.style.textShadow = null;
						counter = 0;
					}
				}, 5);
			}
		</script>
    </head>

    <body>
		<div id="results" style="position:fixed;margin-left:500px;margin-top:225px;max-width:600px;max-height:1000px;overflow-x:auto;overflow-y:hidden;" hidden>
			<p>Sent query:</p>
			<code id="query">lol</code>
			<p>Server responded:</p>
			<code id="response">lol</code>
		</div>
        <h1>API Demo</h1>
		<aside>note: none of the test cases will work past login and registration if login fails</aside>
        <p>&nbsp;</p>
        <div>
            <?php
				include "testlib.php";

				$auth = "";
				$contactID = -1;
				// Begin Login.php test
				// Testing:
				//		1. Login returns a token on correct authentication 
				//		2. Login returns an error on invalid username input
				//		3. Login returns an error on invalid password input
				//		4. Login returns an error on no input 
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Login</h2>";

					$testName = 'Login returns a token on correct authentication';
					$query = sprintf('{"username": "%s", "password": "%s"}', $Login_Config['APIUname'], $Login_Config['APIpword']);
					$result = json_decode(sendEndpointRequest('/Login.php', 'POST', $query));
					echoResults($result->status == 1, $testName, sprintf('{"username": "%s", "password": "XXXXX"}', $Login_Config['APIUname']), json_encode($result));
					if ($result->status == 1)
					{
						$auth = $result->response->cookie;
					}

					$testName = 'Login returns an error on invalid username input';
					$query = sprintf('{"username": "%s", "password": "%s"}', '-2131-258158', $Login_Config['APIpword']);
					$result = sendEndpointRequest('/Login.php', 'POST', $query);
					echoResults($result == $expectedResults['Login2'], $testName, sprintf('{"username": "%s", "password": "%s"}', '-2131-258158', 'XXXXX'), $result);

					$testName = 'Login returns an error in invalid password input';
					$query = sprintf('{"username": "%s", "password": "%s"}', $Login_Config['APIUname'], '-2131-258158');
					$result = sendEndpointRequest('/Login.php', 'POST', $query);
					echoResults($result == $expectedResults['Login3'], $testName, $query, $result);

					$testName = 'Login returns an error on no input';
					$query = '';
					$result = sendEndpointRequest('/Login.php', 'POST', $query);
					echoResults($result == $expectedResults['Login4'], $testName, $query, $result);
					
					// Show how long it took to test this endpoint
					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}

				// Begin CreateUser.php test
				// Testing:
				// 		1. Disallows duplicate accounts
				//		2. Not passing a username
				//		3. Not passing a password
				// Not Testing:
				//		* Actual account creation, cause that would eat up database space every time this test page was used
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Registration</h2>";

					$testName = 'Register disallows duplicate account creation';
					$query = sprintf('{"username": "%s", "password": "%s", "firstName": "Test", "lastName": "Test"}', $Login_Config['APIUname'], '-2131-258158');
					$result = sendEndpointRequest('/CreateUser.php', 'POST', $query);
					echoResults($result == $expectedResults['Register1'], $testName, $query, $result);

					$testName = 'Register disallows account creation with no username';
					$query = sprintf('{"username": "%s", "password": "%s", "firstName": "Test", "lastName": "Test"}', '', '-2131-258158');
					$result = sendEndpointRequest('/CreateUser.php', 'POST', $query);
					echoResults($result == $expectedResults['Register2'], $testName, $query, $result);

					$testName = 'Register disallows account creation with no password';
					$query = sprintf('{"username": "%s", "password": "%s", "firstName": "Test", "lastName": "Test"}', $Login_Config['APIUname'], '');
					$result = sendEndpointRequest('/CreateUser.php', 'POST', $query);
					echoResults($result == $expectedResults['Register3'], $testName, $query, $result);

					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}

				// Begin AddContact.php test
				// Testing:
				// 		1. Adding a contact with no auth token
				//		2. Adding a contact with an invalid auth token
				// 		3. Adding a duplicate contact
				//		4. Adding a contact but passing no parameters
				//		5. Adding a contact properly
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Adding Contacts</h2>";
					echo "<aside>testing deletion and modification will hinge on the success of these tests</aside>";

					$testName = 'Disallows adding a contact if no authorization';
					$query = '{"firstName": "Test", "lastName": "Test", "phone": "Test", "email": "Test"}';
					$result = sendEndpointRequest('/AddContact.php', 'POST', $query);
					echoResults($result == $expectedResults['Add1'], $testName, $query, $result);

					$testName = 'Disallows adding a contact if invalid authorization';
					$query = '{"firstName": "Test", "lastName": "Test", "phone": "Test", "email": "Test"}';
					$result = sendEndpointRequest('/AddContact.php', 'POST', $query, 'wrongAuth');
					echoResults($result == $expectedResults['Add2'], $testName, $query, $result);

					$testName = 'Disallows adding a contact with no parameters';
					$query = '{"firstName": "", "lastName": "", "phone": "", "email": ""}';
					$result = sendEndpointRequest('/AddContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Add4'], $testName, $query, $result);

					$testName = 'Allows adding a contact with proper parameters';
					$query = '{"firstName": "Test", "lastName": "Test", "phone": "Test", "email": "Test"}';
					$result = json_decode(sendEndpointRequest('/AddContact.php', 'POST', $query, $auth));
					echoResults($result->status == 1, $testName, $query, json_encode($result));
					$contactID = $result->contact->id;

					$testName = 'Disallows adding a duplicate contact';
					$query = '{"firstName": "Test", "lastName": "Test", "phone": "Test", "email": "Test"}';
					$result = sendEndpointRequest('/AddContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Add3'], $testName, $query, $result);

					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}

				// Begin ModifyContact.php test
				// Testing:
				// 		1. Modifying a contact with no auth token
				// 		2. Modifying a contact with an invalid auth token
				//		3. Modifying a contact to a new unique contact
				//		4. Modifying a contact to a duplicate contact
				//		5. Modifying a contact with a field not filled out
				//		6. Modifying a contact with no fields filled
				//		7. Modifying a contact with no id passed
				// 		8. Modifying a contact that belongs to another user
				//		9. Modifying a contact that does not exist
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Modifying Contacts</h2>";

					$testName = 'Disallows modifying a contact if no authorization';
					$query = sprintf('{"id": %d, "firstName": "Testy", "lastName": "Testy", "phone": "Testy", "email": "Testy"}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query);
					echoResults($result == $expectedResults['Modify1'], $testName, $query, $result);

					$testName = 'Disallows modifying a contact if invalid authorization';
					$query = sprintf('{"id": %d, "firstName": "Testy", "lastName": "Testy", "phone": "Testy", "email": "Testy"}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, 'wrongAuth');
					echoResults($result == $expectedResults['Modify2'], $testName, $query, $result);

					$testName = 'Properly modifies a contact with complete fields';
					$query = sprintf('{"id": %d, "firstName": "Testy", "lastName": "Testy", "phone": "Testy", "email": "Testy"}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == sprintf($expectedResults['Modify3'], $contactID), $testName, $query, $result);

					$testName = 'Disallows modifying a contact if the target contact already exists';
					$query = sprintf('{"id": %d, "firstName": "Testy", "lastName": "Testy", "phone": "Testy", "email": "Testy"}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == sprintf($expectedResults['Modify4'], $contactID), $testName, $query, $result);

					$testName = 'Properly modifies a contact with missing fields';
					$query = sprintf('{"id": %d, "firstName": "Tester", "phone": "Testy", "email": "Tester"}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == sprintf($expectedResults['Modify5'], $contactID), $testName, $query, $result);

					$testName = 'Disallows modifying a contact if there are no fields passed';
					$query = sprintf('{"id": %d}', $contactID);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Modify6'], $testName, $query, $result);

					$testName = 'Disallows modifying a contact if contact ID is not provided';
					$query = '{"firstName": "Testy", "lastName": "Testy", "phone": "Testy", "email": "Testy"}';
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Modify7'], $testName, $query, $result);

					$testName = 'Disallows modifying a contact that does not belong to the authorized user';
					$query = sprintf('{"id": %d, "firstName": "Tester", "phone": "Testy", "email": "Tester"}', 11);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Modify8'], $testName, $query, $result);

					$testName = 'Disallows modifying a contact that does not exist';
					$query = sprintf('{"id": %d, "firstName": "Tester", "phone": "Testy", "email": "Tester"}', -1);
					$result = sendEndpointRequest('/ModifyContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Modify9'], $testName, $query, $result);

					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}

				// Begin RemoveContact.php test
				// Testing:
				// 		1. Deleting a contact with no authorization
				// 		2. Deleting a contact with invalid authorization
				// 		3. Deleting a contact that does not belong to the user
				// 		4. Deleting a contact that does not exist
				// 		5. Deleting a contact that belongs to the user
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Deleting Contacts</h2>";

					$testName = 'Disallows deleting a contact if no authorization';
					$query = sprintf('{"id": %d}', $contactID);
					$result = sendEndpointRequest('/RemoveContact.php', 'POST', $query);
					echoResults($result == $expectedResults['Remove1'], $testName, $query, $result);

					$testName = 'Disallows deleting a contact if invalid authorization';
					$query = sprintf('{"id": %d}', $contactID);
					$result = sendEndpointRequest('/RemoveContact.php', 'POST', $query, 'wrongAuth');
					echoResults($result == $expectedResults['Remove2'], $testName, $query, $result);

					$testName = 'Disallows deleting a contact that does not belong to the user';
					$query = sprintf('{"id": %d}', 11);
					$result = sendEndpointRequest('/RemoveContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Remove3'], $testName, $query, $result);

					$testName = 'Disallows deleting a contact that does not exist';
					$query = sprintf('{"id": %d}', -1);
					$result = sendEndpointRequest('/RemoveContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Remove4'], $testName, $query, $result);

					$testName = 'Properly deletes a contact that belongs to the user';
					$query = sprintf('{"id": %d}', $contactID);
					$result = sendEndpointRequest('/RemoveContact.php', 'POST', $query, $auth);
					echoResults($result == $expectedResults['Remove5'], $testName, $query, $result);

					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}

				// Begin SearchContacts.php test
				// Testing:
				// 		1. Querying with no authorization
				// 		2. Querying with invalid authorization
				// 		3. Querying with an empty query
				//		4. Querying with a specific string
				//		5. Querying firstName%20lastName
				//		6. Querying with random generated gibberish (no results)
				// 		7. Querying type 1 (name)
				//		8. Querying type 2 (phone)
				//		9. Querying type 3 (email)
				{
					$time = round(microtime(true) * 1000);

					echo "<h2>Searching Contacts</h2>";

					$testName = 'Disallows querying if no authorization';
					$query = '?query=';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query);
					echoResults($result == $expectedResults['Search1'], $testName, $query, $result);

					$testName = 'Disallows querying if invalid authorization';
					$query = '?query=';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, 'wrongAuth');
					echoResults($result == $expectedResults['Search2'], $testName, $query, $result);

					$testName = 'Querying with an empty string returns all contacts';
					$query = '?query=';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search3'], $testName, $query, $result);

					$testName = 'Querying with a specific string returns that contact';
					$query = '?query=johnathan';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search4'], $testName, $query, $result);

					$testName = 'Querying a first name last name pair returns that contact';
					$query = '?query=john%20doe';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search5'], $testName, $query, $result);

					$testName = 'Querying with random gibberish that should not exist';
					$query = '?query=ioduay3397raaghadakldfj839a27a4892uqjdjqdfiou0-2372057-125235uopi12u350-12375826ufsj';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search6'], $testName, $query, $result);

					$testName = 'Querying only a name (type=1)';
					$query = '?query=jane%20doe&type=1';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search7'], $testName, $query, $result);

					$testName = 'Querying only a phone number (type=2)';
					$query = '?query=99999&type=2';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search8'], $testName, $query, $result);

					$testName = 'Querying only an email (type=3)';
					$query = '?query=gmail&type=3';
					$result = sendEndpointRequest('/SearchContacts.php', 'GET', $query, $auth);
					echoResults(json_decode($result)->message == $expectedResults['Search9'], $testName, $query, $result);
					
					echo "<aside>" . (round(microtime(true) * 1000) - $time) . " ms</aside>";
				}
			?>
        </div>
    </body>
</html>