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
					return array("status" => "success");
				else
					return array("status" => "failure");
			}
			catch(PDOException $e)
			{
				return array("status" => "failure");
			}
		}
	}
?>
