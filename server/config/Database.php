<?php
	class Database
	{
		// DB params
		private $host = getenv("HOST");
		private $db_name = getenv("DB_NAME");
		private $username = getenv("USERNAME");
		private $password = getenv("PASSWORD");
		
		private $conn;

		// Connect DB
		public function connect()
		{
			$this->conn = null;

			try
			{
				$dsn = "mysql:host=$this->host;dbname=$this->db_name";
				$this->conn = new PDO($dsn, $this->username, $this->password);
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // To get error msg when the query doesn't work
			}
			catch(PDOException $e)
			{
				echo "Connection error: " . $e->getMessage() . "<br>";
			}

			return $this->conn;
		}
	}
?>
