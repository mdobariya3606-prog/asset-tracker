<?php

namespace App\Models;

use PDO;

class Vendor
{
	private PDO $conn;
	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	public function all()
	{
		$stmt = $this->conn->query('select * from vendors order by name');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function find($id)
	{
		$stmt = $this->conn->prepare('select * from vendors where id = ?');
		$stmt->execute([$id]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}
