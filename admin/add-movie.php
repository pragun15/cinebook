<?php
require_once '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $rating = trim($_POST['rating'] ?? '');
    $poster_image = trim($_POST['poster_image'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ticket_price = (float)($_POST['ticket_price'] ?? 250.00);

    if (empty($title) || empty($genre) || $duration <= 0 || empty($rating) || empty($description) || $ticket_price <= 0) {
        $error = "Please fill in all required fields properly.";
    } else {
        $stmt = $conn->prepare("INSERT INTO movies (title, genre, duration, rating, poster_image, description, ticket_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssisssd", $title, $genre, $duration, $rating, $poster_image, $description, $ticket_price);
            if ($stmt->execute()) {
                header("Location: manage-movies.php?msg=" . urlencode("Movie added successfully."));
                exit;
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Failed to prepare statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">CineBook <span style="font-size: 0.8rem; color: #ef4444;">Admin</span></a>
            <div class="nav-links">
                <a href="admin.php">Dashboard</a>
                <a href="manage-movies.php">Manage Movies</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-form-container">
            <h2 class="admin-title">Add New Movie</h2>
            
            <?php if ($error): ?>
                <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="add-movie.php" method="POST">
                <div class="admin-form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="admin-form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Genre *</label>
                    <input type="text" name="genre" class="admin-form-control" placeholder="e.g., Action, Sci-Fi" required value="<?php echo htmlspecialchars($_POST['genre'] ?? ''); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Duration (minutes) *</label>
                    <input type="number" name="duration" class="admin-form-control" required min="1" value="<?php echo htmlspecialchars($_POST['duration'] ?? ''); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Rating *</label>
                    <input type="text" name="rating" class="admin-form-control" placeholder="e.g., PG-13, R" required value="<?php echo htmlspecialchars($_POST['rating'] ?? ''); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Ticket Price (₹) *</label>
                    <input type="number" step="0.01" name="ticket_price" class="admin-form-control" required min="1" value="<?php echo htmlspecialchars($_POST['ticket_price'] ?? '250.00'); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Poster Image URL</label>
                    <input type="text" name="poster_image" class="admin-form-control" placeholder="e.g., images/posters/movie.jpg" value="<?php echo htmlspecialchars($_POST['poster_image'] ?? ''); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Description *</label>
                    <textarea name="description" class="admin-form-control" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Movie</button>
            </form>
        </div>
    </div>
</body>
</html>
