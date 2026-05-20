<?php

require_once 'db.php';

// Check movie ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie ID.");
}

$movie_id = (int) $_GET['id'];

/*
========================================
FETCH MOVIE DETAILS
========================================
*/

$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");

if (!$stmt) {
    die("Movie query failed: " . $conn->error);
}

$stmt->bind_param("i", $movie_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Movie not found.");
}

$movie = $result->fetch_assoc();

/*
========================================
FETCH SHOWS
========================================
*/

$show_stmt = $conn->prepare("
    SELECT * 
    FROM shows 
    WHERE movie_id = ?
    ORDER BY show_time ASC
");

if (!$show_stmt) {
    die("Show query failed: " . $conn->error);
}

$show_stmt->bind_param("i", $movie_id);
$show_stmt->execute();

$shows_result = $show_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($movie['title']); ?> | CineBook
    </title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>

<main class="movie-details-container">

    <a href="index.php" class="btn btn-outline">
        ← Back to Movies
    </a>

    <div class="movie-details-grid">

        <!-- Poster -->
        <div class="details-poster-wrap">

            <?php if (!empty($movie['poster_image'])) { ?>

                <img 
                    src="<?php echo $movie['poster_image']; ?>" 
                    alt="<?php echo htmlspecialchars($movie['title']); ?>"
                    class="details-poster"
                >

            <?php } else { ?>

                <div class="movie-poster-placeholder">
                    No Poster
                </div>

            <?php } ?>

        </div>

        <!-- Details -->
        <div class="details-info">

            <h1 class="details-title">
                <?php echo htmlspecialchars($movie['title']); ?>
            </h1>

            <div class="details-meta">

                <span class="genre">
                    <?php echo htmlspecialchars($movie['genre']); ?>
                </span>

                <span class="duration">
                    ⏱ <?php echo htmlspecialchars($movie['duration']); ?> mins
                </span>

                <span class="rating">
                    ★ <?php echo htmlspecialchars($movie['rating']); ?>
                </span>

            </div>

            <div class="details-desc">

                <?php
                echo nl2br(htmlspecialchars($movie['description']));
                ?>

            </div>

            <!-- Show Timings -->
            <div class="show-times-section">

                <h3>Available Show Timings</h3>

                <div class="show-times-container">

                    <?php
                    if ($shows_result->num_rows > 0)
                    {
                        while ($show = $shows_result->fetch_assoc())
                        {
                    ?>

                        <a href="book-seat.php?show_id=<?php echo $show['id']; ?>" class="show-time-btn">

                            <span class="time">
                                <?php echo date("h:i A", strtotime($show['show_time'])); ?>
                            </span>

                            <span class="date">
                                <?php echo date("M d, Y", strtotime($show['show_time'])); ?>
                            </span>

                        </a>

                    <?php
                        }
                    }
                    else
                    {
                        echo "<p>No shows available.</p>";
                    }
                    ?>

                </div>

            </div>

        </div>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>

</html>