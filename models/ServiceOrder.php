<?php
/**
 * MƯỜNG THANH LUXURY PHÚ QUỐC — PHP OOP MVC
 * Models/ServiceOrder.php
 */

namespace Models;

use Core\BaseModel;
use Core\Session;

class ServiceOrder extends BaseModel {
    public function getServices(): array {
        return Session::get('services', []);
    }

    public function getAll(): array {
        return Session::get('serviceOrders', []);
    }

    public function findService(string $name): ?array {
        foreach ($this->getServices() as $s) {
            if ($s['name'] === $name) return $s;
        }
        return null;
    }

    public function create(array $data): array {
        $orders = $this->getAll();
        // Chuẩn hóa số lượng MỘT lần rồi dùng cho cả lưu trữ lẫn tính tiền
        $qty = max(1, (int)($data['quantity'] ?? 1));
        $unit = (int)$data['unitPrice'];
        $newOrder = [
            'id' => 'so_' . uniqid(),
            'bookingCode' => $data['bookingCode'] ?? 'WALK_IN',
            'roomNumber' => $data['roomNumber'],
            'guestName' => $data['guestName'],
            'serviceName' => $data['serviceName'],
            'quantity' => $qty,
            'unitPrice' => $unit,
            'totalPrice' => $unit * $qty,
            'status' => 'confirmed',
            'scheduledTime' => $data['scheduledTime'] ?? date('H:i'),
            'note' => $data['note'] ?? 'Phục vụ tận phòng',
            'createdAt' => date('Y-m-d H:i:s')
        ];
        array_unshift($orders, $newOrder);
        Session::set('serviceOrders', $orders);
        return $newOrder;
    }

    public function updateStatus(string $orderId, string $status): bool {
        $orders = $this->getAll();
        foreach ($orders as &$o) {
            if ($o['id'] === $orderId) {
                $o['status'] = $status;
                Session::set('serviceOrders', $orders);
                return true;
            }
        }
        return false;
    }
}
