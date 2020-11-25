<?php
	class Product
	{
		private $conn;
		private $table_name = "product";

		public $id;
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
						id,
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

		public function getProduct()
		{
			$query = "SELECT
						*
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

		public function updateQty()
		{
			$query = "UPDATE
						$this->table_name
					SET
						qty = :qty
					WHERE
						id = :id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":id", $this->id);
			$stmt->bindParam(":qty", $this->qty);

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

		public function addQty()
		{
			$query = "UPDATE
						$this->table_name
					SET
						qty = qty + :qty
					WHERE
						id = :id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":id", $this->id);
			$stmt->bindParam(":qty", $this->qty);

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
