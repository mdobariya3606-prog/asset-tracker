<?php

namespace App\Models;

use PDO;

class Department
{
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	public function create(array $department)
	{
		$stmt = $this->conn->prepare('insert into departments (name) values (:name)');
		$stmt->execute(['name' => $department['name']]);
	}

	public function all()
	{
		$stmt = $this->conn->query('select * from departments');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function find($id)
	{
		$stmt = $this->conn->prepare('select * from departments where id = ?');
		$stmt->execute([$id]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function validate(array $department): array
	{
		$errors = [];

		if (!isset($department['name']) || empty($department['name'])) {
			$errors['name'] = 'Name is required';
		} else {
			$stmt = $this->conn->prepare('select id from departments where name = :name limit 1');
			$stmt->execute(
				[
					'name' => $department['name']
				]
			);
			if ($stmt->rowCount() > 0) {
				$errors['name'] = 'Department already exists';
			}
		}


		return $errors;
	}
}
