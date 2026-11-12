<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/Report.php
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class Report extends BaseModel {
    public function getAnalytics(): array {
        $bookings = Session::get('bookings', []);
        $rooms = Session::get('physicalRooms', []);
        $serviceOrders = Session::get('serviceOrders', []);

        $totalRevenue = 0;
        $totalDeposit = 0;
        $confirmedCount = 0;
        $checkedInCount = 0;

        foreach ($bookings as $b) {
            $totalRevenue += $b['totalAmount'];
            $totalDeposit += $b['depositPaid'];
            if ($b['status'] === 'confirmed') $confirmedCount++;
            if ($b['status'] === 'checked_in') $checkedInCount++;
        }

        $serviceRevenue = 0;
        foreach ($serviceOrders as $so) {
            $serviceRevenue += $so['totalPrice'];
        }

        $totalRooms = count($rooms);
        $occupiedRooms = 0;
        foreach ($rooms as $r) {
            if ($r['status'] === 'occupied') $occupiedRooms++;
        }

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        return [
            'totalRevenue' => $totalRevenue + $serviceRevenue,
            'roomRevenue' => $totalRevenue,
            'depositRevenue' => $totalDeposit,
            'serviceRevenue' => $serviceRevenue,
            'totalBookings' => count($bookings),
            'confirmedBookings' => $confirmedCount,
            'checkedInBookings' => $checkedInCount,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'occupancyRate' => $occupancyRate,
            'adr' => $totalRooms > 0 ? round($totalRevenue / max(1, count($bookings))) : 0,
            'revpar' => $totalRooms > 0 ? round($totalRevenue / $totalRooms) : 0,
        ];
    }

    /**
     * [SỬA LẦN 2] Doanh thu 7 ngày gần nhất tính từ đơn đặt phòng + đơn dịch vụ thật,
     * thay cho mảng số minh hoạ [12.4, 15.8, …] hard-code trong View.
     * Đơn được tính vào ngày khách nhận phòng (checkInDate).
     */
    public function getDailyRevenue(int $days = 7): array {
        $bookings = Session::get('bookings', []);
        $serviceOrders = Session::get('serviceOrders', []);

        $buckets = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $key = date('Y-m-d', strtotime("-{$i} day"));
            $buckets[$key] = ['date' => $key, 'revenue' => 0, 'bookings' => 0];
        }

        foreach ($bookings as $b) {
            $day = substr((string)($b['checkInDate'] ?? ''), 0, 10);
            if (!isset($buckets[$day])) continue;
            $buckets[$day]['revenue'] += (int)($b['totalAmount'] ?? 0);
            $buckets[$day]['bookings']++;
        }
        foreach ($serviceOrders as $so) {
            $day = substr((string)($so['createdAt'] ?? $so['date'] ?? ''), 0, 10);
            if (!isset($buckets[$day])) continue;
            $buckets[$day]['revenue'] += (int)($so['totalPrice'] ?? 0);
        }

        $max = 0;
        foreach ($buckets as $bucket) {
            $max = max($max, $bucket['revenue']);
        }

        $result = [];
        foreach ($buckets as $bucket) {
            $bucket['percent'] = $max > 0 ? (int)round($bucket['revenue'] / $max * 100) : 0;
            $result[] = $bucket;
        }
        return $result;
    }
}
