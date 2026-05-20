<?php
require_once 'db.php';

if (!isset($_GET['show_id']) || !is_numeric($_GET['show_id'])) {
    die("Invalid Show ID.");
}

$show_id = (int)$_GET['show_id'];

// Fetch show and movie details using a join
$stmt = $conn->prepare("
    SELECT s.*, m.title, m.poster_image, m.genre
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Show not found.");
}

$show = $result->fetch_assoc();

function get_booked_seats($conn, $show_id) {
    $booked_seats = [];
    $booked_stmt = $conn->prepare("SELECT seat_numbers FROM bookings WHERE show_id = ?");
    if ($booked_stmt) {
        $booked_stmt->bind_param("i", $show_id);
        $booked_stmt->execute();
        $booked_result = $booked_stmt->get_result();

        while ($row = $booked_result->fetch_assoc()) {
            if (!empty($row['seat_numbers'])) {
                $seats = array_map('trim', explode(',', $row['seat_numbers']));
                $booked_seats = array_merge($booked_seats, $seats);
            }
        }

        $booked_stmt->close();
    }

    return array_values(array_unique(array_filter($booked_seats)));
}

$booking_success = false;
$booking_error = '';
$last_booking_seats = [];
$last_booking_total = 0.0;

$booked_seats = get_booked_seats($conn, $show_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats_raw = trim($_POST['selected_seats'] ?? '');
    $selected_seats = array_values(array_filter(array_map('trim', explode(',', $selected_seats_raw))));

    if (empty($selected_seats)) {
        $booking_error = 'Please select at least one seat.';
    } else {
        $duplicate_seats = array_intersect($selected_seats, $booked_seats);

        if (!empty($duplicate_seats)) {
            $booking_error = 'Some selected seats are already booked. Please choose different seats.';
        } else {
            $ticket_count = count($selected_seats);
            $ticket_price = (float) $show['ticket_price'];
            $total_amount = $ticket_price * $ticket_count;
            $seat_numbers = implode(',', $selected_seats);

            $customer_name = 'Guest';
            $customer_email = 'guest@example.com';

            // NOTE: The bookings table should include a `seat_numbers` column to store selected seats.
            $column_stmt = $conn->prepare("SHOW COLUMNS FROM bookings LIKE 'seat_numbers'");
            $seat_column_exists = false;

            if ($column_stmt) {
                $column_stmt->execute();
                $column_result = $column_stmt->get_result();
                $seat_column_exists = $column_result && $column_result->num_rows > 0;
                $column_stmt->close();
            }

            if (!$seat_column_exists) {
                $booking_error = 'Booking storage is not ready. Please update the database schema.';
            } else {
                $insert_stmt = $conn->prepare(
                    "INSERT INTO bookings (show_id, customer_name, customer_email, seats_booked, seat_numbers, total_amount)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );

                if ($insert_stmt) {
                    $insert_stmt->bind_param(
                        "issisd",
                        $show_id,
                        $customer_name,
                        $customer_email,
                        $ticket_count,
                        $seat_numbers,
                        $total_amount
                    );

                    if ($insert_stmt->execute()) {
                        $booking_success = true;
                        $last_booking_seats = $selected_seats;
                        $last_booking_total = $total_amount;
                        $booked_seats = get_booked_seats($conn, $show_id);
                    } else {
                        $booking_error = 'Booking failed. Please try again.';
                    }

                    $insert_stmt->close();
                } else {
                    $booking_error = 'Booking failed. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Seats - <?php echo htmlspecialchars($show['title']); ?> | CineBook</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Include Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <main class="booking-container">
        <a href="details.php?id=<?php echo $show['movie_id']; ?>" class="btn-outline">&larr; Back to Movie</a>

        <div style="text-align: center; margin-bottom: 30px; margin-top: 20px;">
            <h2 style="font-size: 2.5rem; margin-bottom: 10px;"><?php echo htmlspecialchars($show['title']); ?></h2>
            <p style="color: #cbd5e1; font-size: 1.1rem;">
                <?php echo htmlspecialchars($show['genre']); ?> | 
                <?php echo date("M d, Y", strtotime($show['show_time'])); ?> at 
                <?php echo date("h:i A", strtotime($show['show_time'])); ?>
            </p>
        </div>

        <div class="screen">SCREEN</div>

        <div class="seat-layout" id="seat-map">
            <?php
            $rows = ['A', 'B', 'C', 'D', 'E'];
            foreach ($rows as $row) {
                echo '<div class="seat-row">';
                echo '<span class="seat-row-label">' . $row . '</span>';
                for ($i = 1; $i <= 8; $i++) {
                    $seat_id = $row . $i;
                    $is_booked = in_array($seat_id, $booked_seats, true);
                    $seat_class = $is_booked ? ' booked' : ' available';
                    echo '<div class="seat' . $seat_class . '" data-seat="' . $seat_id . '"></div>';
                }
                echo '</div>';
            }
            ?>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="seat available" style="cursor: default; transform: none;"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="seat selected" style="cursor: default; transform: none;"></div>
                <span>Selected</span>
            </div>
            <div class="legend-item">
                <div class="seat booked" style="cursor: default; transform: none;"></div>
                <span>Booked</span>
            </div>
        </div>

        <form class="booking-summary" id="booking-form" method="POST">
            <?php if (!empty($booking_error)) { ?>
                <p class="booking-error">
                    <?php echo htmlspecialchars($booking_error); ?>
                </p>
            <?php } ?>
            <p style="font-size: 1.2rem; margin-bottom: 10px;">Selected Seats: <strong id="selected-seats-list" style="color: #3b82f6;">None</strong></p>
            <p style="font-size: 1.1rem; margin-bottom: 10px; color: #cbd5e1;">Tickets: <span id="ticket-count">0</span> x $<?php echo number_format($show['ticket_price'], 2); ?></p>
            <h3 style="font-size: 1.8rem; margin: 20px 0;">Total Amount: $<span id="total-amount">0.00</span></h3>

            <input type="hidden" name="selected_seats" id="selected-seats-input" value="">
            <input type="hidden" name="ticket_count" id="ticket-count-input" value="0">
            <input type="hidden" name="total_amount" id="total-amount-input" value="0">
            
            <button id="proceed-btn" class="confirm-btn" type="submit" disabled>Proceed to Booking</button>
        </form>
    </main>

    <?php if ($booking_success) { ?>
        <div class="modal-overlay" id="booking-success-modal">
            <div class="modal-card">
                <h3>Booking Successful</h3>
                <p>Seats: <?php echo htmlspecialchars(implode(', ', $last_booking_seats)); ?></p>
                <p>Total Amount: $<?php echo number_format($last_booking_total, 2); ?></p>
                <button type="button" class="btn btn-primary" id="close-success-modal">Close</button>
            </div>
        </div>
    <?php } ?>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Seat Booking JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const seats = document.querySelectorAll('.seat.available');
            const selectedSeatsList = document.getElementById('selected-seats-list');
            const ticketCount = document.getElementById('ticket-count');
            const totalAmount = document.getElementById('total-amount');
            const proceedBtn = document.getElementById('proceed-btn');
            const selectedSeatsInput = document.getElementById('selected-seats-input');
            const ticketCountInput = document.getElementById('ticket-count-input');
            const totalAmountInput = document.getElementById('total-amount-input');
            const ticketPrice = <?php echo (float)$show['ticket_price']; ?>;

            let selectedSeats = [];

            seats.forEach(seat => {
                seat.addEventListener('click', () => {
                    const seatId = seat.getAttribute('data-seat');
                    
                    if (seat.classList.contains('selected')) {
                        seat.classList.remove('selected');
                        selectedSeats = selectedSeats.filter(id => id !== seatId);
                    } else {
                        seat.classList.add('selected');
                        selectedSeats.push(seatId);
                    }

                    updateSummary();
                });
            });

            function updateSummary() {
                if (selectedSeats.length > 0) {
                    selectedSeatsList.textContent = selectedSeats.join(', ');
                    ticketCount.textContent = selectedSeats.length;
                    totalAmount.textContent = (selectedSeats.length * ticketPrice).toFixed(2);
                    proceedBtn.disabled = false;

                    selectedSeatsInput.value = selectedSeats.join(', ');
                    ticketCountInput.value = selectedSeats.length;
                    totalAmountInput.value = (selectedSeats.length * ticketPrice).toFixed(2);
                    
                    // Optional visual feedback when enabled
                    proceedBtn.style.opacity = '1';
                    proceedBtn.style.cursor = 'pointer';
                } else {
                    selectedSeatsList.textContent = 'None';
                    ticketCount.textContent = '0';
                    totalAmount.textContent = '0.00';
                    proceedBtn.disabled = true;

                    selectedSeatsInput.value = '';
                    ticketCountInput.value = '0';
                    totalAmountInput.value = '0';
                    
                    // Disabled state visual feedback
                    proceedBtn.style.opacity = '0.5';
                    proceedBtn.style.cursor = 'not-allowed';
                }
            }
            
            // Initial call to set disabled button styling correctly
            updateSummary();

            const successModal = document.getElementById('booking-success-modal');
            const closeSuccessModal = document.getElementById('close-success-modal');

            if (successModal && closeSuccessModal) {
                closeSuccessModal.addEventListener('click', () => {
                    successModal.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
