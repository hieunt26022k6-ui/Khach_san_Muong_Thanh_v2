<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP 8 OOP MVC
 * Core/Store.php — LỚP TRUY CẬP DỮ LIỆU DÙNG CHUNG
 *
 * Toàn bộ dữ liệu nghiệp vụ (người dùng, hạng phòng, đơn đặt phòng, dịch vụ, nhật ký...)
 * được đọc/ghi bằng MySQL qua PDO với câu lệnh SQL thật (SELECT / INSERT / UPDATE / DELETE).
 * Các Model chỉ làm việc với mảng kết hợp như trước nên không phải sửa View.
 *
 * Nếu MySQL chưa bật (XAMPP chưa Start) hệ thống tự rơi về tệp JSON
 * database/storage/data.json để buổi demo không bao giờ bị trắng trang.
 */

namespace Core;

use Config\Database;
use PDO;
use Throwable;

final class Store {
    /** Các khóa dữ liệu nghiệp vụ lưu chung. Khóa còn lại (currentUser, __flash, __csrf) vẫn theo phiên. */
    public const KEYS = [
        'users', 'roomCategories', 'physicalRooms', 'packages', 'services', 'bookings',
        'serviceOrders', 'auditLogs', 'incidents', 'reviews', 'policy', 'templates',
        'sentNotifications', '__mt_initialized',
    ];

