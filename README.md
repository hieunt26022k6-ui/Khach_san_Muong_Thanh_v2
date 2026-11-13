# Khách sạn Mường Thanh Luxury Phú Quốc — BTL TMU x DEHA

Website đặt phòng khách sạn (PHP 8 OOP + MVC + **MySQL/PDO**), đồ án cuối kỳ môn **Lập trình web với PHP (TMU x DEHA)**.
Repo này là **bản riêng của Nguyễn Trung Hiếu (Module 4 — Quản trị viên)** sau khi rà soát, sửa lỗi và bổ sung
phần còn thiếu so với barem chấm.

## Chạy trên XAMPP (3 bước)

1. Copy thư mục này vào `C:\xampp\htdocs\Khach_san_Muong_Thanh_v2`
2. Bật **Apache** + **MySQL** trong XAMPP Control Panel
3. Import CSDL: mở <http://localhost/phpmyadmin> → tab **Import** → chọn `database/muongthanh_hotel.sql`
   (hoặc dòng lệnh: `C:\xampp\mysql\bin\mysql.exe -u root < database\muongthanh_hotel.sql`)
4. Mở <http://localhost/Khach_san_Muong_Thanh_v2/>

Lần chạy đầu, hệ thống tự nạp bộ dữ liệu mẫu đầy đủ (4 tài khoản, 9 hạng phòng, 24 phòng vật lý,
3 gói phòng, 7 dịch vụ, đơn đặt phòng, nhật ký…) vào MySQL.

## Tài khoản demo

| Vai trò | Tên đăng nhập | Mật khẩu |
|---|---|---|
| Quản trị viên | `admin` | `123` |
| Lễ tân | `letan` | `123` |
| Khách hàng | `khachhang` / `khachhang2` | `123` |

Mật khẩu mẫu đang để dạng văn bản để tiện demo — lần đăng nhập đầu tiên hệ thống **tự băm lại**
bằng `password_hash()`; tài khoản tạo mới trong trang quản trị lưu ngay dạng băm.

## Cơ sở dữ liệu — quan hệ thật, không phải "lớp chuyển tiếp"

CSDL `muongthanh_hotel` có **15 bảng**, khóa chính/khóa ngoại đầy đủ (`database/muongthanh_hotel.sql`).

| Quan hệ | Bảng | Cách thể hiện |
|---|---|---|
| **1–N** | `room_categories` → `physical_rooms` | `physical_rooms.category_id` FK → `room_categories.id` |
| **1–N** | `users` → `bookings` | `bookings.user_id` FK → `users.id` |
| **1–N** | `bookings` → `service_orders` | `service_orders.booking_id` FK → `bookings.id` |
| **N–N** | `bookings` ↔ `services` | bảng trung gian `booking_services(booking_id, service_id)` khóa chính ghép |
| **1–1** | `users` ↔ `user_profiles` | `user_profiles.user_id` vừa là PK vừa là FK |
| **1–1** | khách sạn ↔ `hotel_policy` | bảng cấu hình đúng một dòng cho mỗi chính sách |

**Truy vấn SQL nằm ở đâu?** — `core/Store.php` (lớp truy cập dữ liệu dùng chung) sinh câu lệnh
`SELECT / INSERT / UPDATE / DELETE` bằng PDO prepared statement cho cả 12 nhóm dữ liệu;
`models/Report.php::getDailyRevenue()` dùng `SELECT … GROUP BY DATE(check_in_date)` để tính doanh thu.
Mỗi bảng còn một cột `payload` kiểu JSON giữ các thuộc tính mở rộng của giao diện, nên View không phải sửa
khi chuyển từ kho JSON sang MySQL.

Nếu MySQL chưa bật, `Store` tự rơi về tệp `database/storage/data.json` để buổi demo không trắng trang —
trang quản trị vẫn chạy, chỉ là dữ liệu không nằm trong MySQL.

## Giao diện: một hệ thống duy nhất (PHP MVC)

