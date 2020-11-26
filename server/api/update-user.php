<?php
	header("Content-Type: application/json");
	header("Accept: application/json");
	
	include_once("../config/Database.php");
	include_once("../models/Auth.php");
	include_once("../models/User.php");

	$database = new Database();
	$db = $database->connect();

	$auth = new Auth($db);
	$user = new User($db);

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
				$f_name = htmlspecialchars(strip_tags(trim($data->f_name)));
				$l_name = htmlspecialchars(strip_tags(trim($data->l_name)));
				$email = htmlspecialchars(strip_tags(trim($data->email)));
				$pass_1 = htmlspecialchars(strip_tags(trim($data->password1)));
				$pass_2 = htmlspecialchars(strip_tags(trim($data->password2)));
				$ph_no = htmlspecialchars(strip_tags(trim($data->ph_no)));
				$address = htmlspecialchars(strip_tags(trim($data->address)));

				// Validation
				if($f_name === "" || $l_name === "" || $email === "" || $pass_1 === "" || $pass_2 === "" || $ph_no === "" || $address === "")
					array_push($errors, "Please enter all fields.");
				if(!filter_var($email, FILTER_VALIDATE_EMAIL))
					array_push($errors, "Please enter a valid e-mail ID.");
				if(strlen($pass_1) < 6 || strlen($pass_2) < 6)
					array_push($errors, "The length of the password should be more than 5 characters.");
				if($pass_1 != $pass_2)
					array_push($errors, "The two passwords should match.");
				if(strlen($ph_no) != 13 || substr($ph_no, 0, 1) != "+")
					array_push($errors, "Please enter the phone number in the correct format. (Eg: +911234567890)");

				if(count($errors) > 0)
					echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => $errors));
				else // Data is error-free
				{
					// Hash password
					$hashed_pass = password_hash($pass_1, PASSWORD_DEFAULT);

					// Send data to 'User.php'
					$user->id = $auth_result["data"]["user_id"];
					$user->f_name = $f_name;
					$user->l_name = $l_name;
					$user->email = $email;
					$user->password = $hashed_pass;
					$user->ph_no = $ph_no;
					$user->address = $address;

					$result = $user->updateUser();

					if($result["status"] === "success")
						echo json_encode(array("status" => "OK", "code" => 200, "data" => array(), "errors" => array()));
					else
						echo json_encode(array("status" => "INTERNAL SERVER ERROR", "code" => 500, "data" => array(), "errors" => array("Database error")));
				}
			}
			else
				echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
		}
		else
			echo json_encode(array("status" => "UNAUTHORIZED", "code" => 401, "data" => array(), "errors" => array("Please log in to add product to cart.")));
	}
	else
		echo json_encode(array("status" => "NOT ACCEPTABLE", "code" => 406, "data" => array(), "errors" => array("Error in receiving data.")));
?>
