<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Core/BaseModel.php
 */

namespace Core;

use Config\Database;
use PDO;

abstract class BaseModel {
    protected ?PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->initMockStorage();
    }

    /**
     * Initializes Session-backed persistent mock storage if MySQL isn't imported yet
     */
    protected function initMockStorage(): void {
        Session::start();
        // [SỬA LẦN 2] Bổ sung điều kiện "users rỗng" : nếu kho dữ liệu bị mất/ghi hỏng thì tự nạp lại
        // dữ liệu mẫu, thay vì chạy tiếp với hệ thống trắng (trước đây chỉ dựa vào cờ __mt_initialized).
        if (!Session::has('__mt_initialized') || empty(Session::get('users', []))) {
            $seedFile = __DIR__ . '/../database/seed_data.php';
            if (file_exists($seedFile)) {
                $seed = require $seedFile;
                Session::set('users', $seed['users'] ?? []);
                Session::set('roomCategories', $seed['roomCategories'] ?? []);
                Session::set('physicalRooms', $seed['physicalRooms'] ?? []);
                Session::set('packages', $seed['packages'] ?? []);
                Session::set('services', $seed['services'] ?? []);
                Session::set('bookings', $seed['bookings'] ?? []);
                Session::set('serviceOrders', $seed['serviceOrders'] ?? []);
                Session::set('auditLogs', $seed['auditLogs'] ?? []);
                Session::set('incidents', $seed['incidents'] ?? []);
                Session::set('reviews', $seed['reviews'] ?? []);
                Session::set('policy', $seed['policy'] ?? null);
                Session::set('templates', $seed['templates'] ?? []);
                Session::set('sentNotifications', $seed['sentNotifications'] ?? []);
                Session::set('__mt_initialized', true);
            }
        }
    }
}
