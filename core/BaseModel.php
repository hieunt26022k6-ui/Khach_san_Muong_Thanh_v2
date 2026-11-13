<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP 8 OOP MVC
 * Core/BaseModel.php — lớp cha của mọi Model, giữ kết nối CSDL và nạp dữ liệu mẫu lần đầu
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
     * Lần chạy đầu tiên (CSDL vừa import, chưa có dữ liệu) thì nạp bộ dữ liệu mẫu.
     * Nếu kho dữ liệu bị xoá sạch bất thường (không còn tài khoản nào) cũng tự nạp lại,
     * để hệ thống không bao giờ hiện trang trắng trước mặt giảng viên.
     */
    protected function initMockStorage(): void {
        Session::start();

        $initialized = (bool)Session::get('__mt_initialized', false);
        $hasUsers = !empty(Session::get('users', []));
        if ($initialized && $hasUsers) {
            return;
        }

        $seedFile = __DIR__ . '/../database/seed_data.php';
        if (!file_exists($seedFile)) {
            return;
        }
        $seed = require $seedFile;
        if (!is_array($seed)) {
            return;
        }
        Store::seedFromArray($seed);
    }
}
