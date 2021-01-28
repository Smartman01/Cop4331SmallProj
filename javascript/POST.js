// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = "http://www.contactmanager.rocks/LAMPAPI";

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
            // document.getElementById("error").innerHTML = res.message;
            return;
        }

        saveCookie();

        window.location.replace("http://www.contactmanager.rocks/html/dashboard.html");
        
        console.log(jsonObject);
        console.log(window.location.href);
    }
    catch (err)
    {
        // document.getElementById("error").innerHTML = res.message;
        console.log(err);
    }
    
    // fetch(baseUrl + loginAPI,
    // {
    //     method: "POST",
    //     headers:
    //     {
    //         "Content-type" : "application/json; charset=UTF-8"
    //     },
    //     body:
    //     {
    //         username: username,
    //         password: password
    //     }
    // })
    // .then(res =>
    // {
    //     if (res.status === -1)
    //     {
    //         // document.getElementById("error").innerHTML = res.message;
    //         return;
    //     }

    //     // saveCookie(first, last)
    //     console.log(res);
    // })
    // .catch(res => console.log(res));
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

    // fetch(baseUrl + createAPI,
    // {
    //     method: "POST",
    //     headers:
    //     {
    //         "Content-type" : "application/json; charset=UTF-8"
    //     },
    //     body:
    //     {
    //         firstName: first,
    //         lastName: last,
    //         username: username,
    //         password: password
    //     }
    // })
    // .then(res =>
    // {
    //     console.log(res)

    //     // if (res.status === -1)
    //     // {
    //     //     document.getElementById("error").innerHTML = res.message;
    //     //     return;
    //     // }

    //     // saveCookie(first, last)
    // })
    // .catch(res => console.log(res));
}

function saveCookie(first, last, id)
{
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "logged in=" + true + ";expires=" + date.toGMTString();
}