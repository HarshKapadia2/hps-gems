<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/User.php");
	include_once("../models/Auth.php");

	$database = new Database();
	$db = $database->connect();

	$user = new User($db);
	$auth = new Auth($db);

	$errors = [];

	$content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";

	if($content_type === "application/json")
	{
		$data = json_decode(file_get_contents("php://input"));

		// Cleanse data
		$email = htmlspecialchars(strip_tags(trim($data->email)));
		$password = htmlspecialchars(strip_tags(trim($data->password)));

		// Validation
		if($email === "" || $password === "")
			array_push($errors, "Please enter all fields.");
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			array_push($errors, "Please enter a valid e-mail ID.");
		if(strlen($password) < 6)
			array_push($errors, "The length of the password should be more than 5 characters.");

		if(count($errors) > 0)
			echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => $errors));
		else // Data is error-free
		{
			// Send data to 'User.php'
			$user->email = $email;

			$result_1 = $user->checkCredentials();

			if($result_1["status"] === "success")
			{
				// Verify password
				$pass_input = isset($result_1["data"]["password"]) ? $result_1["data"]["password"] : "";
				$pass_check = password_verify($password, $pass_input);

				if($pass_check)
				{
					// Generate token (64 char hex str)
					$token = bin2hex(random_bytes(32));

					// Store token in DB
					$auth->token = $token;
					$auth->user_id = $result_1["data"]["id"];

					$result_2 = $auth->storeToken();

					if($result_2["status"] === "success")
						echo json_encode(array("status" => "OK", "code" => 200, "data" => array("token" => $token), "errors" => array()));
					else
						echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
				}
				else
					echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Invalid e-mail or password.")));
			}
			else
				echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
		}
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
