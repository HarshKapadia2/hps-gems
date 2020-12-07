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

	$auth_header_val = isset($_SERVER["HTTP_AUTHENTICATION"]) ? $_SERVER["HTTP_AUTHENTICATION"]: "";

	if($auth_header_val != "")
	{
		$token = substr($auth_header_val, 7);

		$auth->token = $token;
		$auth_result = $auth->authenticate();

		if($auth_result["status"] === "success")
		{
			$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
	
			if($content_type === "application/json")
			{
				$data = json_decode(file_get_contents("php://input"));
	
				// Cleanse data
				$prod_id = htmlspecialchars(strip_tags(trim($data->id)));
				$prod_id = filter_var($prod_id, FILTER_SANITIZE_NUMBER_INT);
		
				// Validation
				if($prod_id == "")
					array_push($errors, "Please enter all fields.");
				if(!filter_var($prod_id, FILTER_VALIDATE_INT))
					array_push($errors, "Positive integer inputs only.");
				if($prod_id <= 0)
					array_push($errors, "Positive integer inputs only.");
		
				if(count($errors) > 0)
					echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => $errors));
	
				$product->id = $prod_id;
				$prod_result = $product->getProduct();
	
				if($prod_result["status"] != "success")
					echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error.")));
				else
				{
					// User is auth, data is error-free and product exists
					// Send data to 'OrderDetail.php'
					$order_detail->user_id = $auth_result["data"]["user_id"];
					$order_detail->prod_id = $prod_id;

					$get_order_result = $order_detail->getSingleUndeliveredOrder();

					if($get_order_result["status"] === "success")
					{
						$delete_order_result = $order_detail->deleteOrder();
			
						if($delete_order_result["status"] === "success")
							echo json_encode(array("status" => "OK", "code" => 200, "data" => array(), "errors" => array()));
						else
							echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
					}
					else
						echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
				}
			}
			else
				echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
		}
		else
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Please log in to remove product from cart.")));
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
