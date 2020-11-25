const undelivered_table = document.querySelector("#undelivered-items-table");
const delivered_table = document.querySelector("#delivered-items-table");
const checkout_btn = document.querySelector("#checkout-btn");
const error_div = document.querySelector("#errors");

let errors = [];

const token = localStorage.getItem("hpsgemstoken");

window.addEventListener
(
	"load",
	async () =>
	{
		if(await auth())
			getCartProducts();
	}
);

checkout_btn.addEventListener("click", () => window.location = "/hps-gems/client/html/checkout.html");


async function auth()
{
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./client/html/signup.html");
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
				createNavLink("Check Out", "./checkout.html");
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

async function getCartProducts()
{
	await fetch
	(
		"/hps-gems/server/api/cart.php",
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
				for(let i = 0; i < res_data.data.item_count; i++)
				{
					if(res_data.data.items[i].is_delivered === "0")
						createRow(undelivered_table, res_data.data.items[i]);
					else
						createRow(delivered_table, res_data.data.items[i]);
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

function removeProduct(row, id, qty)
{
	const form_data = {
		id: id,
		qty: qty
	};

	fetch
	(
		"/hps-gems/server/api/remove-product.php",
		{
			method: "POST",
			headers: {
				"Accept": "application/json",
				"Content-Type": "application/json",
				"Authentication": `Bearer ${token}`
			},
			body: JSON.stringify(form_data)
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
				row.remove();
			else
			{
				errors = res_data.errors;
				displayErrors();

				throw new Error(`${res_data.code} ${res_data.status}: ${res_data.errors}`);
			}
		}
	).catch((err) => console.error(err));
}

function createRow(table, data)
{
	let tr = document.createElement("tr");
	let img_td = document.createElement("td");
	let name_td = document.createElement("td");
	let piece_price_td = document.createElement("td");
	let qty_td = document.createElement("td");
	let total_cost_td = document.createElement("td");
	let img_a = document.createElement("a");
	let name_a = document.createElement("a");
	let img = document.createElement("img");

	img.src = data.pic_url;
	img_a.href = `./product.html?id=${data.prod_id}`;
	name_a.innerText = data.name;
	name_a.href = `./product.html?id=${data.prod_id}`;
	piece_price_td.innerText = data.price;
	qty_td.innerText = data.qty;
	total_cost_td.innerText = data.price * data.qty;

	img_a.appendChild(img);
	img_td.appendChild(img_a);
	tr.appendChild(img_td);
	name_td.appendChild(name_a);
	tr.appendChild(name_td);

	if(table === delivered_table)
	{
		let date_td = document.createElement("td");
		date_td.innerText = data.date;
		tr.appendChild(date_td);
	}

	tr.appendChild(piece_price_td);
	tr.appendChild(qty_td);
	tr.appendChild(total_cost_td);

	if(table === undelivered_table)
	{
		let btn_td = document.createElement("td");
		let btn = document.createElement("button");
		btn.innerText = "Remove";
		btn.addEventListener("click", () => removeProduct(tr, data.prod_id, data.qty));
		btn_td.appendChild(btn);
		tr.appendChild(btn_td);
	}

	table.appendChild(tr);
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
