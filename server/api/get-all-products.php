<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Product.php");

	$database = new Database();
	$db = $database->connect();

	$product = new Product($db);

	$errors = [];

	$result = $product->getAllProducts();

	if($result["status"] === "success")
		echo json_encode(array("status" => "OK", "code" => 200, "data" => $result["data"], "errors" => array()));
	else
		echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
?>
