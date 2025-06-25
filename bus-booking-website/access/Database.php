<?php
require_once __DIR__ . '/../config.php';

class Database {
    public static function getConnection() {
        $serverName = DB_HOST;
        $database = DB_NAME;
        $username = DB_USER;
        $password = DB_PASS;

        $dsn = "sqlsrv:Server=$serverName;Database=$database";

        try {
            return new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            die("Lỗi kết nối SQL Server: " . $e->getMessage());
        }
    }
}
?>
