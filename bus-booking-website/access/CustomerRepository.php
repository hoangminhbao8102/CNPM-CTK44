<?php
require_once 'Database.php';

class CustomerRepository {
    public function addCustomer($customer) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO KHACH_HANG (HoTen, Email, SoDienThoai) VALUES (?, ?, ?)");
        return $stmt->execute([$customer->HoTen, $customer->Email, $customer->SoDienThoai]);
    }
}
?>
