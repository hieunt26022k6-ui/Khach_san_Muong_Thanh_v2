<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Config/Database.php
 */

namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private static bool $connected = false;

    public static function getConnection(): ?PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $dbname = 'muongthanh_hotel';
            $user = 'root';
            $pass = ''; // Mặc định XAMPP không có mật khẩu

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, $user, $pass, $options);
                self::$connected = true;
            } catch (PDOException $e) {
                // Nếu chưa tạo CSDL MySQL trong phpMyAdmin, hệ thống chuyển sang chế độ Session Store tự động
                self::$connected = false;
                self::$instance = null;
            }
        }
        return self::$instance;
    }

    public static function isConnected(): bool {
        if (self::$instance === null) {
            self::getConnection();
        }
        return self::$connected;
    }
}
