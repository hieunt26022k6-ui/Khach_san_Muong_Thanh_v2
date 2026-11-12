# HƯỚNG DẪN CÀI ĐẶT & CHẠY BỘ MÃ NGUỒN PHP 8+ OOP MVC
## MƯỜNG THANH LUXURY PHÚ QUỐC — HỆ THỐNG ĐẶT PHÒNG & QUẢN TRỊ 5 SAO

Mã nguồn được viết theo chuẩn **PHP 8+ Lập Trình Hướng Đối Tượng (OOP)** với kiến trúc **Mô Hình MVC (Model - View - Controller)** hoàn toàn thuần, không dùng thư viện ngoài cồng kềnh, tương thích hoàn hảo 100% với XAMPP (Apache & MySQL) và Visual Studio Code.

---

### 1. CẤU TRÚC THƯ MỤC CHUẨN ĐỒ ÁN MVC
```
muongthanh_hotel/
├── .htaccess                      # Điều hướng URL thân thiện cho Apache
├── index.php                      # Single Front Controller & SPL Autoloader
├── config/
│   ├── Config.php                 # Cấu hình website, hotline, địa chỉ, tỷ lệ cọc 30%
│   └── Database.php               # Kết nối PDO MySQL bảo mật
├── core/
│   ├── Router.php                 # Điều phối URL tới Controller & Action
│   ├── BaseController.php         # Render View, phản hồi JSON, Auth Guard
│   ├── BaseModel.php              # Thao tác CSDL & Kho lưu trữ Session thông minh
│   └── Session.php                # Phiên làm việc & Flash message thông báo
├── models/
│   ├── User.php                   # Xác thực & Phân quyền người dùng (RBAC)
│   ├── Room.php                   # 9 Hạng phòng & 24 Phòng thực tế tầng 2-8
│   ├── Booking.php                # Xử lý đặt phòng, cọc 30%, Check-in/Check-out
│   ├── ServiceOrder.php           # Gọi dịch vụ phòng ẩm thực & spa QR
│   └── Report.php                 # Thống kê doanh thu, tỷ lệ lấp đầy, ADR
├── controllers/
│   ├── CustomerController.php     # Khách hàng: Trang chủ, Khám phá phòng, Cổng dịch vụ
│   ├── ReceptionistController.php # Lễ tân: Sơ đồ phòng tầng 2-8 (PMS), Nhận/Trả phòng
│   ├── AdminController.php        # Quản trị viên: Doanh thu, Khóa tài khoản, Bảng giá
│   ├── AuthController.php         # Đăng nhập, Đăng ký, Đổi vai trò nhanh
│   └── ApiController.php          # Xử lý mã voucher ưu đãi MUONGTHANH10, VIPPHUQUOC20
├── views/
│   ├── layouts/                   # Header, Footer, Modal Đăng Nhập
│   ├── customer/                  # Giao diện đặt phòng 2 cột chuẩn AVS, Cổng dịch vụ
│   ├── receptionist/              # Bàn Lễ tân Front Desk PMS sơ đồ phòng rack
│   └── admin/                     # Bảng điều khiển quản trị viên và báo cáo
├── database/
│   ├── muongthanh_hotel.sql       # File SQL cơ sở dữ liệu import vào phpMyAdmin
│   └── seed_data.php              # Dữ liệu mẫu khởi tạo sẵn
└── assets/
    └── images/                    # Toàn bộ hình ảnh khách sạn 5 sao độ phân giải cao
```

---

### 2. CÁCH CÀI ĐẶT & CHẠY TRÊN XAMPP

#### Bước 1: Sao chép mã nguồn vào XAMPP
- Giải nén toàn bộ thư mục này vào thư mục `htdocs` của XAMPP:
  `C:\xampp\htdocs\muongthanh_hotel\`

#### Bước 2: Khởi động XAMPP Control Panel
- Bật **Apache**: Nhấn nút **Start**.
- Bật **MySQL**: Nhấn nút **Start**.

#### Bước 3: Tạo cơ sở dữ liệu (Tùy chọn)
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin/`
2. Nhấn vào mục **Cơ sở dữ liệu (Databases)** -> Nhập tên: `muongthanh_hotel` -> Nhấn **Tạo (Create)**.
3. Chọn cơ sở dữ liệu `muongthanh_hotel` vừa tạo -> Nhấn tab **Nhập (Import)** -> Chọn tệp `database/muongthanh_hotel.sql` trong thư mục dự án -> Nhấn **Thực hiện (Import)**.

*(Lưu ý: Hệ thống đã được tích hợp chế độ tự động Session Store thông minh; ngay cả khi bạn chưa kịp tạo cơ sở dữ liệu, website vẫn hoạt động 100% mượt mà ngay lập tức mà không bao giờ báo lỗi kết nối!)*

#### Bước 4: Mở website trên trình duyệt
Truy cập đường dẫn:
```text
http://localhost/muongthanh_hotel/
```
Toàn bộ giao diện 5 sao, 9 hạng phòng, bàn lễ tân và bảng điều khiển quản trị viên sẽ hiển thị đầy đủ ngay lập tức!

---

### 3. TÀI KHOẢN ĐĂNG NHẬP 3 PHÂN QUYỀN
Hệ thống có thanh chuyển đổi vai trò nhanh trên thanh điều hướng trên cùng, hoặc bạn có thể đăng nhập bằng tài khoản:
1. **Khách hàng (Customer)**:
   - Tài khoản: `khachhang`
   - Mật khẩu: `123`
2. **Lễ tân (Receptionist - Front Desk PMS)**:
   - Tài khoản: `letan`
   - Mật khẩu: `123`
3. **Quản trị viên (Admin - Tổng Quản Lý)**:
   - Tài khoản: `admin`
   - Mật khẩu: `123`
