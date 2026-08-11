<?php

namespace App\Models;

use PDO;

class AuditLog
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function log(
        $action = 'OTHER',
        $asset_id = null,
        $user_id = null
    ) {

        $action = $this->validatedAction($action);
        $asset_id = $this->validatedAssetId($asset_id);

        $user_id = $user_id ?? $_SESSION['user_id'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt = $this->conn->prepare('
        insert into audit_log (action, user_id, asset_id, user_ip, user_browser)
        values (?, ?, ?, ?, ?)');

        $stmt->execute([
            $action,
            $user_id,
            $asset_id,
            $user_ip,
            $user_browser
        ]);
    }

    private function getActions()
    {
        return [
            'LOGIN',
            'LOGOUT',
            'PASSWORD_CHANGE',
            'USER_CREATION',
            'ASSET_ASSIGNMENT',
            'ASSET_RETURN',
            'OTHER',
        ];
    }

    private function validatedAction($action) {
        $action = strtoupper(trim($action));
        if (! in_array($action, $this->getActions())) {
            return 'OTHER';
        }

        return $action;
    }

    private function validatedAssetId($assetId) {
        if (!$assetId)
            return null;

        $stmt = $this->conn->prepare('select id from assets where id = ?');
        $stmt->execute([$assetId]);

        return $stmt->rowCount() > 0 ? $assetId : null;
    }
}
