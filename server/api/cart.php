<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Auth.php");
	include_once("../models/OrderDetail.php");

	$database = new Database();
	$db = $database->connect();

	$auth = new Auth($db);
	$order_detail = new OrderDetail($db);

	$errors = [];

	$auth_header_val = isset($_SERVER["HTTP_AUTHENTICATION"]) ? $_SERVER["HTTP_AUTHENTICATION"]: "";

	if($auth_header_val != "")
	{
		$token = substr($auth_header_val, 7);
	
		$auth->token = $token;
		$auth_result = $auth->authenticate();
	
		if($auth_result["status"] === "success")
		{
			$order_detail->user_id = $auth_result["data"]["user_id"];
			$result = $order_detail->getOrder();

			if($result["status"] === "success")
				echo json_encode(array("status" => "OK", "code" => 200, "data" => $result["data"], "errors" => array()));
			else
				echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
		}
		else
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Please log in to add product to cart.")));
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
