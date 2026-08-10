<?php

namespace App\Models;

use Exception;
use InvalidArgumentException;
use PDO;

class Asset
{
	private const STATUS_OPTIONS = ['AVAILABLE', 'ASSIGNED', 'REPAIR', 'LOST', 'SCRAP'];
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	public function create(array $asset): int
	{
		$normalized = $this->normalizeInput($asset);
		$errors = $this->validate($normalized);
		if ($errors !== []) {
			throw new InvalidArgumentException(implode(' ', $errors));
		}

		$columns = $this->getTableColumns('assets');
		$allowedFields = ['name', 'category_id', 'brand', 'model', 'serial_number', 'purchase_date', 'warranty_date', 'vendor_id', 'cost', 'status', 'image'];
		$insertData = [];

		foreach ($allowedFields as $field) {
			if (!in_array($field, $columns, true)) {
				continue;
			}

			// FIX: Don't skip fields if they exist in normalized array, allow image to be inserted
			if (array_key_exists($field, $normalized) && $normalized[$field] !== '') {
				$insertData[$field] = $normalized[$field];
			}
		}

		if (empty($insertData)) {
			throw new InvalidArgumentException('No valid asset fields were provided.');
		}

		$columnNames = array_keys($insertData);
		$placeholders = array_map(static fn($column) => ':' . $column, $columnNames);
		$sql = 'INSERT INTO assets (' . implode(', ', $columnNames) . ') VALUES (' . implode(', ', $placeholders) . ')';

		$stmt = $this->conn->prepare($sql);
		$stmt->execute($insertData);

		return (int)$this->conn->lastInsertId();
	}

	private function normalizeInput(array $asset): array
	{
		return [
			'name' => trim((string)($asset['name'] ?? '')),
			'category_id' => trim((string)($asset['category_id'] ?? '')),
			'brand' => trim((string)($asset['brand'] ?? '')),
			'model' => trim((string)($asset['model'] ?? '')),
			'serial_number' => trim((string)($asset['serial_number'] ?? $asset['serial_no'] ?? '')),
			'purchase_date' => trim((string)($asset['purchase_date'] ?? '')),
			'warranty_date' => trim((string)($asset['warranty_date'] ?? '')),
			'vendor_id' => trim((string)($asset['vendor_id'] ?? '')),
			'cost' => trim((string)($asset['cost'] ?? '')),
			'status' => trim((string)($asset['status'] ?? '')),
			'image' => trim((string)($asset['image'] ?? '')),
		];
	}

	public function validate(array $asset, ?int $excludeId = null): array
	{
		try {
			$category_ids = $this->conn->query('select id from categories')->fetchAll(PDO::FETCH_COLUMN);
			$vendor_ids = $this->conn->query('select id from vendors')->fetchAll(PDO::FETCH_COLUMN);
		} catch (Exception $e) {
			logError($e);
			view(500);
			exit();
		}

		$normalized = $this->normalizeInput($asset);
		$errors = [];
		$requiredFields = ['name', 'category_id', 'brand', 'model', 'serial_number', 'purchase_date', 'warranty_date', 'vendor_id', 'cost', 'status'];

		foreach ($requiredFields as $field) {
			if (($normalized[$field] ?? '') === '') {
				$errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
			}
		}

		if (($normalized['name'] ?? '') !== '' && mb_strlen($normalized['name']) > 150) {
			$errors['name'] = 'Asset name must not exceed 150 characters.';
		}

		if (($normalized['category_id'] ?? '') !== '' && !is_numeric($normalized['category_id'])) {
			$errors['category_id'] = 'Category is not valid.';
		}

		if (empty($errors['category_id']) && !in_array((int)$normalized['category_id'], $category_ids, true)) {
			$errors['category_id'] = 'Category is not valid.';
		}

		if (($normalized['brand'] ?? '') !== '' && mb_strlen($normalized['brand']) > 100) {
			$errors['brand'] = 'Brand must not exceed 100 characters.';
		}

		if (($normalized['model'] ?? '') !== '' && mb_strlen($normalized['model']) > 100) {
			$errors['model'] = 'Model must not exceed 100 characters.';
		}

		if (($normalized['vendor_id'] ?? '') !== '' && !is_numeric($normalized['vendor_id'])) {
			$errors['vendor_id'] = 'Vendor is not valid.';
		}

		if (empty($errors['vendor_id']) && !in_array((int)$normalized['vendor_id'], $vendor_ids, true)) {
			$errors['vendor_id'] = 'Vendor is not valid.';
		}

		if (($normalized['serial_number'] ?? '') !== '' && mb_strlen($normalized['serial_number']) > 100) {
			$errors['serial_number'] = 'Serial number must not exceed 100 characters.';
		}

		if (($normalized['cost'] ?? '') !== '' && (!is_numeric($normalized['cost']) || (float)$normalized['cost'] < 0)) {
			$errors['cost'] = 'Cost must be a non-negative number.';
		}

		if (($normalized['status'] ?? '') !== '' && !in_array($normalized['status'], self::STATUS_OPTIONS, true)) {
			$errors['status'] = 'Status is not valid.';
		}

		if (($normalized['image'] ?? '') !== '' && mb_strlen($normalized['image']) > 255) {
			$errors['image'] = 'Image path must not exceed 255 characters.';
		}

		if (($normalized['purchase_date'] ?? '') !== '' && !$this->isValidDate($normalized['purchase_date'])) {
			$errors['purchase_date'] = 'Purchase date must be a valid date in YYYY-MM-DD format.';
		}

		if (($normalized['warranty_date'] ?? '') !== '' && !$this->isValidDate($normalized['warranty_date'])) {
			$errors['warranty_date'] = 'Warranty date must be a valid date in YYYY-MM-DD format.';
		}

		if (($normalized['purchase_date'] ?? '') !== '' && ($normalized['warranty_date'] ?? '') !== '' && $this->isValidDate($normalized['purchase_date']) && $this->isValidDate($normalized['warranty_date'])) {
			if (strtotime($normalized['warranty_date']) < strtotime($normalized['purchase_date'])) {
				$errors['warranty_date'] = 'Warranty date cannot be earlier than purchase date.';
			}
		}

		if (($normalized['asset_id'] ?? '') !== '' && $this->isDuplicate('asset_id', $normalized['asset_id'], $excludeId)) {
			$errors['asset_id'] = 'This asset ID is already in use.';
		}

		if (($normalized['serial_number'] ?? '') !== '' && $this->isDuplicate('serial_number', $normalized['serial_number'], $excludeId)) {
			$errors['serial_number'] = 'This serial number is already in use.';
		}

		return $errors;
	}

