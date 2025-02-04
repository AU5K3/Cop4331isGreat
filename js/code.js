const urlBase = 'http://cop4331isgreat.xyz/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
//	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

	let tmp = {login:login,password:password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "main.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function doRegister()
{
	let login = document.getElementById("registerName").value;
	let password = document.getElementById("registerPassword").value;
	let firstName = document.getElementById("firstName").value;
	let lastName = document.getElementById("lastName").value;
//	var hash = md5( password );
	
	document.getElementById("registerResult").innerHTML = "";

	let tmp = {login:login,password:password,firstName:firstName,lastName:lastName};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Register.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("registerResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "main.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("registerResult").innerHTML = err.message;
	}

}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	let firstName = document.getElementById("firstName").value;
	let lastName = document.getElementById("lastName").value;
	let phone = document.getElementById("phoneNum").value;
	let email = document.getElementById("email").value;
	console.log(phone);

	document.getElementById("addResult").innerHTML = "";
	console.log(userId);
	let tmp = {firstName:firstName,lastName:lastName,phone:phone,email:email,UserID:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("addResult").innerHTML = "Contact has been added";
				location.reload();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("addResult").innerHTML = err.message;
	}
	
}

function searchContacts()
{
	let srch = document.getElementById("searchText").value;
	document.getElementById("searchResult").innerHTML = "";
	
	let contactList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchContacts.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				//document.getElementById("searchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				console.log(JSON.stringify(jsonObject));
				let searchTable = document.createElement('table');
				searchTable.innerHTML = `
					<table id="searchTable">
						<thead>
							<tr>
								<th>First Name</th>
								<th>Last name</th>
								<th>Phone Number</th>
								<th>Email Address</th>
							</tr>
						</thead>
					</table>
				`;
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					currContact = jsonObject.results[i];
					const row = searchTable.insertRow();

					for (const key in currContact) {
						//const cell = document.createElement("td");
						//cell.textContent = currContact[key];
						row.insertCell().innerText = currContact[key];
					}
				}
				document.getElementById('searchResult').appendChild(searchTable);
				//document.getElementsByTagName("p")[0].innerHTML = contactList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("searchResult").innerHTML = err.message;
	}
	
}

function addContactDropdown() {
	const newContainer = document.getElementById("newContactContainer");
	newContainer.innerHTML = `
		<div id="newContact>
			<label for="firstName">First Name: </label><br>
			<input type="text" id="firstName" name="firstName" required><br><br>
			<label for="lastName">Last Name: </label><br>
			<input type="text" id="lastName" name="lastName" required><br><br>
			<label for="phoneNum">Phone Number: </label><br>
			<input type="text" id="phoneNum" name="phoneNum" required><br><br>
			<label for="email">Email Address: </label><br>
			<input type="text" id="email" name="email" required><br><br>
			<button type="button" id= class="buttons" onclick="addContact();">Create Contact</button>
		</div>`;
}

function loadContacts()
{
	//let srch = document.getElementById("searchText").value;
	//document.getElementById("searchResult").innerHTML = "";
	
	//let contactList = "";

	let tmp = {userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/LoadContacts.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				//document.getElementById("searchResult").innerHTML = "Color(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				console.log(JSON.stringify(jsonObject));
				
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					formatData(jsonObject.results[i]);
					//let readableJson = JSON.stringify(jsonObject.results[i], null, "<br />\r\n");
					//contactList = readableJson;
					//console.log(readableJson);
					/*
					if( i < jsonObject.results.length - 1 )
					{
						contactList += "<br />\r\n";
					}
						*/
				}
				
				//document.getElementsByTagName("p")[1].innerHTML = contactList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
	
}

function formatData(currContact) {
	const tableBody = document.querySelector("#contactTable tbody");
	const row = document.createElement("tr");

	for (const key in currContact) {
		const cell = document.createElement("td");
		cell.textContent = currContact[key];
		row.appendChild(cell);
	}

	const buttonBox = document.createElement("td");
	const deleteButton = document.createElement("button");
	deleteButton.innerHTML = '&#10006;';
	deleteButton.classList.add("buttons");

	deleteButton.addEventListener("click", function() {
		console.log(currContact);
		deleteContact(currContact)
		row.remove();
	});
	buttonBox.appendChild(deleteButton);

	//deleteBox.innerHTML = `<button type="button" class="buttons" onclick="deleteContact();">'&#10006;'</button>`;

	const updateButton = document.createElement("button");
	updateButton.innerHTML = '&#128393;';
	updateButton.classList.add("buttons");

	const updateRow = document.createElement("tr");
	updateRow.style.display = "none";
	const updateCell = document.createElement("td");
	updateCell.colSpan = 5;

	updateRow.appendChild(updateCell);
	updateCell.innerHTML = `
		<div id="updateContact">
			<label for="phoneNum">New Phone Number: </label><br>
			<input type="text" id="newPhoneNum" name="phoneNum" value="${currContact.Phone}" required><br><br>
			<label for="email">New Email Address: </label><br>
			<input type="text" id="newEmail" name="email" value="${currContact.Email}" required><br><br>
			<button type="button" class="buttons save-update-btn">Save Contact</button>
		</div>`;

	updateButton.addEventListener("click", function() {
		updateRow.style.display = updateRow.style.display === "none" ? "table-row" : "none";
	});

	updateRow.querySelector(".save-update-btn").addEventListener("click", function() {
		updateContact(currContact, updateRow)
	});

	buttonBox.appendChild(deleteButton);
	buttonBox.appendChild(updateButton);
	row.appendChild(buttonBox);

	//const updateBox = document.createElement("td");
	//updateBox.innerHTML = `<button type="button" class="buttons" onclick="updateContact();">'U+270E'</button>`;
	//row.appendChild(updateBox);
	/*
	const keys = ["FirstName", "LastName", "Phone", "Email"];
	keys.forEach(key => {
		const cell = document.createElement("td");
		const value = currContact.results[key];

		if (typeof value === "object") {
			cell.textContent = JSON.stringify(value, null, 2);
		} else {
			cell.textContent = value;
		}
		row.appendChild(cell);
	});
	
	currContact.results.forEach(contact => {
		contact.forEach(key => {
			const cell = document.createElement("td");
			cell.textContent = contact[key];
			row.appendChild(cell);
		})
	});
	*/
	tableBody.appendChild(row);
	tableBody.appendChild(updateRow);
}

function deleteContact(currContact, row) {
	const FirstName = currContact.FirstName;
	const LastName = currContact.LastName;
	const Phone = currContact.Phone;
	const Email = currContact.Email;

	console.log(userId);
	let tmp = {firstName:FirstName,lastName:LastName,phone:Phone,email:Email,userID:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/DeleteContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				alert("Contact has been removed");
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("addResult").innerHTML = err.message;
	}
}

function updateContact(contact, updateRow) {
	let firstName = contact.FirstName;
	let lastName = contact.LastName;
	let phone = updateRow.querySelector("input[name='phoneNum']").value;
	let email = updateRow.querySelector("input[name='email']").value;
	//console.log(phone);

	document.getElementById("addResult").innerHTML = "";
	console.log(userId);
	let tmp = {firstName:firstName,lastName:lastName,phone:phone,email:email,userID:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/UpdateContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log("contact updated");
				location.reload();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log(err.message);
	}
}
