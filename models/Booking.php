<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/Booking.php
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class Booking extends BaseModel {
    private string $error = '';

    /** Lý do thất bại của thao tác gần nhất (dùng để báo lỗi chính xác cho người dùng) */
    public function getError(): string {
        return $this->error;
    }

    /**
     * [SỬA KH-E04] Đếm số đơn còn hiệu lực của một hạng phòng có khoảng ngày CHỒNG LẤN với [in, out).
     * Hai khoảng [a1,b1) và [a2,b2) chồng lấn khi a1 < b2 và a2 < b1.
     */
    public function countOverlapping(string $categoryId, string $in, string $out): int {
        $n = 0;
        foreach ($this->getAll() as $b) {
            if ($b['categoryId'] !== $categoryId) continue;
            if (!in_array($b['status'], ['confirmed', 'checked_in'], true)) continue;
            if ($b['checkInDate'] < $out && $in < $b['checkOutDate']) $n++;
        }
        return $n;
    }
    public function getAll(): array {
        return Session::get('bookings', []);
    }

    public function findByCode(string $code): ?array {
        $bookings = $this->getAll();
        foreach ($bookings as $b) {
            if (strcasecmp($b['bookingCode'], $code) === 0) {
                return $b;
            }
        }
        return null;
    }

    public function findByCustomerPhone(string $phone): array {
        $bookings = $this->getAll();
        $res = [];
        foreach ($bookings as $b) {
            if ($b['customerPhone'] === $phone) {
                $res[] = $b;
            }
        }
        return $res;
    }

    public function create(array $data): array {
        $bookings = $this->getAll();
        // [SỬA KH-E05] Sinh lại mã nếu đã tồn tại
        do {
            $code = 'MT-' . random_int(1000, 9999);
        } while ($this->findByCode($code) !== null);
        
        $newBooking = [
            'id' => 'bk_' . uniqid(),
            'bookingCode' => $code,
            'customerName' => $data['customerName'],
            'customerPhone' => $data['customerPhone'],
            'customerEmail' => $data['customerEmail'] ?? '',
            'categoryId' => $data['categoryId'],
            'categoryName' => $data['categoryName'],
            'assignedRoomNumber' => $data['assignedRoomNumber'] ?? null,
            'checkInDate' => $data['checkInDate'],
            'checkOutDate' => $data['checkOutDate'],
            'nights' => max(1, (int)$data['nights']),
            'packageId' => $data['packageId'] ?? 'pkg_room_only',
            'packageName' => $data['packageName'] ?? 'Gói Tiêu Chuẩn',
            'totalAmount' => (int)$data['totalAmount'],
            'depositPaid' => (int)$data['depositPaid'],
            'remainingBalance' => (int)($data['totalAmount'] - $data['depositPaid']),
            'status' => 'confirmed',
            'createdAt' => date('Y-m-d H:i:s')
        ];

        array_unshift($bookings, $newBooking);
        Session::set('bookings', $bookings);
        return $newBooking;
    }

    public function checkIn(string $bookingCode, string $roomNumber): bool {
        $this->error = '';
        $roomModel = new Room();

        $booking = $this->findByCode($bookingCode);
        if ($booking === null) {
            $this->error = "Không tìm thấy đơn #{$bookingCode}.";
            return false;
        }
        if ($booking['status'] !== 'confirmed') {
            $this->error = "Đơn #{$bookingCode} đang ở trạng thái '{$booking['status']}', chỉ nhận phòng được đơn 'confirmed'.";
            return false;
        }
        $room = $roomModel->findRoom($roomNumber);
        if ($room === null) {
            $this->error = "Không tìm thấy phòng {$roomNumber}.";
            return false;
        }
        if ($room['status'] !== 'available') {
            $this->error = "Phòng {$roomNumber} không ở trạng thái 'Trống sạch' nên chưa thể bàn giao.";
            return false;
        }

        $bookings = $this->getAll();
        $oldRoom = null;
        foreach ($bookings as &$b) {
            if (strcasecmp($b['bookingCode'], $bookingCode) === 0) {
                $oldRoom = $b['assignedRoomNumber'] ?? null;
                $b['status'] = 'checked_in';
                $b['assignedRoomNumber'] = $roomNumber;
                $guestName = $b['customerName'];
                break;
            }
        }
        unset($b);
        Session::set('bookings', $bookings);

        // [SỬA LT-E04] Nhả phòng đã giữ chỗ trước đó cho chính đơn này nếu khác phòng mới
        if ($oldRoom && $oldRoom !== $roomNumber) {
            $old = $roomModel->findRoom($oldRoom);
            if ($old !== null && $old['status'] === 'reserved') {
                $roomModel->updateRoomStatus($oldRoom, 'available');
            }
        }
        $roomModel->updateRoomStatus($roomNumber, 'occupied', $guestName);
        return true;
    }

    public function checkOut(string $bookingCode): bool {
        $this->error = '';
        $booking = $this->findByCode($bookingCode);
        if ($booking === null) {
            $this->error = "Không tìm thấy đơn #{$bookingCode}.";
            return false;
        }
        if ($booking['status'] !== 'checked_in') {
            $this->error = "Đơn #{$bookingCode} chưa nhận phòng hoặc đã hoàn tất, không thể trả phòng.";
            return false;
        }

        $bookings = $this->getAll();
        $roomNumber = null;
        foreach ($bookings as &$b) {
            if (strcasecmp($b['bookingCode'], $bookingCode) === 0) {
                $b['status'] = 'completed';
                $roomNumber = $b['assignedRoomNumber'] ?? null;
                $b['remainingBalance'] = 0; // Đã thanh toán đầy đủ
                break;
            }
        }
        unset($b);
        Session::set('bookings', $bookings);
        if ($roomNumber) {
            (new Room())->updateRoomStatus($roomNumber, 'cleaning');
        }
        return true;
    }
}
