<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/AdminController.php
 */

namespace Controllers;

use Core\BaseController;
use Core\Session;
use Models\Report;
use Models\User;
use Models\Room;
use Models\Booking;
use Models\AuditLog;

class AdminController extends BaseController {
    private Report $reportModel;
    private User $userModel;
    private Room $roomModel;
    private Booking $bookingModel;

    public function __construct() {
        $this->reportModel = new Report();
        $this->userModel = new User();
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
    }

    /**
     * Bảng điều khiển Quản trị viên (Báo cáo doanh thu & Quản lý)
     */
    public function index(): void {
        $this->requireRole('admin');

        $analytics = $this->reportModel->getAnalytics();
        $categories = $this->roomModel->getCategories();
        $auditLogs = Session::get('auditLogs', []);

        // [SỬA LẦN 2] Tìm kiếm + phân trang — hai yêu cầu bắt buộc của đề mà bản trước chưa có.
        $keyword = trim((string)($_GET['q'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));

        $users = $this->userModel->getAll();
        if ($keyword !== '') {
            $users = array_values(array_filter($users, fn(array $u): bool =>
                $this->containsKeyword($u, ['fullName', 'username', 'email', 'phone', 'role'], $keyword)));
        }
        $userPager = $this->paginateRows($users, $page);

        $bookings = $this->bookingModel->getAll();
        if ($keyword !== '') {
            $bookings = array_values(array_filter($bookings, fn(array $b): bool =>
                $this->containsKeyword($b, ['bookingCode', 'customerName', 'categoryName', 'assignedRoomNumber', 'status'], $keyword)));
        }
        $bookingPager = $this->paginateRows($bookings, $page);

        if ($keyword !== '') {
            $auditLogs = array_values(array_filter($auditLogs, fn(array $l): bool =>
                $this->containsKeyword($l, ['action', 'description', 'operator'], $keyword)));
        }
        $logPager = $this->paginateRows($auditLogs, $page);

        $this->render('admin/dashboard', [
            'pageTitle' => 'Trung Tâm Quản Trị Hệ Thống — Mường Thanh Luxury Phú Quốc',
            'analytics' => $analytics,
            'users' => $userPager['rows'],
            'userTotal' => $userPager['total'],
            'userPages' => $userPager['pages'],
            'categories' => $categories,
            'bookings' => $bookingPager['rows'],
            'bookingTotal' => $bookingPager['total'],
            'bookingPages' => $bookingPager['pages'],
            'auditLogs' => $logPager['rows'],
            'logTotal' => $logPager['total'],
            'logPages' => $logPager['pages'],
            'keyword' => $keyword,
            'page' => $page,
            'dailyRevenue' => $this->reportModel->getDailyRevenue(7),
            'policy' => Session::get('policy', []),
            'activeNav' => 'admin',
            'isStaffPortal' => true,
        ]);
    }

    /** [SỬA LẦN 2] Tạo tài khoản nhân viên (quản lý người dùng theo đề bài). */
    public function createUser(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->userModel->createStaff($_POST);
            if ($result['ok']) {
                AuditLog::record('USER_CREATE', "Tạo tài khoản {$_POST['username']} với vai trò {$_POST['role']}");
                Session::flash('success', $result['message']);
            } else {
                Session::flash('error', $result['message']);
                Session::set('old_input', [
                    'username' => $_POST['username'] ?? '',
                    'fullName' => $_POST['fullName'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'role' => $_POST['role'] ?? 'receptionist',
                ]);
            }
        }
        $this->redirect('index.php?controller=admin&action=index&tab=users');
    }

    /** [SỬA LẦN 2] Gán lại vai trò cho tài khoản. */
    public function updateRole(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (string)($_POST['userId'] ?? '');
            $role = (string)($_POST['role'] ?? '');
            if ($userId && $this->userModel->updateRole($userId, $role)) {
                AuditLog::record('USER_ROLE', "Đổi vai trò tài khoản {$userId} thành {$role}");
                Session::flash('success', "Đã gán vai trò {$role} cho tài khoản.");
            } else {
                Session::flash('error', 'Không đổi được vai trò (tài khoản quản trị không bị hạ quyền).');
            }
        }
        $this->redirect('index.php?controller=admin&action=index&tab=users');
    }

