-- =====================================================================
-- MƯỜNG THANH LUXURY PHÚ QUỐC — CƠ SỞ DỮ LIỆU QUAN HỆ (MySQL 5.7+ / MariaDB 10.4+)
-- Đồ án cuối kỳ môn Lập trình web với PHP (TMU x DEHA)
-- Chạy: phpMyAdmin > Import, hoặc dòng lệnh:
--   mysql -u root < database/muongthanh_hotel.sql
--
-- GHI CHÚ THIẾT KẾ
--   * 1–N : room_categories → physical_rooms ; users → bookings ; bookings → service_orders
--   * N–N : bookings ↔ services  (bảng trung gian booking_services)
--   * 1–1 : users ↔ user_profiles ; khách sạn ↔ hotel_policy (bảng cấu hình một dòng)
--   * Mỗi bảng có thêm cột payload JSON để giữ các thuộc tính mở rộng của giao diện
--     (view không phải sửa), còn các cột có kiểu dữ liệu thật là nguồn dữ liệu chính
--     cho truy vấn, khóa ngoại và báo cáo.
-- =====================================================================

DROP DATABASE IF EXISTS `muongthanh_hotel`;
CREATE DATABASE `muongthanh_hotel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `muongthanh_hotel`;

-- ---------------------------------------------------------------------
-- 1. NGƯỜI DÙNG (khách hàng / lễ tân / quản trị viên)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`          VARCHAR(20)  NOT NULL,
    `username`    VARCHAR(50)  NOT NULL,
    `password`    VARCHAR(255) NOT NULL COMMENT 'password_hash() — không lưu mật khẩu thô',
    `full_name`   VARCHAR(100) NOT NULL,
    `email`       VARCHAR(100) DEFAULT NULL,
    `phone`       VARCHAR(20)  DEFAULT NULL,
    `role`        ENUM('customer','receptionist','admin') NOT NULL DEFAULT 'customer',
    `is_locked`   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Khóa tài khoản thay vì xóa cứng',
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`     JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tài khoản 3 vai trò, phân quyền ở máy chủ';

-- 1–1 với users: mỗi tài khoản có đúng một hồ sơ chi tiết
DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
    `user_id`       VARCHAR(20)  NOT NULL,
    `id_card`       VARCHAR(20)  DEFAULT NULL,
    `address`       VARCHAR(255) DEFAULT NULL,
    `nationality`   VARCHAR(50)  DEFAULT 'Việt Nam',
    `date_of_birth` DATE         DEFAULT NULL,
    `payload`       JSON         DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Quan hệ 1–1 với users (user_id vừa là PK vừa là FK)';

-- ---------------------------------------------------------------------
-- 2. HẠNG PHÒNG VÀ PHÒNG VẬT LÝ (1–N)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `room_categories`;
CREATE TABLE `room_categories` (
    `id`             VARCHAR(20)  NOT NULL,
    `code`           VARCHAR(10)  NOT NULL,
    `name`           VARCHAR(150) NOT NULL,
    `vietnamese_name` VARCHAR(150) DEFAULT NULL,
    `view_type`      VARCHAR(50)  DEFAULT NULL,
    `bed_type`       VARCHAR(50)  DEFAULT NULL,
    `area_sqm`       INT          DEFAULT NULL,
    `base_price`     DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Giá niêm yết / đêm (VNĐ)',
    `max_adults`     TINYINT      NOT NULL DEFAULT 2,
    `max_children`   TINYINT      NOT NULL DEFAULT 0,
    `hero_image`     VARCHAR(255) DEFAULT NULL,
    `image_path`     VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh do admin upload (thư mục assets/uploads)',
    `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
    `description`    TEXT         DEFAULT NULL,
    `payload`        JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_code` (`code`),
    KEY `idx_categories_price` (`base_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hạng phòng + bảng giá do admin thiết lập (AD-12)';

