<?php
use Config\Config;

$subTab = $subTab ?? 'bookings';
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <!-- TIÊU ĐỀ CỔNG KHÁCH HÀNG -->
    <div class="border-b border-[#E8DCB8] pb-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="text-[10px] tracking-[0.25em] uppercase font-bold text-[#A68238]">HỆ THỐNG DÀNH CHO KHÁCH LƯU TRÚ</div>
            <h1 class="font-display text-3xl sm:text-4xl font-bold text-[#0F2032] mt-1">Cổng Dịch Vụ Khách Hàng</h1>
            <p class="text-xs text-stone-600 mt-1">Tra cứu đặt phòng, gọi món tận phòng, theo dõi dịch vụ và hóa đơn</p>
        </div>

        <!-- TABS ĐIỀU HƯỚNG CỔNG -->
        <div class="flex items-center gap-2 bg-[#FAF7F2] p-1.5 rounded-xl border border-[#E8DCB8]">
            <a href="index.php?controller=customer&action=portal&tab=bookings" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $subTab === 'bookings' ? 'bg-[#0F2032] text-[#E5C98B] shadow' : 'text-stone-600 hover:text-stone-900' ?>">
                Đơn Của Tôi
            </a>
            <a href="index.php?controller=customer&action=portal&tab=services" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $subTab === 'services' ? 'bg-[#0F2032] text-[#E5C98B] shadow' : 'text-stone-600 hover:text-stone-900' ?>">
                Gọi Dịch Vụ QR
            </a>
            <a href="index.php?controller=customer&action=portal&tab=invoices" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?= $subTab === 'invoices' ? 'bg-[#0F2032] text-[#E5C98B] shadow' : 'text-stone-600 hover:text-stone-900' ?>">
                Hóa Đơn & Cọc
            </a>
        </div>
    </div>

    <!-- TAB 1: DANH SÁCH ĐƠN ĐẶT PHÒNG -->
    <?php if ($subTab === 'bookings'): ?>
        <div class="space-y-6">
            <h3 class="font-display text-2xl font-bold text-[#0F2032]">Lịch Sử Đặt Phòng & Trạng Thái Lưu Trú</h3>
            <?php if (empty($myBookings)): ?>
                <div class="bg-white p-10 rounded-2xl border border-stone-200 text-center space-y-3">
                    <div class="text-3xl">🛎️</div>
                    <div class="text-stone-600 text-sm">Chưa tìm thấy đơn đặt phòng nào phù hợp.</div>
                    <a href="index.php?controller=customer&action=rooms" class="inline-block px-6 py-2.5 bg-[#C5A25D] text-[#0F2032] font-bold text-xs uppercase tracking-wider rounded">
                        Đặt Phòng Mới Ngay
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($myBookings as $b): ?>
                        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-4 hover:shadow-md transition-all">
                            <div class="flex items-start justify-between border-b border-stone-100 pb-3">
                                <div>
                                    <div class="text-[10px] font-bold text-[#A68238] uppercase tracking-wider">MÃ ĐƠN PHÒNG</div>
                                    <div class="font-display text-2xl font-bold text-[#0F2032]">#<?= htmlspecialchars($b['bookingCode']) ?></div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $b['status'] === 'checked_in' ? 'bg-emerald-100 text-emerald-800' : ($b['status'] === 'completed' ? 'bg-stone-100 text-stone-700' : 'bg-amber-100 text-amber-800') ?>">
                                    <?= $b['status'] === 'checked_in' ? 'Đang Lưu Trú' : ($b['status'] === 'completed' ? 'Đã Trả Phòng' : 'Đã Xác Nhận (Đã cọc)') ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <div class="text-stone-500">Khách lưu trú:</div>
                                    <div class="font-bold text-stone-900"><?= htmlspecialchars($b['customerName']) ?></div>
                                    <div class="text-stone-500 text-[11px]"><?= htmlspecialchars($b['customerPhone']) ?></div>
                                </div>
                                <div>
                                    <div class="text-stone-500">Phòng được gán:</div>
                                    <div class="font-bold text-[#A68238] text-sm"><?= $b['assignedRoomNumber'] ? htmlspecialchars($b['assignedRoomNumber']) : 'Chờ lễ tân bàn giao' ?></div>
                                </div>
                                <div>
                                    <div class="text-stone-500">Hạng phòng:</div>
                                    <div class="font-semibold text-stone-800"><?= htmlspecialchars($b['categoryName']) ?></div>
                                    <div class="text-[11px] text-[#A68238]"><?= htmlspecialchars($b['packageName']) ?></div>
                                </div>
                                <div>
                                    <div class="text-stone-500">Thời gian lưu trú:</div>
                                    <div class="font-medium text-stone-800"><?= $b['checkInDate'] ?> → <?= $b['checkOutDate'] ?> (<?= $b['nights'] ?> đêm)</div>
                                </div>
                            </div>

                            <div class="bg-[#FAF7F2] p-3 rounded-xl border border-[#E8DCB8] flex items-center justify-between text-xs">
                                <div>
                                    <div class="text-stone-500 text-[10px] uppercase">Tổng tiền phòng:</div>
                                    <div class="font-bold text-[#0F2032]"><?= Config::formatVnd($b['totalAmount']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-stone-500 text-[10px] uppercase">Đã cọc 30%:</div>
                                    <div class="font-bold text-emerald-700"><?= Config::formatVnd($b['depositPaid']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <!-- TAB 2: GỌI DỊCH VỤ PHÒNG QR -->
    <?php elseif ($subTab === 'services'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- DANH MỤC DỊCH VỤ PHỤC VỤ TẬN PHÒNG -->
            <div class="lg:col-span-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Thực Đơn Ẩm Thực & Dịch Vụ Gọi Tận Phòng</h3>
                    <span class="text-xs text-stone-500">Phục vụ 24/7</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach ($services as $srv): ?>
                        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-start justify-between">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?= $srv['category'] === 'dining' ? 'bg-amber-100 text-amber-800' : ($srv['category'] === 'spa' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') ?>">
                                        <?= $srv['category'] ?>
                                    </span>
                                    <span class="font-display text-lg font-bold text-[#A68238]"><?= Config::formatVnd($srv['price']) ?></span>
                                </div>
                                <h4 class="font-display text-lg font-bold text-[#0F2032] mt-2"><?= htmlspecialchars($srv['name']) ?></h4>
                                <div class="text-xs text-stone-500">Đơn vị: <?= $srv['unit'] ?></div>
                            </div>

                            <form method="POST" action="index.php?controller=customer&action=orderService" class="space-y-2 pt-2 border-t border-stone-100">
                                <input type="hidden" name="serviceName" value="<?= htmlspecialchars($srv['name']) ?>" />
                                <input type="hidden" name="unitPrice" value="<?= $srv['price'] ?>" />

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <input type="text" name="roomNumber" required placeholder="Số phòng (P.303)" class="px-2.5 py-1.5 border border-stone-300 rounded text-xs" />
                                    <input type="text" name="guestName" required placeholder="Tên khách" class="px-2.5 py-1.5 border border-stone-300 rounded text-xs" />
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="number" name="quantity" value="1" min="1" max="10" class="w-16 px-2 py-1.5 border border-stone-300 rounded text-xs text-center font-bold" />
                                    <button type="submit" class="flex-1 py-1.5 bg-[#0F2032] hover:bg-[#162B40] text-white text-xs font-bold uppercase tracking-wider rounded transition-all">
                                        Gọi Món Này
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CỘT PHẢI: LỊCH SỬ DỊCH VỤ ĐÃ ĐẶT -->
            <div class="lg:col-span-4 space-y-5">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h3 class="font-display text-xl font-bold text-[#0F2032] border-b border-stone-100 pb-2">
                        Đơn Dịch Vụ Đang Thực Hiện
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($myOrders as $ord): ?>
                            <div class="p-3 bg-[#FAF7F2] rounded-xl border border-[#E8DCB8] text-xs space-y-1">
                                <div class="flex items-center justify-between font-bold text-[#0F2032]">
                                    <span><?= htmlspecialchars($ord['serviceName']) ?> (x<?= $ord['quantity'] ?>)</span>
                                    <span class="text-[#A68238]"><?= Config::formatVnd($ord['totalPrice']) ?></span>
                                </div>
                                <div class="flex items-center justify-between text-stone-500 text-[11px]">
                                    <span>Phòng: <strong><?= htmlspecialchars($ord['roomNumber']) ?></strong> (<?= htmlspecialchars($ord['guestName']) ?>)</span>
                                    <span class="text-emerald-700 font-semibold"><?= $ord['status'] ?></span>
                                </div>
                                <div class="text-[10px] text-stone-400">Thời gian: <?= $ord['scheduledTime'] ?> · <?= htmlspecialchars($ord['note']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    <!-- TAB 3: HÓA ĐƠN & TIỀN CỌC 30% -->
    <?php elseif ($subTab === 'invoices'): ?>
        <div class="max-w-3xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border-2 border-[#C5A25D]/40 shadow-xl space-y-6">
            <div class="flex items-start justify-between border-b-2 border-[#C5A25D] pb-4">
                <div>
                    <div class="text-[11px] font-bold text-[#A68238] uppercase tracking-[0.2em]">MƯỜNG THANH LUXURY PHÚ QUỐC</div>
                    <h3 class="font-display text-3xl font-bold text-[#0F2032] mt-1">Phiếu Xác Nhận Cọc & Hóa Đơn</h3>
                </div>
                <div class="text-right text-xs text-stone-500">
                    <div>Ngày in: <?= date('d/m/Y H:i') ?></div>
                    <div class="font-mono text-[#0F2032] font-bold">SO-MT-2026</div>
                </div>
            </div>

            <div class="text-xs text-stone-600 leading-relaxed">
                Quý khách đã đặt cọc thành công 30% tổng tiền phòng theo chính sách lưu trú của tập đoàn Mường Thanh. Khi đến làm thủ tục nhận phòng tại Bàn Lễ tân Front Desk, quý khách chỉ cần xuất trình mã đơn hoặc số điện thoại để nhận phòng.
            </div>

            <div class="bg-[#FAF7F2] p-5 rounded-xl border border-[#E8DCB8] space-y-3 text-xs">
                <div class="flex justify-between text-stone-700 font-medium">
                    <span>Quy định tiền cọc tối thiểu:</span>
                    <strong class="text-stone-900">30% Giá trị đơn đặt</strong>
                </div>
                <div class="flex justify-between text-stone-700 font-medium">
                    <span>Hình thức thanh toán cọc:</span>
                    <span class="text-stone-900">Chuyển khoản QR / Thẻ Visa Master / Tiền mặt tại quầy</span>
                </div>
                <div class="flex justify-between text-stone-700 font-medium border-t border-stone-200 pt-2">
                    <span>Chính sách hoàn hủy miễn phí:</span>
                    <span class="text-emerald-700 font-bold">Trước <?= (int)(\Core\Session::get('policy')['freeCancelBeforeHours'] ?? 48) ?> giờ so với ngày nhận phòng</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
