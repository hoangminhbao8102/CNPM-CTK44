<?php
require_once 'Database.php';

class TicketRepository {
    public function save($ticket) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO VE (MaLichTrinh, MaKH, SoGhe, TrangThai) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$ticket->MaLichTrinh, $ticket->MaKH, $ticket->SoGhe, $ticket->TrangThai]);
    }

    public function cancel($ticketId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE VE SET TrangThai = 'Đã hủy' WHERE MaVe = ?");
        return $stmt->execute([$ticketId]);
    }
}
?>
