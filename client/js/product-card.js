const template = document.createElement("template");
template.innerHTML = `
	<style>
		.product-card
		{
			display: grid;
			place-content: center center;
			width: 17em;
			border: 1px solid #e0e0e0;
			border-radius: 2px;
		}

		a
		{
			text-decoration: none;
			color: black;
		}

		img
		{
			min-height: 30vh;
			width: 15em;
			margin: 1em;
			border-radius: 2px;
		}

		.product-info
		{
			padding: 0 1em 1em 1em;
		}

		.product-title
		{
			font-size: 18px;
			color: #757575;
		}

		.product-title:hover
		{
			text-decoration: underline;
		}

		.cost
		{
			font-size: 25px;
			padding-top: 0.5em;
		}
	</style>

	<div class="product-card">
		<a><img /></a>

		<div class="product-info">
			<a><div class="product-title"></div></a>

			<div class="cost">Rs. <span class="product-cost"></span>/piece</div>
		</div>
	</div>
`;

class ProductCard extends HTMLElement
{
	constructor({ id, name, price,  pic_url })
	{
		super();

		this.attachShadow({ mode: "open" });
		this.shadowRoot.appendChild(template.content.cloneNode(true));
		this.shadowRoot.querySelector(".product-title").innerText = name;
		this.shadowRoot.querySelector(".product-cost").innerText = price;
		this.shadowRoot.querySelector("img").src = pic_url;
		
		const links = this.shadowRoot.querySelectorAll("a");
		for(let i = 0; i < links.length; i++)
			links[i].href = `./client/html/product.html?id=${id}`;
	}
}

window.customElements.define("product-card", ProductCard);