    /**
     * [SỬA LẦN 2] Cập nhật chính sách cọc & hủy — trước đây tab này chỉ đọc (readonly),
     * trái với yêu cầu AD-12 "thiết lập chính sách, chỉ áp dụng cho đơn mới".
     */
    public function updatePolicy(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $policy = Session::get('policy', []);
            $deposit = (int)($_POST['depositPercent'] ?? 0);
            $freeCancel = (int)($_POST['freeCancelBeforeHours'] ?? 0);
            $childNote = trim((string)($_POST['childPolicy'] ?? ''));

            if ($deposit < 0 || $deposit > 100) {
                Session::flash('error', 'Tỉ lệ cọc phải trong khoảng 0–100%.');
                $this->redirect('index.php?controller=admin&action=index&tab=policy');
            }
            if ($freeCancel < 0 || $freeCancel > 720) {
                Session::flash('error', 'Số giờ hủy miễn phí phải trong khoảng 0–720 giờ.');
                $this->redirect('index.php?controller=admin&action=index&tab=policy');
            }

            $policy['depositRequiredPercent'] = $deposit;
            $policy['freeCancelBeforeHours'] = $freeCancel;
            if ($childNote !== '') $policy['childPolicyNote'] = $childNote;
            $policy['updatedAt'] = date('Y-m-d H:i:s');

            Session::set('policy', $policy);
            AuditLog::record('POLICY_UPDATE', "Cọc {$deposit}% · hủy miễn phí trước {$freeCancel} giờ");
            Session::flash('success', 'Đã lưu chính sách mới. Chính sách chỉ áp dụng cho đơn đặt mới.');
        }
        $this->redirect('index.php?controller=admin&action=index&tab=policy');
    }

    /**
     * [SỬA LẦN 2] Xử lý file: tải ảnh đại diện cho hạng phòng (yêu cầu "xử lý file" của đề).
     */
    public function uploadCategoryImage(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=admin&action=index&tab=room_categories');
        }

        $catId = (string)($_POST['categoryId'] ?? '');
        $result = $this->roomModel->saveCategoryImage($catId, $_FILES['image'] ?? null);

        if ($result['ok']) {
            AuditLog::record('ROOM_IMAGE', "Cập nhật ảnh hạng phòng {$catId}");
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }
        $this->redirect('index.php?controller=admin&action=index&tab=room_categories');
    }

    /**
     * Khóa / Mở khóa tài khoản người dùng
     */
    public function toggleUserLock(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'] ?? '';
            if ($userId) {
                if ($this->userModel->toggleLock($userId)) {
                    AuditLog::record('USER_LOCK', "Đổi trạng thái khóa tài khoản {$userId}");
                    Session::flash('success', 'Đã cập nhật trạng thái tài khoản thành công.');
                } else {
                    Session::flash('error', 'Không thể khóa tài khoản này (không tồn tại hoặc là tài khoản quản trị).');
                }
            }
        }
        $this->redirect('index.php?controller=admin&action=index');
    }

    /**
     * Cập nhật giá niêm yết hạng phòng
     */
    public function updatePrice(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $catId = $_POST['categoryId'] ?? '';
            $newPrice = (int)($_POST['basePrice'] ?? 0);
            if ($catId && $newPrice > 0 && $this->roomModel->updateCategoryPrice($catId, $newPrice)) {
                AuditLog::record('PRICE_UPDATE', "Đặt giá hạng {$catId} = {$newPrice}");
                Session::flash('success', 'Đã lưu giá niêm yết mới thành công.');
            } else {
                Session::flash('error', 'Giá không hợp lệ hoặc hạng phòng không tồn tại.');
            }
        }
        $this->redirect('index.php?controller=admin&action=index');
    }

    /**
     * Khôi phục dữ liệu mẫu ban đầu
     */
    public function resetData(): void {
        $this->requireRole('admin');

        // [SỬA HT-E06] Thao tác xóa dữ liệu chỉ nhận POST (đã kèm kiểm tra CSRF ở Router)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::flash('error', 'Khôi phục dữ liệu phải gửi bằng nút xác nhận trong bảng quản trị.');
            $this->redirect('index.php?controller=admin&action=index');
        }

        Session::remove('__mt_initialized');
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
            Session::set('__mt_initialized', true);
            AuditLog::record('RESET_DATA', 'Khôi phục dữ liệu mẫu');
            Session::flash('success', 'Đã khôi phục toàn bộ dữ liệu mẫu ban đầu thành công!');
        }
        $this->redirect('index.php?controller=admin&action=index');
    }
}
