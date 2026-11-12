<!-- MODAL ĐĂNG NHẬP / ĐĂNG KÝ HỆ THỐNG PHÂN QUYỀN -->
<div id="authModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full p-6 sm:p-7 space-y-5 rounded-2xl shadow-2xl border border-stone-200 relative">
        <button onclick="document.getElementById('authModal').classList.add('hidden')" class="absolute top-4 right-4 text-stone-400 hover:text-stone-700 text-xl font-bold">
            ✕
        </button>

        <div class="text-center pb-2 border-b border-stone-100">
            <div class="text-[10px] tracking-[0.25em] text-[#C5A25D] font-bold uppercase">MƯỜNG THANH HOSPITALITY</div>
            <h3 class="font-display text-2xl font-bold text-[#0F2032] mt-1">Đăng Nhập Hệ Thống</h3>
            <p class="text-xs text-stone-500 mt-1">Quản lý phòng nghỉ, lễ tân và quản trị khách sạn</p>
        </div>

        <!-- FORM ĐĂNG NHẬP -->
        <form method="POST" action="index.php?controller=auth&action=login" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Tên đăng nhập</label>
                <input type="text" name="username" required placeholder="khachhang / letan / admin" class="w-full px-3.5 py-2.5 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-[#C5A25D]" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" required placeholder="Nhập 123" class="w-full px-3.5 py-2.5 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-[#C5A25D]" />
            </div>

            <button type="submit" class="w-full py-3 bg-[#0F2032] hover:bg-[#162B40] text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow transition-all">
                Đăng Nhập
            </button>
        </form>

        <!-- CHỌN NHANH VAI TRÒ MẪU -->
        <div class="bg-[#FAF7F2] p-3.5 rounded-xl border border-[#E8DCB8] space-y-2">
            <div class="text-[11px] font-bold text-[#7E6023] uppercase tracking-wider text-center">Tài khoản thử nghiệm sẵn có:</div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <a href="index.php?controller=auth&action=switchRole&role=customer" class="p-2 bg-white rounded border border-stone-200 hover:border-[#C5A25D] shadow-xs">
                    <div class="font-bold text-[#0F2032]">Khách Hàng</div>
                    <div class="text-[10px] text-stone-500">khachhang / 123</div>
                </a>
                <a href="index.php?controller=auth&action=switchRole&role=receptionist" class="p-2 bg-white rounded border border-stone-200 hover:border-[#C5A25D] shadow-xs">
                    <div class="font-bold text-[#0F2032]">Lễ Tân</div>
                    <div class="text-[10px] text-stone-500">letan / 123</div>
                </a>
                <a href="index.php?controller=auth&action=switchRole&role=admin" class="p-2 bg-white rounded border border-stone-200 hover:border-[#C5A25D] shadow-xs">
                    <div class="font-bold text-[#0F2032]">Quản Trị</div>
                    <div class="text-[10px] text-stone-500">admin / 123</div>
                </a>
            </div>
        </div>
    </div>
</div>
