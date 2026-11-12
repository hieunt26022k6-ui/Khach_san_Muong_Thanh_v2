<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Core/BaseController.php
 */

namespace Core;

use Config\Config;

abstract class BaseController {
    /**
     * Render a view file with optional data and layouts
     */
    protected function render(string $viewPath, array $data = [], bool $withLayout = true): void {
        extract($data);
        $currentUser = $this->getCurrentUser();
        $baseUrl = Config::getBaseUrl();

        if ($withLayout) {
            $headerFile = __DIR__ . '/../views/layouts/header.php';
            $footerFile = __DIR__ . '/../views/layouts/footer.php';
            $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';

            if (file_exists($headerFile)) {
                require $headerFile;
            }
            if (file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo "<div class='p-8 text-red-600 font-bold'>Lỗi: Không tìm thấy giao diện view: {$viewPath}</div>";
            }
            if (file_exists($footerFile)) {
                require $footerFile;
            }
        } else {
            $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
            if (file_exists($viewFile)) {
                require $viewFile;
            }
        }
    }

    /**
     * Send JSON response (for AJAX endpoints)
     */
    protected function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirect to another action or URL
     */
    protected function redirect(string $url): void {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $baseUrl = Config::getBaseUrl();
            $url = $baseUrl . '/' . ltrim($url, '/');
        }
        header("Location: {$url}");
        exit;
    }

    /**
     * Retrieve currently logged-in user
     */
    protected function getCurrentUser(): ?array {
        return Session::get('currentUser', null);
    }

    /**
     * [SỬA LẦN 2] Lọc theo từ khoá trên nhiều trường (không phân biệt hoa/thường, có dấu tiếng Việt).
     */
    protected function containsKeyword(array $row, array $fields, string $keyword): bool {
        if ($keyword === '') return true;
        $needle = mb_strtolower($keyword, 'UTF-8');
        foreach ($fields as $field) {
            $value = $row[$field] ?? '';
            if (is_array($value)) continue;
            if ($value !== '' && mb_strpos(mb_strtolower((string)$value, 'UTF-8'), $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * [SỬA LẦN 2] Cắt danh sách theo trang — trả về dữ liệu của trang + tổng số trang.
     */
    protected function paginateRows(array $rows, int $page, int $perPage = 10): array {
        $total = count($rows);
        $pages = max(1, (int)ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        return [
            'rows'  => array_slice($rows, ($page - 1) * $perPage, $perPage),
            'total' => $total,
            'pages' => $pages,
            'page'  => $page,
        ];
    }

    /**
     * Require a specific role or redirect
     */
    protected function requireRole(string $role): void {
        $user = $this->getCurrentUser();
        if (!$user) {
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('index.php?controller=auth&action=login');
        }
        if ($user['role'] !== $role && $user['role'] !== 'admin') {
            Session::flash('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect('index.php?controller=customer&action=index');
        }
    }
}
