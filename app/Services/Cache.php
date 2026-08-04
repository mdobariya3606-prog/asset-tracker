<?php

namespace App\Services;

use PDO;
use Exception;

class Cache
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->ensureCacheTableExists();
    }

    /**
     * Ensure the cache table exists in the database.
     */
    private function ensureCacheTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `cache` (
            `key` VARCHAR(255) NOT NULL,
            `value` MEDIUMTEXT NOT NULL,
            `expiration` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->conn->exec($sql);
    }

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $stmt = $this->conn->prepare("SELECT `value`, `expiration` FROM `cache` WHERE `key` = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return $default;
            }

            // Check if expired
            if (time() >= (int)$row['expiration']) {
                $this->forget($key);
                return $default;
            }

            return unserialize($row['value']);
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function put(string $key, mixed $value, int $seconds): bool
    {
        try {
            // Remove expired records automatically to keep the table clean
            $cleanStmt = $this->conn->prepare("DELETE FROM `cache` WHERE `expiration` < ?");
            $cleanStmt->execute([time()]);

            $expiration = time() + $seconds;
            $serialized = serialize($value);

            $stmt = $this->conn->prepare("
                INSERT INTO `cache` (`key`, `value`, `expiration`)
                VALUES (:key, :value, :expiration)
                ON DUPLICATE KEY UPDATE
                    `value` = VALUES(`value`),
                    `expiration` = VALUES(`expiration`)
            ");

            return $stmt->execute([
                'key' => $key,
                'value' => $serialized,
                'expiration' => $expiration
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM `cache` WHERE `key` = ?");
            return $stmt->execute([$key]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieve the remaining seconds until expiration, or 0 if expired/not found.
     *
     * @param string $key
     * @return int
     */
    public function timeToLive(string $key): int
    {
        try {
            $stmt = $this->conn->prepare("SELECT `expiration` FROM `cache` WHERE `key` = ?");
            $stmt->execute([$key]);
            $expiration = $stmt->fetchColumn();

            if (!$expiration) {
                return 0;
            }

            $remaining = (int)$expiration - time();
            return $remaining > 0 ? $remaining : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}