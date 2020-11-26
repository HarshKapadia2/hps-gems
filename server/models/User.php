<?php
	class User
	{
		private $conn;
		private $table_name = "user";

		public $f_name;
		public $l_name;
		public $email;
		public $password;
		public $ph_no;
		public $address;

		public function __construct($db)
		{
			$this->conn = $db;
		}

		public function signup()
		{
			$query = "INSERT INTO
						$this->table_name
					SET
						first_name = :f_name,
						last_name = :l_name,
						email = :email,
						password = :password,
						phone_no = :ph_no,
						address = :address
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":f_name", $this->f_name);
			$stmt->bindParam(":l_name", $this->l_name);
			$stmt->bindParam(":email", $this->email);
			$stmt->bindParam(":password", $this->password);
			$stmt->bindParam(":ph_no", $this->ph_no);
			$stmt->bindParam(":address", $this->address);

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

		public function checkCredentials()
		{
			$query = "SELECT
						id,
						password
					FROM
						$this->table_name
					WHERE
						email = :email
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":email", $this->email);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => $stmt->fetch(PDO::FETCH_ASSOC));
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function getUser()
		{
			$query = "SELECT
						first_name,
						last_name,
						email,
						phone_no,
						address
					FROM
						$this->table_name
					WHERE
						id = :id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":id", $this->id);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => $stmt->fetch(PDO::FETCH_ASSOC));
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function UpdateUser()
		{
			$query = "UPDATE
						$this->table_name
					SET
						first_name = :f_name,
						last_name = :l_name,
						email = :email,
						password = :password,
						phone_no = :ph_no,
						address = :address
					WHERE
						id = :id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":f_name", $this->f_name);
			$stmt->bindParam(":l_name", $this->l_name);
			$stmt->bindParam(":email", $this->email);
			$stmt->bindParam(":password", $this->password);
			$stmt->bindParam(":ph_no", $this->ph_no);
			$stmt->bindParam(":address", $this->address);
			$stmt->bindParam(":id", $this->id);

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
