<?php
use Config\Config;
?>
<!-- HERO HOÀNG HÔN BÃI TRƯỜNG PHÚ QUỐC -->
<section class="relative bg-[#0F2032] text-white">
    <div class="relative h-[560px] md:h-[640px] overflow-hidden">
        <img src="assets/images/hero_muong_thanh_exterior_1793420632993.jpg" alt="Mường Thanh Luxury Phú Quốc" class="w-full h-full object-cover opacity-60 scale-105 transition-transform duration-1000" />
        <div class="absolute inset-0 bg-gradient-to-b from-[#0F2032]/70 via-[#0F2032]/40 to-[#0F2032]/90 flex flex-col items-center justify-center text-center px-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/10 backdrop-blur-xs border border-[#C5A25D]/40 text-[#E5C98B] text-[11px] font-semibold tracking-[0.26em] uppercase mb-4">
                ♛ KHÁCH SẠN NĂM SAO BÊN BIỂN PHÚ QUỐC
            </div>
            <h1 class="font-display text-4xl sm:text-6xl md:text-7xl font-semibold leading-tight max-w-4xl text-white">
                Đón Hoàng Hôn Ngay Bên Bờ Biển
            </h1>
            <p class="font-display italic text-lg sm:text-2xl text-stone-200 mt-4 max-w-2xl leading-relaxed">
                Những ngày ngập tràn nắng ấm, làn gió biển dịu nhẹ và sự phục vụ tinh tế — kỳ nghỉ trọn vẹn của bạn bắt đầu từ đây.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
                <a href="index.php?controller=customer&action=rooms" class="px-7 py-3.5 bg-[#C5A25D] hover:bg-[#d8b774] text-[#0F2032] font-bold text-xs uppercase tracking-[0.18em] transition shadow-lg rounded">
                    KHÁM PHÁ PHÒNG NGHỈ
                </a>
                <a href="index.php?controller=customer&action=dining" class="px-7 py-3.5 bg-white/10 hover:bg-white/20 text-white border border-white/30 font-semibold text-xs uppercase tracking-[0.18em] transition rounded">
                    ẨM THỰC & DỊCH VỤ
                </a>
            </div>
        </div>
    </div>

    <!-- FLOATING QUICK SEARCH BAR: KẾ HOẠCH KỲ NGHỈ CỦA BẠN -->
    <div class="max-w-6xl mx-auto -mt-14 relative z-30 px-4">
        <form method="GET" action="index.php" class="bg-[#FAF7F2] text-[#0F2032] border border-[#E8DCB8] shadow-2xl p-6 rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="controller" value="customer" />
            <input type="hidden" name="action" value="rooms" />

            <div>
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Ngày nhận phòng</label>
                <input type="date" name="checkIn" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2.5 bg-white border border-stone-300 rounded text-xs font-semibold focus:outline-none focus:border-[#C5A25D]" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Ngày trả phòng</label>
                <input type="date" name="checkOut" value="<?= date('Y-m-d', strtotime('+2 days')) ?>" class="w-full px-3 py-2.5 bg-white border border-stone-300 rounded text-xs font-semibold focus:outline-none focus:border-[#C5A25D]" />
            </div>
            <div>
                <label class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">Số lượng khách & Phòng</label>
                <select name="guests" class="w-full px-3 py-2.5 bg-white border border-stone-300 rounded text-xs font-semibold focus:outline-none focus:border-[#C5A25D]">
                    <option value="2">2 Người lớn · 1 Trẻ em (1 Phòng)</option>
                    <option value="1">1 Người lớn (1 Phòng)</option>
                    <option value="4">4 Người lớn (2 Phòng Gia Đình)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-3 bg-[#0F2032] hover:bg-[#162B40] text-[#E5C98B] font-bold text-xs uppercase tracking-wider rounded shadow transition-all">
                    Tìm Kiếm Phòng Trống
                </button>
            </div>
        </form>
    </div>
</section>

<!-- CÂU CHUYỆN KHÁCH SẠN (HERITAGE & STORY SECTION) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-6 space-y-5">
            <div class="text-[11px] font-bold text-[#A68238] uppercase tracking-[0.25em]">CÂU CHUYỆN KHÁCH SẠN</div>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-[#0F2032] leading-tight">
                Nơi Giao Thoa Hoàng Hôn Bãi Trường & Lòng Hiếu Khách Phương Bắc
            </h2>
            <div class="w-20 h-0.5 bg-[#C5A25D]"></div>
            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                Tọa lạc tại vị trí đắc địa nhất Bãi Trường — bờ biển ngắm hoàng hôn đẹp nhất đảo ngọc Phú Quốc, Mường Thanh Luxury Phú Quốc mang đến không gian nghỉ dưỡng 5 sao tiêu chuẩn quốc tế kết hợp trọn vẹn giữa nét đẹp thiên nhiên nhiệt đới và sự nồng hậu, tinh tế.
            </p>
            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                Hơn 500 phòng nghỉ sang trọng, hồ bơi vô cực sát biển, chuỗi ẩm thực phong phú cùng trung tâm hội nghị Grand Ballroom sức chứa 1.000 khách sẵn sàng đáp ứng mọi nhu cầu nghỉ dưỡng và hội nghị thượng đỉnh.
            </p>
            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-stone-200">
                <div>
                    <div class="font-display text-3xl font-bold text-[#A68238]">500+</div>
                    <div class="text-[11px] text-stone-500 font-medium">Phòng & Suite 5★</div>
                </div>
                <div>
                    <div class="font-display text-3xl font-bold text-[#A68238]">180°</div>
                    <div class="text-[11px] text-stone-500 font-medium">View Biển Hoàng Hôn</div>
                </div>
                <div>
                    <div class="font-display text-3xl font-bold text-[#A68238]">30%</div>
                    <div class="text-[11px] text-stone-500 font-medium">Cọc Linh Hoạt</div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-6 grid grid-cols-2 gap-4">
            <div class="rounded-2xl overflow-hidden shadow-lg h-72">
                <img src="assets/images/room_dtcv_main_1793420634905.jpg" alt="Phòng nghỉ Mường Thanh" class="w-full h-full object-cover" />
            </div>
            <div class="rounded-2xl overflow-hidden shadow-lg h-72 mt-8">
                <img src="assets/images/room_dkov_suite_1793420636995.jpg" alt="Suite hướng biển" class="w-full h-full object-cover" />
            </div>
        </div>
    </div>
</section>

<!-- HẠNG PHÒNG NỔI BẬT & 3 GÓI GIÁ -->
<section class="bg-white py-20 border-y border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="text-[11px] font-bold text-[#A68238] uppercase tracking-[0.25em]">HẠNG PHÒNG NGHỈ DƯỠNG BIỂN</div>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-[#0F2032]">Không Gian Thượng Lưu Tinh Tế</h2>
            <div class="w-16 h-0.5 bg-[#C5A25D] mx-auto"></div>
            <p class="text-xs text-stone-500 leading-relaxed">
                Khám phá bộ sưu tập 9 hạng phòng nghỉ sang trọng, từ Deluxe ban công phố biển đến các căn Penthouse Presidential Suite xa hoa.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            $topCats = array_slice($categories, 0, 3);
            foreach ($topCats as $cat): 
            ?>
                <div class="bg-[#FAF7F2] rounded-2xl overflow-hidden border border-[#E8DCB8] shadow-sm hover:shadow-xl transition-all flex flex-col justify-between group">
                    <div class="relative h-64 overflow-hidden">
                        <img src="<?= htmlspecialchars($cat['heroImage']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        <span class="absolute top-3 left-3 px-3 py-1 bg-[#0F2032]/85 text-[#E5C98B] text-[10px] font-bold uppercase tracking-wider rounded">
                            <?= $cat['viewType'] === 'OCEAN' ? 'Hướng Biển Bãi Trường' : 'Hướng Phố Dương Tơ' ?>
                        </span>
                        <div class="absolute bottom-3 right-3 px-3 py-1 bg-white/95 text-[#0F2032] text-xs font-bold rounded shadow">
                            Từ <?= Config::formatVnd($cat['basePrice']) ?> / đêm
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="text-[11px] text-[#A68238] font-bold uppercase"><?= $cat['code'] ?> · <?= $cat['vietnameseName'] ?></div>
                            <h3 class="font-display text-2xl font-bold text-[#0F2032] mt-1"><?= htmlspecialchars($cat['name']) ?></h3>
                            <p class="text-xs text-stone-600 mt-2 line-clamp-2"><?= htmlspecialchars($cat['description']) ?></p>

                            <div class="flex items-center gap-4 text-xs text-stone-600 mt-4 pt-3 border-t border-stone-200">
                                <span>📐 <?= $cat['areaSqm'] ?>m²</span>
                                <span>👥 <?= $cat['maxAdults'] ?> Khách</span>
                                <span>🛏 <?= $cat['bedType'] ?></span>
                            </div>
                        </div>

                        <a href="index.php?controller=customer&action=rooms&roomId=<?= $cat['id'] ?>" class="block text-center py-3 bg-[#0F2032] hover:bg-[#162B40] text-[#E5C98B] font-bold text-xs uppercase tracking-wider rounded-lg shadow transition-all">
                            Xem Chi Tiết & Đặt Phòng (Cọc 30%)
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center pt-4">
            <a href="index.php?controller=customer&action=rooms" class="inline-flex items-center gap-2 px-8 py-3.5 bg-[#C5A25D] hover:bg-[#d8b774] text-[#0F2032] text-xs font-bold uppercase tracking-widest rounded-lg shadow-md transition-all">
                <span>XEM ĐẦY ĐỦ 9 HẠNG PHÒNG & 3 GÓI GIÁ</span>
                <span>→</span>
            </a>
        </div>
    </div>
</section>

<!-- ẨM THỰC SONASEA & SPA BANNER -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="relative h-96 rounded-3xl overflow-hidden shadow-xl group">
            <img src="assets/images/service_dining_spa_1793420638350.jpg" alt="Ẩm thực Sonasea" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F2032] via-[#0F2032]/40 to-transparent flex flex-col justify-end p-8 text-white space-y-2">
                <span class="text-[10px] tracking-[0.2em] uppercase font-bold text-[#E5C98B]">ẨM THỰC 5 SAO</span>
                <h3 class="font-display text-3xl font-bold">Nhà Hàng Buffet & Hải Sản Sonasea</h3>
                <p class="text-xs text-stone-200">Thưởng thức hải sản tươi sống Phú Quốc cùng buffet sáng hơn 60 món Á - Âu nhìn ra hồ bơi vô cực.</p>
                <div class="pt-2">
                    <a href="index.php?controller=customer&action=dining" class="inline-block px-5 py-2 bg-white/20 hover:bg-white/30 text-white font-bold text-xs uppercase tracking-wider rounded border border-white/40">
                        Khám Phá Menu
                    </a>
                </div>
            </div>
        </div>

        <div class="relative h-96 rounded-3xl overflow-hidden shadow-xl group">
            <img src="assets/images/room_bathroom_luxury_1793420638319.jpg" alt="Pearl Spa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F2032] via-[#0F2032]/40 to-transparent flex flex-col justify-end p-8 text-white space-y-2">
                <span class="text-[10px] tracking-[0.2em] uppercase font-bold text-[#E5C98B]">CHĂM SÓC SỨC KHỎE</span>
                <h3 class="font-display text-3xl font-bold">Pearl Spa & Xông Hơi Đá Muối</h3>
                <p class="text-xs text-stone-200">Liệu trình trị liệu tinh dầu ngọc trai và xông hơi đá muối khoáng nóng Himalaya thư giãn tuyệt đối.</p>
                <div class="pt-2">
                    <a href="index.php?controller=customer&action=spa" class="inline-block px-5 py-2 bg-white/20 hover:bg-white/30 text-white font-bold text-xs uppercase tracking-wider rounded border border-white/40">
                        Khám Phá Liệu Trình
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
