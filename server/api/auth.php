<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Auth.php");

	$database = new Database();
	$db = $database->connect();

	$auth = new Auth($db);

	$errors = [];

	$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";

	if($content_type === "application/json")
	{
		$data = json_decode(file_get_contents("php://input"));

		// Cleanse data
		$token = htmlspecialchars(strip_tags(trim($data->token)));

		// Validation
		if($token === "")
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Error in determining auth state.")));
		else // Data is error-free
		{
			$auth->token = $token;

			$result = $auth->authenticate();

			if($result["status"] === "success")
				echo json_encode(array("status" => "OK", "code" => 200, "data" => $result["data"], "errors" => array()));
			else if($result["status"] === "unauthorized")
				echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Incorrect e-mail ID or password.")));
			else if($result["status"] === "expired")
				echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Login expired.")));
			else
				echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
		}
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
