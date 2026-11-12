<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/ReceptionistController.php
 */

namespace Controllers;

use Core\BaseController;
use Core\Session;
use Models\Room;
use Models\Booking;
use Models\ServiceOrder;
use Models\AuditLog;

class ReceptionistController extends BaseController {
    private Room $roomModel;
    private Booking $bookingModel;
    private ServiceOrder $serviceModel;

    public function __construct() {
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
        $this->serviceModel = new ServiceOrder();
    }

    /**
     * Bàn Lễ tân Front Desk PMS (Sơ đồ phòng tầng 2-8 & Danh sách khách lưu trú)
     */
    public function index(): void {
        $this->requireRole('receptionist');

        $floors = $this->roomModel->getRoomsGroupedByFloor();
        $physicalRooms = $this->roomModel->getPhysicalRooms();
        $bookings = $this->bookingModel->getAll();
        $serviceOrders = $this->serviceModel->getAll();

        $this->render('receptionist/dashboard', [
            'pageTitle' => 'Bàn Lễ Tân Front Desk PMS — Mường Thanh Luxury Phú Quốc',
            'floors' => $floors,
            'physicalRooms' => $physicalRooms,
            'bookings' => $bookings,
            'serviceOrders' => $serviceOrders,
            'activeNav' => 'receptionist',
            'isStaffPortal' => true,
        ]);
    }

    /**
     * Thao tác Check-in (Nhận phòng)
     */
    public function checkIn(): void {
        $this->requireRole('receptionist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingCode = trim($_POST['bookingCode'] ?? '');
            $roomNumber = trim($_POST['roomNumber'] ?? '');

            if ($bookingCode && $roomNumber) {
                // [SỬA LT-E03] Dùng kết quả trả về của Model để báo đúng thành công/thất bại
                if ($this->bookingModel->checkIn($bookingCode, $roomNumber)) {
                    AuditLog::record('CHECK_IN', "Nhận phòng {$roomNumber} cho đơn {$bookingCode}");
                    Session::flash('success', "Check-in thành công cho đơn #{$bookingCode}. Đã bàn giao phòng {$roomNumber} cho khách.");
                } else {
                    Session::flash('error', $this->bookingModel->getError());
                }
            } else {
                Session::flash('error', 'Vui lòng chọn mã đơn và số phòng hợp lệ.');
            }
        }
        $this->redirect('index.php?controller=receptionist&action=index');
    }

    /**
     * Thao tác Check-out (Trả phòng & Thu ngân)
     */
    public function checkOut(): void {
        $this->requireRole('receptionist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingCode = trim($_POST['bookingCode'] ?? '');
            if ($bookingCode) {
                if ($this->bookingModel->checkOut($bookingCode)) {
                    AuditLog::record('CHECK_OUT', "Trả phòng đơn {$bookingCode}");
                    Session::flash('success', "Check-out hoàn tất cho đơn #{$bookingCode}. Phòng đã chuyển sang trạng thái chờ dọn dẹp.");
                } else {
                    Session::flash('error', $this->bookingModel->getError());
                }
            }
        }
        $this->redirect('index.php?controller=receptionist&action=index');
    }

    /**
     * Cập nhật nhanh trạng thái phòng (Dọn phòng / Bảo trì / Sẵn sàng)
     */
    public function updateRoomStatus(): void {
        $this->requireRole('receptionist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomNumber = trim($_POST['roomNumber'] ?? '');
            $status = trim($_POST['status'] ?? 'available');

            if ($roomNumber && in_array($status, ['available', 'occupied', 'cleaning', 'maintenance', 'reserved'])) {
                if ($this->roomModel->updateRoomStatus($roomNumber, $status)) {
                    AuditLog::record('ROOM_STATUS', "Đổi phòng {$roomNumber} sang {$status}");
                    Session::flash('success', "Đã chuyển trạng thái phòng {$roomNumber} thành: {$status}.");
                } else {
                    Session::flash('error', "Không tìm thấy phòng {$roomNumber}.");
                }
            }
        }
        $this->redirect('index.php?controller=receptionist&action=index');
    }
}