	private function isValidDate(string $value): bool
	{
		$value = trim($value);
		if ($value === '') {
			return false;
		}

		$date = date_create_from_format('Y-m-d', $value);
		return $date instanceof \DateTimeInterface && $date->format('Y-m-d') === $value;
	}

	private function isDuplicate(string $field, string $value, ?int $excludeId = null): bool
	{
		if ($value === '') {
			return false;
		}

		$sql = 'SELECT id FROM assets WHERE ' . $field . ' = :value';
		$params = ['value' => $value];

		if ($excludeId !== null) {
			$sql .= ' AND id != :excludeId';
			$params['excludeId'] = $excludeId;
		}

		$stmt = $this->conn->prepare($sql . ' LIMIT 1');
		$stmt->execute($params);
		return (bool)$stmt->fetchColumn();
	}

	private function getTableColumns(string $table): array
	{
		$stmt = $this->conn->query('SHOW COLUMNS FROM `' . $table . '`');
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return array_column($columns, 'Field');
	}

	public function update(int $id, array $asset): bool
	{
		$normalized = $this->normalizeInput($asset);
		$errors = $this->validate($normalized, $id);
		if ($errors !== []) {
			throw new InvalidArgumentException(implode(' ', $errors));
		}

		$columns = $this->getTableColumns('assets');
		$allowedFields = ['name', 'category_id', 'brand', 'model', 'serial_number', 'purchase_date', 'warranty_date', 'vendor_id', 'cost', 'status', 'image'];
		$updateData = [];

		foreach ($allowedFields as $field) {
			// FIX: Allow 'image' and other fields even if passed directly or populated during upload
			if (array_key_exists($field, $asset) || array_key_exists($field, $normalized)) {
				if (!in_array($field, $columns, true)) {
					continue;
				}

				// If the field is present in the raw $asset input, use that value (e.g. image path)
				$value = $asset[$field] ?? $normalized[$field] ?? null;

				// Don't skip empty values if updating unless it's null/empty string for non-required fields
				if ($value !== null && $value !== '') {
					$updateData[$field] = $value;
				}
			}
		}

		if (empty($updateData)) {
			return false;
		}

		$setClauses = [];
		foreach (array_keys($updateData) as $field) {
			$setClauses[] = $field . ' = :' . $field;
		}

		$updateData['id'] = $id;
		$sql = 'UPDATE assets SET ' . implode(', ', $setClauses) . ' WHERE id = :id';
		$stmt = $this->conn->prepare($sql);
		return $stmt->execute($updateData);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->conn->prepare('DELETE FROM assets WHERE id = ?');
		return $stmt->execute([$id]);
	}

	public function all(): array
	{
		$sql = 'SELECT a.*, c.name AS category_name, v.name AS vendor_name
                FROM assets a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN vendors v ON a.vendor_id = v.id
                ORDER BY a.status';
		$stmt = $this->conn->query($sql);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function canManageAssets(string $role): bool
	{
		$role = strtoupper($role);
		return in_array($role, ['ADMIN', 'MANAGER'], true);
	}

	public function canRequestAsset(string $role): bool
	{
		$role = strtoupper($role);
		return in_array($role, ['HR', 'EMPLOYEE'], true);
	}

	public function statusEnum(): array
	{
		return [
			'Available',
			'Assigned',
			'Repair',
			'Lost',
			'Scrap'
		];
	}

	public function updateStatus(int $id, string $status, $assignee_id = null)
	{
		$stmt = $this->conn->prepare('update assets set status = :status, assignee_id = :assignee_id WHERE id = :id');
		$stmt->execute([
			'status' => $status,
			'id' => $id,
			'assignee_id' => $assignee_id,
		]);
	}

	public function findOrFail(int $id): array
	{
		$asset = $this->find($id);
		if ($asset === []) {
			view(404);
		}
		return $asset;
	}

	public function find(int $id): array
	{
		$sql = 'SELECT a.*, u.name AS user_name, c.name AS category_name, v.name AS vendor_name
                FROM assets a
                LEFT JOIN users u ON a.assignee_id = u.id
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN vendors v ON a.vendor_id = v.id
                WHERE a.id = ?';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([$id]);
		return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
	}
}
