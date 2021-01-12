const error_div = document.querySelector("#errors");
const title_tag = document.querySelector("title");
const img = document.querySelector("img");
const prod_name = document.querySelector("#name");
const prod_descp = document.querySelector("#descp");
const price = document.querySelector("#price");
const status = document.querySelector("#status");
const available_qty = document.querySelector("#available-qty");
const ordered_qty = document.querySelector("#order-qty");
const button = document.querySelector("button");
const links= document.querySelector("#links");

let errors = [];

const page_url = window.location.href;
const url = new URL(page_url);
const prod_id = url.searchParams.get("id");

const token = localStorage.getItem("hpsgemstoken");

window.addEventListener
(
	"load",
	() =>
	{
		getProduct(prod_id);
		auth();
	}
);

button.addEventListener
(
	"click",
	() =>
	{
		if(validate(parseInt(ordered_qty.value)))
			addToCart();
	}
);


function getProduct(prod_id)
{
	fetch
	(
		"/server/api/get-single-product.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json"
			},
			mode: "same-origin",
			body: JSON.stringify({ id: prod_id })
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
				img.setAttribute("src", res_data.data.pic_url);
				img.setAttribute("alt", res_data.data.name);

				prod_name.innerText = res_data.data.name;
				prod_descp.innerText = res_data.data.description;
				price.innerText = `Rs. ${res_data.data.price}`;
				available_qty.innerText = res_data.data.qty;
				title_tag.innerText = `HPS Gems: ${res_data.data.name}`;

				if(res_data.data.qty > 0)
					status.innerText = "In stock";
				else
				{
					status.innerText = "Out of stock";
					button.disabled = true;
					ordered_qty.setAttribute("disabled", "");
				}
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

function auth()
{
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./signup.html");
		return;
	}	

	fetch
	(
		"/server/api/auth.php",
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
				createNavLink("Cart", "./cart.html");
				createNavLink(`${res_data.data.first_name} ${res_data.data.last_name}`, "./profile.html");
			}
			else
			{
				createNavLink("Log In/Sign Up", "./signup.html");

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function addToCart()
{
	if(!token)
	{
		errors.push("Please log in to add product to cart.");
		displayErrors();
		return;
	}

	const obj = {
		id: prod_id,
		qty: parseInt(ordered_qty.value)
	};
	
	fetch
	(
		"/server/api/add-to-cart.php",
		{
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Accept": "application/json",
				"Authentication": `Bearer ${token}`
			},
			mode: "same-origin",
			body: JSON.stringify(obj)
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
				window.location = "/client/html/cart.html";
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function validate(qty)
{
	if(isNaN(qty))
		errors.push("Please enter the quantity to be ordered.");
	else if(qty <= 0)
		errors.push("The quantity ordered should be an integer more than 0.");
	else if(qty > parseInt(available_qty.innerText))
		errors.push("You cannot order more than the available quantity.");

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
	error_div.innerHTML = "";

	for(let i = 0; i < errors.length; i++)
	{
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}
