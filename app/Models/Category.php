<?php

namespace App\Models;

use PDO;

class Category
{
	private PDO $conn;
	public function __construct(PDO $conn) {
		$this->conn = $conn;
	}

	public function all() {
		$stmt = $this->conn->query('select * from categories order by name');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}