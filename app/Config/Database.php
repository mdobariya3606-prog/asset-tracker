<?php

namespace App\Config;

use Exception;
use PDO;

class Database
{
	private PDO $conn;

	public function __construct()
	{
		try {
			$this->conn = new PDO(
				"mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
				$_ENV['DB_USERNAME'],
				$_ENV['DB_PASSWORD'],
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				]
			);
		} catch (Exception $e) {
			logError($e);
			view(500);
			exit;
		}
	}

	public function getConnection(): PDO
	{
		return $this->conn;
	}
}