    /**
     * Bản đồ khóa nghiệp vụ → bảng MySQL và các cột có kiểu dữ liệu thật.
     * [khóa trong mảng] => [cột trong bảng, kiểu]  (kiểu: str|int|bool|date|datetime)
     */
    private const MAP = [
        'users' => ['table' => 'users', 'cols' => [
            'username' => ['username', 'str'],
            'password' => ['password', 'str'],
            'fullName' => ['full_name', 'str'],
            'email' => ['email', 'str'],
            'phone' => ['phone', 'str'],
            'role' => ['role', 'str'],
            'isLocked' => ['is_locked', 'bool'],
            'createdAt' => ['created_at', 'datetime'],
        ]],
        'roomCategories' => ['table' => 'room_categories', 'cols' => [
            'code' => ['code', 'str'],
            'name' => ['name', 'str'],
            'vietnameseName' => ['vietnamese_name', 'str'],
            'viewType' => ['view_type', 'str'],
            'bedType' => ['bed_type', 'str'],
            'areaSqm' => ['area_sqm', 'int'],
            'basePrice' => ['base_price', 'int'],
            'maxAdults' => ['max_adults', 'int'],
            'maxChildren' => ['max_children', 'int'],
            'heroImage' => ['hero_image', 'str'],
            'image' => ['image_path', 'str'],
            'description' => ['description', 'str'],
            'isActive' => ['is_active', 'bool'],
        ]],
        'physicalRooms' => ['table' => 'physical_rooms', 'cols' => [
            'roomNumber' => ['room_number', 'str'],
            'floor' => ['floor', 'int'],
            'categoryId' => ['category_id', 'str'],
            'status' => ['status', 'str'],
            'maintenanceReason' => ['maintenance_reason', 'str'],
            'maintenanceUntil' => ['maintenance_until', 'date'],
        ]],
        'packages' => ['table' => 'packages', 'cols' => [
            'code' => ['code', 'str'],
            'name' => ['name', 'str'],
            'description' => ['description', 'str'],
            'priceOffset' => ['price_offset', 'int'],
            'isActive' => ['is_active', 'bool'],
        ]],
        'services' => ['table' => 'services', 'cols' => [
            'code' => ['code', 'str'],
            'name' => ['name', 'str'],
            'category' => ['category', 'str'],
            'price' => ['price', 'int'],
            'unit' => ['unit', 'str'],
            'isActive' => ['is_active', 'bool'],
        ]],
        'bookings' => ['table' => 'bookings', 'cols' => [
            'bookingCode' => ['booking_code', 'str'],
            'customerName' => ['customer_name', 'str'],
            'customerPhone' => ['customer_phone', 'str'],
            'customerEmail' => ['customer_email', 'str'],
            'categoryId' => ['category_id', 'str'],
            'packageId' => ['package_id', 'str'],
            'assignedRoomNumber' => ['assigned_room_number', 'str'],
            'checkInDate' => ['check_in_date', 'date'],
            'checkOutDate' => ['check_out_date', 'date'],
            'nights' => ['nights', 'int'],
            'totalAmount' => ['total_amount', 'int'],
            'depositPaid' => ['deposit_paid', 'int'],
            'remainingBalance' => ['remaining_balance', 'int'],
            'status' => ['status', 'str'],
            'note' => ['note', 'str'],
            'createdAt' => ['created_at', 'datetime'],
        ]],
        'serviceOrders' => ['table' => 'service_orders', 'cols' => [
            'bookingCode' => ['booking_code', 'str'],
            'roomNumber' => ['room_number', 'str'],
            'guestName' => ['guest_name', 'str'],
            'serviceName' => ['service_name', 'str'],
            'quantity' => ['quantity', 'int'],
            'unitPrice' => ['unit_price', 'int'],
            'totalPrice' => ['total_price', 'int'],
            'status' => ['status', 'str'],
            'scheduledTime' => ['scheduled_time', 'str'],
            'note' => ['note', 'str'],
            'createdAt' => ['created_at', 'datetime'],
        ]],
        'auditLogs' => ['table' => 'audit_logs', 'cols' => [
            'action' => ['action', 'str'],
            'description' => ['description', 'str'],
            'operator' => ['operator', 'str'],
            'timestamp' => ['created_at', 'datetime'],
        ]],
        'incidents' => ['table' => 'incidents', 'cols' => [
            'bookingCode' => ['booking_code', 'str'],
            'roomNumber' => ['room_number', 'str'],
            'guestName' => ['guest_name', 'str'],
            'title' => ['title', 'str'],
            'description' => ['description', 'str'],
            'severity' => ['severity', 'str'],
            'status' => ['status', 'str'],
            'resolutionNote' => ['resolution_note', 'str'],
            'reportedAt' => ['reported_at', 'datetime'],
        ]],
        'reviews' => ['table' => 'reviews', 'cols' => [
            'bookingId' => ['booking_id', 'str'],
            'bookingCode' => ['booking_code', 'str'],
            'customerId' => ['customer_id', 'str'],
            'categoryId' => ['category_id', 'str'],
            'guestName' => ['guest_name', 'str'],
            'rating' => ['rating', 'int'],
            'title' => ['title', 'str'],
            'comment' => ['comment', 'str'],
            'replyFromHotel' => ['reply_from_hotel', 'str'],
            'createdAt' => ['created_at', 'datetime'],
        ]],
        'templates' => ['table' => 'notification_templates', 'cols' => [
            'code' => ['code', 'str'],
            'name' => ['name', 'str'],
            'subject' => ['subject', 'str'],
            'bodyTemplate' => ['body_template', 'str'],
            'channel' => ['channel', 'str'],
            'updatedAt' => ['updated_at', 'datetime'],
        ]],
        'sentNotifications' => ['table' => 'sent_notifications', 'cols' => [
            'recipientEmailOrPhone' => ['recipient', 'str'],
            'recipientCustomerId' => ['recipient_customer_id', 'str'],
            'relatedBookingCode' => ['related_booking_code', 'str'],
            'templateCode' => ['template_code', 'str'],
            'priority' => ['priority', 'str'],
            'subject' => ['subject', 'str'],
            'message' => ['message', 'str'],
            'isRead' => ['is_read', 'bool'],
            'sentAt' => ['sent_at', 'datetime'],
        ]],
    ];

    /** Khóa ngoại cần kiểm tra tồn tại trước khi ghi (tránh lỗi FK làm hỏng cả giao dịch). */
    private const FK_GUARD = [
        'bookings.categoryId' => ['room_categories', 'id'],
        'bookings.packageId' => ['packages', 'id'],
        'bookings.assignedRoomNumber' => ['physical_rooms', 'room_number'],
        'physicalRooms.categoryId' => ['room_categories', 'id'],
        'incidents.roomNumber' => ['physical_rooms', 'room_number'],
        'reviews.categoryId' => ['room_categories', 'id'],
        'reviews.bookingId' => ['bookings', 'id'],
    ];

