<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/User.php
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class User extends BaseModel {
    /**
     * Đối chiếu mật khẩu bằng password_verify. Tài khoản cũ còn mật khẩu văn bản thuần
     * (dữ liệu mẫu) vẫn đăng nhập được một lần rồi được băm lại ngay.
     */
    public function authenticate(string $username, string $password): ?array {
        $users = Session::get('users', []);
        foreach ($users as $i => $user) {
            if ($user['username'] !== $username) continue;

            $stored = (string)$user['password'];
            $isHash = password_get_info($stored)['algo'] !== null;
            $ok = $isHash ? password_verify($password, $stored) : hash_equals($stored, $password);
            if (!$ok) return null;
            if (!empty($user['isLocked'])) return null;

            if (!$isHash) { // nâng cấp mật khẩu cũ lên dạng băm
                $users[$i]['password'] = password_hash($password, PASSWORD_DEFAULT);
                Session::set('users', $users);
            }
            unset($user['password']); // không đưa mật khẩu vào phiên
            return $user;
        }
        return null;
    }

    public function getAll(): array {
        return Session::get('users', []);
    }

    public function findById(string $id): ?array {
        $users = Session::get('users', []);
        foreach ($users as $u) {
            if ($u['id'] === $id) return $u;
        }
        return null;
    }

    public function toggleLock(string $id): bool {
        $users = Session::get('users', []);
        foreach ($users as &$u) {
            if ($u['id'] === $id) {
                if (($u['role'] ?? '') === 'admin') {
                    return false; // không cho khóa tài khoản quản trị (tránh tự khóa mình)
                }
                $u['isLocked'] = !$u['isLocked'];
                Session::set('users', $users);
                return true;
            }
        }
        return false;
    }

    /** Kiểm tra trùng cả tên đăng nhập VÀ email (trước đây chỉ chặn trùng username). */
    public function isTaken(string $username, string $email = '', string $ignoreId = ''): bool {
        foreach ($this->getAll() as $u) {
            if (($u['id'] ?? '') === $ignoreId) continue;
            if (strcasecmp((string)($u['username'] ?? ''), $username) === 0) return true;
            if ($email !== '' && strcasecmp((string)($u['email'] ?? ''), $email) === 0) return true;
        }
        return false;
    }

    public function register(array $data): ?array {
        $email = trim((string)($data['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null; // email sai định dạng
        }
        if ($this->isTaken((string)$data['username'], $email)) {
            return null; // trùng tên đăng nhập hoặc trùng email
        }
        $newUser = [
            'id' => 'usr_' . uniqid(),
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'fullName' => $data['fullName'],
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'idCard' => $data['idCard'] ?? '',
            'role' => 'customer',
            'isLocked' => false,
            'createdAt' => date('Y-m-d H:i:s')
        ];
        $users = Session::get('users', []);
        $users[] = $newUser;
        Session::set('users', $users);
        unset($newUser['password']); // không trả mật khẩu cho tầng trên
        return $newUser;
    }

    /**
     * Quản trị viên tạo tài khoản nhân viên (lễ tân) — đề bài yêu cầu
     * "quản lý người dùng": tạo tài khoản + gán vai trò, không chỉ khóa/mở khóa.
     */
    public function createStaff(array $data): array {
        $username = trim((string)($data['username'] ?? ''));
        $password = (string)($data['password'] ?? '');
        $fullName = trim((string)($data['fullName'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $role = in_array($data['role'] ?? '', ['receptionist', 'admin', 'customer'], true)
            ? $data['role'] : 'receptionist';

        if ($username === '' || mb_strlen($username) < 3) {
            return ['ok' => false, 'message' => 'Tên đăng nhập phải có ít nhất 3 ký tự.'];
        }
        if (!preg_match('/^[a-zA-Z0-9_.]{3,32}$/', $username)) {
            return ['ok' => false, 'message' => 'Tên đăng nhập chỉ gồm chữ, số, dấu chấm hoặc gạch dưới (3–32 ký tự).'];
        }
        if (strlen($password) < 6) {
            return ['ok' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự.'];
        }
        if ($fullName === '') {
            return ['ok' => false, 'message' => 'Vui lòng nhập họ tên nhân viên.'];
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Email không đúng định dạng.'];
        }
        if ($this->isTaken($username, $email)) {
            return ['ok' => false, 'message' => 'Tên đăng nhập hoặc email đã được sử dụng.'];
        }

        $users = Session::get('users', []);
        $newUser = [
            'id' => 'usr_' . uniqid(),
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'fullName' => $fullName,
            'email' => $email,
            'phone' => trim((string)($data['phone'] ?? '')),
            'idCard' => '',
            'role' => $role,
            'isLocked' => false,
            'createdAt' => date('Y-m-d H:i:s'),
        ];
        $users[] = $newUser;
        Session::set('users', $users);

        return ['ok' => true, 'message' => "Đã tạo tài khoản {$username} với vai trò {$role}.", 'user' => $newUser];
    }

    /** Gán lại vai trò cho một tài khoản (trừ tài khoản quản trị cao nhất). */
    public function updateRole(string $id, string $role): bool {
        if (!in_array($role, ['receptionist', 'admin', 'customer'], true)) return false;

        $users = Session::get('users', []);
        foreach ($users as &$u) {
            if (($u['id'] ?? '') !== $id) continue;
            if (($u['role'] ?? '') === 'admin' && $role !== 'admin') return false; // không hạ quyền quản trị
            $u['role'] = $role;
            Session::set('users', $users);
            return true;
        }
        return false;
    }
}
