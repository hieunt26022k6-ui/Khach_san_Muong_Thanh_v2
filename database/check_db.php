<?php
/**
 * Script hỗ trợ khi phát triển: kiểm tra dữ liệu đã nạp trong MySQL.
 * Chạy: C:\xampp\php\php.exe database\check_db.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
spl_autoload_register(function ($class) use ($root) {
    $path = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) { require_once $path; return; }
    $parts = explode('\\', $class);
    $path2 = $root . '/' . strtolower($parts[0]) . '/' . ($parts[1] ?? '') . '.php';
    if (file_exists($path2)) { require_once $path2; }
});

new Models\User(); // khởi tạo Model => nạp dữ liệu mẫu nếu CSDL còn trống

echo "Driver: " . Core\Store::driver() . PHP_EOL;
$pdo = Config\Database::getConnection();
if ($pdo === null) { echo "Không kết nối được MySQL\n"; exit(1); }

$tables = ['users','user_profiles','room_categories','physical_rooms','packages','services',
           'bookings','service_orders','booking_services','audit_logs','incidents','reviews',
           'notification_templates','sent_notifications','hotel_policy'];
foreach ($tables as $t) {
    $n = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    printf("%-24s %3d\n", $t, $n);
}
