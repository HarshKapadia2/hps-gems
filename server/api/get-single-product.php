<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Product.php");

	$database = new Database();
	$db = $database->connect();

	$product = new Product($db);

	$errors = [];

	$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";

	if($content_type === "application/json")
	{
		$data = json_decode(file_get_contents("php://input"));

		// Cleanse data
		$prod_id = htmlspecialchars(strip_tags(trim($data->id)));

		// Validation
		if($prod_id === "" || !filter_var($prod_id, FILTER_VALIDATE_INT) || $prod_id <= 0)
			echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in fetching data.")));
		else // Data is error-free
		{
			$product->id = $prod_id;

			$result = $product->getProduct();

			if($result["status"] === "success")
				echo json_encode(array("status" => "OK", "code" => 200, "data" => $result["data"], "errors" => array()));
			else
				echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
		}
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
