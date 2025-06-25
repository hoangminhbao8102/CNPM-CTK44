<?php
require_once 'Database.php';

class RouteRepository {
    public function getAllRoutes() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM TUYEN_XE ORDER BY MaTuyen DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRoute($route) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO TUYEN_XE (MaTuyen, DiemDi, DiemDen) VALUES (?, ?, ?)");
        return $stmt->execute([$route->MaTuyen, $route->DiemDi, $route->DiemDen]);
    }
}
?>
