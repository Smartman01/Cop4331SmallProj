// Used for making post request to api
// Posts: Add, Create, Modify, Remove

const baseUrl = "http://174.138.56.41/LAMPAPI";

const loginAPI = "/Login.php"
const createAPI = "/CreateUser.php"
const addContactAPI = "/AddContact.php"
const modContactAPI = "/ModifyContact.php"
const removeContactAPI = "/RemoveContact.php"

function login() 
{
    let username = document.getElementById("userlogin").value;
    let password = document.getElementById("userpassword").value;

    // var jsonPayload = '{"username" : "' + username + '", "password" : "' + password + '"}';

    // var xhr = new XMLHttpRequest();
	// xhr.open("POST", baseUrl + loginAPI, false);
    // xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    // try
    // {
    //     xhr.send(jsonPayload);
		
    //     var jsonObject = JSON.parse( xhr.responseText );
        
    //     alert(jsonObject);
    // }
    // catch (err)
    // {
    //     alert(err);
    // }
    
    
    fetch(baseUrl + loginAPI,
    {
        method: "POST",
        headers:
        {
            "Content-type" : "application/json; charset=UTF-8"
        },
        body:
        {
            username: username,
            password: password
        }
    })
    .then(res =>
    {
        alert(res);
    })
    .catch(res => alert(res));
}

function register()
{
    let first = document.getElementById("first_name").value;
    let last = document.getElementById("last_name").value;
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    fetch(baseUrl + createAPI,
    {
        method: "POST",
        headers:
        {
            "Content-type" : "application/json; charset=UTF-8"
        },
        body:
        {
            firstName: first,
            lastName: last,
            username: username,
            password: password
        }
    })
    .then(res =>
    {
        alert(res);
    })
    .catch(res => alert(res));
}