<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/AdminController.php
 */

namespace Controllers;

use Core\BaseController;
use Core\Session;
use Core\Store;
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

        // Tìm kiếm + phân trang — hai yêu cầu bắt buộc của đề mà bản trước chưa có.
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
            'packages' => $this->roomModel->getPackages(),
            'physicalRooms' => $this->roomModel->getPhysicalRooms(),
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

    /** Tạo tài khoản nhân viên (quản lý người dùng theo đề bài). */
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

    /** Gán lại vai trò cho tài khoản. */
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
     * Cập nhật chính sách cọc & hủy — trước đây tab này chỉ đọc (readonly),
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

            $lateCancel = (int)($_POST['lateCancelPenaltyPercent'] ?? 0);
            if ($lateCancel < 0 || $lateCancel > 100) {
                Session::flash('error', 'Mức phạt hủy muộn phải trong khoảng 0–100%.');
                $this->redirect('index.php?controller=admin&action=index&tab=policy');
            }

            $policy['depositRequiredPercent'] = $deposit;
            $policy['freeCancelBeforeHours'] = $freeCancel;
            $policy['lateCancelPenaltyPercent'] = $lateCancel;
            if ($childNote !== '') $policy['childPolicyNote'] = $childNote;
            $policy['updatedAt'] = date('Y-m-d H:i:s');

            Session::set('policy', $policy);
            AuditLog::record('POLICY_UPDATE', "Cọc {$deposit}% · hủy miễn phí trước {$freeCancel} giờ · phạt hủy muộn {$lateCancel}%");
            Session::flash('success', 'Đã lưu chính sách mới. Chính sách chỉ áp dụng cho đơn đặt mới.');
        }
        $this->redirect('index.php?controller=admin&action=index&tab=policy');
    }

    /**
     * AD-12: cập nhật gói phòng (phụ thu / đêm) và bật-tắt kinh doanh gói.
     */
    public function updatePackage(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $packageId = (string)($_POST['packageId'] ?? '');
            $priceOffset = (int)($_POST['priceOffset'] ?? 0);
            $isActive = !empty($_POST['isActive']);

            if ($priceOffset < 0) {
                Session::flash('error', 'Phụ thu gói phòng không được âm.');
            } elseif ($this->roomModel->updatePackage($packageId, $priceOffset, $isActive)) {
                AuditLog::record('PACKAGE_UPDATE', 'Cập nhật gói phòng ' . $packageId
                    . ' · phụ thu ' . number_format($priceOffset, 0, ',', '.') . ' ₫/đêm · '
                    . ($isActive ? 'đang bán' : 'tạm ngưng'));
                Session::flash('success', 'Đã cập nhật gói phòng. Thay đổi chỉ áp dụng cho đơn đặt mới.');
            } else {
                Session::flash('error', 'Không tìm thấy gói phòng cần cập nhật.');
            }
        }
        $this->redirect('index.php?controller=admin&action=index&tab=room_categories');
    }

    /**
     * AD-12: khóa phòng bảo trì (không xóa dữ liệu) hoặc mở khai thác lại.
     */
    public function setRoomMaintenance(): void {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomNumber = trim((string)($_POST['roomNumber'] ?? ''));
            $action = (string)($_POST['maintenanceAction'] ?? 'lock');
            $lock = $action === 'lock';
            $reason = trim((string)($_POST['reason'] ?? ''));
            $until = trim((string)($_POST['until'] ?? ''));

            if ($roomNumber === '') {
                Session::flash('error', 'Chưa chọn phòng cần xử lý.');
            } elseif ($this->roomModel->setRoomMaintenance($roomNumber, $lock, $reason, $until)) {
                AuditLog::record('ROOM_MAINTENANCE', ($lock ? 'Khóa bảo trì phòng ' : 'Mở khai thác lại phòng ')
                    . $roomNumber . ($lock && $reason !== '' ? ' · lý do: ' . $reason : ''));
                Session::flash('success', $lock
                    ? "Đã khóa phòng {$roomNumber} để bảo trì (không xóa khỏi hệ thống)."
                    : "Đã mở khai thác lại phòng {$roomNumber}.");
            } else {
                Session::flash('error', 'Không tìm thấy phòng ' . $roomNumber . '.');
            }
        }
        $this->redirect('index.php?controller=admin&action=index&tab=room_categories');
    }

    /**
     * Xử lý file: tải ảnh đại diện cho hạng phòng (yêu cầu "xử lý file" của đề).
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

        // Thao tác xóa dữ liệu chỉ nhận POST (đã kèm kiểm tra CSRF ở Router)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::flash('error', 'Khôi phục dữ liệu phải gửi bằng nút xác nhận trong bảng quản trị.');
            $this->redirect('index.php?controller=admin&action=index');
        }

        // Nạp lại TOÀN BỘ các nhóm dữ liệu (người dùng, phòng, gói, dịch vụ, đơn, sự cố,
        // đánh giá, chính sách, mẫu thông báo, nhật ký) trong một giao dịch duy nhất.
        if (Store::resetAll()) {
            AuditLog::record('RESET_DATA', 'Khôi phục toàn bộ dữ liệu mẫu');
            Session::flash('success', 'Đã khôi phục toàn bộ dữ liệu mẫu ban đầu thành công!');
        } else {
            Session::flash('error', 'Không tìm thấy tệp dữ liệu mẫu để khôi phục.');
        }
        $this->redirect('index.php?controller=admin&action=index');
    }
}
