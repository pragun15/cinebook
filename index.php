<?php
// Include database connection
require_once 'db.php';

// Fetch movies from database
$query = "SELECT * FROM movies ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBook | Modern Movie Ticket Booking</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Experience the Magic of Cinema</h1>
            <p>Book tickets for the latest blockbusters in stunning 4K and IMAX.</p>

            <a href="#movies" class="btn btn-primary">
                Book Tickets Now
            </a>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <form class="search-bar">
            <input 
                type="text" 
                placeholder="Search for movies..."
            >

            <button type="submit" class="btn btn-primary">
                Search
            </button>
        </form>
    </section>

    <!-- Movies Section -->
    <section id="movies" class="movies-section">

        <h2 class="section-title">Now Showing</h2>

        <div class="movies-grid">

            <?php
            if($result && mysqli_num_rows($result) > 0)
            {
                while($movie = mysqli_fetch_assoc($result))
                {
            ?>

                <div class="movie-card">

                    <!-- Movie Poster -->
                    <?php if(!empty($movie['poster_image'])) { ?>

                        <img 
                            src="<?php echo $movie['poster_image']; ?>" 
                            alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                            class="movie-poster"
                        >

                    <?php } else { ?>

                        <div class="movie-poster-placeholder">
                            No Poster
                        </div>

                    <?php } ?>

                    <!-- Movie Info -->
                    <div class="movie-info">

                        <h3 class="movie-title">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </h3>

                        <div class="movie-meta">

                            <span class="genre">
                                <?php echo htmlspecialchars($movie['genre']); ?>
                            </span>

                            <span class="rating">
                                ★ <?php echo htmlspecialchars($movie['rating']); ?>
                            </span>

                        </div>

                        <!-- View Details Button -->
                        <div class="details-btn-wrapper">

                            <a href="details.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary details-link">
                                View Details
                            </a>

                        </div>

                    </div>

                </div>

            <?php
                }
            }
            else
            {
                echo "<p style='color:white;'>No movies found in database.</p>";
            }
            ?>

        </div>

    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="js/script.js"></script>

</body>
</html>