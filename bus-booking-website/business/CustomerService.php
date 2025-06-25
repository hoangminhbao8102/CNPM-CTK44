<?php
require_once __DIR__ . '/../access/CustomerRepository.php';
require_once __DIR__ . '/../models/Customer.php';

class CustomerService {
    public function register($name, $email, $phone) {
        $cus = new Customer();
        $cus->HoTen = $name;
        $cus->Email = $email;
        $cus->SoDienThoai = $phone;

        $repo = new CustomerRepository();
        return $repo->addCustomer($cus);
    }
}
?>
