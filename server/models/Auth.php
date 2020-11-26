<?php
	class Auth
	{
		private $conn;
		private $table_name = "auth";

		public $token;
		public $user_id;

		public function __construct($db)
		{
			$this->conn = $db;
		}

		public function storeToken()
		{
			$query = "INSERT INTO
						$this->table_name
					SET
						token = :token,
						user_id = :user_id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":token", $this->token);
			$stmt->bindParam(":user_id", $this->user_id);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array());
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function authenticate()
		{
			$query = "SELECT
						$this->table_name.user_id,
						$this->table_name.timestamp,
						user.first_name,
						user.last_name
					FROM
						$this->table_name
					INNER JOIN
						user
					ON
						$this->table_name.user_id = user.id
					WHERE
						token = :token
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":token", $this->token);

			try
			{
				if($stmt->execute())
				{
					$data = $stmt->fetch(PDO::FETCH_ASSOC);

					if($data)
					{
						// Check timestamp
						$token_time_diff = strtotime("now") - strtotime($data["timestamp"]);

						if($token_time_diff < 24*60*60) // If token is less than a day old
							return array("status" => "success", "data" => $data);
						else
						{
							$result = $this->deleteExpiredTokens();
							
							if($result["status"] === "success")
								return array("status" => "expired", "data" => array());
							else
								return array("status" => "failure", "data" => array());
						}
					}
					else
						return array("status" => "unauthorized", "data" => array());
				}
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function deleteExpiredTokens()
		{
			$query = "DELETE FROM
						$this->table_name
					WHERE
						timestamp < (NOW() - INTERVAL 1 DAY)
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array());
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function removeToken()
		{
			$query = "DELETE FROM
						$this->table_name
					WHERE
						token = :token
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":token", $this->token);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array());
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}
	}
?>
