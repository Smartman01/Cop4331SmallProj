// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = window.location.origin + "/LAMPAPI";

const loginAPI = "/Login.php"
const createAPI = "/CreateUser.php"
const addContactAPI = "/AddContact.php"
const modContactAPI = "/ModifyContact.php"
const removeContactAPI = "/RemoveContact.php"

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
    // let firstName = document.getElementById("firstName").value;
    // let lastName = document.getElementById("lastName").value;
    // let phone = document.getElementById("phone").value;
    // let email = document.getElementById("email").value;

    // var jsonPayload = JSON.stringify({firstName: first, lastName: last, phone: phone, email: email});

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + addContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );

    //     document.getElementById("Success").innerHTML = jsonObject.message;
    // }
    // catch (err)
    // {
    //     document.getElementById("error").innerHTML = err;
    // }
}

function edit()
{
    // let firstName = document.getElementById("firstName").value;
    // let lastName = document.getElementById("lastName").value;
    // let phone = document.getElementById("phone").value;
    // let email = document.getElementById("email").value;
    // let id = document.getElementById("selectedID").value;

    // console.log(document.cookie);

    // var jsonPayload = JSON.stringify({firstName: first, lastName: last, phone: phone, email: email, id: id});

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + modContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );

    //     document.getElementById("Success").innerHTML = jsonObject.message;
    // }
    // catch (err)
    // {
    //     document.getElementById("error").innerHTML = err;
    // }
}

function deleteContact(id)
{
    // let id = document.getElementById("selectedID").value;

    // var jsonPayload = JSON.stringify({id: id});

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + modContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );

    //     document.getElementById("Success").innerHTML = jsonObject.message;
    // }
    // catch (err)
    // {
    //     document.getElementById("error").innerHTML = err;
    // }
}

function searchContact()
{
    // let query = document.getElementById("query").value;

    // var jsonPayload = JSON.stringify({query: query});

    // var list = "";

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + modContactAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // xhr.setRequestHeader("AUTH", getCookie('auth'));
    // try
    // {
    //     xhr.onreadystatechange = function() 
	// 	{
    //         var jsonObject = JSON.parse( xhr.responseText );

	// 		if (jsonObject.status == 1) 
	// 		{
    //             document.getElementById("Success").innerHTML = jsonObject.message;
				
	// 			for (var i = 0; i< jsonObject.contacts.length; i++)
	// 			{
    //                 let contact = `Name: ${jsonObject.contacts[i].firstName} ${jsonObject.contacts[i].lastName} Phone: ${jsonObject.contacts[i].phone} Email: ${jsonObject.contacts[i].email} ID: ${jsonObject.contacts[i].id}`
	// 				list += `<p id="${jsonObject.contacts[i].id}">${contact} <button type="submit" onclick="${deleteContact(jsonObject.contacts[i].id)}">Delete Contact</button></p>`;

	// 				if (i < jsonObject.results.length - 1)
	// 				{
	// 					list += "<br />\r\n";
	// 				}
	// 			}
				
	// 			document.getElementById("searchResults").innerHTML = list;
	// 		}
	// 	};
	// 	xhr.send(jsonPayload);
    // }
    // catch (err)
    // {
    //     document.getElementById("error").innerHTML = err;
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