    private static ?bool $dbMode = null;
    private static ?bool $jsonMode = null;
    private static bool $inTransaction = false;

    /** Đang chạy trên MySQL (true) hay trên tệp JSON dự phòng (false). */
    public static function driver(): string {
        return self::db() !== null ? 'MySQL (PDO)' : 'JSON (dự phòng)';
    }

    private static function db(): ?PDO {
        if (self::$dbMode === false) { return null; }
        $pdo = Database::getConnection();
        self::$dbMode = $pdo instanceof PDO;
        return $pdo;
    }

    private static function jsonPath(): string {
        return __DIR__ . '/../database/storage/data.json';
    }

    private static function jsonEnabled(): bool {
        if (self::$jsonMode === null) {
            $dir = dirname(self::jsonPath());
            if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
            self::$jsonMode = is_dir($dir) && is_writable($dir)
                && (!file_exists(self::jsonPath()) || is_writable(self::jsonPath()));
        }
        return self::$jsonMode;
    }

    public static function handles(string $key): bool {
        if (!in_array($key, self::KEYS, true)) { return false; }
        return self::db() !== null || self::jsonEnabled();
    }

    public static function enabled(): bool {
        return self::handles('users');
    }

    // =================================================================
    // ĐỌC / GHI
    // =================================================================

    public static function get(string $key, mixed $default = null): mixed {
        if (!in_array($key, self::KEYS, true)) { return $default; }

        if (self::db() !== null) {
            $value = self::dbGet($key);
        } else {
            $all = self::jsonReadAll();
            $value = array_key_exists($key, $all) ? $all[$key] : null;
        }
        return $value === null ? $default : $value;
    }

    public static function has(string $key): bool {
        return self::get($key) !== null;
    }

    public static function set(string $key, mixed $value): void {
        if (!in_array($key, self::KEYS, true)) { return; }

        if (self::db() !== null) {
            self::dbWrite($key, $value);
        } else {
            self::jsonMutate(function (array &$d) use ($key, $value) { $d[$key] = $value; });
        }
    }

    public static function remove(string $key): void {
        if (!in_array($key, self::KEYS, true)) { return; }

        if (self::db() !== null) {
            if ($key === 'policy') {
                self::db()->exec('DELETE FROM hotel_policy');
            } elseif ($key === '__mt_initialized') {
                self::db()->exec("DELETE FROM system_flags WHERE flag_key = 'seeded'");
            } elseif (isset(self::MAP[$key])) {
                self::db()->exec('DELETE FROM `' . self::MAP[$key]['table'] . '`');
            }
            return;
        }
        self::jsonMutate(function (array &$d) use ($key) { unset($d[$key]); });
    }

    /**
     * Gom nhiều thao tác ghi vào MỘT giao dịch (transaction).
     * Dùng khi một nghiệp vụ phải cập nhật nhiều bảng: hoặc tất cả cùng thành công, hoặc không gì thay đổi.
     */
    public static function transaction(callable $fn): mixed {
        $pdo = self::db();
        if ($pdo === null || self::$inTransaction) {
            return $fn();
        }
        $pdo->beginTransaction();
        self::$inTransaction = true;
        try {
            $result = $fn();
            $pdo->commit();
            self::$inTransaction = false;
            return $result;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            self::$inTransaction = false;
            error_log('[Store] Giao dịch bị huỷ: ' . $e->getMessage());
            throw $e;
        }
    }

    // =================================================================
    // NHÁNH MySQL
    // =================================================================

