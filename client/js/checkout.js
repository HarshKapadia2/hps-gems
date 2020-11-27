const error_div = document.querySelector("#errors");
const address = document.querySelector("#address");
const order_date = document.querySelector("#order-date");
const items = document.querySelector("#items");
const item_img = document.querySelector(".item-img");
const item_name = document.querySelector(".item-name");
const item_rate = document.querySelector(".item-rate");
const total_items_price = document.querySelector("#total-items-price");
const shipping_price = document.querySelector("#shipping-price");
const tax = document.querySelector("#tax");
const final_price = document.querySelector("#final-price");
const order_btn = document.querySelector("button");

let errors = [];
let total_price = 0;
let order_items_count = 0;

const token = localStorage.getItem("hpsgemstoken");

window.addEventListener
(
	"load",
	async () =>
	{
		if(await auth())
			getCheckoutDetails();
	}
);

order_btn.addEventListener("click", () => placeOrder());


async function auth()
{
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./signup.html");

		order_btn.disabled = true;

		errors.push("Log in to view checkout.");
		displayErrors();

		return false;
	}

	let fetch_result = false;

	fetch_result = await fetch
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

				errors.push("Log in to view checkout.");
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => { console.error(err); return false; });

	return fetch_result;
}

function getCheckoutDetails()
{
	fetch
	(
		"/server/api/checkout.php",
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
				address.innerText = `Address: ${res_data.data.address}`;
				order_date.innerText = `Order date: ${todaysDate()}`;
				shipping_price.innerText = "Rs. 100";

				for(let i = 0; i < res_data.data.item_count; i++)
				{
					let item_data = res_data.data.items[i];
					createItem(item_data.pic_url, item_data.name, item_data.qty, item_data.price);

					order_items_count++;
				}

				total_items_price.innerText = `Rs. ${total_price}`;
				tax.innerText = `Rs. ${0.02 * total_price}`;
				final_price.innerText = `Rs. ${100 + (0.02 * total_price) + total_price}`;

				if(order_items_count === 0)
				{
					order_btn.disabled = true;

					errors.push("No items in cart.");
					displayErrors();
				}
			}
			else
			{
				createNavLink("Log In/Sign Up", "./signup.html");

				errors = res_data.data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function placeOrder()
{
	fetch
	(
		"/server/api/place-order.php",
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
				window.location = "/client/html/cart.html";
			else
			{
				createNavLink("Log In/Sign Up", "./signup.html");

				errors = res_data.data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function createItem(pic_url, name, qty, price)
{
	let item_div = document.createElement("div");
	let img_div = document.createElement("div");
	let img_tag = document.createElement("img");
	let name_div = document.createElement("div");
	let rate_div = document.createElement("div");

	item_div.classList.add("item");
	img_div.classList.add("item-img");
	img_tag.src = pic_url;
	name_div.classList.add("item-name");
	name_div.innerText = name;
	rate_div.classList.add("item-rate");
	rate_div.innerText = `${qty} x ${price} = Rs. ${parseInt(qty) * parseFloat(price)}`;

	img_div.appendChild(img_tag);
	item_div.appendChild(img_div);
	item_div.appendChild(name_div);
	item_div.appendChild(rate_div);
	items.appendChild(item_div);

	total_price += parseInt(qty) * parseFloat(price);
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
	for(let i = 0; i < errors.length; i++){
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}

function todaysDate()
{
	let today = new Date();

	const dd = String(today.getDate()).padStart(2, '0');
	const mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0
	const yyyy = today.getFullYear();

	today = yyyy + "-" + mm + "-" + dd;
	
	return today;
}
