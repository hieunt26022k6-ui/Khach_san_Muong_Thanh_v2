<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Config/Config.php
 */

namespace Config;

class Config {
    public const APP_NAME = 'Mường Thanh Luxury Phú Quốc';
    public const APP_TAGLINE = 'Khách Sạn 5 Sao Bên Biển Bãi Trường — Chuẩn Nghỉ Dưỡng AVS';
    public const HOTEL_ADDRESS = 'Khu phức hợp Bãi Trường, Xã Dương Tơ, TP. Phú Quốc, Kiên Giang';
    public const HOTEL_HOTLINE = '1900 1833 — (0297) 364 5555';
    public const HOTEL_EMAIL = 'info@phuquoc.muongthanh.vn';
    public const DEPOSIT_PERCENT = 30; // Tiền cọc tối thiểu 30%

    // Mặc định TẮT cả hai công tắc để bản nộp không còn đường tắt đăng nhập.
    // Khi cần trình diễn nhanh mới bật tạm DEMO_MODE = true.
    public const DEBUG = false;     // true: hiện lỗi chi tiết (chỉ dùng khi phát triển)
    public const DEMO_MODE = false; // true: cho phép thanh "chuyển vai trò nhanh" (chỉ dùng khi trình diễn)

    public static function getBaseUrl(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));
        if ($dir === '/' || $dir === '.') {
            $dir = '';
        }
        return $protocol . $host . $dir;
    }

    public static function formatVnd(int|float $amount): string {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
}
