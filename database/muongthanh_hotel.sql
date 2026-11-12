-- ==============================================================================
-- CƠ SỞ DỮ LIỆU: MƯỜNG THANH LUXURY PHÚ QUỐC
-- KIẾN TRÚC: PHP 8+ OOP MÔ HÌNH MVC (MODEL - VIEW - CONTROLLER)
-- HỖ TRỢ XAMPP (MYSQL 8.0+ / MARIADB 10.4+)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `muongthanh_hotel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `muongthanh_hotel`;

-- 1. BẢNG NGƯỜI DÙNG & TÀI KHOẢN (RBAC)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `id_card` VARCHAR(30) DEFAULT NULL,
    `role` ENUM('customer', 'receptionist', 'admin') NOT NULL DEFAULT 'customer',
    `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `id_card`, `role`, `is_locked`) VALUES
('usr_cust_01', 'khachhang', '123', 'Lê Minh Khách', 'khach@gmail.com', '0912345678', '079095001288', 'customer', 0),
('usr_rec_01', 'letan', '123', 'Nguyễn Thu Thảo', 'thuthao.frontdesk@muongthanh.vn', '0908765432', '001192008899', 'receptionist', 0),
('usr_adm_01', 'admin', '123', 'Hoàng Anh Tuấn (Tổng Quản Lý)', 'gm.phuquoc@muongthanh.vn', '0988112233', '001085000123', 'admin', 0),
('usr_cust_02', 'khachhang2', '123', 'Trần Ngọc Bảo Trâm', 'baotram.tran@gmail.com', '0912998877', '001198004512', 'customer', 0);

