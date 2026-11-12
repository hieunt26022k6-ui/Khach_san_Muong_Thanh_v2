<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * database/seed_data.php
 */

return [
    'users' => [
        [
            'id' => 'usr_cust_01',
            'username' => 'khachhang',
            'password' => '123',
            'fullName' => 'Lê Minh Khách',
            'email' => 'khach@gmail.com',
            'phone' => '0912345678',
            'idCard' => '079095001288',
            'role' => 'customer',
            'isLocked' => false,
            'createdAt' => '2026-09-10 08:30:00'
        ],
        [
            'id' => 'usr_rec_01',
            'username' => 'letan',
            'password' => '123',
            'fullName' => 'Nguyễn Thu Thảo',
            'email' => 'thuthao.frontdesk@muongthanh.vn',
            'phone' => '0908765432',
            'idCard' => '001192008899',
            'role' => 'receptionist',
            'isLocked' => false,
            'createdAt' => '2026-08-01 07:00:00'
        ],
        [
            'id' => 'usr_adm_01',
            'username' => 'admin',
            'password' => '123',
            'fullName' => 'Hoàng Anh Tuấn (Tổng Quản Lý)',
            'email' => 'gm.phuquoc@muongthanh.vn',
            'phone' => '0988112233',
            'idCard' => '001085000123',
            'role' => 'admin',
            'isLocked' => false,
            'createdAt' => '2026-01-01 00:00:00'
        ],
        [
            'id' => 'usr_cust_02',
            'username' => 'khachhang2',
            'password' => '123',
            'fullName' => 'Trần Ngọc Bảo Trâm',
            'email' => 'baotram.tran@gmail.com',
            'phone' => '0912998877',
            'idCard' => '001198004512',
            'role' => 'customer',
            'isLocked' => false,
            'createdAt' => '2026-09-15 10:15:00'
        ]
    ],

    'roomCategories' => [
        [
            'id' => 'dtcv',
            'code' => 'DTCV',
            'name' => 'Deluxe Twin City View',
            'vietnameseName' => 'Deluxe 2 Giường Đơn — Hướng Phố',
            'viewType' => 'CITY',
            'bedType' => '2 giường đơn (1.2m x 2.0m)',
            'areaSqm' => 37,
            'basePrice' => 1950000,
            'maxAdults' => 2,
            'maxChildren' => 1,
            'heroImage' => 'assets/images/room_dtcv_main_1793420634905.jpg',
            'description' => 'Không gian trang nhã với nội thất gỗ cao cấp, 2 giường đơn êm ái, ban công nhìn toàn cảnh phố biển Phú Quốc sôi động.',
            'features' => ['Ban công riêng', 'Bồn tắm nằm', 'Smart TV 55 inch', 'Wifi tốc độ cao', 'Minibar miễn phí']
        ],
        [
            'id' => 'ddcv',
            'code' => 'DDCV',
            'name' => 'Deluxe Double City View',
            'vietnameseName' => 'Deluxe Giường Đôi — Hướng Phố',
            'viewType' => 'CITY',
            'bedType' => '1 giường King (2.0m x 2.0m)',
            'areaSqm' => 37,
            'basePrice' => 2100000,
            'maxAdults' => 2,
            'maxChildren' => 1,
            'heroImage' => 'assets/images/room_dtcv_main_1793420634905.jpg',
            'description' => 'Lựa chọn lý tưởng cho các cặp đôi với giường King size êm ái, ánh sáng ấm cúng và tầm nhìn thành phố biển lãng mạn về đêm.',
            'features' => ['Giường King Size', 'Bồn tắm & vòi sen đứng', 'Smart TV', 'Máy pha trà cà phê', 'Két an toàn']
        ],
        [
            'id' => 'dkov',
            'code' => 'DKOV',
            'name' => 'Deluxe King Ocean View',
            'vietnameseName' => 'Deluxe Giường Đôi — Hướng Trực Diện Biển',
            'viewType' => 'OCEAN',
            'bedType' => '1 giường King siêu lớn (2.2m x 2.0m)',
            'areaSqm' => 42,
            'basePrice' => 2850000,
            'maxAdults' => 2,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Hạng phòng biểu tượng đón trọn khoảnh khắc hoàng hôn Bãi Trường lộng lẫy từ ban công kính rộng mở sát biển.',
            'features' => ['Ban công view biển 180°', 'Bồn tắm hướng biển', 'Loa Bluetooth Marshall', 'Nespresso', 'Bàn trà ngắm hoàng hôn']
        ],
        [
            'id' => 'dtov',
            'code' => 'DTOV',
            'name' => 'Deluxe Twin Ocean View',
            'vietnameseName' => 'Deluxe 2 Giường Đơn — Hướng Biển',
            'viewType' => 'OCEAN',
            'bedType' => '2 giường đơn lớn (1.4m x 2.0m)',
            'areaSqm' => 42,
            'basePrice' => 2850000,
            'maxAdults' => 2,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Hoàn hảo cho gia đình hoặc đồng nghiệp du lịch biển, 2 giường rộng rãi đón làn gió biển trong lành mỗi sớm mai.',
            'features' => ['Ban công kính ngắm biển', 'Bồn tắm nằm cao cấp', 'Bàn làm việc doanh nhân', 'Trà chiều chào đón']
        ],
        [
            'id' => 'esov',
            'code' => 'ESOV',
            'name' => 'Executive Suite Ocean View',
            'vietnameseName' => 'Executive Suite — Phòng Khách Riêng Hướng Biển',
            'viewType' => 'OCEAN',
            'bedType' => '1 giường Super King (2.2m x 2.2m)',
            'areaSqm' => 68,
            'basePrice' => 4500000,
            'maxAdults' => 2,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Suite cao cấp với phòng khách riêng biệt và phòng ngủ view biển panorama tuyệt mỹ.',
            'features' => ['Phòng khách riêng biệt', 'Sofa da cao cấp', 'Bồn tắm sục Jacuzzi', 'Đặc quyền Executive Lounge']
        ],
        [
            'id' => 'jrsb',
            'code' => 'JRSB',
            'name' => 'Junior Suite Beachfront',
            'vietnameseName' => 'Junior Suite — Sát Biển Ban Công Vườn',
            'viewType' => 'GARDEN_OCEAN',
            'bedType' => '1 giường King',
            'areaSqm' => 58,
            'basePrice' => 3800000,
            'maxAdults' => 2,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dtcv_main_1793420634905.jpg',
            'description' => 'Chỉ vài bước chân ra tới bãi cát trắng mịn, kết hợp hài hòa giữa khu vườn nhiệt đới xanh mát và tiếng sóng biển rì rào.',
            'features' => ['Lối đi thẳng ra biển', 'Ghế nằm tắm nắng riêng', 'Bồn tắm đá cẩm thạch', 'Rượu vang chào mừng']
        ],
        [
            'id' => 'psov',
            'code' => 'PSOV',
            'name' => 'Premier Suite Oceanfront',
            'vietnameseName' => 'Premier Suite — Góc Panorama Mặt Biển',
            'viewType' => 'OCEAN',
            'bedType' => '1 giường Super King',
            'areaSqm' => 85,
            'basePrice' => 6200000,
            'maxAdults' => 3,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Vị trí góc đỉnh cao với kính cong tràn viền mở tầm nhìn 270 độ ra toàn bộ vịnh Thái Lan.',
            'features' => ['Tầm nhìn 270° biển', 'Bồn sục Jacuzzi hướng biển', 'Bàn ăn 6 khách', 'Quản gia riêng phục vụ']
        ],
        [
            'id' => 'exlo',
            'code' => 'EXLO',
            'name' => 'Executive Lounge Suite',
            'vietnameseName' => 'Executive Lounge Suite — Tầng Cao VIP',
            'viewType' => 'OCEAN',
            'bedType' => '1 giường King',
            'areaSqm' => 72,
            'basePrice' => 5400000,
            'maxAdults' => 2,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Nằm tại tầng 8 cao nhất khách sạn với đặc quyền sử dụng Executive Sky Lounge, cocktail hoàng hôn miễn phí mỗi ngày.',
            'features' => ['Toàn quyền Executive Lounge', 'Cocktail & Canapé miễn phí', 'Xe đưa đón sân bay riêng', 'Check-in tại phòng']
        ],
        [
            'id' => 'pres',
            'code' => 'PRES',
            'name' => 'Presidential Suite Grand',
            'vietnameseName' => 'Phòng Tổng Thống — Siêu Căn Hộ Nghỉ Dưỡng',
            'viewType' => 'OCEAN',
            'bedType' => '2 phòng ngủ Master King',
            'areaSqm' => 185,
            'basePrice' => 18000000,
            'maxAdults' => 4,
            'maxChildren' => 2,
            'heroImage' => 'assets/images/room_dkov_suite_1793420636995.jpg',
            'description' => 'Biểu tượng xa hoa đỉnh cao bậc nhất đảo ngọc: 2 phòng ngủ master, phòng họp thượng đỉnh, đàn piano cơ và phòng tắm dát vàng.',
            'features' => ['Diện tích 185m²', '2 Phòng ngủ Master', 'Bếp riêng & Quản gia 24/7', 'Phòng xông hơi riêng', 'Xe Limousine đón tiễn']
        ]
    ],

    'packages' => [
        [
            'id' => 'pkg_room_only',
            'name' => 'Gói Phòng Tiêu Chuẩn',
            'description' => 'Giá tốt nhất, miễn phí trà cà phê và hồ bơi vô cực',
            'priceOffset' => 0,
            'features' => ['Nước suối và trà đón khách', 'Sử dụng hồ bơi & gym miễn phí', 'Wifi tốc độ cao']
        ],
        [
            'id' => 'pkg_breakfast',
            'name' => 'Gói Kèm Buffet Sáng',
            'description' => 'Bao gồm buffet sáng quốc tế hơn 60 món tại nhà hàng Sonasea',
            'priceOffset' => 350000,
            'features' => ['Buffet sáng 5 sao cho 2 khách', 'Ưu tiên vị trí bàn nhìn ra biển', 'Trái cây tươi đón phòng']
        ],
        [
            'id' => 'pkg_all_inclusive',
            'name' => 'Gói Nghỉ Dưỡng VIP Trọn Gói',
            'description' => 'Buffet sáng + 60 phút Pearl Spa + Xe đón tiễn sân bay',
            'priceOffset' => 950000,
            'features' => ['Buffet sáng 2 khách', 'Liệu trình Pearl Spa 60 phút', 'Đưa đón sân bay 2 chiều', 'Giảm 20% đồ uống Sky Bar']
        ]
    ],

    'physicalRooms' => [
        // Tầng 2
        ['id' => 'p_201', 'roomNumber' => 'P.201', 'floor' => 2, 'categoryId' => 'dtcv', 'status' => 'available'],
        ['id' => 'p_202', 'roomNumber' => 'P.202', 'floor' => 2, 'categoryId' => 'dtcv', 'status' => 'occupied', 'currentGuestName' => 'Nguyễn Văn An'],
        ['id' => 'p_203', 'roomNumber' => 'P.203', 'floor' => 2, 'categoryId' => 'ddcv', 'status' => 'reserved', 'currentGuestName' => 'Đỗ Mai Hương'],
        ['id' => 'p_204', 'roomNumber' => 'P.204', 'floor' => 2, 'categoryId' => 'jrsb', 'status' => 'cleaning'],

        // Tầng 3
        ['id' => 'p_301', 'roomNumber' => 'P.301', 'floor' => 3, 'categoryId' => 'dtcv', 'status' => 'available'],
        ['id' => 'p_302', 'roomNumber' => 'P.302', 'floor' => 3, 'categoryId' => 'ddcv', 'status' => 'available'],
        ['id' => 'p_303', 'roomNumber' => 'P.303', 'floor' => 3, 'categoryId' => 'dkov', 'status' => 'occupied', 'currentGuestName' => 'Lê Minh Khách'],
        ['id' => 'p_304', 'roomNumber' => 'P.304', 'floor' => 3, 'categoryId' => 'dtov', 'status' => 'available'],

        // Tầng 4
        ['id' => 'p_401', 'roomNumber' => 'P.401', 'floor' => 4, 'categoryId' => 'dkov', 'status' => 'available'],
        ['id' => 'p_402', 'roomNumber' => 'P.402', 'floor' => 4, 'categoryId' => 'dtov', 'status' => 'maintenance'],
        ['id' => 'p_403', 'roomNumber' => 'P.403', 'floor' => 4, 'categoryId' => 'esov', 'status' => 'occupied', 'currentGuestName' => 'Trần Ngọc Bảo Trâm'],
        ['id' => 'p_404', 'roomNumber' => 'P.404', 'floor' => 4, 'categoryId' => 'jrsb', 'status' => 'available'],

        // Tầng 5
        ['id' => 'p_501', 'roomNumber' => 'P.501', 'floor' => 5, 'categoryId' => 'dkov', 'status' => 'available'],
        ['id' => 'p_502', 'roomNumber' => 'P.502', 'floor' => 5, 'categoryId' => 'dkov', 'status' => 'occupied', 'currentGuestName' => 'Phạm Quốc Hùng'],
        ['id' => 'p_503', 'roomNumber' => 'P.503', 'floor' => 5, 'categoryId' => 'esov', 'status' => 'available'],
        ['id' => 'p_504', 'roomNumber' => 'P.504', 'floor' => 5, 'categoryId' => 'psov', 'status' => 'reserved', 'currentGuestName' => 'Vũ Thị Lan'],

        // Tầng 6
        ['id' => 'p_601', 'roomNumber' => 'P.601', 'floor' => 6, 'categoryId' => 'dkov', 'status' => 'available'],
        ['id' => 'p_602', 'roomNumber' => 'P.602', 'floor' => 6, 'categoryId' => 'psov', 'status' => 'available'],
        ['id' => 'p_603', 'roomNumber' => 'P.603', 'floor' => 6, 'categoryId' => 'exlo', 'status' => 'occupied', 'currentGuestName' => 'David Miller'],
        ['id' => 'p_604', 'roomNumber' => 'P.604', 'floor' => 6, 'categoryId' => 'exlo', 'status' => 'available'],

        // Tầng 7
        ['id' => 'p_701', 'roomNumber' => 'P.701', 'floor' => 7, 'categoryId' => 'esov', 'status' => 'available'],
        ['id' => 'p_702', 'roomNumber' => 'P.702', 'floor' => 7, 'categoryId' => 'psov', 'status' => 'available'],

        // Tầng 8 (VIP & Tổng Thống)
        ['id' => 'p_801', 'roomNumber' => 'P.801', 'floor' => 8, 'categoryId' => 'exlo', 'status' => 'available'],
        ['id' => 'p_804', 'roomNumber' => 'P.804', 'floor' => 8, 'categoryId' => 'pres', 'status' => 'reserved', 'currentGuestName' => 'Đoàn Khách VIP Vingroup']
    ],

    'services' => [
        ['id' => 'srv_01', 'name' => 'Phở Bò Thăn Phú Quốc Đặc Biệt', 'category' => 'dining', 'price' => 195000, 'unit' => 'Tô'],
        ['id' => 'srv_02', 'name' => 'Ghẹ Hàm Ninh Hấp Bia & Tiêu Sọ', 'category' => 'dining', 'price' => 680000, 'unit' => 'Phần 500g'],
        ['id' => 'srv_03', 'name' => 'Bữa Tối Lãng Mạn Bên Bờ Biển (Set 2 Khách)', 'category' => 'dining', 'price' => 2200000, 'unit' => 'Set'],
        ['id' => 'srv_04', 'name' => 'Pearl Spa — Massage Toàn Thân Tinh Dầu 60 Phút', 'category' => 'spa', 'price' => 850000, 'unit' => 'Khách'],
        ['id' => 'srv_05', 'name' => 'Tắm Khoáng Thảo Dược & Xông Hơi Đá Muối', 'category' => 'spa', 'price' => 450000, 'unit' => 'Khách'],
        ['id' => 'srv_06', 'name' => 'Đưa Đón Sân Bay Phú Quốc (Xe Dcar Limousine)', 'category' => 'transport', 'price' => 400000, 'unit' => 'Chuyến'],
        ['id' => 'srv_07', 'name' => 'Giặt Ủi Hỏa Tốc (2 Giờ Giao Phòng)', 'category' => 'laundry', 'price' => 150000, 'unit' => 'Lượt']
    ],

    'bookings' => [
        [
            'id' => 'bk_01',
            'bookingCode' => 'MT-8492',
            'customerName' => 'Lê Minh Khách',
            'customerPhone' => '0912345678',
            'customerEmail' => 'khach@gmail.com',
            'categoryId' => 'dkov',
            'categoryName' => 'Deluxe King Ocean View',
            'assignedRoomNumber' => 'P.303',
            'checkInDate' => '2026-11-08',
            'checkOutDate' => '2026-11-11',
            'nights' => 3,
            'packageId' => 'pkg_breakfast',
            'packageName' => 'Gói Kèm Buffet Sáng',
            'totalAmount' => 9600000,
            'depositPaid' => 2880000,
            'remainingBalance' => 6720000,
            'status' => 'checked_in',
            'createdAt' => '2026-11-05 14:20:00'
        ],
        [
            'id' => 'bk_02',
            'bookingCode' => 'MT-5120',
            'customerName' => 'Trần Ngọc Bảo Trâm',
            'customerPhone' => '0912998877',
            'customerEmail' => 'baotram.tran@gmail.com',
            'categoryId' => 'esov',
            'categoryName' => 'Executive Suite Ocean View',
            'assignedRoomNumber' => 'P.403',
            'checkInDate' => '2026-11-08',
            'checkOutDate' => '2026-11-12',
            'nights' => 4,
            'packageId' => 'pkg_all_inclusive',
            'packageName' => 'Gói Nghỉ Dưỡng VIP Trọn Gói',
            'totalAmount' => 21800000,
            'depositPaid' => 6540000,
            'remainingBalance' => 15260000,
            'status' => 'checked_in',
            'createdAt' => '2026-11-04 09:15:00'
        ],
        [
            'id' => 'bk_03',
            'bookingCode' => 'MT-9912',
            'customerName' => 'Đỗ Mai Hương',
            'customerPhone' => '0987112233',
            'customerEmail' => 'huong.do@yahoo.com',
            'categoryId' => 'ddcv',
            'categoryName' => 'Deluxe Double City View',
            'assignedRoomNumber' => 'P.203',
            'checkInDate' => '2026-11-09',
            'checkOutDate' => '2026-11-11',
            'nights' => 2,
            'packageId' => 'pkg_room_only',
            'packageName' => 'Gói Phòng Tiêu Chuẩn',
            'totalAmount' => 4200000,
            'depositPaid' => 1260000,
            'remainingBalance' => 2940000,
            'status' => 'confirmed',
            'createdAt' => '2026-11-06 18:40:00'
        ]
    ],

    'serviceOrders' => [
        [
            'id' => 'so_01',
            'bookingCode' => 'MT-8492',
            'roomNumber' => 'P.303',
            'guestName' => 'Lê Minh Khách',
            'serviceName' => 'Phở Bò Thăn Phú Quốc Đặc Biệt',
            'quantity' => 2,
            'unitPrice' => 195000,
            'totalPrice' => 390000,
            'status' => 'completed',
            'scheduledTime' => '07:30',
            'note' => 'Ăn tại phòng, nhiều rau thơm',
            'createdAt' => '2026-11-08 07:10:00'
        ],
        [
            'id' => 'so_02',
            'bookingCode' => 'MT-5120',
            'roomNumber' => 'P.403',
            'guestName' => 'Trần Ngọc Bảo Trâm',
            'serviceName' => 'Pearl Spa — Massage Toàn Thân Tinh Dầu 60 Phút',
            'quantity' => 2,
            'unitPrice' => 850000,
            'totalPrice' => 1700000,
            'status' => 'confirmed',
            'scheduledTime' => '16:00',
            'note' => 'Phòng đôi cạnh nhau',
            'createdAt' => '2026-11-08 10:00:00'
        ]
    ],

    'auditLogs' => [
        ['id' => 'log_01', 'action' => 'CHECK_IN', 'description' => 'Khách Lê Minh Khách nhận phòng P.303 thành công', 'operator' => 'letan', 'timestamp' => '2026-11-08 14:05:00'],
        ['id' => 'log_02', 'action' => 'CHECK_IN', 'description' => 'Khách Trần Ngọc Bảo Trâm nhận phòng P.403 thành công', 'operator' => 'letan', 'timestamp' => '2026-11-08 14:30:00'],
        ['id' => 'log_03', 'action' => 'SERVICE_ORDER', 'description' => 'Tạo đơn dịch vụ Phở Bò Thăn x2 cho phòng P.303', 'operator' => 'khachhang', 'timestamp' => '2026-11-08 07:10:00']
    ],

    'incidents' => [
        [
            'id' => 'inc_01',
            'bookingCode' => 'MT100002',
            'roomNumber' => '301',
            'guestName' => 'Lê Minh Khách',
            'title' => 'Điều khiển điều hòa phòng 301 yếu pin & cần thêm 02 khăn tắm lớn',
            'description' => 'Khách liên hệ quầy Lễ tân báo màn hình remote điều hòa hơi mờ và nhờ bộ phận Buồng phòng bổ sung thêm 2 khăn tắm hồ bơi.',
            'severity' => 'low',
            'status' => 'in_progress',
            'resolutionNote' => 'Lễ tân đã chuyển yêu cầu cho kỹ thuật ca trực thay pin remote và tổ Buồng phòng mang khăn lên tầng 3.',
            'reportedAt' => '2026-10-30 17:45',
        ],
        [
            'id' => 'inc_02',
            'bookingCode' => 'MT100001',
            'roomNumber' => '202',
            'guestName' => 'Lê Minh Khách',
            'title' => 'Yêu cầu kiểm tra két sắt điện tử phòng 202 báo khóa mã tạm thời',
            'description' => 'Khách nhập sai mã 3 lần khiến két sắt tự khóa bảo mật 15 phút, cần nhân viên kỹ thuật mở khóa bằng chìa Master.',
            'severity' => 'medium',
            'status' => 'resolved',
            'resolutionNote' => 'Trưởng ca Lễ tân cùng Kỹ thuật đã lên xác minh CCCD và mở khóa két sắt thành công trong 5 phút.',
            'reportedAt' => '2026-10-25 20:15',
        ]
    ],

    'reviews' => [
        [
            'id' => 'rev_01',
            'bookingId' => 'bk_01',
            'bookingCode' => 'MT100001',
            'customerId' => 'usr_cust_01',
            'guestName' => 'Lê Minh Khách',
            'categoryId' => 'dkov',
            'rating' => 5,
            'title' => 'Phòng sạch đẹp, nhân viên thân thiện, bữa sáng ngon.',
            'comment' => 'Phòng sạch đẹp, nhân viên thân thiện, bữa sáng ngon.',
            'createdAt' => '2026-10-27 10:30',
            'replyFromHotel' => 'Trân trọng cảm ơn quý khách Lê Minh Khách đã tin chọn Mường Thanh Luxury Phú Quốc!',
        ]
    ],

    'policy' => [
        'standardCheckInTime' => '14:00',
        'standardCheckOutTime' => '12:00',
        'earlyCheckInFeePercent' => 30,
        'lateCheckOutFeePercent' => 50,
        'freeCancelBeforeHours' => 336,
        'lateCancelPenaltyPercent' => 50,
        'depositRequiredPercent' => 30,
        'vatPercent' => 8,
        'serviceChargePercent' => 5.4,
        'childPolicyNote' => 'Trẻ em dưới 06 tuổi ngủ chung giường với bố mẹ được miễn phí bữa sáng. Trẻ từ 06–11 tuổi phụ thu ăn sáng theo quy định.',
        'paymentPolicyNote' => 'Bắt buộc đặt cọc 30% trước khi nhận phòng. Khi trả phòng phải tất toán 100% hóa đơn còn lại trước khi Lễ tân xác nhận hoàn tất Check-out.',
    ],

    'templates' => [
        [
            'id' => 'tpl_booking_confirm',
            'code' => 'BOOKING_CONFIRMED',
            'name' => 'Xác nhận đặt phòng & Nhắc lịch nhận phòng',
            'subject' => '[Mường Thanh Luxury Phú Quốc] Xác nhận đặt phòng #{BOOKING_CODE}',
            'bodyTemplate' => 'Kính chào quý khách {GUEST_NAME}, Mường Thanh Luxury Phú Quốc xác nhận đơn đặt phòng #{BOOKING_CODE} hạng phòng {ROOM_NAME} từ {CHECK_IN} đến {CHECK_OUT}. Tổng chi phí dự kiến: {TOTAL_VND}. Hân hạnh được đón tiếp Quý khách!',
            'channel' => 'email',
            'updatedAt' => '2026-10-01 09:00',
        ],
        [
            'id' => 'tpl_service_update',
            'code' => 'SERVICE_STATUS_UPDATE',
            'name' => 'Thông báo & Nhắc lịch phục vụ dịch vụ bổ sung',
            'subject' => '[Mường Thanh Concierge] Cập nhật yêu cầu dịch vụ #{ORDER_CODE}',
            'bodyTemplate' => 'Xin chào quý khách {GUEST_NAME} (Phòng {ROOM_NUMBER}), yêu cầu dịch vụ "{SERVICE_NAME}" (#{ORDER_CODE}) khung giờ {SCHEDULED_TIME} đã chuyển sang trạng thái: {STATUS}.',
            'channel' => 'in_app',
            'updatedAt' => '2026-10-05 14:00',
        ]
    ],

    'sentNotifications' => [
        [
            'id' => 'noti_01',
            'recipientEmailOrPhone' => 'khach@gmail.com',
            'recipientCustomerId' => 'usr_cust_01',
            'targetRoles' => ['customer', 'receptionist', 'admin'],
            'priority' => 'important',
            'relatedBookingCode' => 'MT100002',
            'templateCode' => 'CHECKIN_REMINDER',
            'subject' => '[Nhắc việc Nghiệp vụ] Khách đang lưu trú P.301 (#MT100002) — Nhắc lịch trả phòng & thu công nợ',
            'message' => 'Khách Lê Minh Khách (P.301 - Phòng Premier Hướng Biển) đã đặt cọc 2.750.000 đ, hiện có 02 dịch vụ phát sinh (1.170.000 đ). Số tiền còn phải thu khi trả phòng ngày 01/11/2026: 7.762.180 đ.',
            'sentAt' => '2026-10-31 08:00',
            'isRead' => false,
        ]
    ]
];