DROP TABLE IF EXISTS `physical_rooms`;
CREATE TABLE `physical_rooms` (
    `id`                  VARCHAR(20) NOT NULL,
    `room_number`         VARCHAR(10) NOT NULL,
    `floor`               INT         DEFAULT NULL,
    `category_id`         VARCHAR(20) NOT NULL,
    `status`              ENUM('available','occupied','cleaning','maintenance') NOT NULL DEFAULT 'available',
    `maintenance_reason`  VARCHAR(255) DEFAULT NULL COMMENT 'Lý do khóa phòng bảo trì (AD-12)',
    `maintenance_until`   DATE         DEFAULT NULL,
    `payload`             JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_rooms_number` (`room_number`),
    KEY `idx_rooms_category` (`category_id`),
    CONSTRAINT `fk_rooms_category` FOREIGN KEY (`category_id`)
        REFERENCES `room_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Quan hệ 1–N với room_categories';

-- ---------------------------------------------------------------------
-- 3. GÓI PHÒNG VÀ DỊCH VỤ
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
    `id`           VARCHAR(20)   NOT NULL,
    `code`         VARCHAR(20)   DEFAULT NULL,
    `name`         VARCHAR(150)  NOT NULL,
    `price_offset` DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Phụ thu / đêm so với giá hạng phòng',
    `is_active`    TINYINT(1)    NOT NULL DEFAULT 1,
    `description`  TEXT          DEFAULT NULL,
    `payload`      JSON          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_packages_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Gói phòng (ăn sáng, đưa đón...) admin bật/tắt (AD-12)';

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
    `id`         VARCHAR(20)   NOT NULL,
    `code`       VARCHAR(20)   DEFAULT NULL,
    `name`       VARCHAR(150)  NOT NULL,
    `category`   VARCHAR(50)   DEFAULT NULL,
    `price`      DECIMAL(12,0) NOT NULL DEFAULT 0,
    `unit`       VARCHAR(20)   DEFAULT 'lần',
    `is_active`  TINYINT(1)    NOT NULL DEFAULT 1,
    `payload`    JSON          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_services_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Dịch vụ bổ sung: ăn uống, spa, giặt ủi...';

-- ---------------------------------------------------------------------
-- 4. ĐƠN ĐẶT PHÒNG (1–N với users/room_categories/packages)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `id`                    VARCHAR(30)  NOT NULL,
    `booking_code`          VARCHAR(20)  NOT NULL,
    `user_id`               VARCHAR(20)  DEFAULT NULL COMMENT 'NULL khi khách đặt không cần tài khoản',
    `customer_name`         VARCHAR(100) NOT NULL,
    `customer_phone`        VARCHAR(20)  DEFAULT NULL,
    `customer_email`        VARCHAR(100) DEFAULT NULL,
    `category_id`           VARCHAR(20)  DEFAULT NULL,
    `package_id`            VARCHAR(20)  DEFAULT NULL,
    `assigned_room_number`  VARCHAR(10)  DEFAULT NULL,
    `check_in_date`         DATE         NOT NULL,
    `check_out_date`        DATE         NOT NULL,
    `nights`                INT          NOT NULL DEFAULT 1,
    `total_amount`          DECIMAL(14,0) NOT NULL DEFAULT 0,
    `deposit_paid`          DECIMAL(14,0) NOT NULL DEFAULT 0,
    `remaining_balance`     DECIMAL(14,0) NOT NULL DEFAULT 0,
    `status`                ENUM('pending','confirmed','checked_in','completed','cancelled')
                                         NOT NULL DEFAULT 'pending',
    `note`                  VARCHAR(255) DEFAULT NULL,
    `created_at`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`               JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bookings_code` (`booking_code`),
    KEY `idx_bookings_user` (`user_id`),
    KEY `idx_bookings_status` (`status`),
    KEY `idx_bookings_checkin` (`check_in_date`),
    CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bookings_category` FOREIGN KEY (`category_id`)
        REFERENCES `room_categories`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bookings_package` FOREIGN KEY (`package_id`)
        REFERENCES `packages`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bookings_room` FOREIGN KEY (`assigned_room_number`)
        REFERENCES `physical_rooms`(`room_number`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Đơn đặt phòng — 1–N tới bốn bảng danh mục';

-- ---------------------------------------------------------------------
-- 5. DỊCH VỤ THEO ĐƠN + BẢNG TRUNG GIAN N–N
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `service_orders`;
CREATE TABLE `service_orders` (
    `id`             VARCHAR(20)   NOT NULL,
    `booking_id`     VARCHAR(30)   DEFAULT NULL,
    `booking_code`   VARCHAR(20)   DEFAULT NULL,
    `service_id`     VARCHAR(20)   DEFAULT NULL,
    `room_number`    VARCHAR(10)   DEFAULT NULL,
    `guest_name`     VARCHAR(100)  DEFAULT NULL,
    `service_name`   VARCHAR(150)  NOT NULL,
    `quantity`       INT           NOT NULL DEFAULT 1,
    `unit_price`     DECIMAL(12,0) NOT NULL DEFAULT 0,
    `total_price`    DECIMAL(14,0) NOT NULL DEFAULT 0,
    `status`         ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    `scheduled_time` VARCHAR(20)   DEFAULT NULL,
    `note`           VARCHAR(255)  DEFAULT NULL,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`        JSON          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_so_booking` (`booking_id`),
    KEY `idx_so_service` (`service_id`),
    CONSTRAINT `fk_so_booking` FOREIGN KEY (`booking_id`)
        REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_so_service` FOREIGN KEY (`service_id`)
        REFERENCES `services`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chi tiết dịch vụ của một đơn (1–N với bookings)';

-- N–N: một đơn đặt phòng dùng nhiều dịch vụ, một dịch vụ xuất hiện trong nhiều đơn
DROP TABLE IF EXISTS `booking_services`;
CREATE TABLE `booking_services` (
    `booking_id`  VARCHAR(30)   NOT NULL,
    `service_id`  VARCHAR(20)   NOT NULL,
    `quantity`    INT           NOT NULL DEFAULT 1,
    `unit_price`  DECIMAL(12,0) NOT NULL DEFAULT 0,
    PRIMARY KEY (`booking_id`, `service_id`),
    KEY `idx_bs_service` (`service_id`),
    CONSTRAINT `fk_bs_booking` FOREIGN KEY (`booking_id`)
        REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bs_service` FOREIGN KEY (`service_id`)
        REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng trung gian N–N giữa bookings và services';

-- ---------------------------------------------------------------------
-- 6. NHẬT KÝ, SỰ CỐ, ĐÁNH GIÁ
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id`          VARCHAR(30)  NOT NULL,
    `action`      VARCHAR(50)  NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `operator`    VARCHAR(100) DEFAULT NULL,
    `operator_id` VARCHAR(20)  DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`     JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_logs_action` (`action`),
    KEY `idx_logs_created` (`created_at`),
    CONSTRAINT `fk_logs_operator` FOREIGN KEY (`operator_id`)
        REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Nhật ký thao tác quản trị (AD-13)';

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE `incidents` (
    `id`             VARCHAR(20)  NOT NULL,
    `booking_code`   VARCHAR(20)  DEFAULT NULL,
    `room_number`    VARCHAR(10)  DEFAULT NULL,
    `guest_name`     VARCHAR(100) DEFAULT NULL,
    `title`          VARCHAR(150) NOT NULL,
    `description`    TEXT         DEFAULT NULL,
    `severity`       ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    `status`         ENUM('open','processing','resolved') NOT NULL DEFAULT 'open',
    `resolution_note` VARCHAR(255) DEFAULT NULL,
    `reported_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`        JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_incidents_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sự cố phòng do khách / lễ tân ghi nhận';

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id`              VARCHAR(20)  NOT NULL,
    `booking_id`      VARCHAR(30)  DEFAULT NULL,
    `booking_code`    VARCHAR(20)  DEFAULT NULL,
    `customer_id`     VARCHAR(20)  DEFAULT NULL,
    `category_id`     VARCHAR(20)  DEFAULT NULL,
    `guest_name`      VARCHAR(100) DEFAULT NULL,
    `rating`          TINYINT      NOT NULL DEFAULT 5,
    `title`           VARCHAR(150) DEFAULT NULL,
    `comment`         TEXT         DEFAULT NULL,
    `reply_from_hotel` TEXT        DEFAULT NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`         JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_reviews_category` (`category_id`),
    CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`)
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_reviews_category` FOREIGN KEY (`category_id`)
        REFERENCES `room_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Đánh giá sau lưu trú';

-- ---------------------------------------------------------------------
-- 7. MẪU THÔNG BÁO VÀ THÔNG BÁO ĐÃ GỬI
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `notification_templates`;
CREATE TABLE `notification_templates` (
    `id`            VARCHAR(20)  NOT NULL,
    `code`          VARCHAR(30)  NOT NULL,
    `name`          VARCHAR(100) DEFAULT NULL,
    `subject`       VARCHAR(200) DEFAULT NULL,
    `body_template` TEXT         DEFAULT NULL,
    `channel`       VARCHAR(20)  DEFAULT 'email',
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`       JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_templates_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Mẫu email / SMS, admin sửa được';

DROP TABLE IF EXISTS `sent_notifications`;
CREATE TABLE `sent_notifications` (
    `id`                    VARCHAR(30)  NOT NULL,
    `recipient`             VARCHAR(100) DEFAULT NULL,
    `recipient_customer_id` VARCHAR(20)  DEFAULT NULL,
    `related_booking_code`  VARCHAR(20)  DEFAULT NULL,
    `template_code`         VARCHAR(30)  DEFAULT NULL,
    `priority`              VARCHAR(20)  DEFAULT 'normal',
    `subject`               VARCHAR(200) DEFAULT NULL,
    `message`               TEXT         DEFAULT NULL,
    `is_read`               TINYINT(1)   NOT NULL DEFAULT 0,
    `sent_at`               DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `payload`               JSON         DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sent_customer` (`recipient_customer_id`),
    CONSTRAINT `fk_sent_customer` FOREIGN KEY (`recipient_customer_id`)
        REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hộp thư thông báo đã gửi cho khách';

-- ---------------------------------------------------------------------
-- 8. CHÍNH SÁCH KHÁCH SẠN (bảng cấu hình 1 dòng — quan hệ 1–1 với "khách sạn")
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hotel_policy`;
CREATE TABLE `hotel_policy` (
    `setting_key`   VARCHAR(50)  NOT NULL,
    `setting_value` VARCHAR(255) DEFAULT NULL,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cọc, phạt hủy muộn, giờ nhận/trả phòng (AD-12)';

-- Cờ đánh dấu CSDL đã được nạp dữ liệu mẫu hay chưa
DROP TABLE IF EXISTS `system_flags`;
CREATE TABLE `system_flags` (
    `flag_key`   VARCHAR(50) NOT NULL,
    `flag_value` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`flag_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 9. DỮ LIỆU MẪU TỐI THIỂU (hệ thống tự nạp bộ dữ liệu đầy đủ ở lần chạy đầu;
--    phần dưới chỉ để CSDL vừa import đã có tài khoản admin đăng nhập được)
-- ---------------------------------------------------------------------
INSERT INTO `users` (`id`,`username`,`password`,`full_name`,`email`,`phone`,`role`,`is_locked`) VALUES
('usr_adm_01','admin','123','TS. Vũ Đình Trọng (Giám Đốc Điều Hành)','admin@muongthanh.vn','0909999888','admin',0),
('usr_rec_01','letan','123','Lê Thị Mai Hương (Trưởng Ca Lễ Tân)','letan@muongthanh.vn','0987654321','receptionist',0),
('usr_cust_01','khachhang','123','Lê Minh Khách','khach@gmail.com','0912345678','customer',0);

INSERT INTO `hotel_policy` (`setting_key`,`setting_value`) VALUES
('standardCheckInTime','14:00'),('standardCheckOutTime','12:00'),
('freeCancelBeforeHours','336'),('lateCancelPenaltyPercent','50'),
('depositRequiredPercent','30'),('vatPercent','8'),('serviceChargePercent','5');

INSERT INTO `system_flags` (`flag_key`,`flag_value`) VALUES ('seeded','0');

-- ---------------------------------------------------------------------
-- 10. TRUY VẤN MINH HOẠ QUAN HỆ (dùng khi bảo vệ đồ án)
-- ---------------------------------------------------------------------
-- a) N–N: những dịch vụ đi kèm một đơn đặt phòng
-- SELECT b.booking_code, s.name, bs.quantity, bs.unit_price
--   FROM booking_services bs
--   JOIN bookings b ON b.id = bs.booking_id
--   JOIN services s ON s.id = bs.service_id
--  WHERE b.booking_code = 'MT-8492';
--
-- b) 1–N: số phòng vật lý của từng hạng phòng
-- SELECT c.name, COUNT(p.id) AS so_phong
--   FROM room_categories c LEFT JOIN physical_rooms p ON p.category_id = c.id
--  GROUP BY c.id;
--
-- c) 1–1: hồ sơ chi tiết của từng tài khoản
-- SELECT u.username, u.full_name, pr.address, pr.id_card
--   FROM users u LEFT JOIN user_profiles pr ON pr.user_id = u.id;
--
-- d) Báo cáo doanh thu 7 ngày (AD-13)
-- SELECT DATE(check_in_date) AS ngay, COUNT(*) AS so_don, SUM(total_amount) AS doanh_thu
--   FROM bookings WHERE status <> 'cancelled'
--  GROUP BY DATE(check_in_date) ORDER BY ngay DESC LIMIT 7;
