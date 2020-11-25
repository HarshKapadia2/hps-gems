<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Auth.php");

	$database = new Database();
	$db = $database->connect();

	$auth = new Auth($db);

	$errors = [];

	$auth_header_val = isset($_SERVER["HTTP_AUTHENTICATION"]) ? $_SERVER["HTTP_AUTHENTICATION"]: "";

	if($auth_header_val != "")
	{
		$token = substr($auth_header_val, 7);

		$auth->token = $token;
		$result = $auth->authenticate();

		if($result["status"] === "success")
			echo json_encode(array("status" => "OK", "code" => 200, "data" => $result["data"], "errors" => array()));
		else if($result["status"] === "unauthorized")
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Incorrect e-mail ID or password.")));
		else if($result["status"] === "expired")
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Login expired. Please log in again.")));
		else
			echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
