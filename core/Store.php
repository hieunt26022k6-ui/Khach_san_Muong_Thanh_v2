<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Core/Store.php
 *
 * [SỬA HT-E01] Kho dữ liệu DÙNG CHUNG bằng tệp JSON (database/storage/data.json).
 * Mọi phiên trình duyệt đọc/ghi cùng một nơi, nên đơn đặt phòng của khách, thao tác của lễ tân
 * và khóa tài khoản của quản trị viên nhìn thấy nhau. Đây là bước đệm trước khi chuyển sang MySQL.
 * Nếu thư mục không ghi được, Session tự quay về lưu theo $_SESSION như cũ.
 */

namespace Core;

final class Store {
    /** Các khóa dữ liệu nghiệp vụ lưu chung. Khóa còn lại (currentUser, __flash, __csrf) vẫn theo phiên. */
    public const KEYS = [
        'users', 'roomCategories', 'physicalRooms', 'packages', 'services', 'bookings',
        'serviceOrders', 'auditLogs', 'incidents', 'reviews', 'policy', 'templates',
        'sentNotifications', '__mt_initialized',
    ];

    private static ?bool $enabled = null;

    private static function path(): string {
        return __DIR__ . '/../database/storage/data.json';
    }

    public static function enabled(): bool {
        if (self::$enabled === null) {
            $dir = dirname(self::path());
            if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
            self::$enabled = is_dir($dir) && is_writable($dir)
                && (!file_exists(self::path()) || is_writable(self::path()));
        }
        return self::$enabled;
    }

    public static function handles(string $key): bool {
        return self::enabled() && in_array($key, self::KEYS, true);
    }

    private static function readAll(): array {
        $file = self::path();
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

    /** Đọc – sửa – ghi trong MỘT khóa độc quyền để hai yêu cầu không ghi đè lẫn nhau. */
    private static function mutate(callable $fn): void {
        $fh = fopen(self::path(), 'c+');
        if (!$fh) { return; }
        flock($fh, LOCK_EX);
        $raw  = stream_get_contents($fh);
        $data = json_decode((string)$raw, true);
        if (!is_array($data)) { $data = []; }
        $fn($data);

        // [SỬA LẦN 2] Mã hoá TRƯỚC khi ghi. Bản trước ftruncate rồi mới json_encode: chỉ cần một
        // chuỗi không phải UTF-8 (dữ liệu form gửi sai bảng mã) là json_encode trả false →
        // fwrite ghi rỗng → toàn bộ dữ liệu trong data.json bị xoá sạch.
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

    public static function get(string $key, mixed $default = null): mixed {
        $all = self::readAll();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function has(string $key): bool {
        $all = self::readAll();
        return isset($all[$key]);
    }

    public static function set(string $key, mixed $value): void {
        self::mutate(function (array &$d) use ($key, $value) { $d[$key] = $value; });
    }

    public static function remove(string $key): void {
        self::mutate(function (array &$d) use ($key) { unset($d[$key]); });
    }
}
