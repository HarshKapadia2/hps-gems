<?php
	class Product
	{
		private $conn;
		private $table_name = "product";

		public $name;
		public $description;
		public $price;
		public $qty;
		public $pic_url;

		public function __construct($db)
		{
			$this->conn = $db;
		}

		public function getAllProducts()
		{
			$query = "SELECT
						name,
						price,
						pic_url
					FROM
						$this->table_name
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array("no_of_products" => $stmt->rowCount(), "products" => $stmt->fetchAll()));
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
