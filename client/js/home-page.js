const product_cards = document.querySelector("#product-cards");
const error_div = document.querySelector("#errors");
const links = document.querySelector("#links");

let errors = [];

window.addEventListener
(
	"load",
	() =>
	{
		displayProducts();
		auth();
	}
);


function displayProducts()
{
	fetch
	(
		"/server/api/get-all-products.php",
		{
			headers: {
				"Accept": "application/json"
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
				no_of_products = res_data.data.no_of_products;
				
				for(let i = 0; i < no_of_products; i++)
				{
					let product_card = new ProductCard(res_data.data.products[i]);
					product_cards.appendChild(product_card);
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
	const token = localStorage.getItem("hpsgemstoken");
	
	if(!token)
	{
		createNavLink("Log In/Sign Up", "./client/html/signup.html");
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
				createNavLink("Cart", "./client/html/cart.html");
				createNavLink(`${res_data.data.first_name} ${res_data.data.last_name}`, "./client/html/profile.html");
			}
			else
			{
				createNavLink("Log In/Sign Up", "./client/html/signup.html");

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
