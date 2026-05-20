<?php
require_once '../db.php';

$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-movies.php?err=" . urlencode("Invalid movie ID."));
    exit;
}

$id = (int)$_GET['id'];

// Fetch existing movie
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: manage-movies.php?err=" . urlencode("Movie not found."));
    exit;
}
$movie = $result->fetch_assoc();
$stmt->close();

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
        // If poster image is empty, keep existing
        if (empty($poster_image)) {
            $poster_image = $movie['poster_image'];
        }

        $update_stmt = $conn->prepare("UPDATE movies SET title = ?, genre = ?, duration = ?, rating = ?, poster_image = ?, description = ?, ticket_price = ? WHERE id = ?");
        if ($update_stmt) {
            $update_stmt->bind_param("ssisssdi", $title, $genre, $duration, $rating, $poster_image, $description, $ticket_price, $id);
            if ($update_stmt->execute()) {
                header("Location: manage-movies.php?msg=" . urlencode("Movie updated successfully."));
                exit;
            } else {
                $error = "Database error: " . $update_stmt->error;
            }
            $update_stmt->close();
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
    <title>Edit Movie | Admin</title>
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
            <h2 class="admin-title">Edit Movie</h2>
            
            <?php if ($error): ?>
                <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="edit-movie.php?id=<?php echo $id; ?>" method="POST">
                <div class="admin-form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="admin-form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? $movie['title']); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Genre *</label>
                    <input type="text" name="genre" class="admin-form-control" required value="<?php echo htmlspecialchars($_POST['genre'] ?? $movie['genre']); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Duration (minutes) *</label>
                    <input type="number" name="duration" class="admin-form-control" required min="1" value="<?php echo htmlspecialchars($_POST['duration'] ?? $movie['duration']); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Rating *</label>
                    <input type="text" name="rating" class="admin-form-control" required value="<?php echo htmlspecialchars($_POST['rating'] ?? $movie['rating']); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Ticket Price (₹) *</label>
                    <input type="number" step="0.01" name="ticket_price" class="admin-form-control" required min="1" value="<?php echo htmlspecialchars($_POST['ticket_price'] ?? $movie['ticket_price']); ?>">
                </div>
                
                <div class="admin-form-group">
                    <label>Poster Image URL</label>
                    <input type="text" name="poster_image" class="admin-form-control" placeholder="Leave blank to keep existing" value="">
                    <small style="color: #94a3b8; margin-top: 5px; display: block;">Current: <?php echo htmlspecialchars($movie['poster_image']); ?></small>
                </div>
                
                <div class="admin-form-group">
                    <label>Description *</label>
                    <textarea name="description" class="admin-form-control" required><?php echo htmlspecialchars($_POST['description'] ?? $movie['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Movie</button>
            </form>
        </div>
    </div>
</body>
</html>
