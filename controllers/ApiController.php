<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Controllers/ApiController.php
 */

namespace Controllers;

use Core\BaseController;
use Models\Room;
use Models\Booking;
use Models\Voucher;

class ApiController extends BaseController {
    public function validateVoucher(): void {
        $code = (string)($_GET['code'] ?? '');
        $subTotal = max(0, (int)($_GET['total'] ?? 0));

        // [SỬA KH-E02] Dùng chung lớp Models\Voucher với Controller đặt phòng
        $res = Voucher::apply($code, $subTotal);
        if ($res['valid']) {
            $this->json(['valid' => true, 'discount' => $res['discount'], 'label' => $res['label']]);
        }
        $this->json(['valid' => false, 'message' => 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.'], 400);
    }
}
