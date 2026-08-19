<?php

namespace App\Models;

use App\Controllers\Asset\SelectAssetController;
use DateTime;
use PDO;

class AssetRequest
{
	private PDO $conn;
	private Asset $assetModel;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->assetModel = new Asset($conn);
	}

	public function create(int $id)
	{
		if (!$this->assetModel->isAvailable($id)) {
			$_SESSION['general'] = 'Asset #' . $id . ' is not available for request.';
			route('assets');
			exit;
		}
		
		$isAlreadyRequested = (new SelectAssetController($this->conn))->isAlreadyRequested();
		if ($isAlreadyRequested) {
			view(403);
			exit;
		}

		view('asset.requests.create');
		exit;
	}

	public function findOrFail(int $id): array
	{
		$stmt = $this->conn->prepare("SELECT * FROM asset_requests WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$assetRequest = $stmt->fetch();
		if (empty($assetRequest)) {
			view(404);
			exit;
		}
		return $assetRequest;
	}

	public function validate(array $assetRequest): array
	{
		$errors = [];

		$reason = trim($assetRequest['reason']);
		$dueDate = $assetRequest['due_date'];

		if (empty($reason)) {
			$errors['reason'] = 'Please provide a reason for requesting this asset.';
		} elseif (strlen($reason) < 10) {
			$errors['reason'] = 'Reason must be at least 10 characters long.';
		}

		if (empty($dueDate)) {
			$errors['due_date'] = 'Please select a due date.';
		} else {
			$timestamp = strtotime($dueDate);

			if ($timestamp === false) {
				$errors['due_date'] = 'Please enter valid date and time.';
			} elseif ($dueDate < date('Y-m-d H:i:s')) {
				$errors['due_date'] = 'Due date cannot be in the past.';
			} else {
				$hour = (int) date('H', $timestamp);
				$minute = (int) date('i', $timestamp);
				$totalMinutes= $hour * 60 + $minute;

				if ($totalMinutes < 9 * 60 || $totalMinutes > 18 * 60) {
					$errors['due_date'] = 'Due time must be between 9:00 AM and 6:00 PM.';
				}
			}
		}

		if (!empty($errors)) {
			$errors['old']['reason'] = $reason;
			$errors['old']['due_date'] = $dueDate;
		}
		
		return $errors;
	}

	public function update(int $id, array $updatedAssetRequest, array $assetRequest)
	{
		$value = fn($key) => $assetRequest[$key] ?? $updatedAssetRequest[$key] ?? null;

		$stmt = $this->conn->prepare("
        UPDATE asset_requests
        SET
            status = :status,
            approved_at = :approved_at,
            approved_by = :approved_by,
            rejected_at = :rejected_at,
            rejected_by = :rejected_by,
            rejection_reason = :rejection_reason,
            issued_at = :issued_at,
            issued_by = :issued_by,
            remark = :remark,
            returned_at = :returned_at
        WHERE id = :id
    ");

		$stmt->execute([
			'status' => $updatedAssetRequest['status'],
			'approved_at' => $value('approved_at'),
			'approved_by' => $value('approved_by'),
			'rejected_at' => $value('rejected_at'),
			'rejected_by' => $value('rejected_by'),
			'rejection_reason' => $updatedAssetRequest['rejection_reason'] ?? '',
			'issued_at' => $value('issued_at'),
			'issued_by' => $value('issued_by'),
			'remark' => $updatedAssetRequest['remark'] ?? '',
			'returned_at' => $value('returned_at'),
			'id' => $id,
		]);
	}

	public function pendingRequests(): int
	{
		$sql = 'select count(*) from asset_requests where status = "PENDING"';

		$role = $_SESSION['user_role'];

		if ($role === 'HR' || $role === 'EMPLOYEE') {
			$sql .= ' AND user_id = ' . $_SESSION['user_id'];
		}

		return $this->conn->query($sql)->fetchColumn();
	}

	public function export($option = 'pdf')
	{
		middleware('auth');
		$stmt = $this->conn->query('select * from asset_requests order by status');

		$role = $_SESSION['user_role'];

		if ($role === 'HR' || $role === 'EMPLOYEE') {
			$stmt = $this->conn->prepare('select * from asset_requests where user_id = ? order by status');
			$stmt->execute([$_SESSION['user_id']]);
		}

		$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (strtolower(trim($option)) == 'excel') {
			view('asset.requests.excel', ['requests' => $requests]);
			exit;
		}

		view('asset.requests.pdf', ['requests' => $requests]);
		exit;
	}
}