    private static function dbGet(string $key): mixed {
        $pdo = self::db();
        if ($pdo === null) { return null; }

        if ($key === '__mt_initialized') {
            $st = $pdo->query("SELECT flag_value FROM system_flags WHERE flag_key = 'seeded'");
            return $st->fetchColumn() === '1';
        }
        if ($key === 'policy') {
            $rows = $pdo->query('SELECT setting_key, setting_value FROM hotel_policy')->fetchAll();
            $out = [];
            foreach ($rows as $r) { $out[$r['setting_key']] = self::castOut('policy', $r['setting_value']); }
            return $out;
        }
        if (!isset(self::MAP[$key])) { return null; }

        $table = self::MAP[$key]['table'];
        if ($key === 'users') {
            // Quan hệ 1–1 với user_profiles: kèm hồ sơ chi tiết ngay trong mỗi tài khoản
            $sql = "SELECT u.*, p.id_card AS p_id_card, p.address AS p_address,
                           p.nationality AS p_nationality, p.date_of_birth AS p_date_of_birth
                      FROM `users` u
                      LEFT JOIN `user_profiles` p ON p.user_id = u.id
                  ORDER BY u.role DESC, u.created_at ASC";
            $rows = $pdo->query($sql)->fetchAll();
        } else {
            $rows = $pdo->query('SELECT * FROM `' . $table . '`')->fetchAll();
        }

        $out = [];
        foreach ($rows as $row) {
            $out[] = self::rowToArray($key, $row);
        }
        return $out;
    }

    /** Ghép cột có kiểu dữ liệu thật + cột payload JSON thành mảng như cũ cho Model/View. */
    private static function rowToArray(string $key, array $row): array {
        $payload = [];
        if (!empty($row['payload'])) {
            $decoded = json_decode((string)$row['payload'], true);
            if (is_array($decoded)) { $payload = $decoded; }
        }
        $item = $payload;
        $item['id'] = $row['id'] ?? ($payload['id'] ?? null);

        foreach (self::MAP[$key]['cols'] as $appKey => [$col, $type]) {
            if (!array_key_exists($col, $row)) { continue; }
            $value = $row[$col];
            if ($value === null) { continue; }
            $item[$appKey] = match ($type) {
                'int' => (int)$value,
                'bool' => (bool)(int)$value,
                'date' => substr((string)$value, 0, 10),
                default => (string)$value,
            };
        }

        if ($key === 'users') {
            if (array_key_exists('p_id_card', $row) && $row['p_id_card'] !== null) { $item['idCard'] = $row['p_id_card']; }
            if (array_key_exists('p_address', $row) && $row['p_address'] !== null) { $item['address'] = $row['p_address']; }
            if (array_key_exists('p_nationality', $row) && $row['p_nationality'] !== null) { $item['nationality'] = $row['p_nationality']; }
            if (array_key_exists('p_date_of_birth', $row) && $row['p_date_of_birth'] !== null) { $item['dateOfBirth'] = substr((string)$row['p_date_of_birth'], 0, 10); }
        }
        return $item;
    }

