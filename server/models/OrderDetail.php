<?php
	class OrderDetail
	{
		private $conn;
		private $table_name = "order_detail";

		public $id;
		public $user_id;
		public $prod_id;
		public $qty;
		public $status;
		public $date;

		public function __construct($db)
		{
			$this->conn = $db;
		}

		public function addOrder()
		{
			$update_result = $this->updateOrderQty();

			if($update_result["status"] === "success")
				return array("status" => "success", "data" => array());
			
			$query = "INSERT INTO
						$this->table_name
					SET
						user_id = :user_id,
						prod_id = :prod_id,
						qty = :qty
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);
			$stmt->bindParam(":prod_id", $this->prod_id);
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

		public function updateOrderQty()
		{
			$query = "UPDATE
						$this->table_name
					SET
						qty = qty + :qty
					WHERE
							user_id = :user_id
						AND
							prod_id = :prod_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);
			$stmt->bindParam(":prod_id", $this->prod_id);
			$stmt->bindParam(":qty", $this->qty);

			try
			{
				$result = $stmt->execute();
				$affectedRows = $stmt->rowCount();

				if($result && $affectedRows > 0)
					return array("status" => "success", "data" => array());
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function getOrder()
		{
			$query = "SELECT
						$this->table_name.*,
						product.name,
						product.price,
						product.pic_url
					FROM
						$this->table_name
					INNER JOIN
						product
					ON
						$this->table_name.prod_id = product.id
					WHERE
						$this->table_name.user_id = :user_id
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array("item_count" => $stmt->rowCount(), "items" => $stmt->fetchAll(PDO::FETCH_ASSOC)));
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function deleteOrder()
		{
			$query = "DELETE FROM
						$this->table_name
					WHERE
							user_id = :user_id
						AND
							prod_id = :prod_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);
			$stmt->bindParam(":prod_id", $this->prod_id);

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

		public function getUndeliveredOrders()
		{
			$query = "SELECT
						$this->table_name.*,
						product.name,
						product.price,
						product.pic_url
					FROM
						$this->table_name
					INNER JOIN
						product
					ON
						$this->table_name.prod_id = product.id
					WHERE
							$this->table_name.user_id = :user_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);

			try
			{
				if($stmt->execute())
					return array("status" => "success", "data" => array("item_count" => $stmt->rowCount(), "items" => $stmt->fetchAll(PDO::FETCH_ASSOC)));
				else
					return array("status" => "failure", "data" => array());
			}
			catch(PDOException $e)
			{
				return array("status" => "failure", "data" => array());
			}
		}

		public function getSingleUndeliveredOrder()
		{
			$query = "SELECT
						$this->table_name.*,
						product.name,
						product.price,
						product.pic_url
					FROM
						$this->table_name
					INNER JOIN
						product
					ON
						$this->table_name.prod_id = product.id
					WHERE
							$this->table_name.user_id = :user_id
						AND
							$this->table_name.prod_id = :prod_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);
			$stmt->bindParam(":prod_id", $this->prod_id);

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

		public function placeOrder()
		{
			$query = "UPDATE
						$this->table_name
					SET
						is_delivered = true,
						date = CURRENT_TIMESTAMP
					WHERE
							user_id = :user_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
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
		
		public function orderSingleProduct()
		{
			$query = "UPDATE
						$this->table_name
					SET
						is_delivered = true,
						date = CURRENT_TIMESTAMP
					WHERE
							user_id = :user_id
						AND
							prod_id = :prod_id
						AND
							is_delivered = false
			";

			// Prepare data
			$stmt = $this->conn->prepare($query);

			// Bind data
			$stmt->bindParam(":user_id", $this->user_id);
			$stmt->bindParam(":prod_id", $this->prod_id);

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

		public function withdrawSingleOrder()
		{
			$query = "UPDATE
						$this->table_name
					SET
						is_delivered = false,
						date = NULL
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
