<?php
	class Database
	{
		// DB params
		private $host;
		private $db_name;
		private $username;
		private $password;
		private $conn;

		public function __construct()
		{
			$this->host = getenv("DB_HOST");
			$this->db_name = getenv("DB_NAME");
			$this->username = getenv("DB_USERNAME");
			$this->password = getenv("DB_PASSWORD");
		}

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
				$this->conn = null;
			}

			return $this->conn;
		}
	}
?>
