<?php

namespace App\Models;

use PDO;

class Dashboard
{
    public function __construct(private PDO $conn)
    {
    }

    public function data(int $userId, string $role): array
    {
        $role = strtoupper($role);
        $year = (int) date('Y');

        if ($role === 'ADMIN') {
            return [
                'role' => $role,
                'summary' => $this->adminSummary(),
                'recentActivity' => $this->recentActivity(),
                'departmentAssets' => $this->departmentAssets(),
                'monthlyAssignments' => array_values($this->monthlyActivity('issued_at', $year)),
                'monthlyReturns' => array_values($this->monthlyActivity('returned_at', $year)),
                'year' => $year,
            ];
        }

        return [
            'role' => $role,
            'summary' => $this->employeeSummary($userId),
            'recentActivity' => $this->recentActivity($userId),
            'departmentAssets' => [],
            'monthlyAssignments' => [],
            'monthlyReturns' => [],
            'year' => $year,
        ];
    }

    private function adminSummary(): array
    {
        $userStmt = $this->conn->query(
            'SELECT COUNT(*) FROM users WHERE role = "EMPLOYEE" AND deleted_at IS NULL'
        );
        $assetStmt = $this->conn->query(
            'SELECT
                COUNT(*) AS assets,
                COALESCE(SUM(status = "AVAILABLE"), 0) AS available_assets,
                COALESCE(SUM(status IN ("ASSIGNED", "ISSUED")), 0) AS assigned_assets,
                COALESCE(SUM(status = "REPAIR"), 0) AS repair_assets
             FROM assets'
        );

        $assets = $assetStmt->fetch(PDO::FETCH_ASSOC);
        return [
            'employees' => (int) $userStmt->fetchColumn(),
            'assets' => (int) ($assets['assets'] ?? 0),
            'available_assets' => (int) ($assets['available_assets'] ?? 0),
            'assigned_assets' => (int) ($assets['assigned_assets'] ?? 0),
            'repair_assets' => (int) ($assets['repair_assets'] ?? 0),
        ];
    }

    private function employeeSummary(int $userId): array
    {
        $assetStmt = $this->conn->prepare(
            'SELECT COUNT(*) FROM assets WHERE assignee_id = ?'
        );
        $assetStmt->execute([$userId]);

        $requestStmt = $this->conn->prepare(
            'SELECT COUNT(*) FROM asset_requests
             WHERE user_id = ? AND status IN ("PENDING", "APPROVED", "ISSUED")'
        );
        $requestStmt->execute([$userId]);

        return [
            'assigned_assets' => (int) $assetStmt->fetchColumn(),
            'active_requests' => (int) $requestStmt->fetchColumn(),
        ];
    }

    private function recentActivity(?int $userId = null): array
    {
        $params = [];
        $userFilter = '';
        if ($userId !== null) {
            $userFilter = ' AND ar.user_id = ?';
            $params[] = $userId;
        }

        $sql = '
            SELECT *
            FROM (
                SELECT ar.id, ar.asset_id,
                    COALESCE(a.name, ar.asset_name) AS asset_name,
                    u.name AS employee_name,
                    "ASSIGNMENT" AS operation,
                    ar.issued_at AS event_at
                FROM asset_requests ar
                LEFT JOIN assets a ON a.id = ar.asset_id
                LEFT JOIN users u ON u.id = ar.user_id
                WHERE ar.issued_at IS NOT NULL' . $userFilter . '
                UNION ALL
                SELECT ar.id, ar.asset_id,
                    COALESCE(a.name, ar.asset_name) AS asset_name,
                    u.name AS employee_name,
                    "RETURN" AS operation,
                    ar.returned_at AS event_at
                FROM asset_requests ar
                LEFT JOIN assets a ON a.id = ar.asset_id
                LEFT JOIN users u ON u.id = ar.user_id
                WHERE ar.returned_at IS NOT NULL' . $userFilter . '
            ) activity
            ORDER BY event_at DESC, id DESC
            LIMIT 5';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array_merge($params, $params));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function departmentAssets(): array
    {
        $stmt = $this->conn->query(
            'SELECT d.name, COUNT(a.id) AS asset_count
             FROM departments d
             LEFT JOIN users u ON u.department_id = d.id AND u.deleted_at IS NULL
             LEFT JOIN assets a ON a.assignee_id = u.id
             GROUP BY d.id, d.name
             ORDER BY d.name'
        );

        return array_map(static function (array $row): array {
            return ['name' => $row['name'], 'count' => (int) $row['asset_count']];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function monthlyActivity(string $column, int $year): array
    {
        $stmt = $this->conn->prepare(
            "SELECT MONTH($column) AS month_number, COUNT(*) AS total
             FROM asset_requests
             WHERE $column IS NOT NULL AND YEAR($column) = ?
             GROUP BY MONTH($column)"
        );
        $stmt->execute([$year]);

        $months = array_fill(1, 12, 0);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $months[(int) $row['month_number']] = (int) $row['total'];
        }
        return $months;
    }
}
