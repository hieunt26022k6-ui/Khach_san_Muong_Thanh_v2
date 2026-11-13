<?php
use Config\Config;
$isStaffPortal = $isStaffPortal ?? false;
?>
</main>

<!-- AUTH MODAL INCLUDED -->
<?php require __DIR__ . '/auth_modal.php'; ?>

<!-- LOOKUP MODAL INCLUDED -->
<div id="lookupModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-6 sm:p-7 space-y-5 rounded-2xl shadow-2xl border border-stone-200 relative">
        <button onclick="document.getElementById('lookupModal').classList.add('hidden')" class="absolute top-4 right-4 text-stone-400 hover:text-stone-700 text-xl font-bold">
            ✕
        </button>
        <div class="text-center pb-2 border-b border-stone-100">
            <div class="text-[10px] tracking-[0.25em] text-[#C5A25D] font-bold uppercase">TRA CỨU TRỰC TUYẾN</div>
            <h3 class="font-display text-2xl font-bold text-[#0F2032] mt-1">Tra Cứu Đơn Đặt Phòng</h3>
            <p class="text-xs text-stone-500 mt-1">Nhập mã đơn đặt phòng hoặc số điện thoại người đặt</p>
        </div>
        <form method="GET" action="index.php" class="space-y-4">
            <input type="hidden" name="controller" value="customer" />
            <input type="hidden" name="action" value="portal" />
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Mã đơn đặt phòng (ví dụ: MT-8492)</label>
                <input type="text" name="bookingCode" placeholder="MT-8492 hoặc số điện thoại" class="w-full px-3.5 py-2.5 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-[#C5A25D] uppercase" />
            </div>
            <button type="submit" class="w-full py-3 bg-[#0F2032] hover:bg-[#162B40] text-[#E5C98B] font-bold text-xs uppercase tracking-wider rounded-lg shadow transition-all">
                Tra Cứu Ngay
            </button>
        </form>
    </div>
</div>

<?php if (!$isStaffPortal): ?>
<!-- LUXURY CUSTOMER FOOTER -->
<footer class="bg-[#FAF7F2] text-[#2D2A24] pt-16 pb-8 border-t border-[#E8DCB8] mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-[#E8DCB8]">
        <!-- Col 1: Brand & Heritage -->
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <span class="text-xl font-display font-semibold tracking-wide text-[#2D2A24]">MƯỜNG THANH LUXURY</span>
            </div>
            <div class="text-[10px] text-[#A68238] font-bold tracking-[0.2em] uppercase">PHÚ QUỐC RESORT & SPA ★★★★★</div>
            <p class="text-xs text-[#6E6658] leading-relaxed">
                Tổ hợp nghỉ dưỡng tiêu chuẩn 5 sao quốc tế tọa lạc bên bờ cát vàng mịn màng của Bãi Trường — nơi ngắm hoàng hôn rực rỡ và thơ mộng nhất đảo ngọc Phú Quốc.
            </p>
            <div class="text-xs text-[#A68238]">
                Hotline 24/7: <strong class="text-[#2D2A24]"><?= Config::HOTEL_HOTLINE ?></strong>
            </div>
        </div>

        <!-- Col 2: Services -->
        <div class="space-y-3">
            <h4 class="font-display text-lg font-medium text-[#A68238]">Khám Phá Dịch Vụ</h4>
            <ul class="space-y-2 text-xs text-[#6E6658]">
                <li><a href="index.php?controller=customer&action=rooms" class="hover:text-[#A68238] transition-colors">9 Hạng phòng và Suite hướng biển & phố</a></li>
                <li><a href="index.php?controller=customer&action=dining" class="hover:text-[#A68238] transition-colors">Nhà hàng Sonasea & Rooftop Sky Lounge</a></li>
                <li><a href="index.php?controller=customer&action=spa" class="hover:text-[#A68238] transition-colors">Pearl Spa & Massage trị liệu tinh dầu</a></li>
                <li><a href="index.php?controller=customer&action=conference" class="hover:text-[#A68238] transition-colors">Trung tâm hội nghị & Grand Ballroom</a></li>
                <li><a href="index.php?controller=customer&action=portal" class="hover:text-[#A68238] transition-colors">Cổng khách hàng & Đặt dịch vụ phòng QR</a></li>
            </ul>
        </div>

        <!-- Col 3: Contact & Location -->
        <div class="space-y-3">
            <h4 class="font-display text-lg font-medium text-[#A68238]">Thông Tin Liên Hệ</h4>
            <div class="space-y-2 text-xs text-[#6E6658] leading-relaxed">
                <p><strong class="text-[#2D2A24]">Địa chỉ:</strong> <?= Config::HOTEL_ADDRESS ?></p>
                <p><strong class="text-[#2D2A24]">Email:</strong> <?= Config::HOTEL_EMAIL ?></p>
                <p><strong class="text-[#2D2A24]">Giờ nhận phòng:</strong> 14:00 · <strong class="text-[#2D2A24]">Trả phòng:</strong> 12:00</p>
            </div>
        </div>

        <!-- Col 4: Staff Portal Links -->
        <div class="space-y-3">
            <h4 class="font-display text-lg font-medium text-[#A68238]">Dành Cho Nhân Viên</h4>
            <div class="space-y-1.5 text-xs text-[#6E6658]">
                <button type="button" onclick="document.getElementById('authModal').classList.remove('hidden')" class="block text-[#2D2A24] hover:text-[#A68238] transition-colors cursor-pointer font-semibold">
                    <?= $currentUser ? "Tài khoản: {$currentUser['fullName']}" : "Đăng nhập hệ thống" ?>
                </button>
                <div class="text-[11px] text-[#8C8270]">Khách hàng · Lễ tân · Quản trị viên</div>
            </div>
        </div>
    </div>

    <!-- BOTTOM ROW WITH SMALL DOWNLOAD BUTTON -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-[#6E6658]">
        <div>© 2026 Mường Thanh Luxury Phú Quốc. Bản quyền thuộc về Tập đoàn Khách sạn Mường Thanh.</div>
        <div class="flex items-center gap-3">
            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
            <form method="POST" action="index.php?controller=admin&action=resetData" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục toàn bộ dữ liệu mẫu ban đầu?')">
                <button type="submit" class="text-[#8C8270] hover:text-[#2D2A24] underline cursor-pointer">Khôi phục dữ liệu</button>
            </form>
            <span class="text-[#CFC6B8]">·</span>
            <?php endif; ?>
            <a
              href="muongthanh_luxury_hotel.zip"
              download="muongthanh_luxury_hotel.zip"
              class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#F0E6D8] hover:bg-[#E5D7C3] text-[#63553E] hover:text-[#2D2A24] border border-[#D8C7B0] rounded text-[11px] font-medium transition cursor-pointer"
              title="Tải mã nguồn website (.zip)"
            >
              <svg class="w-3.5 h-3.5 text-[#A68238]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              <span>Tải mã nguồn (.zip)</span>
            </a>
        </div>
    </div>
</footer>
<?php endif; ?>

<?php /* Tự gắn mã CSRF vào mọi biểu mẫu POST trên trang */ ?>
<script>
(function () {
    var token = <?= json_encode(\Core\Session::csrfToken()) ?>;
    document.querySelectorAll('form').forEach(function (f) {
        if ((f.getAttribute('method') || '').toLowerCase() !== 'post') return;
        if (f.querySelector('input[name="csrf"]')) return;
        var i = document.createElement('input');
        i.type = 'hidden'; i.name = 'csrf'; i.value = token;
        f.appendChild(i);
    });
})();
</script>
</body>
</html>