-- 2. BẢNG 9 HẠNG PHÒNG NGHỈ DƯỠNG
DROP TABLE IF EXISTS `room_categories`;
CREATE TABLE `room_categories` (
    `id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `vietnamese_name` VARCHAR(150) NOT NULL,
    `view_type` VARCHAR(50) NOT NULL,
    `bed_type` VARCHAR(100) NOT NULL,
    `area_sqm` INT NOT NULL,
    `base_price` BIGINT NOT NULL,
    `max_adults` INT NOT NULL DEFAULT 2,
    `max_children` INT NOT NULL DEFAULT 1,
    `hero_image` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `room_categories` (`id`, `code`, `name`, `vietnamese_name`, `view_type`, `bed_type`, `area_sqm`, `base_price`, `max_adults`, `max_children`, `hero_image`, `description`) VALUES
('dtcv', 'DTCV', 'Deluxe Twin City View', 'Deluxe 2 Giường Đơn — Hướng Phố', 'CITY', '2 giường đơn (1.2m x 2.0m)', 37, 1950000, 2, 1, 'assets/images/room_dtcv_main_1793420634905.jpg', 'Không gian trang nhã với nội thất gỗ cao cấp, ban công nhìn toàn cảnh phố biển sôi động.'),
('ddcv', 'DDCV', 'Deluxe Double City View', 'Deluxe Giường Đôi — Hướng Phố', 'CITY', '1 giường King (2.0m x 2.0m)', 37, 2100000, 2, 1, 'assets/images/room_dtcv_main_1793420634905.jpg', 'Lựa chọn lý tưởng cho cặp đôi với giường King size êm ái và tầm nhìn thành phố biển lãng mạn.'),
('dkov', 'DKOV', 'Deluxe King Ocean View', 'Deluxe Giường Đôi — Hướng Trực Diện Biển', 'OCEAN', '1 giường King siêu lớn (2.2m x 2.0m)', 42, 2850000, 2, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Hạng phòng biểu tượng đón trọn khoảnh khắc hoàng hôn Bãi Trường lộng lẫy từ ban công kính.'),
('dtov', 'DTOV', 'Deluxe Twin Ocean View', 'Deluxe 2 Giường Đơn — Hướng Biển', 'OCEAN', '2 giường đơn lớn (1.4m x 2.0m)', 42, 2850000, 2, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Hoàn hảo cho gia đình hoặc đồng nghiệp du lịch biển, đón làn gió biển trong lành mỗi sớm mai.'),
('esov', 'ESOV', 'Executive Suite Ocean View', 'Executive Suite — Phòng Khách Riêng Hướng Biển', 'OCEAN', '1 giường Super King (2.2m x 2.2m)', 68, 4500000, 2, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Suite cao cấp với phòng khách riêng biệt và phòng ngủ view biển panorama tuyệt mỹ.'),
('jrsb', 'JRSB', 'Junior Suite Beachfront', 'Junior Suite — Sát Biển Ban Công Vườn', 'GARDEN_OCEAN', '1 giường King', 58, 3800000, 2, 2, 'assets/images/room_dtcv_main_1793420634905.jpg', 'Vài bước chân ra tới bãi cát trắng mịn, kết hợp hài hòa giữa khu vườn nhiệt đới và sóng biển.'),
('psov', 'PSOV', 'Premier Suite Oceanfront', 'Premier Suite — Góc Panorama Mặt Biển', 'OCEAN', '1 giường Super King', 85, 6200000, 3, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Vị trí góc đỉnh cao với kính cong tràn viền mở tầm nhìn 270 độ ra toàn bộ vịnh Thái Lan.'),
('exlo', 'EXLO', 'Executive Lounge Suite', 'Executive Lounge Suite — Tầng Cao VIP', 'OCEAN', '1 giường King', 72, 5400000, 2, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Tầng 8 cao nhất với đặc quyền sử dụng Executive Sky Lounge, cocktail hoàng hôn miễn phí mỗi ngày.'),
('pres', 'PRES', 'Presidential Suite Grand', 'Phòng Tổng Thống — Siêu Căn Hộ Nghỉ Dưỡng', 'OCEAN', '2 phòng ngủ Master King', 185, 18000000, 4, 2, 'assets/images/room_dkov_suite_1793420636995.jpg', 'Biểu tượng xa hoa đỉnh cao bậc nhất: 2 phòng ngủ master, phòng họp thượng đỉnh, đàn piano và phòng tắm dát vàng.');

-- 3. BẢNG PHÒNG THỰC TẾ (ROOM RACK TẦNG 2 ĐẾN TẦNG 8)
DROP TABLE IF EXISTS `physical_rooms`;
CREATE TABLE `physical_rooms` (
    `id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `room_number` VARCHAR(20) NOT NULL UNIQUE,
    `floor` INT NOT NULL,
    `category_id` VARCHAR(20) NOT NULL,
    `status` ENUM('available', 'occupied', 'reserved', 'cleaning', 'maintenance') NOT NULL DEFAULT 'available',
    `current_guest_name` VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `room_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `physical_rooms` (`id`, `room_number`, `floor`, `category_id`, `status`, `current_guest_name`) VALUES
('p_201', 'P.201', 2, 'dtcv', 'available', NULL),
('p_202', 'P.202', 2, 'dtcv', 'occupied', 'Nguyễn Văn An'),
('p_203', 'P.203', 2, 'ddcv', 'reserved', 'Đỗ Mai Hương'),
('p_204', 'P.204', 2, 'jrsb', 'cleaning', NULL),
('p_301', 'P.301', 3, 'dtcv', 'available', NULL),
('p_302', 'P.302', 3, 'ddcv', 'available', NULL),
('p_303', 'P.303', 3, 'dkov', 'occupied', 'Lê Minh Khách'),
('p_304', 'P.304', 3, 'dtov', 'available', NULL),
('p_401', 'P.401', 4, 'dkov', 'available', NULL),
('p_402', 'P.402', 4, 'dtov', 'maintenance', NULL),
('p_403', 'P.403', 4, 'esov', 'occupied', 'Trần Ngọc Bảo Trâm'),
('p_404', 'P.404', 4, 'jrsb', 'available', NULL),
('p_501', 'P.501', 5, 'dkov', 'available', NULL),
('p_502', 'P.502', 5, 'dkov', 'occupied', 'Phạm Quốc Hùng'),
('p_503', 'P.503', 5, 'esov', 'available', NULL),
('p_504', 'P.504', 5, 'psov', 'reserved', 'Vũ Thị Lan'),
('p_601', 'P.601', 6, 'dkov', 'available', NULL),
('p_602', 'P.602', 6, 'psov', 'available', NULL),
('p_603', 'P.603', 6, 'exlo', 'occupied', 'David Miller'),
('p_604', 'P.604', 6, 'exlo', 'available', NULL),
('p_701', 'P.701', 7, 'esov', 'available', NULL),
('p_702', 'P.702', 7, 'psov', 'available', NULL),
('p_801', 'P.801', 8, 'exlo', 'available', NULL),
('p_804', 'P.804', 8, 'pres', 'reserved', 'Đoàn Khách VIP Vingroup');

-- 4. BẢNG ĐẶT PHÒNG & THANH TOÁN CỌC 30%
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `booking_code` VARCHAR(30) NOT NULL UNIQUE,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `customer_email` VARCHAR(100) DEFAULT NULL,
    `category_id` VARCHAR(20) NOT NULL,
    `category_name` VARCHAR(100) NOT NULL,
    `assigned_room_number` VARCHAR(20) DEFAULT NULL,
    `check_in_date` DATE NOT NULL,
    `check_out_date` DATE NOT NULL,
    `nights` INT NOT NULL DEFAULT 1,
    `package_id` VARCHAR(50) NOT NULL,
    `package_name` VARCHAR(100) NOT NULL,
    `total_amount` BIGINT NOT NULL,
    `deposit_paid` BIGINT NOT NULL,
    `remaining_balance` BIGINT NOT NULL,
    `status` ENUM('confirmed', 'checked_in', 'completed', 'cancelled') NOT NULL DEFAULT 'confirmed',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bookings` (`id`, `booking_code`, `customer_name`, `customer_phone`, `customer_email`, `category_id`, `category_name`, `assigned_room_number`, `check_in_date`, `check_out_date`, `nights`, `package_id`, `package_name`, `total_amount`, `deposit_paid`, `remaining_balance`, `status`) VALUES
('bk_01', 'MT-8492', 'Lê Minh Khách', '0912345678', 'khach@gmail.com', 'dkov', 'Deluxe King Ocean View', 'P.303', '2026-11-08', '2026-11-11', 3, 'pkg_breakfast', 'Gói Kèm Buffet Sáng', 9600000, 2880000, 6720000, 'checked_in'),
('bk_02', 'MT-5120', 'Trần Ngọc Bảo Trâm', '0912998877', 'baotram.tran@gmail.com', 'esov', 'Executive Suite Ocean View', 'P.403', '2026-11-08', '2026-11-12', 4, 'pkg_all_inclusive', 'Gói Nghỉ Dưỡng VIP Trọn Gói', 21800000, 6540000, 15260000, 'checked_in'),
('bk_03', 'MT-9912', 'Đỗ Mai Hương', '0987112233', 'huong.do@yahoo.com', 'ddcv', 'Deluxe Double City View', 'P.203', '2026-11-09', '2026-11-11', 2, 'pkg_room_only', 'Gói Phòng Tiêu Chuẩn', 4200000, 1260000, 2940000, 'confirmed');

-- 5. BẢNG DỊCH VỤ VÀ GỌI MÓN PHÒNG QR
DROP TABLE IF EXISTS `service_orders`;
CREATE TABLE `service_orders` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `booking_code` VARCHAR(30) DEFAULT NULL,
    `room_number` VARCHAR(20) NOT NULL,
    `guest_name` VARCHAR(100) NOT NULL,
    `service_name` VARCHAR(150) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` BIGINT NOT NULL,
    `total_price` BIGINT NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'confirmed',
    `scheduled_time` VARCHAR(20) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service_orders` (`id`, `booking_code`, `room_number`, `guest_name`, `service_name`, `quantity`, `unit_price`, `total_price`, `status`, `scheduled_time`, `note`) VALUES
('so_01', 'MT-8492', 'P.303', 'Lê Minh Khách', 'Phở Bò Thăn Phú Quốc Đặc Biệt', 2, 195000, 390000, 'completed', '07:30', 'Ăn tại phòng, nhiều rau thơm'),
('so_02', 'MT-5120', 'P.403', 'Trần Ngọc Bảo Trâm', 'Pearl Spa — Massage Toàn Thân Tinh Dầu 60 Phút', 2, 850000, 1700000, 'confirmed', '16:00', 'Phòng đôi cạnh nhau');

SET FOREIGN_KEY_CHECKS = 1;
