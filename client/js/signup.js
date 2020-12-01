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

window.addEventListener("load", () => auth());

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


function auth()
{
	if(!token)
		return;

	fetch
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
				submit_btn.disabled = true;
				f_name.disabled = true;
				l_name.disabled = true;
				email.disabled = true;
				pass_1.disabled = true;
				pass_2.disabled = true;
				ph_no.disabled = true;
				address.disabled = true;
				
				errors.push("You are already logged in. Happy shopping!");
				displayErrors();
			}
		}
	).catch((err) => { console.error(err);});
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
		"/server/api/signup.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json"
			},
			mode: "same-origin",
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
				window.location = "/client/html/login.html";
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}
