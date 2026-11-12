<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — HỆ THỐNG ĐẶT PHÒNG & QUẢN TRỊ KHÁCH SẠN
 * KIẾN TRÚC: PHP 8+ OOP MÔ HÌNH MVC (MODEL - VIEW - CONTROLLER)
 * KHỞI CHẠY TRỰC TIẾP TRÊN XAMPP (APACHE & MYSQL) HOẶC VISUAL STUDIO CODE
 */

declare(strict_types=1);

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Báo cáo mọi lỗi; việc có HIỂN THỊ lỗi ra trang hay không do Config::DEBUG quyết định (xem bên dưới)
error_reporting(E_ALL);

// SPL AUTOLOADER: Tự động tải các lớp theo không gian tên (PSR-4)
spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    $fileVariants = [
        __DIR__ . DIRECTORY_SEPARATOR . $classPath . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . strtolower($classPath) . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . lcfirst($classPath) . '.php',
    ];

    $parts = explode('\\', $class);
    if (count($parts) >= 2) {
        $folder = strtolower($parts[0]);
        $subclass = $parts[1];
        $fileVariants[] = __DIR__ . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $subclass . '.php';
    }

    foreach ($fileVariants as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// [SỬA HT-E04] Chỉ hiện lỗi chi tiết khi đang phát triển; bản triển khai thật đặt Config::DEBUG = false
ini_set('display_errors', \Config\Config::DEBUG ? '1' : '0');
ini_set('log_errors', '1');

// Khởi tạo phiên làm việc Session
Core\Session::start();

// 1. Xử lý API hoặc Chế độ truyền thống nếu có tham số controller/action
if (isset($_GET['controller']) && $_GET['controller'] === 'api') {
    Core\Router::dispatch();
    exit;
}

// [SỬA HT-E03] Mọi địa chỉ có tham số controller đều đi qua Router, nên toàn bộ liên kết và biểu mẫu
// trong View PHP (index.php?controller=...) hoạt động mà không cần thêm view_mode=classic_php.
// Chỉ trang gốc (không có controller) mới mở giao diện đóng gói.
if (isset($_GET['controller']) || (isset($_GET['view_mode']) && $_GET['view_mode'] === 'classic_php')) {
    Core\Router::dispatch();
    exit;
}

// 2. Chuyển đổi vai trò nhanh qua URL (vd: ?role=customer | receptionist | admin)
// [SỬA HT-E07] Chuyển vai trò không cần đăng nhập => chỉ được bật khi Config::DEMO_MODE = true
if (isset($_GET['role']) && \Config\Config::DEMO_MODE) {
    $targetRole = $_GET['role'];
    $users = Core\Session::get('users', []);
    foreach ($users as $u) {
        if ($u['role'] === $targetRole) {
            Core\Session::set('currentUser', $u);
            break;
        }
    }
    header('Location: index.php');
    exit;
}

// 3. Khởi tạo toàn bộ các Models OOP cốt lõi
$userModel = new Models\User();
$roomModel = new Models\Room();
$bookingModel = new Models\Booking();
$serviceModel = new Models\ServiceOrder();
$reportModel = new Models\Report();

// 4. Thu thập toàn bộ trạng thái dữ liệu nghiệp vụ khách sạn từ PHP Models
$currentUser = Core\Session::get('currentUser', null);

// [SỬA LẦN 2] Nhân viên/quản trị đã đăng nhập bằng phiên PHP thì KHÔNG đưa vào giao diện React
// (giao diện đó phân quyền ở trình duyệt). Đưa thẳng sang bảng điều khiển PHP có kiểm tra quyền ở máy chủ.
if ($currentUser && in_array($currentUser['role'], ['admin', 'receptionist'], true)) {
    $target = $currentUser['role'] === 'admin'
        ? 'index.php?controller=admin&action=index'
        : 'index.php?controller=receptionist&action=index';
    header('Location: ' . $target);
    exit;
}

// [SỬA LẦN 2] Không bao giờ đẩy mật khẩu (kể cả mã băm) ra HTML cho trình duyệt đọc.
$publicUsers = array_map(function (array $u): array {
    unset($u['password']);
    return $u;
}, Core\Session::get('users', []));

$serverState = [
    'currentUser' => $currentUser,
    'users' => $publicUsers,
    'roomCategories' => Core\Session::get('roomCategories', []),
    'physicalRooms' => Core\Session::get('physicalRooms', []),
    'packages' => Core\Session::get('packages', []),
    'services' => Core\Session::get('services', []),
    'bookings' => Core\Session::get('bookings', []),
    'serviceOrders' => Core\Session::get('serviceOrders', []),
    'incidents' => Core\Session::get('incidents', []),
    'reviews' => Core\Session::get('reviews', []),
    'policy' => Core\Session::get('policy', null),
    'templates' => Core\Session::get('templates', []),
    'sentNotifications' => Core\Session::get('sentNotifications', []),
    'auditLogs' => Core\Session::get('auditLogs', []),
];

// 5. Kết xuất View giao diện chính (Views/Layouts/app.php)
require __DIR__ . '/views/layouts/app.php';
