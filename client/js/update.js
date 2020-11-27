const error_div = document.querySelector("#errors");
const f_name = document.querySelector("#first-name");
const l_name = document.querySelector("#last-name");
const email = document.querySelector("#e-mail");
const pass_1 = document.querySelector("#password1");
const pass_2 = document.querySelector("#password2");
const ph_no = document.querySelector("#ph-no");
const address = document.querySelector("#address");
const submit_btn = document.querySelector("button");
const form = document.querySelector(".form");

let errors = [];
let new_ph_no = "";

const token = localStorage.getItem("hpsgemstoken");

window.addEventListener
(
	"load",
	async () =>
	{
		if(!await auth())
		{
			submit_btn.disabled = true;
			f_name.disabled = true;
			l_name.disabled = true;
			email.disabled = true;
			pass_1.disabled = true;
			pass_2.disabled = true;
			ph_no.disabled = true;
			address.disabled = true;
			
			errors.push("Log in to update details.");
			displayErrors();
		}
	}
);

submit_btn.addEventListener
(
	"click",
	(e) =>
	{
		e.preventDefault();
		
		new_ph_no = "+" + ph_no.value;

		if(validate())
			sendData();
	}
);


async function auth()
{
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./signup.html");

		errors.push("Log in to view cart.");
		displayErrors();

		return false;
	}

	let fetch_result = false;

	fetch_result = await fetch
	(
		"/hps-gems/server/api/auth.php",
		{
			headers: {
				"Accept": "application/json",
				"Authentication": `Bearer ${token}`
			}
		}
	).then
	(
		(res) =>
		{
			if(res.ok)
				return res.json();
			else
			{
				errors.push("Failure in fetching data.");
				displayErrors();

				throw new Error("Failure in fetching data.");
			}
		}
	).then
	(
		(res_data) =>
		{
			if(res_data.code === 200)
			{
				createNavLink("Cart", "./cart.html");
				createNavLink(`${res_data.data.first_name} ${res_data.data.last_name}`, "./profile.html");
				
				return true;
			}
			else
			{
				createNavLink("Log In/Sign Up", "./signup.html");

				errors.push("Log in to view cart.");
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => { console.error(err); return false; });

	return fetch_result;
}

function sendData()
{
	const form_data = {
		f_name: f_name.value,
		l_name: l_name.value,
		email: email.value,
		password1: pass_1.value,
		password2: pass_2.value,
		ph_no: new_ph_no,
		address: address.value
	};

	fetch
	(
		"/hps-gems/server/api/update-user.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json",
				"Authentication": `Bearer ${token}`
			},
			body: JSON.stringify(form_data)
		}
	).then
	(
		(res) =>
		{
			if(res.ok)
			{
				return res.json();
			}
			else
			{
				errors.push("Failure in fetching data.");
				displayErrors();

				throw new Error("Failure in fetching data.");
			}
		}
	).then
	(
		(res_data) =>
		{
			if(res_data.code === 200)
				window.location = "/hps-gems/client/html/profile.html";
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function validate()
{
	if(f_name.value === "" || l_name.value === "" || email.value === "" || pass_1.value === "" || pass_2.value === "" || new_ph_no === "" || address.value === "")
		errors.push("Please enter all fields.");
	if(pass_1.value.length < 6 || pass_2.value.length < 6)
		errors.push("The length of the password should be more than 5 characters.");
	if(pass_1.value != pass_2.value)
		errors.push("The two passwords should match.");
	if(new_ph_no.length != 13)
		errors.push("Phone number format: 2 digit country code followed by 10 digit phone number (Eg: 919876543210)");

	if(errors.length > 0)
	{
		displayErrors();
		return false;
	}
	else
		return true;
}

function createNavLink(text, url)
{
	let a = document.createElement("a");
	let li = document.createElement("li");

	li.innerText = text;
	a.href = url;
	
	a.appendChild(li);
	links.appendChild(a);
}

function displayErrors()
{
	for(let i = 0; i < errors.length; i++)
	{
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}
