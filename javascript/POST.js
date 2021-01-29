// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = "https://www.contactmanager.rocks/LAMPAPI";

const loginAPI = "/Login.php"
const createAPI = "/CreateUser.php"
const addContactAPI = "/AddContact.php"
const modContactAPI = "/ModifyContact.php"
const removeContactAPI = "/RemoveContact.php"

function login() 
{
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    var jsonPayload = '{"username" : "' + username + '", "password" : "' + password + '"}';

    var xhr = new XMLHttpRequest();
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

        saveCookie();

        window.location.href = "https://www.contactmanager.rocks/html/dashboard.html";
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

    var jsonPayload = '{"firstName" : "' + first + '", "lastName" : "' + last + '", "username" : "' + username + '", "password" : "' + password + '"}';

    var xhr = new XMLHttpRequest();
	xhr.open("POST", baseUrl + createAPI, false);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );
        
        console.log(jsonObject);
    }
    catch (err)
    {
        console.log(err);
    }
}

function saveCookie(first, last, id)
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "logged in=" + true + ";expires=" + date.toGMTString();
}