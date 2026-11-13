<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/Voucher.php
 *
 * MỘT nguồn duy nhất cho quy tắc mã ưu đãi. Giao diện (qua API) và Controller đặt phòng
 * cùng dùng lớp này, nên máy chủ tự tính mức giảm thay vì tin con số do trình duyệt gửi lên.
 */

namespace Models;

class Voucher {
    private const RULES = [
        'MUONGTHANH10' => ['percent' => 10,     'label' => 'Ưu đãi đặt sớm 10%'],
        'VIPPHUQUOC20' => ['percent' => 20,     'label' => 'Đặc quyền thành viên VIP 20%'],
        'HOANGHON30'   => ['amount'  => 300000, 'label' => 'Voucher hoàng hôn Bãi Trường -300k'],
    ];

    /** @return array{valid:bool,discount:int,label:string} */
    public static function apply(string $code, int $subTotal): array {
        $code = strtoupper(trim($code));
        if ($code === '' || !isset(self::RULES[$code]) || $subTotal <= 0) {
            return ['valid' => false, 'discount' => 0, 'label' => ''];
        }
        $rule = self::RULES[$code];
        $discount = isset($rule['percent'])
            ? (int)round($subTotal * $rule['percent'] / 100)
            : (int)$rule['amount'];
        // Không bao giờ giảm quá giá trị đơn
        $discount = max(0, min($discount, $subTotal));
        return ['valid' => true, 'discount' => $discount, 'label' => $rule['label']];
    }
}
