<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/CustomerController.php
 */

namespace Controllers;

use Core\BaseController;
use Core\Session;
use Models\Room;
use Models\Booking;
use Models\ServiceOrder;
use Models\Voucher;

class CustomerController extends BaseController {
    private Room $roomModel;
    private Booking $bookingModel;
    private ServiceOrder $serviceModel;

    public function __construct() {
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
        $this->serviceModel = new ServiceOrder();
    }

    /**
     * Trang chủ AVS Resort 5 sao
     */
    public function index(): void {
        $categories = $this->roomModel->getCategories();
        $packages = $this->roomModel->getPackages();
        $this->render('customer/home', [
            'pageTitle' => 'Mường Thanh Luxury Phú Quốc — Trang Chủ Nghỉ Dưỡng',
            'categories' => $categories,
            'packages' => $packages,
            'activeNav' => 'home'
        ]);
    }

    /**
     * Khám phá 9 hạng phòng & Đặt phòng chuẩn AVS 2 cột
     */
    public function rooms(): void {
        $categories = $this->roomModel->getCategories();
        $packages = $this->roomModel->getPackages();
        $selectedRoomId = $_GET['roomId'] ?? 'dtcv';

        $this->render('customer/rooms', [
            'pageTitle' => 'Khám Phá Hạng Phòng — Mường Thanh Luxury Phú Quốc',
            'categories' => $categories,
            'packages' => $packages,
            'selectedRoomId' => $selectedRoomId,
            'activeNav' => 'rooms'
        ]);
    }

    /**
     * Xử lý xác nhận đặt phòng
     */
    public function book(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=customer&action=rooms');
        }

        $catId = $_POST['categoryId'] ?? '';
        $category = $this->roomModel->findCategory($catId);
        if (!$category) {
            Session::flash('error', 'Hạng phòng không hợp lệ.');
            $this->redirect('index.php?controller=customer&action=rooms');
        }

        // --- Kiểm tra thông tin liên hệ ---
        $name  = trim($_POST['customerName'] ?? '');
        $phone = trim($_POST['customerPhone'] ?? '');
        if ($name === '' || mb_strlen($name) > 100 || !preg_match('/^[0-9+\s().-]{8,20}$/', $phone)) {
            Session::flash('error', 'Vui lòng nhập họ tên và số điện thoại hợp lệ (8–20 ký tự số).');
            $this->redirect('index.php?controller=customer&action=rooms&roomId=' . urlencode($catId));
        }

        // --- Chuẩn hóa ngày: số đêm lấy từ ngày, không tin số đêm do trình duyệt gửi ---
        $today = date('Y-m-d');
        $in  = $this->parseDate($_POST['checkInDate'] ?? '') ?? $today;
        $out = $this->parseDate($_POST['checkOutDate'] ?? '');
        if ($in < $today) {
            Session::flash('error', 'Ngày nhận phòng không được ở quá khứ.');
            $this->redirect('index.php?controller=customer&action=rooms&roomId=' . urlencode($catId));
        }
        if ($out === null || $out <= $in) {
            $nightsPost = max(1, (int)($_POST['nights'] ?? 1));
            $out = date('Y-m-d', strtotime($in . ' +' . $nightsPost . ' day'));
        }
        $nights = max(1, (int)((strtotime($out) - strtotime($in)) / 86400));

        // --- [SỬA KH-E04] Kiểm tra còn phòng trong khoảng ngày ---
        $stock = $this->roomModel->countByCategory($catId);
        $taken = $this->bookingModel->countOverlapping($catId, $in, $out);
        if ($taken >= $stock) {
            Session::flash('error', "Hạng phòng {$category['name']} đã hết phòng từ {$in} đến {$out}. Vui lòng chọn ngày hoặc hạng phòng khác.");
            $this->redirect('index.php?controller=customer&action=rooms&roomId=' . urlencode($catId));
        }

        // --- [SỬA KH-E02] Máy chủ tự tra phụ thu gói và mức giảm, KHÔNG tin số tiền từ biểu mẫu ---
        $package = $this->roomModel->findPackage($_POST['packageId'] ?? '')
                   ?? $this->roomModel->findPackage('pkg_room_only');
        $pkgOffset = (int)($package['priceOffset'] ?? 0);

        $subTotal = ((int)$category['basePrice'] + $pkgOffset) * $nights;
        $voucher  = Voucher::apply((string)($_POST['voucherCode'] ?? ''), $subTotal);
        $totalAmount = max(0, $subTotal - $voucher['discount']);
        $depositPaid = (int)round($totalAmount * \Config\Config::DEPOSIT_PERCENT / 100);

        $newBooking = $this->bookingModel->create([
            'customerName'  => $name,
            'customerPhone' => $phone,
            'customerEmail' => trim($_POST['customerEmail'] ?? ''),
            'categoryId'    => $catId,
            'categoryName'  => $category['name'],
            'checkInDate'   => $in,
            'checkOutDate'  => $out,
            'nights'        => $nights,
            'packageId'     => $package['id'] ?? 'pkg_room_only',
            'packageName'   => $package['name'] ?? 'Gói Tiêu Chuẩn',
            'totalAmount'   => $totalAmount,
            'depositPaid'   => $depositPaid,
        ]);

