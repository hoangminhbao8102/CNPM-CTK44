<?php
require_once __DIR__ . '/../access/TicketRepository.php';
require_once __DIR__ . '/../models/Ticket.php';

class TicketService {
    public function bookTicket($MaLichTrinh, $MaKH, $SoGhe) {
        $ticket = new Ticket();
        $ticket->MaLichTrinh = $MaLichTrinh;
        $ticket->MaKH = $MaKH;
        $ticket->SoGhe = $SoGhe;
        $ticket->TrangThai = 'Đã đặt';

        $repo = new TicketRepository();
        return $repo->save($ticket);
    }

    public function cancelTicket($MaVe) {
        $repo = new TicketRepository();
        return $repo->cancel($MaVe);
    }
}
?>
