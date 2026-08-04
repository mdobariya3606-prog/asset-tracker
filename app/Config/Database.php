<?php

namespace App\Config;

use PDO;

class Database {
	private PDO $conn;

	public function __construct()
	{
		$this->conn = new PDO(
			"mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
			$_ENV['DB_USERNAME'],
			$_ENV['DB_PASSWORD'],
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			]
		);
	}

	public function getConnection(): PDO
	{
		return $this->conn;
	}
}