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

submit_btn.addEventListener
(
	"click",
	(e) =>
	{
		e.preventDefault();
		error_div.innerHTML = "";
		
		if(validate())
			sendData();
	}
);


function validate()
{
	if(f_name.value === "" || l_name.value === "" || email.value === "" || pass_1.value === "" || pass_2.value === "" || ph_no.value === "" || address.value === "")
		errors.push("Please enter all fields.");
	if(pass_1.value.length < 6 || pass_2.value.length < 6)
		errors.push("The length of the password should be more than 5 characters.");
	if(pass_1.value != pass_2.value)
		errors.push("The two passwords should match.");
	if(ph_no.value.length != 13 || ph_no.value[0] != "+")
		errors.push("Please enter the phone number in the correct format. (Eg: +911234567890)");

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
		error_div.innerHTML += `${errors[i]}<br>`;

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
		ph_no: ph_no.value,
		address: address.value
	};

	fetch
	(
		"../../server/api/signup.php",
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
		(data) =>
		{
			if(data.code === 200)
				window.location = "/hps-gems/client/html/login.html";
			else
			{
				errors = data.errors;
				displayErrors();

				throw new Error(`${data.code} ${data.status}: ${data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}
