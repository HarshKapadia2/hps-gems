const error_div = document.querySelector("#errors");
const title_tag = document.querySelector("title");
const img = document.querySelector("img");
const prod_name = document.querySelector("#name");
const prod_descp = document.querySelector("#descp");
const price = document.querySelector("#price");
const status = document.querySelector("#status");
const available_qty = document.querySelector("#available-qty");
const order_qty = document.querySelector("#order-qty");
const button = document.querySelector("button");

const page_url = window.location.href;

let errors = [];

window.addEventListener
(
	"load",
	() =>
	{
		const url = new URL(page_url);
		const prod_id = url.searchParams.get("id");

		getProduct(prod_id);
	}
);


function getProduct(prod_id)
{
	fetch
	(
		"/hps-gems/server/api/get-single-product.php",
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
					order_qty.setAttribute("disabled", "");
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

function displayErrors()
{
	for(let i = 0; i < errors.length; i++){
		let div = document.createElement("div");
		div.innerText = errors[i];
		error_div.appendChild(div);
	}

	errors = [];
}
