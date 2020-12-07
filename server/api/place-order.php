<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Auth.php");
	include_once("../models/OrderDetail.php");
	include_once("../models/Product.php");

	$database = new Database();
	$db = $database->connect();

	$auth = new Auth($db);
	$order_detail = new OrderDetail($db);
	$product = new Product($db);

	$errors = [];

	$auth_header_val = isset($_SERVER["HTTP_AUTHENTICATION"]) ? $_SERVER["HTTP_AUTHENTICATION"] : "";

	if($auth_header_val != "")
	{
		$token = substr($auth_header_val, 7);
	
		$auth->token = $token;
		$auth_result = $auth->authenticate();
	
		if($auth_result["status"] === "success")
		{
			$order_detail->user_id = $auth_result["data"]["user_id"];
			$undelivered_orders = $order_detail->getUndeliveredOrders();

			if($undelivered_orders["status"] === "success")
			{
				if($undelivered_orders["data"]["item_count"] > 0)
				{
					$prod_name = [];
					$is_error_present = false;
					for($i = 0; $i < $undelivered_orders["data"]["item_count"]; $i++)
					{
						$product->id = $undelivered_orders["data"]["items"][$i]["prod_id"];
						$ordered_qty = $undelivered_orders["data"]["items"][$i]["qty"];

						$prod_result = $product->getProduct();
						if($prod_result["status"] === "success")
						{
							if($prod_result["data"]["qty"] < $ordered_qty)
								array_push($prod_name, $prod_result["data"]["name"]);
						}
						else
						{
							echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
							$is_error_present = true;
							break;
						}
					}

					if(count($prod_name) > 0)
						echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("The following product(s) does/do not have sufficient quantity:", $prod_name, "Please remove the item(s) from the cart and check the available quantity of the product(s).")));
					else
					{
						for($i = 0; $i < $undelivered_orders["data"]["item_count"]; $i++)
						{
							$prod_id = $undelivered_orders["data"]["items"][$i]["prod_id"];
							$order_id = $undelivered_orders["data"]["items"][$i]["id"];

							$order_detail->prod_id = $prod_id;
							$place_order_result = $order_detail->orderSingleProduct();

							if($place_order_result["status"] === "success")
							{
								$product->id = $prod_id;
								$product->qty = $undelivered_orders["data"]["items"][$i]["qty"];
								$prod_qty_result = $product->subtractQty();

								if($prod_qty_result["status"] === "failure")
								{
									// Withdraw order
									$order_detail->id = $order_id;
									$order_withdraw_result = $order_detail->withdrawSingleOrder();

									$is_error_present = true;
									echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
									break;
								}
							}
							else
							{
								echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
								$is_error_present = true;
								break;
							}
						}

						if($is_error_present === false)
							echo json_encode(array("status" => "OK", "code" => 200, "data" => array(), "errors" => array()));
					}
				}
				else
					echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("No items in cart.")));
			}
			else
				echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
		}
		else
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Please log in to add product to cart.")));
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
