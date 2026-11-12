<?php
use Config\Config;

$currentTab = $_GET['tab'] ?? 'room_map';

$totalAvailable = 0;
$totalOccupied = 0;
$totalReserved = 0;
$totalCleaning = 0;
$totalMaintenance = 0;

foreach ($physicalRooms as $r) {
    if ($r['status'] === 'available') $totalAvailable++;
    elseif ($r['status'] === 'occupied') $totalOccupied++;
    elseif ($r['status'] === 'reserved') $totalReserved++;
    elseif ($r['status'] === 'cleaning') $totalCleaning++;
    elseif ($r['status'] === 'maintenance') $totalMaintenance++;
}
?>
<!-- GIAO DIỆN BÀN LỄ TÂN PMS CÓ LEFT SIDEBAR CHUẨN RECEPTIONIST DASHBOARD -->
<div class="flex min-h-screen bg-[#F0EFE9] text-[#162B40]">
    <!-- 1. LEFT SIDEBAR PMS CHUYÊN DỤNG -->
    <aside class="w-64 bg-[#0F2032] text-white flex flex-col justify-between shrink-0 shadow-2xl border-r border-[#C5A25D]/30 min-h-screen sticky top-0">
        <div>
            <!-- Header Logo PMS -->
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border border-[#C5A25D] flex items-center justify-center bg-gradient-to-br from-[#162B40] to-[#0A1522] shadow">
                    <span class="text-[#E5C98B] font-display text-2xl font-bold">M</span>
                </div>
                <div>
                    <div class="text-[9px] tracking-[0.22em] text-[#E5C98B] font-bold uppercase">MƯỜNG THANH PMS</div>
                    <div class="font-display text-lg font-bold text-white leading-tight">BÀN LỄ TÂN 5★</div>
                </div>
            </div>

            <!-- Profile Lễ Tân Đang Trực -->
            <div class="p-4 mx-4 my-4 bg-slate-800/80 rounded-xl border border-slate-700/80 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#C5A25D] text-[#0F243C] flex items-center justify-center font-bold text-sm">
                    TT
                </div>
                <div>
                    <div class="text-xs font-bold text-white">Nguyễn Thu Thảo</div>
                    <div class="text-[10px] text-[#C5A25D] uppercase tracking-wider">Lễ tân ca trực</div>
                </div>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="px-3 space-y-1 text-xs font-semibold tracking-wide">
                <a href="index.php?controller=receptionist&action=index&tab=room_map" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'room_map' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>🗺️</span>
                    <span>Sơ đồ phòng (Room Rack)</span>
                </a>
                <a href="index.php?controller=receptionist&action=index&tab=bookings" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'bookings' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>📋</span>
                    <span>Đặt phòng & Check-in/out</span>
                </a>
                <a href="index.php?controller=receptionist&action=index&tab=services" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'services' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>🍽️</span>
                    <span>Dịch vụ phòng & Ẩm thực QR</span>
                </a>
                <a href="index.php?controller=receptionist&action=index&tab=payments" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'payments' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>💳</span>
                    <span>Thu ngân & Hóa đơn Folio</span>
                </a>
                <a href="index.php?controller=receptionist&action=index&tab=housekeeping" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $currentTab === 'housekeeping' ? 'bg-[#C5A25D] text-[#0F2032] font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span>🧹</span>
                    <span>Quản lý buồng phòng</span>
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
                <div class="text-[10px] tracking-[0.25em] uppercase font-bold text-[#A68238]">FRONT DESK MANAGEMENT SYSTEM</div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[#0F2032]">Bàn Lễ Tân & Quản Trị Lưu Trú</h1>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('modalCheckIn').classList.remove('hidden')" class="px-4 py-2 bg-[#C5A25D] hover:bg-[#d8b774] text-[#0F2032] font-bold text-xs uppercase tracking-wider rounded-lg shadow transition-all">
                    + Nhận Phòng (Check-In)
                </button>
                <button onclick="document.getElementById('modalCheckOut').classList.remove('hidden')" class="px-4 py-2 bg-[#0F2032] hover:bg-[#162B40] text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow transition-all">
                    Trả Phòng (Check-Out)
                </button>
            </div>
        </div>

        <!-- KPI SUMMARY BAR -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs text-center">
                <div class="text-[10px] uppercase font-bold text-stone-500">Phòng Trống Sạch</div>
                <div class="font-display text-3xl font-bold text-emerald-600 mt-1"><?= $totalAvailable ?></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs text-center">
                <div class="text-[10px] uppercase font-bold text-stone-500">Đang Ở (Occupied)</div>
                <div class="font-display text-3xl font-bold text-blue-600 mt-1"><?= $totalOccupied ?></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs text-center">
                <div class="text-[10px] uppercase font-bold text-stone-500">Đã Đặt (Reserved)</div>
                <div class="font-display text-3xl font-bold text-amber-600 mt-1"><?= $totalReserved ?></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs text-center">
                <div class="text-[10px] uppercase font-bold text-stone-500">Đang Dọn Dẹp</div>
                <div class="font-display text-3xl font-bold text-orange-600 mt-1"><?= $totalCleaning ?></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-xs text-center">
                <div class="text-[10px] uppercase font-bold text-stone-500">Bảo Trì</div>
                <div class="font-display text-3xl font-bold text-rose-600 mt-1"><?= $totalMaintenance ?></div>
            </div>
        </div>

        <!-- NỘI DUNG THEO TAB -->
        <?php if ($currentTab === 'room_map'): ?>
            <!-- SƠ ĐỒ PHÒNG ROOM RACK TẦNG 2 ĐẾN TẦNG 8 -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Sơ Đồ Phòng Khách Sạn (Room Rack)</h3>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Trống</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Đang ở</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Đã đặt</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-500"></span> Chờ dọn</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Bảo trì</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <?php foreach ($floors as $floorNum => $roomsOnFloor): ?>
                        <div class="bg-[#FAF7F2] p-4 rounded-xl border border-[#E8DCB8]">
                            <div class="text-xs font-bold text-[#A68238] uppercase tracking-wider mb-3">TẦNG <?= $floorNum ?> (<?= count($roomsOnFloor) ?> Phòng)</div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                                <?php foreach ($roomsOnFloor as $rm): 
                                    $bgClass = 'bg-emerald-50 border-emerald-300 text-emerald-900';
                                    $badge = 'Trống';
                                    if ($rm['status'] === 'occupied') {
                                        $bgClass = 'bg-blue-50 border-blue-300 text-blue-900';
                                        $badge = 'Đang ở';
                                    } elseif ($rm['status'] === 'reserved') {
                                        $bgClass = 'bg-amber-50 border-amber-300 text-amber-900';
                                        $badge = 'Đã đặt';
                                    } elseif ($rm['status'] === 'cleaning') {
                                        $bgClass = 'bg-orange-50 border-orange-300 text-orange-900';
                                        $badge = 'Chờ dọn';
                                    } elseif ($rm['status'] === 'maintenance') {
                                        $bgClass = 'bg-rose-50 border-rose-300 text-rose-900';
                                        $badge = 'Bảo trì';
                                    }
                                ?>
                                    <div class="p-3 rounded-xl border <?= $bgClass ?> shadow-xs flex flex-col justify-between space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-sm"><?= $rm['roomNumber'] ?></span>
                                            <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-white/80"><?= $badge ?></span>
                                        </div>
                                        <div class="text-[10px] uppercase font-semibold text-stone-600 truncate"><?= strtoupper($rm['categoryId']) ?></div>
                                        <?php if (!empty($rm['currentGuestName'])): ?>
                                            <div class="text-[11px] font-medium truncate text-stone-800">👤 <?= htmlspecialchars($rm['currentGuestName']) ?></div>
                                        <?php else: ?>
                                            <div class="text-[10px] text-stone-400 italic">Sẵn sàng</div>
                                        <?php endif; ?>

                                        <form method="POST" action="index.php?controller=receptionist&action=updateRoomStatus" class="pt-1 border-t border-black/10 flex items-center justify-between">
                                            <input type="hidden" name="roomNumber" value="<?= $rm['roomNumber'] ?>" />
                                            <select name="status" onchange="this.form.submit()" class="text-[10px] bg-white border border-stone-300 rounded px-1 py-0.5 w-full">
                                                <option value="available" <?= $rm['status'] === 'available' ? 'selected' : '' ?>>Trống sạch</option>
                                                <option value="occupied" <?= $rm['status'] === 'occupied' ? 'selected' : '' ?>>Đang ở</option>
                                                <option value="cleaning" <?= $rm['status'] === 'cleaning' ? 'selected' : '' ?>>Chờ dọn</option>
                                                <option value="maintenance" <?= $rm['status'] === 'maintenance' ? 'selected' : '' ?>>Bảo trì</option>
                                            </select>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($currentTab === 'bookings'): ?>
            <!-- DANH SÁCH ĐƠN ĐẶT PHÒNG PMS -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Quản Lý Đơn Đặt Phòng & Nhận/Trả Phòng</h3>
                    <span class="text-xs text-stone-500">Cọc 30% đã ghi nhận tự động</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-[#FAF7F2] text-[#A68238] uppercase font-bold text-[10px] tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="p-3">Mã Đơn</th>
                                <th class="p-3">Khách Hàng</th>
                                <th class="p-3">Hạng Phòng</th>
                                <th class="p-3">Phòng Gán</th>
                                <th class="p-3">Lưu Trú</th>
                                <th class="p-3">Tổng Tiền</th>
                                <th class="p-3">Đã Cọc 30%</th>
                                <th class="p-3">Còn Lại</th>
                                <th class="p-3">Trạng Thái</th>
                                <th class="p-3 text-right">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-stone-50">
                                    <td class="p-3 font-bold text-[#0F2032]">#<?= htmlspecialchars($b['bookingCode']) ?></td>
                                    <td class="p-3">
                                        <div class="font-bold text-stone-900"><?= htmlspecialchars($b['customerName']) ?></div>
                                        <div class="text-[11px] text-stone-500"><?= htmlspecialchars($b['customerPhone']) ?></div>
                                    </td>
                                    <td class="p-3 font-medium"><?= htmlspecialchars($b['categoryName']) ?></td>
                                    <td class="p-3 font-bold text-[#A68238]"><?= $b['assignedRoomNumber'] ? htmlspecialchars($b['assignedRoomNumber']) : '—' ?></td>
                                    <td class="p-3"><?= $b['checkInDate'] ?> → <?= $b['checkOutDate'] ?> (<?= $b['nights'] ?>đ)</td>
                                    <td class="p-3 font-bold"><?= Config::formatVnd($b['totalAmount']) ?></td>
                                    <td class="p-3 text-emerald-700 font-bold"><?= Config::formatVnd($b['depositPaid']) ?></td>
                                    <td class="p-3 font-bold text-[#0F2032]"><?= Config::formatVnd($b['remainingBalance']) ?></td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $b['status'] === 'checked_in' ? 'bg-emerald-100 text-emerald-800' : ($b['status'] === 'completed' ? 'bg-stone-100 text-stone-700' : 'bg-amber-100 text-amber-800') ?>">
                                            <?= $b['status'] ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-right space-x-1">
                                        <?php if ($b['status'] === 'confirmed'): ?>
                                            <button onclick="openCheckInFor('<?= $b['bookingCode'] ?>')" class="px-2.5 py-1 bg-[#C5A25D] text-[#0F2032] rounded text-[11px] font-bold">
                                                Check-In
                                            </button>
                                        <?php elseif ($b['status'] === 'checked_in'): ?>
                                            <form method="POST" action="index.php?controller=receptionist&action=checkOut" class="inline">
                                                <input type="hidden" name="bookingCode" value="<?= $b['bookingCode'] ?>" />
                                                <button type="submit" class="px-2.5 py-1 bg-[#0F2032] text-white rounded text-[11px] font-bold">
                                                    Check-Out
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-stone-400 italic text-[11px]">Hoàn tất</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($currentTab === 'services'): ?>
            <!-- DỊCH VỤ PHÒNG & GỌI MÓN QR -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Đơn Dịch Vụ Gọi Phòng (Room Service & Spa)</h3>
                    <span class="text-xs text-stone-500">Cập nhật trực tiếp từ khách lưu trú</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-[#FAF7F2] text-[#A68238] uppercase font-bold text-[10px] tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="p-3">Mã / Giờ</th>
                                <th class="p-3">Phòng</th>
                                <th class="p-3">Tên Khách</th>
                                <th class="p-3">Dịch Vụ Yêu Cầu</th>
                                <th class="p-3">Số Lượng</th>
                                <th class="p-3">Thành Tiền</th>
                                <th class="p-3">Ghi Chú</th>
                                <th class="p-3">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <?php foreach ($serviceOrders as $so): ?>
                                <tr class="hover:bg-stone-50">
                                    <td class="p-3 font-mono"><?= $so['scheduledTime'] ?></td>
                                    <td class="p-3 font-bold text-[#A68238]"><?= htmlspecialchars($so['roomNumber']) ?></td>
                                    <td class="p-3 font-bold"><?= htmlspecialchars($so['guestName']) ?></td>
                                    <td class="p-3 font-medium"><?= htmlspecialchars($so['serviceName']) ?></td>
                                    <td class="p-3 font-bold"><?= $so['quantity'] ?></td>
                                    <td class="p-3 font-bold text-[#0F2032]"><?= Config::formatVnd($so['totalPrice']) ?></td>
                                    <td class="p-3 text-stone-500"><?= htmlspecialchars($so['note'] ?? '—') ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                            <?= $so['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($currentTab === 'payments'): ?>
            <!-- THU NGÂN & HÓA ĐƠN FOLIO -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <h3 class="font-display text-2xl font-bold text-[#0F2032] border-b border-stone-100 pb-3">
                    Thu Ngân & Quyết Toán Hóa Đơn Folio
                </h3>
                <p class="text-xs text-stone-600">
                    Mỗi đơn đặt phòng đã được tự động thanh toán cọc 30%. Khi khách check-out, hệ thống tự động cộng dồn tiền phòng còn lại cùng các dịch vụ ăn uống, spa phát sinh trong suốt kỳ nghỉ.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                    <div class="p-4 bg-[#FAF7F2] rounded-xl border border-[#E8DCB8]">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Cọc Đã Nhận (30%)</div>
                        <div class="font-display text-2xl font-bold text-emerald-700 mt-1">10.680.000 ₫</div>
                    </div>
                    <div class="p-4 bg-[#FAF7F2] rounded-xl border border-[#E8DCB8]">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Tiền Phòng Còn Thu Tại Quầy</div>
                        <div class="font-display text-2xl font-bold text-[#0F2032] mt-1">24.920.000 ₫</div>
                    </div>
                    <div class="p-4 bg-[#FAF7F2] rounded-xl border border-[#E8DCB8]">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Dịch Vụ Phát Sinh Đã Quyết Toán</div>
                        <div class="font-display text-2xl font-bold text-purple-700 mt-1">2.090.000 ₫</div>
                    </div>
                </div>
            </div>

        <?php elseif ($currentTab === 'housekeeping'): ?>
            <!-- QUẢN LÝ BUỒNG PHÒNG -->
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm space-y-4">
                <h3 class="font-display text-2xl font-bold text-[#0F2032] border-b border-stone-100 pb-3">
                    Bàn Giao Trạng Thái Buồng Phòng (Housekeeping)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-orange-300 bg-orange-50 space-y-2">
                        <div class="font-bold text-orange-900 text-sm">Phòng Chờ Dọn Dẹp (<?= $totalCleaning ?>)</div>
                        <p class="text-xs text-orange-800">Cần thay ga giường, khăn tắm và khử khuẩn sau khi khách trả phòng.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-rose-300 bg-rose-50 space-y-2">
                        <div class="font-bold text-rose-900 text-sm">Phòng Đang Bảo Trì (<?= $totalMaintenance ?>)</div>
                        <p class="text-xs text-rose-800">Kiểm tra điều hòa và hệ thống vòi sen nóng lạnh.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-emerald-300 bg-emerald-50 space-y-2">
                        <div class="font-bold text-emerald-900 text-sm">Phòng Sẵn Sàng (<?= $totalAvailable ?>)</div>
                        <p class="text-xs text-emerald-800">Đã kiểm tra chất lượng 5 sao, sẵn sàng bàn giao chìa khóa.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- MODAL CHECK-IN -->
<div id="modalCheckIn" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-6 rounded-2xl shadow-2xl border border-stone-200 space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
            <h4 class="font-display text-xl font-bold text-[#0F2032]">Check-In Nhận Phòng</h4>
            <button onclick="document.getElementById('modalCheckIn').classList.add('hidden')" class="text-stone-400 font-bold">✕</button>
        </div>
        <form method="POST" action="index.php?controller=receptionist&action=checkIn" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold mb-1">Chọn Đơn Đặt Phòng</label>
                <select name="bookingCode" id="selectCheckInBooking" required class="w-full p-2.5 border rounded">
                    <?php foreach ($bookings as $b): 
                        if ($b['status'] === 'confirmed'): ?>
                            <option value="<?= $b['bookingCode'] ?>">#<?= $b['bookingCode'] ?> — <?= htmlspecialchars($b['customerName']) ?> (<?= $b['categoryName'] ?>)</option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Gán Số Phòng Trống</label>
                <select name="roomNumber" required class="w-full p-2.5 border rounded">
                    <?php foreach ($physicalRooms as $r): 
                        if ($r['status'] === 'available'): ?>
                            <option value="<?= $r['roomNumber'] ?>"><?= $r['roomNumber'] ?> — Hạng <?= strtoupper($r['categoryId']) ?> (Tầng <?= $r['floor'] ?>)</option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full py-2.5 bg-[#C5A25D] text-[#0F2032] font-bold text-xs uppercase tracking-wider rounded">
                Bàn Giao Chìa Khóa Phòng
            </button>
        </form>
    </div>
</div>

<!-- MODAL CHECK-OUT -->
<div id="modalCheckOut" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-6 rounded-2xl shadow-2xl border border-stone-200 space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
            <h4 class="font-display text-xl font-bold text-[#0F2032]">Check-Out & Thu Ngân</h4>
            <button onclick="document.getElementById('modalCheckOut').classList.add('hidden')" class="text-stone-400 font-bold">✕</button>
        </div>
        <form method="POST" action="index.php?controller=receptionist&action=checkOut" class="space-y-4 text-xs">
            <div>
                <label class="block font-semibold mb-1">Chọn Phòng Cần Trả</label>
                <select name="bookingCode" required class="w-full p-2.5 border rounded">
                    <?php foreach ($bookings as $b): 
                        if ($b['status'] === 'checked_in'): ?>
                            <option value="<?= $b['bookingCode'] ?>">#<?= $b['bookingCode'] ?> — <?= $b['assignedRoomNumber'] ?> (<?= htmlspecialchars($b['customerName']) ?>)</option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full py-2.5 bg-[#0F2032] text-white font-bold text-xs uppercase tracking-wider rounded">
                Quyết Toán & Trả Phòng
            </button>
        </form>
    </div>
</div>

<script>
function openCheckInFor(code) {
    document.getElementById('selectCheckInBooking').value = code;
    document.getElementById('modalCheckIn').classList.remove('hidden');
}
</script>
