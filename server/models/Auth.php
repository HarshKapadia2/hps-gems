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
	}
?>
