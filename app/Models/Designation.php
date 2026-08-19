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

	public function getDesignations()
	{
		$departmentId = filter_input(
			INPUT_GET,
			'department_id',
			FILTER_VALIDATE_INT
		);

		if (!$departmentId) {
			http_response_code(400);
			echo json_encode([
				'error' => 'Invalid department.'
			]);
			exit;
		}

		$designations = $this->getByDepartmentId($departmentId);

		header('Content-Type: application/json');

		echo json_encode($designations);
		exit;
	}

	public function getByDepartmentId(int $departmentId): array
	{
		$stmt = $this->conn->prepare('
			SELECT id, name
			FROM designations
			WHERE department_id = ?
			ORDER BY name ASC'
		);

		$stmt->execute([$departmentId]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
