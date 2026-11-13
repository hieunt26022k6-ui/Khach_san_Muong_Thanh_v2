<?php
use Config\Config;

$selectedRoom = null;
foreach ($categories as $c) {
    if ($c['id'] === $selectedRoomId) {
        $selectedRoom = $c;
        break;
    }
}
if (!$selectedRoom) {
    $selectedRoom = $categories[0];
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 space-y-8">
    <!-- TIÊU ĐỀ TRANG -->
    <div class="border-b border-[#E8DCB8] pb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-[#A68238] uppercase tracking-[0.22em]">
            <span>CHỌN PHÒNG NGHỈ</span>
            <span>·</span>
            <span>9 HẠNG PHÒNG 5 SAO BÃI TRƯỜNG</span>
        </div>
        <h1 class="font-display text-3xl sm:text-4xl font-bold text-[#0F2032] mt-1">
            Bố Cục Đặt Phòng Nghỉ Dưỡng Mường Thanh Luxury
        </h1>
        <p class="text-xs text-stone-600 mt-1">
            Lựa chọn hạng phòng yêu thích và gói giá đi kèm. Cọc trước 30% để giữ phòng đảm bảo theo chuẩn quy định khách sạn.
        </p>

        <!-- BỘ LỌC HẠNG PHÒNG NHANH -->
        <div class="flex flex-wrap items-center gap-2 pt-4">
            <span class="text-xs font-bold text-stone-500 uppercase mr-1">Bộ lọc:</span>
            <button type="button" onclick="filterRooms('ALL')" id="filter-all" class="filter-btn px-3 py-1.5 rounded-full text-xs font-bold bg-[#0F2032] text-white transition-all">
                Tất cả (9)
            </button>
            <button type="button" onclick="filterRooms('OCEAN')" id="filter-ocean" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-stone-700 border border-stone-200 hover:border-[#C5A25D] transition-all">
                Hướng Biển (6)
            </button>
            <button type="button" onclick="filterRooms('CITY')" id="filter-city" class="filter-btn px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-stone-700 border border-stone-200 hover:border-[#C5A25D] transition-all">
                Hướng Phố (3)
            </button>
        </div>
    </div>

    <!-- BỐ CỤC 2 CỘT CHUẨN AVS HOTEL PHÚ QUỐC -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- CỘT TRÁI (8 CỘT): DANH SÁCH 9 HẠNG PHÒNG VỚI GÓI GIÁ -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">
            <?php foreach ($categories as $cat): 
                $isSelected = ($cat['id'] === $selectedRoom['id']);
            ?>
                <div id="room-card-<?= $cat['id'] ?>" data-view="<?= $cat['viewType'] ?>" class="room-item bg-white rounded-2xl border <?= $isSelected ? 'border-[#C5A25D] ring-2 ring-[#C5A25D]/30' : 'border-stone-200' ?> overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <!-- ẢNH & THÔNG TIN CƠ BẢN -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                        <div class="md:col-span-5 relative h-60 md:h-auto overflow-hidden">
                            <img src="<?= htmlspecialchars($cat['heroImage']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-full h-full object-cover" />
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-[#0F2032]/85 text-[#E5C98B] text-[10px] font-bold uppercase tracking-wider rounded">
                                <?= $cat['viewType'] === 'OCEAN' ? 'Hướng Biển' : 'Hướng Phố' ?>
                            </span>
                        </div>
                        <div class="md:col-span-7 p-5 sm:p-6 flex flex-col justify-between space-y-3">
                            <div>
                                <div class="text-[11px] font-bold text-[#A68238] uppercase tracking-wider"><?= $cat['code'] ?> · <?= $cat['vietnameseName'] ?></div>
                                <h3 class="font-display text-2xl font-bold text-[#0F2032]"><?= htmlspecialchars($cat['name']) ?></h3>
                                <p class="text-xs text-stone-600 mt-1"><?= htmlspecialchars($cat['description']) ?></p>

                                <div class="grid grid-cols-3 gap-2 text-xs text-stone-700 bg-[#FAF7F2] p-2.5 rounded-lg border border-[#E8DCB8] mt-3">
                                    <div>📐 <strong><?= $cat['areaSqm'] ?>m²</strong></div>
                                    <div>👥 <strong><?= $cat['maxAdults'] ?> Khách</strong></div>
                                    <div>🛏 <strong><?= $cat['bedType'] ?></strong></div>
                                </div>

                                <!-- TIỆN NGHI ĐI KÈM -->
                                <div class="flex flex-wrap gap-1.5 pt-2">
                                    <?php foreach ($cat['features'] as $feat): ?>
                                        <span class="text-[10px] bg-stone-100 text-stone-600 px-2 py-0.5 rounded">✓ <?= htmlspecialchars($feat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-stone-100">
                                <div>
                                    <div class="text-[10px] text-stone-500 uppercase">Giá tiêu chuẩn từ:</div>
                                    <div class="font-display text-2xl font-bold text-[#A68238]">
                                        <?= Config::formatVnd($cat['basePrice']) ?> <span class="text-xs text-stone-500 font-sans font-normal">/ đêm</span>
                                    </div>
                                </div>
                                <button type="button" onclick="selectRoom('<?= $cat['id'] ?>', '<?= htmlspecialchars($cat['name']) ?>', <?= $cat['basePrice'] ?>)" class="px-5 py-2.5 rounded <?= $isSelected ? 'bg-[#0F2032] text-[#E5C98B]' : 'bg-[#C5A25D] hover:bg-[#d8b774] text-[#0F2032]' ?> font-bold text-xs uppercase tracking-wider transition-all">
                                    <?= $isSelected ? 'Đang Chọn' : 'Chọn Phòng Này' ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 3 GÓI GIÁ PHÒNG TIÊU CHUẨN CỦA HẠNG NÀY -->
                    <div class="bg-[#FAF7F2] p-4 sm:p-5 border-t border-stone-200">
                        <div class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-3">Lựa chọn gói giá phòng:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <?php foreach ($packages as $idx => $pkg): 
                                $pkgPrice = $cat['basePrice'] + $pkg['priceOffset'];
                            ?>
                                <label class="p-3 bg-white rounded-xl border border-stone-200 hover:border-[#C5A25D] cursor-pointer flex flex-col justify-between space-y-2 text-xs transition-all">
                                    <div class="flex items-start justify-between">
                                        <input type="radio" name="pkg_choice_<?= $cat['id'] ?>" value="<?= $pkg['id'] ?>" <?= $idx === 1 ? 'checked' : '' ?> onchange="selectPackage('<?= $pkg['id'] ?>', '<?= htmlspecialchars($pkg['name']) ?>', <?= $pkg['priceOffset'] ?>)" class="text-[#C5A25D] focus:ring-[#C5A25D]" />
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                            <?= $pkg['priceOffset'] === 0 ? 'Tiết kiệm' : '+ ' . Config::formatVnd($pkg['priceOffset']) ?>
                                        </span>
                                    </div>
                                    <div class="font-bold text-[#0F2032]"><?= htmlspecialchars($pkg['name']) ?></div>
                                    <div class="text-[11px] text-stone-500 leading-tight"><?= htmlspecialchars($pkg['description']) ?></div>
                                    <div class="font-display text-lg font-bold text-[#A68238] pt-1 border-t border-stone-100">
                                        <?= Config::formatVnd($pkgPrice) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CỘT PHẢI (4 CỘT): KHUNG STICKY "KỲ NGHỈ CỦA BẠN" CỌC 30% -->
        <div class="lg:col-span-5 xl:col-span-4 sticky top-24">
            <div class="bg-white rounded-2xl border-2 border-[#C5A25D] shadow-xl overflow-hidden p-6 space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <div class="text-[10px] tracking-[0.2em] uppercase font-bold text-[#A68238]">TỔNG HỢP BOOKING</div>
                    <h3 class="font-display text-2xl font-bold text-[#0F2032]">Kỳ Nghỉ Của Bạn</h3>
                </div>

                <!-- FORM ĐẶT PHÒNG GỬI VỀ CONTROLLER -->
                <form id="bookingForm" method="POST" action="index.php?controller=customer&action=book" class="space-y-4">
                    <input type="hidden" name="categoryId" id="formCategoryId" value="<?= $selectedRoom['id'] ?>" />
                    <input type="hidden" name="packageId" id="formPackageId" value="pkg_breakfast" />
                    <input type="hidden" name="packageName" id="formPackageName" value="Gói Kèm Buffet Sáng" />
                    <input type="hidden" name="packageOffset" id="formPackageOffset" value="350000" />
                    <input type="hidden" name="nights" id="formNights" value="2" />
                    <input type="hidden" name="voucherDiscount" id="formVoucherDiscount" value="0" />
                    <input type="hidden" name="voucherCode" id="formVoucherCode" value="" />

                    <!-- PHÒNG ĐANG CHỌN -->
                    <div class="bg-[#FAF7F2] p-3.5 rounded-xl border border-[#E8DCB8] text-xs space-y-1">
                        <div class="text-stone-500 text-[10px] uppercase">Hạng phòng:</div>
                        <div id="displayRoomName" class="font-bold text-[#0F2032] text-sm"><?= htmlspecialchars($selectedRoom['name']) ?></div>
                        <div id="displayPackageName" class="text-[#A68238] font-medium">Gói Kèm Buffet Sáng (+350.000 ₫/đêm)</div>
                    </div>

                    <!-- THỜI GIAN LƯU TRÚ -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-stone-600 font-semibold mb-1">Nhận phòng</label>
                            <input type="date" name="checkInDate" id="checkInDate" value="<?= date('Y-m-d') ?>" onchange="recalculatePrice()" class="w-full px-3 py-2 border border-stone-300 rounded text-xs focus:outline-none focus:border-[#C5A25D]" />
                        </div>
                        <div>
                            <label class="block text-stone-600 font-semibold mb-1">Trả phòng</label>
                            <input type="date" name="checkOutDate" id="checkOutDate" value="<?= date('Y-m-d', strtotime('+2 days')) ?>" onchange="recalculatePrice()" class="w-full px-3 py-2 border border-stone-300 rounded text-xs focus:outline-none focus:border-[#C5A25D]" />
                        </div>
                    </div>

                    <!-- THÔNG TIN KHÁCH HÀNG -->
                    <div class="space-y-3 pt-2 border-t border-stone-100 text-xs">
                        <div>
                            <label class="block text-stone-700 font-semibold mb-1">Họ và tên quý khách *</label>
                            <input type="text" name="customerName" required placeholder="Nguyễn Văn A" class="w-full px-3 py-2 border border-stone-300 rounded text-xs focus:outline-none focus:border-[#C5A25D]" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-stone-700 font-semibold mb-1">Số điện thoại *</label>
                                <input type="tel" name="customerPhone" required placeholder="0912..." class="w-full px-3 py-2 border border-stone-300 rounded text-xs focus:outline-none focus:border-[#C5A25D]" />
                            </div>
                            <div>
                                <label class="block text-stone-700 font-semibold mb-1">Email</label>
                                <input type="email" name="customerEmail" placeholder="a@gmail.com" class="w-full px-3 py-2 border border-stone-300 rounded text-xs focus:outline-none focus:border-[#C5A25D]" />
                            </div>
                        </div>
                    </div>

                    <!-- MÃ GIẢM GIÁ VOUCHER -->
                    <div class="pt-2 border-t border-stone-100">
                        <label class="block text-[11px] font-semibold text-stone-700 mb-1">Mã ưu đãi (MUONGTHANH10, VIPPHUQUOC20)</label>
                        <div class="flex gap-2">
                            <input type="text" id="voucherCodeInput" placeholder="Nhập mã..." class="flex-1 px-3 py-2 border border-stone-300 rounded text-xs uppercase font-medium focus:outline-none focus:border-[#C5A25D]" />
                            <button type="button" onclick="applyVoucher()" class="px-3 py-2 bg-stone-800 text-white text-xs font-bold rounded hover:bg-stone-900 transition-colors">
                                Áp Dụng
                            </button>
                        </div>
                        <div id="voucherMsg" class="text-[11px] text-emerald-600 mt-1 hidden"></div>
                    </div>

                    <!-- TÍNH TOÁN CHI PHÍ & TIỀN CỌC 30% -->
                    <div class="bg-[#0F2032] text-white p-4 rounded-xl space-y-2 text-xs">
                        <div class="flex justify-between text-stone-300">
                            <span>Thời gian lưu trú:</span>
                            <span id="displayNights">2 đêm</span>
                        </div>
                        <div class="flex justify-between text-stone-300">
                            <span>Tổng tiền phòng:</span>
                            <span id="displayTotalRoom" class="font-bold">6.400.000 ₫</span>
                        </div>
                        <div id="voucherRow" class="flex justify-between text-emerald-400 hidden">
                            <span>Giảm giá:</span>
                            <span id="displayDiscount">-0 ₫</span>
                        </div>
                        <div class="border-t border-white/20 pt-2 flex justify-between items-baseline font-bold text-sm">
                            <span>Tổng thanh toán:</span>
                            <span id="displayGrandTotal" class="font-display text-xl text-[#E5C98B]">6.400.000 ₫</span>
                        </div>
                        <div class="bg-white/10 p-2.5 rounded border border-[#C5A25D]/40 mt-2 flex justify-between items-center text-[#E5C98B]">
                            <div>
                                <div class="text-[10px] uppercase font-bold">Tiền cọc giữ chỗ (30%):</div>
                                <div class="text-[9px] text-stone-300">Quy định đảm bảo của khách sạn</div>
                            </div>
                            <span id="displayDeposit" class="font-display text-lg font-bold text-white">1.920.000 ₫</span>
                        </div>
                    </div>

                    <!-- NÚT XÁC NHẬN ĐẶT PHÒNG -->
                    <button type="submit" class="w-full py-3.5 bg-[#C5A25D] hover:bg-[#d8b774] text-[#0F2032] font-bold text-xs uppercase tracking-wider rounded-lg shadow-lg transition-all hover:scale-[1.01]">
                        XÁC NHẬN ĐẶT PHÒNG (CỌC 30%)
                    </button>
                    <div class="text-center text-[10px] text-stone-500">
                        🔒 Bảo mật SSL · Nhận mã xác nhận tức thì qua tin nhắn & email
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentRoomPrice = <?= $selectedRoom['basePrice'] ?>;
let currentPackageOffset = 350000;
let voucherDiscount = 0;

function formatVnd(val) {
    return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
}

function selectRoom(id, name, price) {
    currentRoomPrice = price;
    document.getElementById('formCategoryId').value = id;
    document.getElementById('displayRoomName').innerText = name;
    
    // Cập nhật hiệu ứng border card
    document.querySelectorAll('.room-item').forEach(el => {
        el.classList.remove('border-[#C5A25D]', 'ring-2', 'ring-[#C5A25D]/30');
        el.classList.add('border-stone-200');
    });
    const activeEl = document.getElementById('room-card-' + id);
    if (activeEl) {
        activeEl.classList.add('border-[#C5A25D]', 'ring-2', 'ring-[#C5A25D]/30');
        activeEl.classList.remove('border-stone-200');
    }

    recalculatePrice();
}

function selectPackage(id, name, offset) {
    currentPackageOffset = offset;
    document.getElementById('formPackageId').value = id;
    document.getElementById('formPackageName').value = name;
    document.getElementById('formPackageOffset').value = offset;
    document.getElementById('displayPackageName').innerText = name + (offset > 0 ? ' (+' + formatVnd(offset) + '/đêm)' : '');
    recalculatePrice();
}

let appliedVoucherCode = '';

function stayNights() {
    const d1 = new Date(document.getElementById('checkInDate').value);
    const d2 = new Date(document.getElementById('checkOutDate').value);
    let diffDays = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
    if (diffDays < 1 || isNaN(diffDays)) diffDays = 1;
    return diffDays;
}

// Chỉ vẽ lại các con số (không gọi mạng)
function renderTotals() {
    const nights = stayNights();
    document.getElementById('formNights').value = nights;
    document.getElementById('displayNights').innerText = nights + ' đêm';

    const subTotal = (currentRoomPrice + currentPackageOffset) * nights;
    const grand = Math.max(0, subTotal - voucherDiscount);
    const deposit = Math.round(grand * 0.3);

    document.getElementById('displayTotalRoom').innerText = formatVnd(subTotal);
    document.getElementById('displayGrandTotal').innerText = formatVnd(grand);
    document.getElementById('displayDeposit').innerText = formatVnd(deposit);
}

function recalculatePrice() {
    renderTotals();
    // Mã đã áp dụng phải được tính lại khi đổi ngày/gói (trước đây mức giảm bị "đóng băng")
    if (appliedVoucherCode) applyVoucher(true);
}

// Quy tắc mã ưu đãi do máy chủ quyết định (API), giao diện chỉ hiển thị
function applyVoucher(silent) {
    const code = (silent && appliedVoucherCode)
        ? appliedVoucherCode
        : document.getElementById('voucherCodeInput').value.trim().toUpperCase();
    const subTotal = (currentRoomPrice + currentPackageOffset) * stayNights();

    fetch('index.php?controller=api&action=validateVoucher&code=' + encodeURIComponent(code) + '&total=' + subTotal)
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (d.valid) {
                appliedVoucherCode = code;
                voucherDiscount = d.discount;
                document.getElementById('formVoucherDiscount').value = voucherDiscount;
                document.getElementById('formVoucherCode').value = code;
                document.getElementById('voucherRow').classList.remove('hidden');
                document.getElementById('displayDiscount').innerText = '-' + formatVnd(voucherDiscount);
                document.getElementById('voucherMsg').innerText = '✓ ' + d.label;
                document.getElementById('voucherMsg').classList.remove('hidden');
            } else {
                appliedVoucherCode = '';
                voucherDiscount = 0;
                document.getElementById('formVoucherCode').value = '';
                document.getElementById('formVoucherDiscount').value = 0;
                document.getElementById('voucherRow').classList.add('hidden');
                document.getElementById('voucherMsg').classList.add('hidden');
                if (!silent) alert(d.message || 'Mã giảm giá không hợp lệ.');
            }
            renderTotals();
        })
        .catch(function () { if (!silent) alert('Không kiểm tra được mã ưu đãi, vui lòng thử lại.'); });
}

function filterRooms(view) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-[#0F2032]', 'text-white');
        btn.classList.add('bg-white', 'text-stone-700');
    });

    if (view === 'ALL') {
        document.getElementById('filter-all').classList.add('bg-[#0F2032]', 'text-white');
        document.querySelectorAll('.room-item').forEach(el => el.classList.remove('hidden'));
    } else {
        document.getElementById('filter-' + view.toLowerCase()).classList.add('bg-[#0F2032]', 'text-white');
        document.querySelectorAll('.room-item').forEach(el => {
            if (el.getAttribute('data-view') === view) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
    }
}

// Khởi chạy tính giá ngay khi tải trang
recalculatePrice();
</script>
