// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = "https://contactmanager.rocks/LAMPAPI";

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

        window.location.href = "https://contactmanager.rocks/html/dashboard.html";
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
	xhr.open("POST", baseUrl + createAPI, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    try
    {
        xhr.send(jsonPayload);
		
        var jsonObject = JSON.parse( xhr.responseText );

        alert("Account has been created you may login now.");

        window.location.href = "https://contactmanager.rocks";
    }
    catch (err)
    {
        alert(err);
    }
}

function add()
{
    // let firstName = document.getElementById("firstName").value;
    // let lastName = document.getElementById("lastName").value;
    // let phone = document.getElementById("phone").value;
    // let email = document.getElementById("email").value;

    // let auth = "";

    // console.log(document.cookie);

    // var jsonPayload = '{"firstName" : "' + first + '", "lastName" : "' + last + '", "phone" : "' + phone + '", "email" : "' + email + '", "auth" : "' + auth + '"}';

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + addContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );
    // }
    // catch (err)
    // {
    //     alert(err);
    // }
}

function edit()
{
    // let firstName = document.getElementById("firstName").value;
    // let lastName = document.getElementById("lastName").value;
    // let phone = document.getElementById("phone").value;
    // let email = document.getElementById("email").value;
    // let contactID = "";
        

    // let auth = "";

    // console.log(document.cookie);

    // var jsonPayload = '{"firstName" : "' + first + '", "lastName" : "' + last + '", "phone" : "' + phone + '", "email" : "' + email + '", "auth" : "' + auth + '", contactID" : "' + contactID + '"}';

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + modContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );
    // }
    // catch (err)
    // {
    //     alert(err);
    // }
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