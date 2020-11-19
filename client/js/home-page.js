const product_cards = document.querySelector("#product-cards");
const error_div = document.querySelector("#errors");

let errors = [];

window.addEventListener("load", () => getProducts());


function getProducts()
{
	fetch
	(
		"/hps-gems/server/api/get-all-products.php",
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
			console.log(res_data.data.products[0].name);
			
			if(res_data.code === 200)
			{
				no_of_products = res_data.data.no_of_products;
				
				for(let i = 0; i < no_of_products; i++)
				{
					let product_card = new ProductCard(res_data.data.products[i]);
					product_cards.appendChild(product_card);
				}
				console.log(product_cards);
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

function displayErrors()
{
	for(let i = 0; i < errors.length; i++){
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}
