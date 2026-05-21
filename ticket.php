<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid booking ID.");
}

$booking_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        b.id AS booking_id,
        b.seat_numbers,
        b.seats_booked,
        b.total_amount,
        b.booking_date,
        s.show_time,
        m.title,
        m.poster_image
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    WHERE b.id = ?
    LIMIT 1
");

if (!$stmt) {
    die("Ticket query failed: " . $conn->error);
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ticket not found.");
}

$ticket = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo htmlspecialchars($ticket['booking_id']); ?> | CineBook</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <main class="ticket-page">
        <div class="ticket-header">
            <div>
                <h1>Movie Ticket</h1>
                <p>Your cinematic experience is confirmed.</p>
            </div>
            <span class="ticket-badge">Confirmed</span>
        </div>

        <div class="ticket-shell">
            <div class="ticket-left">
                <div class="ticket-poster">
                    <?php if (!empty($ticket['poster_image'])): ?>
                        <img src="<?php echo htmlspecialchars($ticket['poster_image']); ?>" alt="<?php echo htmlspecialchars($ticket['title']); ?>">
                    <?php else: ?>
                        <div class="movie-poster-placeholder">No Poster</div>
                    <?php endif; ?>
                </div>
                <div class="ticket-movie">
                    <h2><?php echo htmlspecialchars($ticket['title']); ?></h2>
                    <p class="ticket-meta">
                        Show Date: <?php echo date('M d, Y', strtotime($ticket['show_time'])); ?>
                        <span>•</span>
                        Time: <?php echo date('h:i A', strtotime($ticket['show_time'])); ?>
                    </p>
                </div>
            </div>

            <div class="ticket-divider">
                <span></span>
            </div>

            <div class="ticket-right">
                <div class="ticket-info-grid">
                    <div>
                        <span class="ticket-label">Booking ID</span>
                        <span class="ticket-value">#<?php echo str_pad($ticket['booking_id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div>
                        <span class="ticket-label">Seats</span>
                        <span class="ticket-value"><?php echo htmlspecialchars($ticket['seat_numbers']); ?></span>
                    </div>
                    <div>
                        <span class="ticket-label">Tickets</span>
                        <span class="ticket-value"><?php echo (int)$ticket['seats_booked']; ?></span>
                    </div>
                    <div>
                        <span class="ticket-label">Total Paid</span>
                        <span class="ticket-value">₹<?php echo number_format($ticket['total_amount'], 2); ?></span>
                    </div>
                </div>

                <div class="ticket-stamp">
                    <div class="ticket-message">
                        <span class="ticket-message-label">Enjoy Your Show 🍿</span>
                        <p>Thanks for booking with CineBook.</p>
                    </div>
                    <div class="ticket-time">
                        <span class="ticket-label">Booked On</span>
                        <span class="ticket-value">
                            <?php echo date('M d, Y h:i A', strtotime($ticket['booking_date'])); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ticket-actions">
            <a href="index.php" class="btn btn-outline">Browse Movies</a>
            <button type="button" class="btn btn-primary" id="print-ticket">Download / Print Ticket</button>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        const printButton = document.getElementById('print-ticket');
        if (printButton) {
            printButton.addEventListener('click', () => {
                window.print();
            });
        }
    </script>
</body>
</html>