    private static function dbWrite(string $key, mixed $value): void {
        $pdo = self::db();
        if ($pdo === null) { return; }

        if ($key === '__mt_initialized') {
            $st = $pdo->prepare("REPLACE INTO system_flags (flag_key, flag_value) VALUES ('seeded', ?)");
            $st->execute([$value ? '1' : '0']);
            return;
        }
        if ($key === 'policy') {
            self::dbWritePolicy(is_array($value) ? $value : []);
            return;
        }
        if (!isset(self::MAP[$key]) || !is_array($value)) { return; }

        $run = function () use ($key, $value, $pdo) {
            $table = self::MAP[$key]['table'];
            $incoming = [];
            foreach ($value as $row) {
                if (!is_array($row)) { continue; }
                $id = (string)($row['id'] ?? '');
                if ($id === '') { $id = self::makeId($key); $row['id'] = $id; }
                $incoming[$id] = $row;
            }

            // 1) Xoá những bản ghi không còn trong mảng (đồng bộ đúng nghĩa)
            $existing = $pdo->query('SELECT id FROM `' . $table . '`')->fetchAll(PDO::FETCH_COLUMN);
            $toDelete = array_diff($existing, array_keys($incoming));
            if ($toDelete) {
                $marks = implode(',', array_fill(0, count($toDelete), '?'));
                $st = $pdo->prepare('DELETE FROM `' . $table . '` WHERE id IN (' . $marks . ')');
                $st->execute(array_values($toDelete));
            }

            // 2) Cập nhật bản ghi đã có, thêm bản ghi mới
            foreach ($incoming as $id => $row) {
                $cols = ['id'];
                $vals = [$id];
                foreach (self::MAP[$key]['cols'] as $appKey => [$col, $type]) {
                    if (!array_key_exists($appKey, $row)) { continue; }
                    $cols[] = $col;
                    $vals[] = self::toColumnValue($key, $appKey, $type, $row[$appKey]);
                }
                // Kiểm tra khóa ngoại: giá trị không tồn tại thì ghi NULL thay vì làm hỏng giao dịch
                foreach ($cols as $i => $col) {
                    $guardKey = $key . '.' . self::appKeyOfColumn($key, $col);
                    if ($col !== 'id' && isset(self::FK_GUARD[$guardKey]) && $vals[$i] !== null && $vals[$i] !== '') {
                        [$pTable, $pCol] = self::FK_GUARD[$guardKey];
                        if (!self::existsValue($pTable, $pCol, $vals[$i])) { $vals[$i] = null; }
                    }
                }
                // Cột khóa ngoại suy ra từ dữ liệu (đơn đặt phòng ↔ dịch vụ)
                if ($key === 'serviceOrders') {
                    $bookingId = self::resolveBookingId((string)($row['bookingCode'] ?? ''));
                    if ($bookingId !== null) { $cols[] = 'booking_id'; $vals[] = $bookingId; }
                    $serviceId = self::resolveServiceId(
                        (string)($row['serviceName'] ?? ''),
                        (int)($row['unitPrice'] ?? 0)
                    );
                    if ($serviceId !== null) { $cols[] = 'service_id'; $vals[] = $serviceId; }
                }
                $cols[] = 'payload';
                $vals[] = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

                $marks = implode(',', array_fill(0, count($cols), '?'));
                $updates = [];
                foreach ($cols as $col) {
                    if ($col === 'id') { continue; }
                    $updates[] = '`' . $col . '` = VALUES(`' . $col . '`)';
                }
                $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $cols) . '`) VALUES (' . $marks . ')'
                     . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
                $pdo->prepare($sql)->execute($vals);
            }

            // 3) Hồ sơ 1–1 của người dùng
            if ($key === 'users') { self::dbSyncProfiles($incoming); }
            // 4) Bảng trung gian N–N bookings ↔ services luôn phản ánh dịch vụ đã ghi
            if ($key === 'serviceOrders' || $key === 'bookings') { self::dbSyncBookingServices(); }
        };

