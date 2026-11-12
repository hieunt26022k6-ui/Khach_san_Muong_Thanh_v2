<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/AuthController.php
 */

namespace Controllers;

use Core\BaseController;
use Core\Session;
use Models\User;

class AuthController extends BaseController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Xử lý Đăng nhập
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $user = $this->userModel->authenticate($username, $password);
            if ($user) {
                Session::regenerate(); // [SỬA HT-E04] chống cố định phiên
                Session::set('currentUser', $user);
                Session::flash('success', "Xin chào {$user['fullName']}! Đăng nhập thành công.");

                if ($user['role'] === 'receptionist') {
                    $this->redirect('index.php?controller=receptionist&action=index');
                } elseif ($user['role'] === 'admin') {
                    $this->redirect('index.php?controller=admin&action=index');
                } else {
                    $this->redirect('index.php?controller=customer&action=portal');
                }
            } else {
                Session::flash('error', 'Tên đăng nhập hoặc mật khẩu không chính xác, hoặc tài khoản đã bị khóa.');
                $this->redirect('index.php?controller=customer&action=index');
            }
        } else {
            $this->redirect('index.php?controller=customer&action=index');
        }
    }

    /**
     * Xử lý Đăng ký tài khoản khách hàng mới
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $fullName = trim($_POST['fullName'] ?? '');

            if (strlen($password) < 6) {
                Session::flash('error', 'Mật khẩu phải có ít nhất 6 ký tự.');
                $this->redirect('index.php?controller=customer&action=index');
            }
            if (!$username || !$password || !$fullName) {
                Session::flash('error', 'Vui lòng nhập đầy đủ họ tên, tên đăng nhập và mật khẩu.');
                $this->redirect('index.php?controller=customer&action=index');
            }

            $newUser = $this->userModel->register([
                'username' => $username,
                'password' => $password,
                'fullName' => $fullName,
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'idCard' => trim($_POST['idCard'] ?? ''),
            ]);

            if ($newUser) {
                Session::regenerate();
                Session::set('currentUser', $newUser);
                Session::flash('success', "Chúc mừng {$fullName} đã đăng ký thành viên Mường Thanh Luxury thành công!");
                $this->redirect('index.php?controller=customer&action=portal');
            } else {
                Session::flash('error', 'Tên đăng nhập này đã được sử dụng. Vui lòng chọn tên khác.');
                $this->redirect('index.php?controller=customer&action=index');
            }
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(): void {
        Session::remove('currentUser');
        Session::regenerate();
        Session::flash('success', 'Bạn đã đăng xuất khỏi hệ thống.');
        $this->redirect('index.php?controller=customer&action=index');
    }

    /**
     * Phím tắt chuyển đổi vai trò nhanh (Dành cho kiểm thử đồ án / giảng viên)
     */
    public function switchRole(): void {
        // [SỬA HT-E07] Tính năng này bỏ qua đăng nhập, chỉ dùng khi trình diễn
        if (!\Config\Config::DEMO_MODE) {
            Session::flash('error', 'Tính năng chuyển vai trò nhanh đã bị tắt.');
            $this->redirect('index.php?controller=customer&action=index');
        }
        $role = $_GET['role'] ?? 'customer';
        $users = $this->userModel->getAll();

        $targetUser = null;
        foreach ($users as $u) {
            if ($u['role'] === $role) {
                $targetUser = $u;
                break;
            }
        }

        if ($targetUser) {
            unset($targetUser['password']);
            Session::set('currentUser', $targetUser);
            Session::flash('success', "Đã chuyển sang vai trò: {$targetUser['fullName']} ({$role})");
            if ($role === 'receptionist') {
                $this->redirect('index.php?controller=receptionist&action=index');
            } elseif ($role === 'admin') {
                $this->redirect('index.php?controller=admin&action=index');
            } else {
                $this->redirect('index.php?controller=customer&action=portal');
            }
        } else {
            $this->redirect('index.php?controller=customer&action=index');
        }
    }
}
