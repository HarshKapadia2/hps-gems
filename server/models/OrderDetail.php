<?php
	class OrderDetail
	{
		private $conn;
		private $table_name = "order_detail";

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
			$update_result = $this->updateOrder();

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

		public function updateOrder()
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
	}
?>
