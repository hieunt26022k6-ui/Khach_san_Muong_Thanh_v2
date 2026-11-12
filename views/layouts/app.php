<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * View Layout Chính: Kết nối dữ liệu từ Models/Controllers PHP với giao diện 5 sao
 */
use Config\Config;

$pageTitle = $pageTitle ?? (Config::APP_NAME . ' — Đặt Phòng & Quản Trị Khách Sạn');
$description = Config::APP_TAGLINE;
$stateJson = json_encode($serverState ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// Tự động nhận diện tệp JS và CSS đóng gói mới nhất trong thư mục assets
$assetsDir = dirname(__DIR__, 2) . '/assets';
$jsFiles = glob($assetsDir . '/index-*.js');
$jsFile = !empty($jsFiles) ? basename($jsFiles[0]) : 'index-BgY9yGQX.js';

$cssFiles = glob($assetsDir . '/index-*.css');
$cssFile = !empty($cssFiles) ? basename($cssFiles[0]) : 'index-BmDaYg5o.css';
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    
    <!-- Google Fonts: Cormorant Garamond & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS đóng gói độc lập trong thư mục assets/ (Chạy offline trên XAMPP) -->
    <link rel="stylesheet" crossorigin href="./assets/<?= htmlspecialchars($cssFile) ?>">
    
    <!-- PHP Model State Hydration: Tiêm trực tiếp dữ liệu từ PHP OOP Models vào Runtime -->
    <script>
      window.__MT_PHP_RUNTIME__ = true;
      window.__MT_SERVER_STATE__ = <?= $stateJson ?>;

      /* [SỬA LẦN 2] Chặn leo thang đặc quyền qua localStorage.
         Giao diện React đọc currentUser từ localStorage trước, nên ai sửa giá trị đó
         thành {"role":"admin"} là vào được bảng quản trị. Đoạn này chạy TRƯỚC bundle:
         - phiên PHP chưa đăng nhập  -> xoá currentUser trong localStorage, ép về giao diện khách
         - phiên PHP có đăng nhập    -> đồng bộ lại đúng người dùng do máy chủ trả về
         - luôn xoá trường password khỏi bản sao localStorage của users              */
      (function () {
        var K = 'muong_thanh_luxury_pq_v7_';
        var state = window.__MT_SERVER_STATE__ || {};
        try {
          var rawUsers = localStorage.getItem(K + 'users');
          if (rawUsers) {
            var arr = JSON.parse(rawUsers);
            if (Array.isArray(arr)) {
              var cleaned = arr.map(function (u) { var c = Object.assign({}, u); delete c.password; return c; });
              localStorage.setItem(K + 'users', JSON.stringify(cleaned));
            }
          }
          var serverUser = state.currentUser || null;
          localStorage.setItem(K + 'currentUser', JSON.stringify(serverUser));
          if (!serverUser) {
            localStorage.setItem(K + 'activeView', JSON.stringify('customer'));
          }
        } catch (e) { /* trình duyệt chặn localStorage: bỏ qua */ }
      })();
    </script>
    <script type="module" crossorigin src="./assets/<?= htmlspecialchars($jsFile) ?>"></script>
  </head>
  <body class="bg-[#FAF7F2] text-stone-900 antialiased selection:bg-amber-800 selection:text-white">
    <!-- Vùng hiển thị ứng dụng tương tác 3 phân quyền 5 sao -->
    <div id="root"></div>

    <!-- [SỬA LẦN 2] Lưới an toàn: bundle React không có Error Boundary nên lỗi là trắng trang.
         Nếu sau 5 giây #root vẫn rỗng, hiện thông báo + đường dẫn sang giao diện PHP thuần. -->
    <script>
      setTimeout(function () {
        var root = document.getElementById('root');
        if (!root || root.innerHTML.trim() !== '') return;
        root.innerHTML = '<div style="max-width:560px;margin:80px auto;padding:28px;font-family:sans-serif;' +
          'background:#fff;border:1px solid #E8DCB8;border-radius:16px;text-align:center">' +
          '<h2 style="color:#0F2032;margin:0 0 8px">Không tải được giao diện tương tác</h2>' +
          '<p style="color:#555;font-size:14px">Trình duyệt có thể đang chặn JavaScript hoặc dữ liệu cục bộ.<br>' +
          'Vui lòng tải lại trang (Ctrl + F5), hoặc dùng giao diện HTML/PHP thuần bên dưới.</p>' +
          '<p><a href="index.php?view_mode=classic_php" style="display:inline-block;margin-top:8px;padding:10px 18px;' +
          'background:#0F2032;color:#E5C98B;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700">' +
          'Mở giao diện HTML/PHP</a></p></div>';
      }, 5000);
    </script>

    <noscript>
      <div style="padding: 24px; font-family: sans-serif; text-align: center; background: #0F2032; color: #fff;">
        <h2 style="color: #C5A25D;">Mường Thanh Luxury Phú Quốc</h2>
        <p>Vui lòng kích hoạt JavaScript trên trình duyệt để trải nghiệm toàn bộ tính năng đặt phòng trực tuyến và quản trị khách sạn.</p>
        <p><a href="index.php?view_mode=classic_php" style="color: #E5C98B;">Nhấp vào đây để xem chế độ HTML PHP tĩnh</a></p>
      </div>
    </noscript>
  </body>
</html>
