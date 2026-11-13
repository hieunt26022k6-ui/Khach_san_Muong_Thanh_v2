<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/AuditLog.php
 *
 * Ghi nhật ký thao tác nhạy cảm (nhận/trả phòng, đổi giá, khóa tài khoản, khôi phục dữ liệu).
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class AuditLog extends BaseModel {
    public static function record(string $action, string $description): void {
        $logs = Session::get('auditLogs', []);
        $user = Session::get('currentUser', null);
        array_unshift($logs, [
            'id'          => 'log_' . uniqid(),
            'action'      => $action,
            'description' => $description,
            'operator'    => $user['username'] ?? 'khách',
            'timestamp'   => date('Y-m-d H:i:s'),
        ]);
        Session::set('auditLogs', array_slice($logs, 0, 200)); // giữ 200 dòng gần nhất
    }
}
