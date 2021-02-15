// Used for making post request to api
// Posts: Add, Create, Modify, Remove, Search

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
	xhr.open("POST", baseUrl + createAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        document.getElementById("success").innerHTML = jsonObject.message;

        if (jsonObject.status === -1)
        {
            return;
        }

        window.location.href = window.location.origin;
    }
    catch (err)
    {
        document.getElementById("success").innerHTML = err;
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

        document.getElementById("first_name").value = "";
        document.getElementById("last_name").value = "";
        document.getElementById("phone").value = "";
        document.getElementById("email").value = "";
    }
    catch (err)
    {
        document.getElementById("add_success").innerHTML = err;
    }
}

function edit(id)
{
    // Gets values to be editted
    let firstName = document.getElementById(`edit_first_name_${id}`).value;
    let lastName = document.getElementById(`edit_last_name_${id}`).value;
    let phone = document.getElementById(`edit_phone_${id}`).value;
    let email = document.getElementById(`edit_email_${id}`).value;

    // console.log(document.cookie);

    var jsonPayload = JSON.stringify({firstName: firstName, lastName: lastName, phone: phone, email: email, id: id});

    // Makes an api call to modify an existing contact
    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + modContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        if (firstName != "")
            document.getElementById(`fn_${id}`).innerHTML = firstName;

        if (lastName != "")
            document.getElementById(`ln_${id}`).innerHTML = lastName;

        if (phone != "")
            document.getElementById(`p_${id}`).innerHTML = phone;

        if (email != "")
            document.getElementById(`e_${id}`).innerHTML = email;

        document.getElementById(`edit_first_name_${id}`).value = "";
        document.getElementById(`edit_last_name_${id}`).value = "";
        document.getElementById(`edit_phone_${id}`).value = "";
        document.getElementById(`edit_email_${id}`).value = "";

        document.getElementById(`success_${id}`).innerHTML = jsonObject.message;
    }
    catch (err)
    {
        document.getElementById(`success_${id}`).innerHTML = err;
    }
}

// Passed the id of the contact to be deleted
function deleteContact(id)
{
    var jsonPayload = JSON.stringify({id: id});

    // Makes api call to delete
    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + removeContactAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        document.getElementById(`success_${id}`).innerHTML = jsonObject.message;

        document.getElementById(`contact_${id}`).remove();
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

    let input_type = document.getElementById("input_type").selectedIndex;

    var list = "";

    // Makes an api call to search contacts
    var xhr = new XMLHttpRequest();
	xhr.open("GET", baseUrl + searcnContactAPI + "?query=" + query + "&type=" + input_type, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    xhr.setRequestHeader("AUTH", getCookie('auth'));
    try
    {
        xhr.onreadystatechange = function() 
		{
            var jsonObject = JSON.parse( xhr.responseText );

			if (jsonObject.status == 1) 
			{
                document.getElementById("query").innerHTML = "";

                let count = 0;

                if (jsonObject.contacts.length > 0)
                {
                    for (var i = 0; i< jsonObject.contacts.length; i++)
                    {
                        
                        count++;

                        // Adds contacts to a list to be displayed
                        list += addTable(jsonObject.contacts[i], i);

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
				
                document.getElementById("search_success").innerHTML = jsonObject.message;
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

// Creates a tables for each contact that is returned by search
function addTable(contact, index)
{
    // The contact with info
    let contactInfo = `<b>Name</b>: <id="fn_${contact.id}">${contact.firstName}</>`;
    contactInfo += ` <id="ln_${contact.id}">${contact.lastName}</> <b>Phone</b>: <id="p_${contact.id}">${contact.phone}</>`;
    contactInfo += ` <b>Email</b>: <id="e_${contact.id}">${contact.email}</>`;

    // Adds the contact and delete button
    let list = `<div id="contact_${contact.id}"><fieldset>
                <legend>Contact: ${index}</legend>
                    <p id="${contact.id}">${contactInfo} <button type="submit" onclick="deleteContact(${contact.id})"><b>DELETE</b></button></p>\n`;

    // A table of input fields to edit the contact
    list += `<table>
                <tbody>
                    <tr>
                        <th><label for="edit_first_name_${contact.id}">First Name</label></th>
                        <th><label for="edit_last_name_${contact.id}">Last Name</label></th>
                        <th><label for="edit_phone_${contact.id}">Phone Number</label></th>
                        <th><label for="edit_email_${contact.id}">Email</label></th>
                    </tr>
                    <tr>
                        <th>
                            <input name="edit_first_name_${contact.id}" id="edit_first_name_${contact.id}" placeholder="Enter First Name" type="text">
                        </th>
                        <th>
                            <input name="edit_last_name_${contact.id}" id="edit_last_name_${contact.id}" placeholder="Enter Last Name" type="text">
                        </th>
                        <th>
                            <input name="edit_phone_${contact.id}" id="edit_phone_${contact.id}" placeholder="Enter Phone Number" type="number">
                        </th>
                        <th>
                            <input name="edit_email_${contact.id}" id="edit_email_${contact.id}" placeholder="Enter Email" type="email">
                        </th>
                        <th>
                            <button onclick="edit(${contact.id})"><b>EDIT</b></button>
                        </th>
                    </tr>
                </tbody>
            </table>

            <p id="success_${contact.id}"></p>
            </fieldset></div>`

    return list;
}

// Saves the authenication to the browser's cookies
function saveCookie(auth)
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "auth=" + auth + ";expires=" + date.toGMTString();
}

// Used to retrieves the a specific cookie such as the header
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