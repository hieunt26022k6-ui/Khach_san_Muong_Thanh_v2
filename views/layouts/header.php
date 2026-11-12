<?php
use Config\Config;
use Core\Session;

$baseUrl = Config::getBaseUrl();
$currentUser = Session::get('currentUser');
$successFlash = Session::flash('success');
$errorFlash = Session::flash('error');
$activeNav = $activeNav ?? 'home';
$isStaffPortal = $isStaffPortal ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? Config::APP_NAME) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { 300: '#E5C98B', 400: '#D8B774', 500: '#C5A25D', 600: '#A68238', 700: '#7E6023' },
                        navy: { 800: '#162B40', 900: '#0F2032', 950: '#0A1522' },
                        sand: { 50: '#FAF7F2', 100: '#F4ECE0', 200: '#E8DCB8', 300: '#D5C4A1' }
                    },
                    fontFamily: {
                        serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FAF7F2; color: #1C1917; }
        h1, h2, h3, .font-display { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#FAF7F2] text-stone-900 selection:bg-[#C5A25D] selection:text-white">

<?php if (!$isStaffPortal): ?>
<!-- TOP FAST-ROLE SWITCHER BAR (DÀNH CHO KIỂM THỬ ĐỒ ÁN / GIẢNG VIÊN) -->
<div class="bg-[#0A1522] text-stone-300 text-[11px] py-1.5 px-4 sm:px-8 border-b border-[#C5A25D]/20">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-4 text-stone-400">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#C5A25D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <?= Config::HOTEL_ADDRESS ?>
            </span>
            <span class="hidden md:inline text-stone-600">|</span>
            <span class="hidden md:inline">Hotline: <strong class="text-white"><?= Config::HOTEL_HOTLINE ?></strong></span>
        </div>

        <?php if (Config::DEMO_MODE): ?>
        <div class="flex items-center gap-2">
            <span class="text-stone-400 text-[10px] uppercase tracking-wider hidden md:inline">Chuyển vai trò nhanh:</span>
            <a href="index.php?controller=auth&action=switchRole&role=customer" class="px-2 py-0.5 rounded text-[10px] <?= ($currentUser['role'] ?? '') === 'customer' ? 'bg-[#C5A25D] text-[#0F2032] font-bold' : 'bg-white/10 text-stone-300 hover:text-white' ?>">
                Khách hàng
            </a>
            <a href="index.php?controller=auth&action=switchRole&role=receptionist" class="px-2 py-0.5 rounded text-[10px] <?= ($currentUser['role'] ?? '') === 'receptionist' ? 'bg-[#C5A25D] text-[#0F2032] font-bold' : 'bg-white/10 text-stone-300 hover:text-white' ?>">
                Lễ tân (PMS)
            </a>
            <a href="index.php?controller=auth&action=switchRole&role=admin" class="px-2 py-0.5 rounded text-[10px] <?= ($currentUser['role'] ?? '') === 'admin' ? 'bg-[#C5A25D] text-[#0F2032] font-bold' : 'bg-white/10 text-stone-300 hover:text-white' ?>">
                Quản trị viên
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- MAIN LUXURY HEADER EXACTLY MATCHING REACT APP -->
<header class="sticky top-0 z-40 bg-[#0F2032] text-white border-b border-slate-800 px-4 sm:px-8 h-20 flex items-center justify-between gap-4">
    <!-- LEFT NAV LINKS: PHÒNG VÀ SUITE · ẨM THỰC · SPA & CHĂM SÓC SỨC KHỎE -->
    <nav class="hidden lg:flex items-center gap-7 text-xs font-medium tracking-[0.14em] uppercase">
        <a href="index.php?controller=customer&action=rooms" class="py-1 transition-colors whitespace-nowrap shrink-0 <?= $activeNav === 'rooms' ? 'text-[#C5A25D] border-b-2 border-[#C5A25D]' : 'text-slate-200 hover:text-[#C5A25D]' ?>">
            PHÒNG VÀ SUITE
        </a>
        <a href="index.php?controller=customer&action=dining" class="py-1 transition-colors whitespace-nowrap shrink-0 <?= $activeNav === 'dining' ? 'text-[#C5A25D] border-b-2 border-[#C5A25D]' : 'text-slate-200 hover:text-[#C5A25D]' ?>">
            ẨM THỰC
        </a>
        <a href="index.php?controller=customer&action=spa" class="py-1 transition-colors whitespace-nowrap shrink-0 <?= $activeNav === 'spa' ? 'text-[#C5A25D] border-b-2 border-[#C5A25D]' : 'text-slate-200 hover:text-[#C5A25D]' ?>">
            SPA & CHĂM SÓC SỨC KHỎE
        </a>
    </nav>

    <!-- CENTER GOLDEN CROWN EMBLEM LOGO -->
    <a href="index.php?controller=customer&action=index" class="flex flex-col items-center justify-center shrink-0 group">
        <svg class="w-5 h-4 text-[#C5A25D] mb-0.5" viewBox="0 0 24 18" fill="none" stroke="currentColor" strokeWidth="1.8">
            <path d="M2 15h20M3 12l2-8 5 4 2-6 2 6 5-4 2 8H3z" />
        </svg>
        <div class="text-[#C5A25D] font-display text-lg sm:text-xl font-semibold tracking-[0.22em] leading-none">
            MƯỜNG THANH
        </div>
        <div class="text-[8px] tracking-[0.28em] text-slate-300 uppercase mt-1">
            LUXURY PHÚ QUỐC
        </div>
        <div class="text-[8px] tracking-[0.25em] text-[#C5A25D] leading-none mt-0.5">
            ★★★★★
        </div>
    </a>

    <!-- RIGHT NAV LINKS: HỘI NGHỊ · TRA CỨU · ROLES · ĐĂNG NHẬP / PROFILE · ĐẶT PHÒNG -->
    <div class="flex items-center gap-3.5 lg:gap-5 text-xs tracking-[0.12em] uppercase shrink-0">
        <a href="index.php?controller=customer&action=conference" class="hidden xl:inline-block py-1 transition-colors <?= $activeNav === 'conference' ? 'text-[#C5A25D] border-b-2 border-[#C5A25D]' : 'text-slate-200 hover:text-[#C5A25D]' ?>">
            HỘI NGHỊ
        </a>

        <button type="button" onclick="document.getElementById('lookupModal').classList.remove('hidden')" class="hidden md:flex items-center gap-1.5 py-1 text-slate-200 hover:text-[#C5A25D] transition-colors cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span>TRA CỨU</span>
        </button>

        <!-- Nút truy cập nghiệp vụ dành cho nhân viên khi đăng nhập -->
        <?php if ($currentUser && $currentUser['role'] === 'receptionist'): ?>
            <a href="index.php?controller=receptionist&action=index" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] shadow-xs">
                <span>🛎️ Bàn Lễ tân PMS</span>
            </a>
        <?php endif; ?>

        <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
            <a href="index.php?controller=admin&action=index" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-500 text-white font-bold text-[11px] shadow-xs">
                <span>👑 Trang Quản trị</span>
            </a>
        <?php endif; ?>

        <?php if ($currentUser && $currentUser['role'] === 'customer'): ?>
            <a href="index.php?controller=customer&action=portal" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-[#C5A25D] border border-[#C5A25D]/50 font-semibold text-[11px]">
                <span>📋 Đơn của tôi</span>
            </a>
        <?php endif; ?>

        <!-- USER PROFILE OR LOGIN -->
        <?php if ($currentUser): ?>
            <div class="flex items-center gap-2">
                <a href="<?= $currentUser['role'] === 'admin' ? 'index.php?controller=admin&action=index' : ($currentUser['role'] === 'receptionist' ? 'index.php?controller=receptionist&action=index' : 'index.php?controller=customer&action=portal') ?>" class="flex items-center gap-2 px-2.5 py-1.5 rounded-full bg-slate-800/95 border border-[#C5A25D]/60 text-white hover:border-[#C5A25D] transition-all">
                    <div class="w-6 h-6 rounded-full bg-[#C5A25D] text-[#0F243C] flex items-center justify-center shrink-0 font-bold text-xs">
                        <?= mb_substr($currentUser['fullName'], 0, 1) ?>
                    </div>
                    <div class="hidden sm:flex flex-col text-left leading-tight">
                        <span class="font-semibold normal-case tracking-normal text-xs text-slate-100 max-w-[120px] truncate">
                            <?= htmlspecialchars($currentUser['fullName']) ?>
                        </span>
                        <span class="text-[9px] text-[#C5A25D] font-medium tracking-wide uppercase">
                            <?= $currentUser['role'] === 'admin' ? 'Quản trị viên' : ($currentUser['role'] === 'receptionist' ? 'Lễ tân 5★' : 'Hội viên Mường Thanh') ?>
                        </span>
                    </div>
                </a>
                <a href="index.php?controller=auth&action=logout" class="hidden sm:inline-block text-slate-300 hover:text-red-400 text-[11px] uppercase tracking-wider">
                    ĐĂNG XUẤT
                </a>
            </div>
        <?php else: ?>
            <button type="button" onclick="document.getElementById('authModal').classList.remove('hidden')" class="text-slate-200 hover:text-[#C5A25D] transition-colors cursor-pointer font-semibold">
                ĐĂNG NHẬP
            </button>
        <?php endif; ?>

        <!-- GOLDEN ĐẶT PHÒNG BUTTON -->
        <a href="index.php?controller=customer&action=rooms" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-[#C5A25D] hover:bg-[#b08d48] text-[#0B192C] font-bold tracking-wider transition-colors cursor-pointer shadow-xs whitespace-nowrap">
            ĐẶT PHÒNG
        </a>
    </div>
</header>
<?php endif; ?>

<!-- TOAST FLASH NOTIFICATIONS -->
<?php if ($successFlash): ?>
    <div class="bg-emerald-600 text-white text-xs sm:text-sm px-4 py-2.5 text-center font-medium shadow-md flex items-center justify-center gap-2">
        <span>✓</span>
        <span><?= htmlspecialchars($successFlash) ?></span>
    </div>
<?php endif; ?>

<?php if ($errorFlash): ?>
    <div class="bg-rose-600 text-white text-xs sm:text-sm px-4 py-2.5 text-center font-medium shadow-md flex items-center justify-center gap-2">
        <span>⚠</span>
        <span><?= htmlspecialchars($errorFlash) ?></span>
    </div>
<?php endif; ?>

<main class="flex-1">
