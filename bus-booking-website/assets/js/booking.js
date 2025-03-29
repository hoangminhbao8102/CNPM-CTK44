const seats = document.querySelectorAll('.seat:not(.booked)');
    const seatInput = document.getElementById('seatInput');

    seats.forEach(seat => {
        seat.addEventListener('click', () => {
            seats.forEach(s => s.classList.remove('selected'));
            seat.classList.add('selected');
            seatInput.value = seat.dataset.seat;
        });
    });

    document.getElementById("bookingForm").addEventListener("submit", function(e) {
        if (!seatInput.value) {
            alert("Vui lòng chọn một ghế trước khi đặt vé.");
            e.preventDefault();
        }
    });