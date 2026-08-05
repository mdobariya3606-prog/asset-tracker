<?php

namespace App\Models;

use PDO;

class Designation
{
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	public function create(array $designation)
	{
		$stmt = $this->conn->prepare('insert into designations (name) values (:name)');
		$stmt->execute(['name' => $designation['name']]);
	}

	public function all()
	{
		$stmt = $this->conn->query('select * from designations');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function find($id)
	{
		$stmt = $this->conn->prepare('select * from designations where id = ?');
		$stmt->execute([$id]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function validate(array $designation): array
	{
		$errors = [];
		if (empty($designation['name'])) {
			$errors['name'] = 'Name is required';
		}

		$stmt = $this->conn->prepare('select * from designations where name = :name');
		$stmt->execute(['name' => $designation['name']]);

		if ($stmt->rowCount() > 0) {
			$errors['name'] = 'Designation already exists';
		}

		return $errors;
	}
}