        if (self::$inTransaction) {
            $run();
        } else {
            $pdo->beginTransaction();
            try { $run(); $pdo->commit(); }
            catch (Throwable $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } error_log('[Store] ' . $e->getMessage()); }
        }
    }

    private static function dbWritePolicy(array $policy): void {
        $pdo = self::db();
        if ($pdo === null) { return; }
        $st = $pdo->prepare('REPLACE INTO hotel_policy (setting_key, setting_value) VALUES (?, ?)');
        foreach ($policy as $k => $v) {
            $st->execute([(string)$k, is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE)]);
        }
    }

    private static function dbSyncProfiles(array $users): void {
        $pdo = self::db();
        if ($pdo === null) { return; }
        $st = $pdo->prepare(
            'INSERT INTO user_profiles (user_id, id_card, address, nationality, date_of_birth)
             VALUES (?,?,?,?,?)
             ON DUPLICATE KEY UPDATE id_card = VALUES(id_card), address = VALUES(address),
                 nationality = VALUES(nationality), date_of_birth = VALUES(date_of_birth)'
        );
        foreach ($users as $id => $u) {
            $idCard = $u['idCard'] ?? null;
            $address = $u['address'] ?? null;
            $nationality = $u['nationality'] ?? null;
            $dob = $u['dateOfBirth'] ?? null;
            // Mỗi tài khoản luôn có đúng MỘT hồ sơ (quan hệ 1–1), kể cả khi chưa nhập địa chỉ
            $st->execute([$id, $idCard ?: null, $address ?: null, $nationality ?: 'Việt Nam', $dob ?: null]);
        }
    }

    /** Dựng lại bảng trung gian N–N từ dữ liệu dịch vụ của từng đơn. */
    private static function dbSyncBookingServices(): void {
        $pdo = self::db();
        if ($pdo === null) { return; }
        $pdo->exec('DELETE FROM booking_services');
        $pdo->exec(
            'INSERT INTO booking_services (booking_id, service_id, quantity, unit_price)
             SELECT so.booking_id, so.service_id, SUM(so.quantity), MAX(so.unit_price)
               FROM service_orders so
              WHERE so.booking_id IS NOT NULL AND so.service_id IS NOT NULL
              GROUP BY so.booking_id, so.service_id'
        );
    }

    /** Mã đơn → id đơn trong bảng bookings (NULL nếu đơn không tồn tại). */
    private static function resolveBookingId(string $bookingCode): ?string {
        static $cache = [];
        if ($bookingCode === '') { return null; }
        if (array_key_exists($bookingCode, $cache)) { return $cache[$bookingCode]; }
        $st = self::db()->prepare('SELECT id FROM bookings WHERE booking_code = ? LIMIT 1');
        $st->execute([$bookingCode]);
        $id = $st->fetchColumn();
        return $cache[$bookingCode] = ($id === false ? null : (string)$id);
    }

    /**
     * Tên dịch vụ → id dịch vụ trong bảng services.
     * Dịch vụ phát sinh (chưa có trong danh mục) được thêm vào danh mục để bảng trung gian N–N
     * luôn có đủ hai đầu khóa ngoại — đúng bản chất quan hệ nhiều-nhiều.
     */
    private static function resolveServiceId(string $serviceName, int $unitPrice = 0): ?string {
        $serviceName = trim($serviceName);
        if ($serviceName === '') { return null; }

        static $cache = [];
        $ck = mb_strtolower($serviceName, 'UTF-8');
        if (array_key_exists($ck, $cache)) { return $cache[$ck]; }

        $st = self::db()->prepare('SELECT id FROM services WHERE LOWER(name) = ? LIMIT 1');
        $st->execute([$ck]);
        $id = $st->fetchColumn();
        if ($id !== false) { return $cache[$ck] = (string)$id; }

        $newId = 'svc_' . substr(md5($ck), 0, 8);
        $ins = self::db()->prepare(
            'INSERT INTO services (id, code, name, category, price, unit, is_active, payload)
             VALUES (?,?,?,?,?,?,1,?)'
        );
        $ins->execute([
            $newId,
            'PS' . strtoupper(substr(md5($ck), 0, 4)),
            $serviceName,
            'Dịch vụ phát sinh',
            $unitPrice,
            'lần',
            json_encode(['id' => $newId, 'name' => $serviceName, 'category' => 'Dịch vụ phát sinh',
                         'price' => $unitPrice, 'unit' => 'lần', 'isActive' => true], JSON_UNESCAPED_UNICODE),
        ]);
        return $cache[$ck] = $newId;
    }

    private static function existsValue(string $table, string $col, mixed $value): bool {
        static $cache = [];
        $ck = $table . '|' . $col . '|' . (string)$value;
        if (array_key_exists($ck, $cache)) { return $cache[$ck]; }
        $st = self::db()->prepare('SELECT 1 FROM `' . $table . '` WHERE `' . $col . '` = ? LIMIT 1');
        $st->execute([$value]);
        return $cache[$ck] = (bool)$st->fetchColumn();
    }

    private static function appKeyOfColumn(string $key, string $col): string {
        foreach (self::MAP[$key]['cols'] as $appKey => [$c, $t]) {
            if ($c === $col) { return $appKey; }
        }
        return '';
    }

    private static function toColumnValue(string $key, string $appKey, string $type, mixed $value): mixed {
        if ($value === null) { return null; }
        return match ($type) {
            'int' => (int)$value,
            'bool' => !empty($value) ? 1 : 0,
            'date' => substr((string)$value, 0, 10) ?: null,
            'datetime' => substr((string)$value, 0, 19) ?: date('Y-m-d H:i:s'),
            default => is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE),
        };
    }

    /** Chuyển giá trị policy (chuỗi trong bảng) về đúng kiểu số/chuỗi cho giao diện. */
    private static function castOut(string $key, mixed $value): mixed {
        if ($key !== 'policy') { return $value; }
        if (is_string($value) && preg_match('/^-?\d+$/', $value)) { return (int)$value; }
        if (is_string($value) && preg_match('/^-?\d+\.\d+$/', $value)) { return (float)$value; }
        return $value;
    }

    private static function makeId(string $key): string {
        $prefix = match ($key) {
            'users' => 'usr', 'bookings' => 'bk', 'serviceOrders' => 'so', 'auditLogs' => 'log',
            'incidents' => 'inc', 'reviews' => 'rev', 'templates' => 'tpl', 'sentNotifications' => 'noti',
            'physicalRooms' => 'p', 'roomCategories' => 'cat', 'packages' => 'pkg', 'services' => 'svc',
            default => 'row',
        };
        return $prefix . '_' . bin2hex(random_bytes(4));
    }

    // =================================================================
    // NHÁNH JSON DỰ PHÒNG (khi MySQL chưa bật)
    // =================================================================

    private static function jsonReadAll(): array {
        $file = self::jsonPath();
        if (!file_exists($file)) { return []; }
        $fh = fopen($file, 'rb');
        if (!$fh) { return []; }
        flock($fh, LOCK_SH);
        $raw = stream_get_contents($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
        $data = json_decode((string)$raw, true);
        return is_array($data) ? $data : [];
    }

    private static function jsonMutate(callable $fn): void {
        if (!self::jsonEnabled()) { return; }
        $fh = fopen(self::jsonPath(), 'c+');
        if (!$fh) { return; }
        flock($fh, LOCK_EX);
        $raw  = stream_get_contents($fh);
        $data = json_decode((string)$raw, true);
        if (!is_array($data)) { $data = []; }
        $fn($data);

        // Mã hoá TRƯỚC khi ghi: một chuỗi sai bảng mã không được phép xoá sạch tệp dữ liệu
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($encoded === false) {
            flock($fh, LOCK_UN);
            fclose($fh);
            error_log('[Store] Không mã hoá được dữ liệu — giữ nguyên tệp cũ để tránh mất dữ liệu.');
            return;
        }
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, $encoded);
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    // =================================================================
    // NẠP / KHÔI PHỤC DỮ LIỆU MẪU
    // =================================================================

    /** Nạp bộ dữ liệu mẫu (database/seed_data.php) vào MySQL/JSON — dùng ở lần chạy đầu. */
    public static function seedFromArray(array $seed): void {
        self::transaction(function () use ($seed) {
            foreach (self::KEYS as $key) {
                if ($key === '__mt_initialized') { continue; }
                if (!array_key_exists($key, $seed)) { continue; }
                self::set($key, $seed[$key]);
            }
            self::set('__mt_initialized', true);
        });
    }

    /** Khôi phục toàn bộ dữ liệu mẫu (nút "Khôi phục dữ liệu" của admin) — mọi khóa đều được nạp lại. */
    public static function resetAll(): bool {
        $seedFile = __DIR__ . '/../database/seed_data.php';
        if (!file_exists($seedFile)) { return false; }
        $seed = require $seedFile;
        if (!is_array($seed)) { return false; }

        $pdo = self::db();
        if ($pdo !== null) {
            // Xoá theo thứ tự an toàn rồi nạp lại trong CÙNG một giao dịch
            $pdo->beginTransaction();
            self::$inTransaction = true;
            try {
                $pdo->exec('DELETE FROM booking_services');
                foreach (['service_orders', 'audit_logs', 'incidents', 'reviews', 'sent_notifications',
                          'bookings', 'physical_rooms', 'room_categories', 'packages', 'services',
                          'notification_templates', 'user_profiles', 'users', 'hotel_policy'] as $t) {
                    $pdo->exec('DELETE FROM `' . $t . '`');
                }
                self::seedFromArray($seed);
                $pdo->commit();
                self::$inTransaction = false;
                return true;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                self::$inTransaction = false;
                error_log('[Store] resetAll lỗi: ' . $e->getMessage());
                return false;
            }
        }

        self::jsonMutate(function (array &$d) use ($seed) { $d = $seed; $d['__mt_initialized'] = true; });
        return true;
    }
}
