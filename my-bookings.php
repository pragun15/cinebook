<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

// Fetch all bookings with joined show and movie details
$query = "
    SELECT 
        b.id AS booking_id,
        b.seat_numbers,
        b.seats_booked AS ticket_count,
        b.total_amount,
        b.booking_date,
        s.show_time,
        m.title,
        m.poster_image,
        m.genre
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    ORDER BY b.booking_date DESC
";

$result = $conn->query($query);
$bookings = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Bookings | CineBook</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <main class="bookings-container">

        <h1 class="page-title">
            My Bookings
        </h1>

        <?php if (empty($bookings)): ?>

            <div class="empty-state">

                <div class="empty-icon">
                    🎟️
                </div>

                <h2>
                    No bookings found.
                </h2>

                <p>
                    You haven't booked any movie tickets yet.
                </p>

                <a href="index.php" class="btn btn-primary">
                    Browse Movies
                </a>

            </div>

        <?php else: ?>

            <div class="bookings-list">

                <?php foreach ($bookings as $booking): ?>

                    <div class="booking-card">

                        <div class="booking-poster">

                            <?php if (!empty($booking['poster_image'])): ?>

                                <img 
                                    src="<?php echo htmlspecialchars($booking['poster_image']); ?>" 
                                    alt="<?php echo htmlspecialchars($booking['title']); ?>"
                                >

                            <?php else: ?>

                                <div class="movie-poster-placeholder">

                                    <span>
                                        <?php echo htmlspecialchars($booking['title']); ?>
                                    </span>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="booking-info">

                            <div class="booking-header">

                                <div>

                                    <h3 class="booking-title">
                                        <?php echo htmlspecialchars($booking['title']); ?>
                                    </h3>

                                    <p class="booking-genre">
                                        <?php echo htmlspecialchars($booking['genre']); ?>
                                    </p>

                                </div>

                                <span class="booking-status">
                                    Confirmed
                                </span>

                            </div>

                            <hr class="booking-divider">

                            <div class="booking-details-grid">

                                <div class="detail-item">

                                    <span class="detail-label">
                                        Date
                                    </span>

                                    <span class="detail-value">
                                        <?php echo date('M d, Y', strtotime($booking['show_time'])); ?>
                                    </span>

                                </div>

                                <div class="detail-item">

                                    <span class="detail-label">
                                        Time
                                    </span>

                                    <span class="detail-value">
                                        <?php echo date('h:i A', strtotime($booking['show_time'])); ?>
                                    </span>

                                </div>

                                <div class="detail-item">

                                    <span class="detail-label">
                                        Seats
                                    </span>

                                    <span class="detail-value seats-badge">
                                        <?php echo htmlspecialchars($booking['seat_numbers']); ?>
                                    </span>

                                </div>

                                <div class="detail-item">

                                    <span class="detail-label">
                                        Tickets
                                    </span>

                                    <span class="detail-value">
                                        <?php echo (int)$booking['ticket_count']; ?>
                                    </span>

                                </div>

                            </div>

                            <hr class="booking-divider">

                            <div class="booking-footer">

                                <div class="booking-id">

                                    ID:
                                    #<?php echo str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT); ?>

                                </div>

                                <div class="booking-total">

                                    Total:
                                    <span class="amount">
                                        ₹<?php echo number_format($booking['total_amount'], 2); ?>
                                    </span>

                                </div>

                            </div>

                            <div class="booking-actions">
                                <a href="ticket.php?id=<?php echo (int)$booking['booking_id']; ?>" class="btn btn-outline btn-sm">
                                    View Ticket
                                </a>
                            </div>

                            <?php if (!empty($booking['booking_date'])): ?>

                                <div class="booking-timestamp">

                                    Booked on
                                    <?php echo date('M d, Y h:i A', strtotime($booking['booking_date'])); ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>