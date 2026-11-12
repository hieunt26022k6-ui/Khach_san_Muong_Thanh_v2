<?php
use Core\Session;   // [SỬA QT-E01] dòng còn thiếu gây lỗi fatal
use Config\Config;

$currentTab = $_GET['tab'] ?? 'overview';
$physicalRooms = Session::get('physicalRooms', []);

// [SỬA LẦN 2] Tên người đang đăng nhập lấy từ phiên, không hard-code "Hoàng Anh Tuấn".
$me = Session::get('currentUser', []);
$meName = $me['fullName'] ?? 'Quản trị viên';
$meRole = $me['role'] ?? 'admin';

// [SỬA LẦN 2] Trạng thái tìm kiếm / phân trang dùng chung cho 3 bảng.
$keyword = $keyword ?? '';
$pageNo = (int)($page ?? 1);
$pagerUrl = function (int $p, string $tab) use ($keyword): string {
    return 'index.php?controller=admin&action=index&tab=' . $tab
        . '&q=' . urlencode($keyword) . '&page=' . $p;
};
$renderPager = function (int $current, int $pages, int $total, string $tab) use ($pagerUrl): void {
    if ($pages <= 1) {
        echo '<div class="text-[11px] text-stone-400 pt-3">Tổng cộng ' . $total . ' bản ghi</div>';
        return;
    }
    echo '<div class="flex items-center justify-between pt-3 text-[11px] text-stone-500">';
    echo '<span>Tổng cộng <strong>' . $total . '</strong> bản ghi — trang ' . $current . '/' . $pages . '</span>';
    echo '<div class="flex items-center gap-1">';
    echo '<a href="' . htmlspecialchars($pagerUrl(max(1, $current - 1), $tab)) . '" class="px-2.5 py-1 rounded border ' . ($current <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-stone-100') . '">‹ Trước</a>';
    for ($p = 1; $p <= $pages; $p++) {
        $cls = $p === $current ? 'bg-[#0F2032] text-white' : 'hover:bg-stone-100 border';
        echo '<a href="' . htmlspecialchars($pagerUrl($p, $tab)) . '" class="px-2.5 py-1 rounded ' . $cls . '">' . $p . '</a>';
    }
    echo '<a href="' . htmlspecialchars($pagerUrl(min($pages, $current + 1), $tab)) . '" class="px-2.5 py-1 rounded border ' . ($current >= $pages ? 'pointer-events-none opacity-40' : 'hover:bg-stone-100') . '">Sau ›</a>';
    echo '</div></div>';
};
?>
<!-- GIAO DIỆN QUẢN TRỊ VIÊN ADMIN CÓ LEFT SIDEBAR CHUẨN ADMIN DASHBOARD -->
<div class="flex min-h-screen bg-[#F0EFE9] text-[#162B40]">
    <!-- 1. LEFT SIDEBAR ADMIN CHUYÊN DỤNG -->
    <aside class="w-64 bg-[#0F2032] text-white flex flex-col justify-between shrink-0 shadow-2xl border-r border-[#C5A25D]/30 min-h-screen sticky top-0">
        <div>
            <!-- Header Logo Admin -->
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border border-[#C5A25D] flex items-center justify-center bg-gradient-to-br from-[#162B40] to-[#0A1522] shadow">
                    <span class="text-[#E5C98B] font-display text-2xl font-bold">👑</span>
                </div>
                <div>
                    <div class="text-[9px] tracking-[0.22em] text-[#E5C98B] font-bold uppercase">MƯỜNG THANH ADMIN</div>
                    <div class="font-display text-lg font-bold text-white leading-tight">TỔNG QUẢN TRỊ</div>
                </div>
            </div>

            <!-- Profile Tổng Quản Lý -->
            <div class="p-4 mx-4 my-4 bg-slate-800/80 rounded-xl border border-slate-700/80 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-amber-500 text-[#0F243C] flex items-center justify-center font-bold text-sm">
                    GM
                </div>
                <div>
                    <div class="text-xs font-bold text-white"><?= htmlspecialchars($meName) ?></div>
                    <div class="text-[10px] text-amber-400 uppercase tracking-wider"><?= $meRole === 'admin' ? 'Tổng Quản Lý (GM)' : htmlspecialchars($meRole) ?></div>
                </div>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="px-3 space-y-1 text-xs font-semibold tracking-wide">
                <a href="index.php?controller=admin&action=index&tab=overview" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'overview' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>📊</span>
                    <span>Tổng quan & Doanh thu</span>
                </a>
                <a href="index.php?controller=admin&action=index&tab=room_categories" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'room_categories' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>🏷️</span>
                    <span>Hạng phòng & Bảng giá</span>
                </a>
                <a href="index.php?controller=admin&action=index&tab=users" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'users' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>👥</span>
                    <span>Quản lý người dùng (RBAC)</span>
                </a>
                <a href="index.php?controller=admin&action=index&tab=bookings" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'bookings' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>📑</span>
                    <span>Toàn bộ đơn đặt phòng</span>
                </a>
                <a href="index.php?controller=admin&action=index&tab=policy" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'policy' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>⚖️</span>
                    <span>Chính sách cọc & Hủy</span>
                </a>
                <a href="index.php?controller=admin&action=index&tab=logs" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'logs' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>📜</span>
                    <span>Nhật ký hệ thống (Audit)</span>
                </a>
            </nav>
        </div>

        <!-- Bottom Actions -->
        <div class="p-4 border-t border-slate-800 space-y-2 text-xs">
            <a href="index.php?controller=customer&action=index" class="block w-full py-2 bg-slate-800 hover:bg-slate-700 text-[#E5C98B] font-bold text-center rounded transition-all">
                👁️ Đổi sang xem Khách hàng
            </a>
            <a href="index.php?controller=auth&action=logout" class="block w-full py-2 text-stone-400 hover:text-red-400 text-center font-medium transition-all">
                Đăng xuất
            </a>
        </div>
    </aside>

    <!-- 2. MAIN CONTENT AREA -->
    <main class="flex-1 p-6 sm:p-8 space-y-6 overflow-y-auto">
        <!-- TOP TOOLBAR -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="text-[10px] tracking-[0.25em] uppercase font-bold text-[#A68238]">EXECUTIVE HOTEL MANAGEMENT DASHBOARD</div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#0F2032]">Bảng Điều Khiển Quản Trị Viên</h1>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- [SỬA LẦN 2] Ô tìm kiếm bản ghi (yêu cầu bắt buộc của đề) -->
                <form method="GET" action="index.php" class="flex items-center gap-2">
                    <input type="hidden" name="controller" value="admin" />
                    <input type="hidden" name="action" value="index" />
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($currentTab) ?>" />
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tìm tên, mã đơn, email, nhật ký…"
                           class="w-56 px-3 py-2 bg-white border border-stone-300 rounded-lg text-xs focus:border-[#C5A25D] outline-none" />
                    <button type="submit" class="px-3.5 py-2 bg-[#0F2032] hover:bg-[#162B40] text-white text-xs font-semibold rounded-lg">Tìm</button>
                    <?php if ($keyword !== ''): ?>
                        <a href="index.php?controller=admin&action=index&tab=<?= htmlspecialchars($currentTab) ?>" class="text-[11px] text-stone-500 underline">Xóa lọc</a>
                    <?php endif; ?>
                </form>
                <form method="POST" action="index.php?controller=admin&action=resetData" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục toàn bộ dữ liệu mẫu ban đầu?')">
                    <button type="submit" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-lg transition-all">Khôi Phục Dữ Liệu</button>
                </form>
            </div>
        </div>

        <!-- 4 KPI FINANCIAL CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs space-y-2">
                <div class="text-stone-500 text-[11px] font-bold uppercase tracking-wider">Tổng Doanh Thu</div>
                <div class="font-display text-3xl font-bold text-[#0F2032]"><?= Config::formatVnd($analytics['totalRevenue']) ?></div>
                <div class="text-[11px] text-emerald-600 font-medium">Bao gồm tiền phòng & dịch vụ ăn uống spa</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs space-y-2">
                <div class="text-stone-500 text-[11px] font-bold uppercase tracking-wider">Doanh Thu Tiền Cọc 30%</div>
                <div class="font-display text-3xl font-bold text-[#A68238]"><?= Config::formatVnd($analytics['depositRevenue']) ?></div>
                <div class="text-[11px] text-stone-500">Đã thu qua cổng thanh toán đảm bảo</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs space-y-2">
                <div class="text-stone-500 text-[11px] font-bold uppercase tracking-wider">Tỷ Lệ Lấp Đầy (Occupancy)</div>
                <div class="font-display text-3xl font-bold text-blue-600"><?= $analytics['occupancyRate'] ?>%</div>
                <div class="text-[11px] text-stone-500"><?= $analytics['occupiedRooms'] ?> / <?= $analytics['totalRooms'] ?> phòng đang có khách</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs space-y-2">
                <div class="text-stone-500 text-[11px] font-bold uppercase tracking-wider">Doanh Thu Dịch Vụ Gọi Phòng</div>
                <div class="font-display text-3xl font-bold text-purple-600"><?= Config::formatVnd($analytics['serviceRevenue']) ?></div>
                <div class="text-[11px] text-stone-500">Ẩm thực Sonasea & Pearl Spa</div>
            </div>
        </div>

        <!-- NỘI DUNG THEO TAB -->
        <?php if ($currentTab === 'overview'): ?>
            <!-- BIỂU ĐỒ DOANH THU 7 NGÀY GẦN NHẤT & ĐƠN PHÒNG MỚI -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h3 class="font-display text-xl font-bold text-[#0F2032] border-b border-stone-100 pb-2">
                        Doanh Thu & Tình Trạng Lấp Đầy 7 Ngày Gần Nhất
                    </h3>
                    <div class="grid grid-cols-7 gap-2 pt-4 text-center text-xs">
                        <?php
                        // [SỬA LẦN 2] Số liệu lấy từ Report::getDailyRevenue() — trước đây là mảng hard-code.
                        $weekdays = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'CN'];
                        $dailyRevenue = $dailyRevenue ?? [];
                        foreach ($dailyRevenue as $day):
                            $label = $weekdays[(int)date('N', strtotime($day['date']))] ?? $day['date'];
                        ?>
                            <div class="bg-[#FAF7F2] p-3 rounded-xl border border-[#E8DCB8] flex flex-col justify-between space-y-2">
                                <span class="font-bold text-stone-600"><?= $label ?></span>
                                <div class="h-24 bg-stone-100 rounded flex items-end justify-center p-1">
                                    <div class="w-full bg-[#C5A25D] rounded-xs" style="height: <?= max(2, (int)$day['percent']) ?>%"></div>
                                </div>
                                <span class="font-bold text-[#0F2032] text-[11px]"><?= number_format($day['revenue'] / 1000000, 1, ',', '.') ?>tr</span>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($dailyRevenue)): ?>
                            <div class="col-span-7 text-stone-400 py-6">Chưa có dữ liệu doanh thu.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h3 class="font-display text-xl font-bold text-[#0F2032] border-b border-stone-100 pb-2">
                        Chỉ Số Vận Hành Khách Sạn
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between p-2.5 bg-[#FAF7F2] rounded-lg">
                            <span class="text-stone-600">Giá phòng bình quân (ADR):</span>
                            <strong class="text-[#0F2032]"><?= Config::formatVnd($analytics['adr']) ?></strong>
                        </div>
                        <div class="flex justify-between p-2.5 bg-[#FAF7F2] rounded-lg">
                            <span class="text-stone-600">Doanh thu / phòng (RevPAR):</span>
                            <strong class="text-[#A68238]"><?= Config::formatVnd($analytics['revpar']) ?></strong>
                        </div>
                        <div class="flex justify-between p-2.5 bg-[#FAF7F2] rounded-lg">
                            <span class="text-stone-600">Tỷ lệ hủy phòng:</span>
                            <strong class="text-emerald-700">0.0%</strong>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($currentTab === 'room_categories'): ?>
            <!-- CẤU HÌNH BẢNG GIÁ 9 HẠNG PHÒNG -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Cấu Hình Bảng Giá 9 Hạng Phòng</h3>
                    <span class="text-xs text-stone-500">Cập nhật trực tiếp lên hệ thống đặt phòng khách hàng</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($categories as $cat): ?>
                        <div class="p-4 bg-[#FAF7F2] rounded-xl border border-[#E8DCB8] space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-[#0F2032]"><?= htmlspecialchars($cat['name']) ?></span>
                                <span class="text-[10px] uppercase font-bold text-[#A68238]"><?= $cat['code'] ?></span>
                            </div>

                            <form method="POST" action="index.php?controller=admin&action=updatePrice" class="flex items-center gap-2">
                                <input type="hidden" name="categoryId" value="<?= $cat['id'] ?>" />
                                <input type="number" name="basePrice" value="<?= $cat['basePrice'] ?>" step="50000" class="w-full px-2.5 py-1.5 bg-white border border-stone-300 rounded text-xs font-bold text-stone-900" />
                                <button type="submit" class="px-3 py-1.5 bg-[#0F2032] text-white text-[10px] font-bold uppercase rounded">
                                    Lưu
                                </button>
                            </form>

                            <!-- [SỬA LẦN 2] Xử lý file: tải ảnh đại diện hạng phòng -->
                            <div class="flex items-center gap-2 pt-2 border-t border-[#E8DCB8]">
                                <?php if (!empty($cat['image']) && file_exists(__DIR__ . '/../../' . $cat['image'])): ?>
                                    <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>"
                                         class="w-12 h-12 rounded-lg object-cover border border-stone-200" />
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-lg bg-stone-200 flex items-center justify-center text-stone-400 text-[10px]">No img</div>
                                <?php endif; ?>
                                <form method="POST" action="index.php?controller=admin&action=uploadCategoryImage" enctype="multipart/form-data" class="flex items-center gap-1.5">
                                    <input type="hidden" name="categoryId" value="<?= $cat['id'] ?>" />
                                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required
                                           class="w-32 text-[10px] file:mr-1 file:px-2 file:py-1 file:rounded file:border-0 file:bg-stone-200 file:text-[10px]" />
                                    <button type="submit" class="px-2 py-1 bg-[#C5A25D] text-[#0F2032] text-[10px] font-bold rounded whitespace-nowrap">Tải ảnh</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($currentTab === 'users'): ?>
            <!-- QUẢN LÝ NGƯỜI DÙNG & PHÂN QUYỀN RBAC -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Quản Lý Người Dùng & Phân Quyền (RBAC)</h3>
                    <span class="text-xs text-stone-500">Khách hàng · Lễ tân · Quản trị viên</span>
                </div>

                <!-- [SỬA LẦN 2] Tạo tài khoản nhân viên + gán vai trò (đề yêu cầu "quản lý người dùng") -->
                <details class="bg-[#FAF7F2] border border-[#E8DCB8] rounded-xl p-4">
                    <summary class="cursor-pointer text-xs font-bold uppercase tracking-wider text-[#A68238]">+ Thêm tài khoản nhân viên</summary>
                    <form method="POST" action="index.php?controller=admin&action=createUser" class="grid grid-cols-1 md:grid-cols-5 gap-3 pt-4 text-xs">
                        <div>
                            <label class="block font-semibold mb-1">Họ và tên</label>
                            <input type="text" name="fullName" required maxlength="80" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Tên đăng nhập</label>
                            <input type="text" name="username" required minlength="3" maxlength="32" pattern="[a-zA-Z0-9_.]{3,32}" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Mật khẩu (≥ 6 ký tự)</label>
                            <input type="password" name="password" required minlength="6" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Vai trò</label>
                            <div class="flex items-center gap-2">
                                <select name="role" class="w-full px-2.5 py-2 bg-white border border-stone-300 rounded">
                                    <option value="receptionist">Lễ tân</option>
                                    <option value="admin">Quản trị viên</option>
                                    <option value="customer">Khách hàng</option>
                                </select>
                                <button type="submit" class="px-3 py-2 bg-[#0F2032] text-white font-bold rounded whitespace-nowrap">Tạo</button>
                            </div>
                        </div>
                    </form>
                </details>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-[#FAF7F2] text-[#A68238] uppercase font-bold text-[10px] tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="p-3">Họ Và Tên</th>
                                <th class="p-3">Tài Khoản</th>
                                <th class="p-3">Email / SĐT</th>
                                <th class="p-3">Vai Trò</th>
                                <th class="p-3">Trạng Thái</th>
                                <th class="p-3 text-right">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-stone-50">
                                    <td class="p-3 font-bold text-stone-900"><?= htmlspecialchars($u['fullName']) ?></td>
                                    <td class="p-3 font-mono text-stone-600"><?= htmlspecialchars($u['username']) ?></td>
                                    <td class="p-3 text-stone-500"><?= htmlspecialchars($u['email'] ?? $u['phone'] ?? '—') ?></td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : ($u['role'] === 'receptionist' ? 'bg-blue-100 text-blue-800' : 'bg-stone-100 text-stone-800') ?>">
                                            <?= $u['role'] ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= !empty($u['isLocked']) ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                            <?= !empty($u['isLocked']) ? 'Đã Khóa' : 'Hoạt Động' ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <?php if ($u['role'] !== 'admin'): ?>
                                            <!-- [SỬA LẦN 2] Gán lại vai trò ngay trong bảng -->
                                            <form method="POST" action="index.php?controller=admin&action=updateRole" class="inline-flex items-center gap-1 mr-2">
                                                <input type="hidden" name="userId" value="<?= $u['id'] ?>" />
                                                <select name="role" class="px-1.5 py-1 border border-stone-300 rounded text-[11px]">
                                                    <option value="receptionist" <?= $u['role'] === 'receptionist' ? 'selected' : '' ?>>Lễ tân</option>
                                                    <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Khách</option>
                                                    <option value="admin">Quản trị</option>
                                                </select>
                                                <button type="submit" class="px-2 py-1 rounded bg-stone-700 text-white text-[11px] font-bold">Gán</button>
                                            </form>
                                            <form method="POST" action="index.php?controller=admin&action=toggleUserLock" class="inline">
                                                <input type="hidden" name="userId" value="<?= $u['id'] ?>" />
                                                <button type="submit" class="px-2.5 py-1 rounded text-[11px] font-bold <?= !empty($u['isLocked']) ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' ?>">
                                                    <?= !empty($u['isLocked']) ? 'Mở Khóa' : 'Khóa' ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-stone-400 italic text-[11px]">Cố định</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php $renderPager((int)($page ?? 1), (int)($userPages ?? 1), (int)($userTotal ?? 0), 'users'); ?>
            </div>

        <?php elseif ($currentTab === 'bookings'): ?>
            <!-- TOÀN BỘ ĐƠN ĐẶT PHÒNG -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <h3 class="font-display text-2xl font-bold text-[#0F2032] border-b border-stone-100 pb-3">
                    Toàn Bộ Hồ Sơ Đặt Phòng Toàn Hệ Thống
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-[#FAF7F2] text-[#A68238] uppercase font-bold text-[10px] tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="p-3">Mã Đơn</th>
                                <th class="p-3">Khách Hàng</th>
                                <th class="p-3">Hạng Phòng</th>
                                <th class="p-3">Phòng Gán</th>
                                <th class="p-3">Thời Gian</th>
                                <th class="p-3">Tổng Tiền</th>
                                <th class="p-3">Cọc 30%</th>
                                <th class="p-3">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-stone-50">
                                    <td class="p-3 font-bold text-[#0F2032]">#<?= htmlspecialchars($b['bookingCode']) ?></td>
                                    <td class="p-3 font-bold"><?= htmlspecialchars($b['customerName']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($b['categoryName']) ?></td>
                                    <td class="p-3 font-bold text-[#A68238]"><?= $b['assignedRoomNumber'] ?: '—' ?></td>
                                    <td class="p-3"><?= $b['checkInDate'] ?> → <?= $b['checkOutDate'] ?></td>
                                    <td class="p-3 font-bold"><?= Config::formatVnd($b['totalAmount']) ?></td>
                                    <td class="p-3 text-emerald-700 font-bold"><?= Config::formatVnd($b['depositPaid']) ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-stone-100">
                                            <?= $b['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php $renderPager((int)($page ?? 1), (int)($bookingPages ?? 1), (int)($bookingTotal ?? 0), 'bookings'); ?>
            </div>

        <?php elseif ($currentTab === 'policy'): ?>
            <!-- CẤU HÌNH CHÍNH SÁCH CỌC & HỦY PHÒNG -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4 max-w-2xl">
                <h3 class="font-display text-2xl font-bold text-[#0F2032] border-b border-stone-100 pb-2">
                    Cấu Hình Chính Sách Lưu Trú & Tiền Cọc
                </h3>
                <div class="space-y-4 text-xs">
                    <!-- [SỬA LẦN 2] Tab chính sách nay sửa được (trước đây chỉ readonly) -->
                    <form method="POST" action="index.php?controller=admin&action=updatePolicy" class="space-y-4">
                        <div>
                            <label class="block font-semibold mb-1">Tỷ lệ tiền cọc tối thiểu khi đặt phòng (%)</label>
                            <input type="number" name="depositPercent" min="0" max="100" step="1"
                                   value="<?= (int)($policy['depositRequiredPercent'] ?? Config::DEPOSIT_PERCENT) ?>"
                                   class="w-full p-2.5 bg-white border border-stone-300 rounded font-bold text-[#0F2032]" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Hủy miễn phí trước bao nhiêu giờ</label>
                            <input type="number" name="freeCancelBeforeHours" min="0" max="720" step="1"
                                   value="<?= (int)($policy['freeCancelBeforeHours'] ?? 48) ?>"
                                   class="w-full p-2.5 bg-white border border-stone-300 rounded" />
                            <p class="text-[11px] text-stone-500 mt-1">Giờ nhận phòng tiêu chuẩn: <?= htmlspecialchars((string)($policy['standardCheckInTime'] ?? '14:00')) ?></p>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Chính sách trẻ em và giường phụ</label>
                            <input type="text" name="childPolicy" maxlength="200"
                                   value="<?= htmlspecialchars((string)($policy['childPolicyNote'] ?? 'Miễn phí 01 trẻ em dưới 6 tuổi ngủ chung giường với bố mẹ')) ?>"
                                   class="w-full p-2.5 bg-white border border-stone-300 rounded" />
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-stone-100">
                            <span class="text-[11px] text-stone-500">
                                Cập nhật lần cuối: <?= htmlspecialchars((string)($policy['updatedAt'] ?? 'chưa có')) ?> ·
                                Chỉ áp dụng cho đơn đặt mới, đơn cũ giữ nguyên giá &amp; chính sách đã chốt.
                            </span>
                            <button type="submit" class="px-4 py-2 bg-[#0F2032] hover:bg-[#162B40] text-white text-xs font-bold rounded-lg">Lưu Chính Sách</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php elseif ($currentTab === 'logs'): ?>
            <!-- NHẬT KÝ HỆ THỐNG AUDIT -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-3">
                <h3 class="font-display text-2xl font-bold text-[#0F2032] border-b border-stone-100 pb-2">
                    Nhật Ký Thao Tác Hệ Thống (Audit Trail)
                </h3>
                <div class="space-y-2">
                    <?php foreach ($auditLogs as $log): ?>
                        <div class="p-2.5 bg-stone-50 rounded-lg text-xs flex items-center justify-between text-stone-700">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-stone-200 text-stone-800"><?= $log['action'] ?></span>
                                <span><?= htmlspecialchars($log['description']) ?></span>
                            </div>
                            <div class="text-[11px] text-stone-400">
                                Bởi <strong><?= htmlspecialchars($log['operator']) ?></strong> lúc <?= $log['timestamp'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php $renderPager((int)($page ?? 1), (int)($logPages ?? 1), (int)($logTotal ?? 0), 'logs'); ?>
            </div>
        <?php endif; ?>
    </main>
</div>
