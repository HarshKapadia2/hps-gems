const update_btn = document.querySelector("button");
const error_div = document.querySelector("#errors");
const f_name = document.querySelector("#f-name");
const l_name = document.querySelector("#l-name");
const address = document.querySelector("#address");
const email = document.querySelector("#e-mail");
const ph_no = document.querySelector("#ph-no");

let errors = [];

const token = localStorage.getItem("hpsgemstoken");

window.addEventListener
(
	"load",
	async () =>
	{
		if(await auth())
			getProfile();
	}
);

update_btn.addEventListener("click", () => window.location = "/hps-gems/client/html/update.html");


async function auth()
{
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./signup.html");

		errors.push("Log in to view details.");
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
				createLogOutNavLink();
				
				return true;
			}
			else
			{
				createNavLink("Log In/Sign Up", "./signup.html");

				errors.push("Log in to view details.");
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => { console.error(err); return false; });

	return fetch_result;
}

function getProfile()
{
	fetch
	(
		"/hps-gems/server/api/get-user.php",
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
			{
				f_name.innerText = res_data.data.first_name;
				l_name.innerText = res_data.data.last_name;
				address.innerText = res_data.data.address;
				email.innerText = res_data.data.email;
				ph_no.innerText = res_data.data.phone_no;
			}
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function logOut()
{
	fetch
	(
		"/hps-gems/server/api/logout.php",
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
			{
				localStorage.removeItem("hpsgemstoken");

				window.location = "/hps-gems/index.html";
			}
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
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

function createLogOutNavLink()
{
	let li = document.createElement("li");

	li.innerText = "Log Out";
	li.addEventListener("click", () => logOut());
	
	links.appendChild(li);
}

function displayErrors()
{
	error_div.innerHTML = "";

	for(let i = 0; i < errors.length; i++)
	{
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}