        // [SỬA KH-E01] Tính chuỗi tiền TRƯỚC khi nối vào thông báo (PHP không gọi hàm bên trong chuỗi)
        $depositText = number_format($depositPaid, 0, ',', '.');
        Session::flash('success', "Đặt phòng thành công! Mã đơn của quý khách là: {$newBooking['bookingCode']}. Vui lòng chuẩn bị cọc 30% ({$depositText} ₫) để hoàn tất.");
        $this->redirect('index.php?controller=customer&action=portal&bookingCode=' . $newBooking['bookingCode']);
    }

    /** Trả về ngày dạng Y-m-d nếu hợp lệ, ngược lại null */
    private function parseDate(string $value): ?string {
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return ($d && $d->format('Y-m-d') === $value) ? $value : null;
    }

    /**
     * Cổng khách hàng lưu trú (Tra cứu, Gọi dịch vụ phòng QR, Hóa đơn)
     */
    public function portal(): void {
        $currentUser = $this->getCurrentUser();
        $bookingCode = $_GET['bookingCode'] ?? '';
        $phone = $currentUser['phone'] ?? '';

        $myBookings = [];
        if ($bookingCode) {
            $b = $this->bookingModel->findByCode($bookingCode);
            if ($b) $myBookings[] = $b;
        } elseif ($phone) {
            $myBookings = $this->bookingModel->findByCustomerPhone($phone);
        }
        // [SỬA KH-E03] Không còn trả toàn bộ đơn cho người chưa đăng nhập / không nhập mã đơn

        $services = $this->serviceModel->getServices();
        $myOrders = $this->serviceModel->getAll();

        $this->render('customer/portal', [
            'pageTitle' => 'Cổng Dịch Vụ Khách Hàng — Mường Thanh Luxury Phú Quốc',
            'myBookings' => $myBookings,
            'services' => $services,
            'myOrders' => $myOrders,
            'activeNav' => 'portal',
            'subTab' => $_GET['tab'] ?? 'bookings'
        ]);
    }

    /**
     * Gọi dịch vụ phòng
     */
    public function orderService(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=customer&action=portal&tab=services');
        }

        // [SỬA SV-E01] Đơn giá lấy từ danh mục dịch vụ phía máy chủ, số lượng tối thiểu 1
        $service = $this->serviceModel->findService((string)($_POST['serviceName'] ?? ''));
        $roomNumber = trim($_POST['roomNumber'] ?? '');
        $room = $this->roomModel->findRoom($roomNumber);

        if ($service === null) {
            Session::flash('error', 'Dịch vụ không tồn tại.');
        } elseif ($room === null || $room['status'] !== 'occupied') {
            Session::flash('error', 'Chỉ đặt được dịch vụ cho phòng đang có khách lưu trú. Vui lòng kiểm tra lại số phòng.');
        } else {
            $quantity = max(1, min(20, (int)($_POST['quantity'] ?? 1)));
            $this->serviceModel->create([
                'bookingCode'   => trim($_POST['bookingCode'] ?? '') ?: 'WALK_IN',
                'roomNumber'    => $roomNumber,
                'guestName'     => mb_substr(trim($_POST['guestName'] ?? 'Quý Khách'), 0, 100),
                'serviceName'   => $service['name'],
                'quantity'      => $quantity,
                'unitPrice'     => (int)$service['price'],
                'scheduledTime' => preg_match('/^\d{2}:\d{2}$/', $_POST['scheduledTime'] ?? '') ? $_POST['scheduledTime'] : date('H:i'),
                'note'          => mb_substr(trim($_POST['note'] ?? 'Phục vụ tận phòng'), 0, 300),
            ]);
            \Models\AuditLog::record('SERVICE_ORDER', "Đơn dịch vụ {$service['name']} x{$quantity} cho phòng {$roomNumber}");
            Session::flash('success', "Đã ghi nhận yêu cầu dịch vụ '{$service['name']}' cho phòng {$roomNumber}. Nhân viên sẽ phục vụ trong ít phút.");
        }
        $this->redirect('index.php?controller=customer&action=portal&tab=services');
    }

    /**
     * Trang ẩm thực Sonasea
     */
    public function dining(): void {
        $this->render('customer/dining', [
            'pageTitle' => 'Ẩm Thực Sonasea & Sky Lounge — Mường Thanh Luxury Phú Quốc',
            'activeNav' => 'dining'
        ]);
    }

    /**
     * Trang Pearl Spa
     */
    public function spa(): void {
        $this->render('customer/spa', [
            'pageTitle' => 'Pearl Spa & Chăm Sóc Sức Khỏe — Mường Thanh Luxury Phú Quốc',
            'activeNav' => 'spa'
        ]);
    }

    /**
     * Trang Hội nghị & Grand Ballroom
     */
    public function conference(): void {
        $this->render('customer/conference', [
            'pageTitle' => 'Hội Nghị & Sự Kiện — Mường Thanh Luxury Phú Quốc',
            'activeNav' => 'conference'
        ]);
    }
}
