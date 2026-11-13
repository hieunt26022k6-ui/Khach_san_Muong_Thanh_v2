<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/Room.php
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class Room extends BaseModel {
    public function getCategories(): array {
        return Session::get('roomCategories', []);
    }

    public function findCategory(string $id): ?array {
        $cats = $this->getCategories();
        foreach ($cats as $c) {
            if ($c['id'] === $id) return $c;
        }
        return null;
    }

    public function getPackages(): array {
        return Session::get('packages', []);
    }

    public function findPackage(string $id): ?array {
        foreach ($this->getPackages() as $p) {
            if ($p['id'] === $id) return $p;
        }
        return null;
    }

    public function findRoom(string $roomNumber): ?array {
        foreach ($this->getPhysicalRooms() as $r) {
            if ($r['roomNumber'] === $roomNumber) return $r;
        }
        return null;
    }

    /** Số phòng vật lý thuộc một hạng phòng (tồn kho) */
    public function countByCategory(string $categoryId): int {
        $n = 0;
        foreach ($this->getPhysicalRooms() as $r) {
            if ($r['categoryId'] === $categoryId) $n++;
        }
        return $n;
    }

    public function getPhysicalRooms(): array {
        return Session::get('physicalRooms', []);
    }

    public function getRoomsGroupedByFloor(): array {
        $rooms = $this->getPhysicalRooms();
        $floors = [];
        foreach ($rooms as $r) {
            $f = $r['floor'];
            if (!isset($floors[$f])) {
                $floors[$f] = [];
            }
            $floors[$f][] = $r;
        }
        ksort($floors);
        return $floors;
    }

    public function updateRoomStatus(string $roomNumber, string $newStatus, ?string $guestName = null): bool {
        $rooms = Session::get('physicalRooms', []);
        $updated = false;
        foreach ($rooms as &$r) {
            if ($r['roomNumber'] === $roomNumber) {
                $r['status'] = $newStatus;
                if ($newStatus === 'occupied') {
                    $r['currentGuestName'] = $guestName ?? $r['currentGuestName'] ?? 'Khách lưu trú';
                } else {
                    unset($r['currentGuestName']);
                }
                $updated = true;
                break;
            }
        }
        if ($updated) {
            Session::set('physicalRooms', $rooms);
        }
        return $updated;
    }

    public function updateCategoryPrice(string $categoryId, int $newPrice): bool {
        $cats = Session::get('roomCategories', []);
        foreach ($cats as &$c) {
            if ($c['id'] === $categoryId) {
                $c['basePrice'] = $newPrice;
                Session::set('roomCategories', $cats);
                return true;
            }
        }
        return false;
    }

    /**
     * Xử lý file upload ảnh hạng phòng (yêu cầu "xử lý file" của đề BTL).
     * Bốn lớp kiểm tra: lỗi upload → đuôi file → MIME thật (finfo) → dung lượng ≤ 2MB.
     */
    /**
     * AD-12: cập nhật gói phòng (phụ thu mỗi đêm + trạng thái đang bán / tạm ngưng).
     * Chỉ ảnh hưởng tới các đơn tạo MỚI — đơn cũ đã chốt giá trong bảng bookings.
     */
    public function updatePackage(string $packageId, int $priceOffset, bool $isActive): bool {
        $packages = Session::get('packages', []);
        foreach ($packages as &$p) {
            if ($p['id'] !== $packageId) { continue; }
            $p['priceOffset'] = $priceOffset;
            $p['isActive'] = $isActive;
            Session::set('packages', $packages);
            return true;
        }
        return false;
    }

    /**
     * AD-12: khóa phòng bảo trì / mở khai thác lại phòng vật lý.
     * Khóa mềm (đổi trạng thái) chứ không xóa bản ghi, để lịch sử đơn vẫn tra được.
     */
    public function setRoomMaintenance(string $roomNumber, bool $lock, string $reason = '', string $until = ''): bool {
        $rooms = Session::get('physicalRooms', []);
        foreach ($rooms as &$r) {
            if (strcasecmp((string)($r['roomNumber'] ?? ''), $roomNumber) !== 0) { continue; }
            if ($lock) {
                $r['status'] = 'maintenance';
                $r['maintenanceReason'] = $reason !== '' ? $reason : 'Bảo trì theo kế hoạch';
                $r['maintenanceUntil'] = $until !== '' ? $until : date('Y-m-d', strtotime('+7 day'));
            } else {
                $r['status'] = 'available';
                $r['maintenanceReason'] = null;
                $r['maintenanceUntil'] = null;
            }
            Session::set('physicalRooms', $rooms);
            return true;
        }
        return false;
    }

    public function saveCategoryImage(string $categoryId, ?array $file): array {
        if (!$file || !isset($file['error']) || is_array($file['error'])) {
            return ['ok' => false, 'message' => 'Chưa chọn tệp ảnh.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => 'Tải tệp thất bại (mã lỗi ' . $file['error'] . ').'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['ok' => false, 'message' => 'Ảnh vượt quá 2MB.'];
        }

        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return ['ok' => false, 'message' => 'Chỉ nhận ảnh .jpg, .jpeg, .png, .webp.'];
        }

        $mime = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = (string)finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
            return ['ok' => false, 'message' => 'Nội dung tệp không phải ảnh hợp lệ (' . $mime . ').'];
        }

        $cats = Session::get('roomCategories', []);
        $exists = false;
        foreach ($cats as $c) {
            if ($c['id'] === $categoryId) { $exists = true; break; }
        }
        if (!$exists) {
            return ['ok' => false, 'message' => 'Hạng phòng không tồn tại.'];
        }

        // Thư mục assets/uploads nằm trong vùng công khai của website nên trình duyệt
        // hiển thị được ảnh vừa upload (thư mục database/storage bị .htaccess chặn vì chứa dữ liệu).
        $uploadDir = __DIR__ . '/../assets/uploads';
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0775, true)) {
            return ['ok' => false, 'message' => 'Không tạo được thư mục lưu ảnh.'];
        }

        $fileName = 'room_' . preg_replace('/[^a-z0-9_]/i', '', $categoryId) . '_' . time() . '.' . $ext;
        $target = $uploadDir . '/' . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['ok' => false, 'message' => 'Không lưu được tệp ảnh lên máy chủ.'];
        }

        foreach ($cats as &$c) {
            if ($c['id'] === $categoryId) {
                $c['image'] = 'assets/uploads/' . $fileName;
                break;
            }
        }
        Session::set('roomCategories', $cats);

        return ['ok' => true, 'message' => 'Đã cập nhật ảnh cho hạng phòng.', 'path' => 'assets/uploads/' . $fileName];
    }
}