- **Khách hàng**: `index.php?controller=customer&…` (View PHP: trang chủ, tìm phòng, đặt phòng, cổng dịch vụ, ẩm thực, spa).
- **Lễ tân / Quản trị**: đăng nhập bằng form PHP → vào bảng điều khiển PHP có kiểm tra quyền ở máy chủ.
- Bundle React build sẵn (`assets/index-*.js`) chỉ còn mở được khi gọi rõ `?view_mode=spa`;
  đây là bản thiết kế tham chiếu ban đầu, **không phải giao diện chính** và không tham gia phân quyền.

## Module 4 — Quản trị viên (phần của Hiếu)

| Mã | Chức năng | Nơi cài đặt |
|---|---|---|
| AD-11 | Quản lý tài khoản & phân quyền: tạo tài khoản, gán vai trò, khóa/mở khóa (không xóa cứng) | `AdminController::createUser/updateRole/toggleUserLock`, `models/User.php` |
| AD-12 | Bảng giá hạng phòng, **gói phòng (phụ thu + bật/tắt)**, **khóa phòng bảo trì**, chính sách cọc – hủy – **phạt hủy muộn** | `AdminController::updatePrice/updatePackage/setRoomMaintenance/updatePolicy`, `models/Room.php` |
| AD-13 | Dashboard, báo cáo doanh thu (SQL GROUP BY), nhật ký thao tác server-side | `models/Report.php`, `models/AuditLog.php` |

Bảng điều khiển có 7 tab: Tổng quan & Doanh thu · Hạng phòng & Bảng giá · **Gói phòng & Bảo trì** ·
Quản lý người dùng (RBAC) · Toàn bộ đơn đặt phòng · Chính sách cọc & Hủy · Nhật ký hệ thống.

## Yêu cầu bắt buộc của đề — đã có

- **Phân trang** (10 dòng/trang) + **tìm kiếm** trên người dùng, đơn đặt phòng, nhật ký
- **Xử lý file**: upload ảnh hạng phòng (kiểm tra 4 lớp: lỗi upload → đuôi → MIME thật → ≤ 2MB),
  ảnh lưu ở `assets/uploads/` nên hiển thị được ngay trên trình duyệt
- **Đăng nhập + quản lý người dùng** ở phía admin, phân quyền kiểm tra ở máy chủ (`requireRole`)
- **CSRF** cho mọi POST, `password_hash`/`password_verify`, escape HTML bằng `htmlspecialchars`
- **CSDL quan hệ** MySQL với đủ 3 loại quan hệ N–N, 1–N, 1–1 (bảng trên)
- Nghiệp vụ có transaction: trả phòng cập nhật đơn + trạng thái phòng trong **một** giao dịch

## Cấu trúc

```
index.php                 # front controller + autoloader
config/                   # Config, Database (PDO)
core/                     # Router, Session, Store (MySQL), BaseController, BaseModel
controllers/              # Admin, Auth, Customer, Receptionist, Api
models/                   # User, Room, Booking, Report, AuditLog, Voucher, ServiceOrder
views/                    # layouts + admin/ customer/ receptionist/
database/                 # muongthanh_hotel.sql (15 bảng), seed_data.php, storage/
assets/                   # hình ảnh, bundle giao diện cũ, uploads/
```

## Quy trình làm việc nhóm

```bash
git pull                # trước khi làm
# ...sửa code...
git add -A && git commit -m "..." && git push
```

File dùng chung cần thống nhất người giữ: `index.php`, `core/`, `database/*.sql`.

## Giới hạn đã biết (ghi rõ để không bị hỏi khó)

- Các bảng dùng cột `payload` JSON cho thuộc tính mở rộng — chấp nhận được vì đề yêu cầu quan hệ và
  truy vấn SQL, không yêu cầu chuẩn hóa tuyệt đối từng cột.
- Report còn một số chỉ số ước tính (ADR/RevPAR) và đã ghi chú ngay trên giao diện.
- Tên "25 phòng" trong bundle React cũ không sửa được (file minified); CSDL thực tế có 24 phòng vật lý.
