// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = window.location.origin + "/LAMPAPI";

const loginAPI = "/Login.php"
const createAPI = "/CreateUser.php"
const addContactAPI = "/AddContact.php"
const modContactAPI = "/ModifyContact.php"
const removeContactAPI = "/RemoveContact.php"
const searcnContactAPI = "/SearchContacts.php"

function login() 
{
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    var jsonPayload = JSON.stringify({username: username, password: password});

    var xhr = new XMLHttpRequest();
    // The false passed here makes it a synchronous request, which I enabled for simplicity's sake
    // Messing with the response asynchronously requires a tiny bit of rewriting
	xhr.open("POST", baseUrl + loginAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        if (jsonObject.status !== 1)
        {
            document.getElementById("error").innerHTML = jsonObject.message;
            return;
        }

        saveCookie(jsonObject.response.cookie);

        window.location.href = window.location.origin + "/html/dashboard.html";
    }
    catch (err)
    {
        document.getElementById("error").innerHTML = err;
    }
}

function register()
{
    let first = document.getElementById("first_name").value;
    let last = document.getElementById("last_name").value;
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    var jsonPayload = JSON.stringify({firstName: first, lastName: last, username: username, password: password});

    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + createAPI, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        alert("Account has been created you may login now.");

        window.location.href = window.location.origin;
    }
    catch (err)
    {
        document.getElementById("error").innerHTML = err;
    }
}

function add()
{
    let firstName = document.getElementById("first_name").value;
    let lastName = document.getElementById("last_name").value;
    let phone = document.getElementById("phone").value;
    let email = document.getElementById("email").value;

    var jsonPayload = JSON.stringify({firstName: firstName, lastName: lastName, phone: phone, email: email});

    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + addContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        document.getElementById("add_success").innerHTML = jsonObject.message;
    }
    catch (err)
    {
        document.getElementById("add_success").innerHTML = err;
    }
}

function edit(id)
{
    let firstName = document.getElementById(`edit_first_name_${id}`).value;
    let lastName = document.getElementById(`edit_last_name_${id}`).value;
    let phone = document.getElementById(`edit_phone_${id}`).value;
    let email = document.getElementById(`edit_email_${id}`).value;

    console.log(document.cookie);

    var jsonPayload = JSON.stringify({firstName: firstName, lastName: lastName, phone: phone, email: email, id: id});

    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + modContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        document.getElementById(`success_${id}`).innerHTML = jsonObject.message + " reload page and search again to see the edit.";
    }
    catch (err)
    {
        document.getElementById(`success_${id}`).innerHTML = err;
    }
}

function deleteContact(id)
{
    var jsonPayload = JSON.stringify({id: id});

    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + removeContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        document.getElementById(`success_${id}`).innerHTML = jsonObject.message + " reload page to see deletion";
    }
    catch (err)
    {
        document.getElementById(`success_${id}`).innerHTML = err;
    }
}

function searchContact()
{
    let query = document.getElementById("query").value;

    var jsonPayload = JSON.stringify({query: query});

    var list = "";

    var xhr = new XMLHttpRequest();
	xhr.open("GET", baseUrl + searcnContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.onreadystatechange = function() 
		{
            var jsonObject = JSON.parse( xhr.responseText );

			if (jsonObject.status == 1) 
			{
                if (jsonObject.contacts.length > 0)
                {
                    for (var i = 0; i< jsonObject.contacts.length; i++)
                    {
                        let contact = `<b>Name</b>: ${jsonObject.contacts[i].firstName} ${jsonObject.contacts[i].lastName} <b>Phone</b>: ${jsonObject.contacts[i].phone} <b>Email</b>: ${jsonObject.contacts[i].email}`;
                        list += `<fieldset>
                                    <legend>Add a Contact</legend>
                                        <p id="${jsonObject.contacts[i].id}">${contact} <button type="submit" onclick="deleteContact(${jsonObject.contacts[i].id})"><b>DELETE</b></button></p>\n ${addTable(jsonObject.contacts[i].id)}`;

                        if (i < jsonObject.contacts.length - 1)
                        {
                            list += "<br />";
                        }
                    }
                }
                else
                {
                    list = "Try adding some contacts first!";
                }
				
				document.getElementById("searchResults").innerHTML = list;
			}
		};
		xhr.send(jsonPayload);
    }
    catch (err)
    {
        document.getElementById("search_success").innerHTML = err;
    }
}

function addTable(id)
{
    return `<table>
                <tbody>
                    <tr>
                        <th><label for="edit_first_name_${id}">First Name</label></th>
                        <th><label for="edit_last_name_${id}">Last Name</label></th>
                        <th><label for="edit_phone_${id}">Phone Number</label></th>
                        <th><label for="edit_email_${id}">Email</label></th>
                    </tr>
                    <tr>
                        <th>
                            <input name="edit_first_name_${id}" id="edit_first_name_${id}" placeholder="Enter First Name" type="text">
                        </th>
                        <th>
                            <input name="edit_last_name_${id}" id="edit_last_name_${id}" placeholder="Enter Last Name" type="text">
                        </th>
                        <th>
                            <input name="edit_phone_${id}" id="edit_phone_${id}" placeholder="Enter Phone Number" type="number">
                        </th>
                        <th>
                            <input name="edit_email_${id}" id="edit_email_${id}" placeholder="Enter Email" type="email">
                        </th>
                        <th>
                            <button onclick="edit(${id})"><b>EDIT</b></button>
                        </th>
                    </tr>
                </tbody>
            </table>
            
            <p id="success_${id}"></p>
            </fieldset>`
}

function saveCookie(auth)
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "auth=" + auth + ";expires=" + date.toGMTString();
}

function getCookie(cookieName) 
{
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(';');
    for(var i = 0; i < cookieArray.length; i++) {
      var c = cookieArray[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}