<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Core/Router.php
 */

namespace Core;

use ReflectionMethod;

class Router {
    public static function dispatch(): void {
        $controllerName = preg_replace('/[^a-zA-Z]/', '', (string)($_GET['controller'] ?? 'customer'));
        $actionName = (string)($_GET['action'] ?? 'index');

        $controllerClass = 'Controllers\\' . ucfirst($controllerName) . 'Controller';

        if (!class_exists($controllerClass)) {
            // Fallback: nếu không có Controller thì về trang chủ khách hàng
            $controllerClass = 'Controllers\\CustomerController';
            $actionName = 'index';
        }

        // [SỬA HT-E04] Mọi yêu cầu POST (trừ API chỉ đọc) phải mang mã CSRF hợp lệ
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && $controllerName !== 'api') {
            if (!Session::checkCsrf($_POST['csrf'] ?? null)) {
                http_response_code(403);
                echo 'Yêu cầu bị từ chối: thiếu hoặc sai mã bảo vệ (CSRF). Hãy tải lại trang rồi thử lại.';
                return;
            }
        }

        $controllerInstance = new $controllerClass();

        // [SỬA HT-E02] Chỉ cho gọi phương thức public, không phải phương thức ma thuật (__construct...)
        $callable = false;
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $actionName) && method_exists($controllerInstance, $actionName)) {
            $callable = (new ReflectionMethod($controllerInstance, $actionName))->isPublic();
        }
        if (!$callable) {
            $actionName = 'index';
        }

        $controllerInstance->$actionName();
    }
}
