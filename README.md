# Khách sạn Mường Thanh Luxury Phú Quốc — BTL TMU x DEHA

Website đặt phòng khách sạn (PHP 8 OOP + MVC), đồ án cuối kỳ môn **Lập trình web với PHP (TMU x DEHA)**.

Repo này là **bản riêng của Nguyễn Trung Hiếu (Module 4 — Quản trị viên)** sau khi rà soát và sửa lỗi,
tách khỏi repo của nhóm để cả nhóm cùng commit mà không ảnh hưởng bản đang nộp.

## Chạy trên XAMPP

1. Copy thư mục này vào `C:\xampp\htdocs\Khach_san_Muong_Thanh_v2`
2. Bật **Apache** trong XAMPP Control Panel
3. Mở `http://localhost/Khach_san_Muong_Thanh_v2/`

Bản chạy nhanh không cần Apache:

```bash
C:\xampp\php\php.exe -S 127.0.0.1:8000 -t .
# http://127.0.0.1:8000/
```

## Tài khoản demo

| Vai trò | Tên đăng nhập | Mật khẩu |
|---|---|---|
| Quản trị viên | `admin` | `123` |
| Lễ tân | `letan` | `123` |
| Khách hàng | `khachhang` / `khachhang2` | `123` |

Đăng nhập bằng nút **ĐĂNG NHẬP** ở chân trang (form PHP, có CSRF). Sau khi đăng nhập, quản trị viên và
lễ tân được đưa thẳng vào bảng điều khiển PHP:

- Admin: `index.php?controller=admin&action=index`
- Lễ tân: `index.php?controller=receptionist&action=index`

## Module 4 — Quản trị viên (phần của Hiếu)

| Mã | Chức năng | File |
|---|---|---|
| AD-11 | Quản lý tài khoản nhân viên & phân quyền: tạo tài khoản, gán vai trò, khóa/mở khóa (không xóa cứng) | `controllers/AdminController.php`, `models/User.php` |
| AD-12 | Giá & gói phòng, khóa bảo trì, chính sách cọc/hủy (chỉ áp dụng cho đơn mới) | `controllers/AdminController.php`, `models/Room.php` |
| AD-13 | Dashboard, báo cáo doanh thu, nhật ký thao tác (audit log) | `models/Report.php`, `models/AuditLog.php` |

Bảng điều khiển có 6 tab: Tổng quan & Doanh thu · Hạng phòng & Bảng giá · Quản lý người dùng (RBAC) ·
Toàn bộ đơn đặt phòng · Chính sách cọc & Hủy · Nhật ký hệ thống.

## Yêu cầu bắt buộc của đề — đã có

- **Phân trang** (10 dòng/trang) + **tìm kiếm** trên danh sách người dùng, đơn đặt phòng, nhật ký
- **Xử lý file**: upload ảnh hạng phòng (`move_uploaded_file`, kiểm tra 4 lớp: lỗi upload → đuôi → MIME thật → ≤ 2MB)
- **Đăng nhập + quản lý người dùng** ở phía admin
- **CSRF token** cho mọi POST, `password_hash`/`password_verify`, kiểm tra quyền ở máy chủ (`requireRole`)
- PHP OOP/MVC, 3 loại quan hệ bảng N–N, 1–N, 1–1 trong `database/muongthanh_hotel.sql`

## Cấu trúc

```
index.php                 # front controller + autoloader
config/                   # Config, Database
core/                     # Router, Session, Store, BaseController, BaseModel
controllers/              # Admin, Auth, Customer, Receptionist, Api
models/                   # User, Room, Booking, Report, AuditLog, Voucher, ServiceOrder
views/                    # layouts + admin/ customer/ receptionist/
database/                 # muongthanh_hotel.sql, seed_data.php, storage/
assets/                   # bundle giao diện + hình ảnh
```

## Ghi chú kỹ thuật

- Dữ liệu hiện lưu ở `database/storage/data.json` (kho dùng chung nhiều phiên trình duyệt) — **bước đệm
  trước khi chuyển sang MySQL/PDO**; file `.sql` đã có sẵn schema.
- `Config::DEBUG` và `Config::DEMO_MODE` đang **false** (bản nộp). Bật `DEMO_MODE` chỉ khi cần chuyển vai
  trò nhanh lúc trình diễn.
- Giao diện khách là bundle React build sẵn (`assets/index-*.js`) nên **không sửa trực tiếp được**;
  phần nghiệp vụ và phân quyền chạy ở UI PHP.

## Quy trình làm việc nhóm

```bash
git pull            # trước khi làm
# ...sửa code...
git add -A && git commit -m "mo ta thay doi"
git push            # xong việc là push ngay
```

Mỗi người nên làm trên nhánh riêng (`module-admin`, `module-letan`, …) rồi merge vào `main`.
`index.php`, `core/`, `config/Database.php`, `database/*.sql` là file dùng chung — thống nhất ai giữ trước khi sửa.
