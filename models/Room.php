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
     * [SỬA LẦN 2] Xử lý file upload ảnh hạng phòng (yêu cầu "xử lý file" của đề BTL).
     * Bốn lớp kiểm tra: lỗi upload → đuôi file → MIME thật (finfo) → dung lượng ≤ 2MB.
     */
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

        $uploadDir = __DIR__ . '/../database/storage/uploads';
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
                $c['image'] = 'database/storage/uploads/' . $fileName;
                break;
            }
        }
        Session::set('roomCategories', $cats);

        return ['ok' => true, 'message' => 'Đã cập nhật ảnh cho hạng phòng.', 'path' => 'database/storage/uploads/' . $fileName];
    }
}
