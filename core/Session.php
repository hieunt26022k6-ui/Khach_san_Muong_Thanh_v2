<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Core/Session.php
 */

namespace Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // [SỬA HT-E04] Cookie phiên an toàn hơn
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    /** Tạo lại mã phiên sau đăng nhập để chống tấn công cố định phiên (session fixation). */
    public static function regenerate(): void {
        self::start();
        session_regenerate_id(true);
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        if (Store::handles($key)) { return Store::get($key, $default); }
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        if (Store::handles($key)) { Store::set($key, $value); return; }
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool {
        self::start();
        if (Store::handles($key)) { return Store::has($key); }
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        if (Store::handles($key)) { Store::remove($key); return; }
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $message = null): mixed {
        self::start();
        if ($message !== null) {
            $_SESSION['__flash'][$key] = $message;
            return null;
        }
        $val = $_SESSION['__flash'][$key] ?? null;
        unset($_SESSION['__flash'][$key]);
        return $val;
    }

    /** [SỬA HT-E04] Mã chống CSRF: một mã ngẫu nhiên cho mỗi phiên. */
    public static function csrfToken(): string {
        self::start();
        if (empty($_SESSION['__csrf'])) {
            $_SESSION['__csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['__csrf'];
    }

    public static function checkCsrf(?string $token): bool {
        self::start();
        return !empty($_SESSION['__csrf']) && is_string($token) && hash_equals($_SESSION['__csrf'], $token);
    }
}
