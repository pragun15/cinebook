<?php
require_once '../db.php';

$query = "SELECT * FROM movies ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">CineBook <span style="font-size: 0.8rem; color: #ef4444;">Admin</span></a>
            <div class="nav-links">
                <a href="admin.php">Dashboard</a>
                <a href="manage-movies.php">Manage Movies</a>
                <a href="add-movie.php">Add Movie</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h2 class="admin-title" style="margin-bottom:0;">Manage Movies</h2>
            <a href="add-movie.php" class="btn btn-primary">Add New Movie</a>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['err'])): ?>
            <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
        <?php endif; ?>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Duration</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($movie = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($movie['poster_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($movie['poster_image']); ?>" class="admin-thumbnail" alt="Poster">
                                    <?php else: ?>
                                        <div class="admin-thumbnail" style="background:#334155;"></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td><?php echo htmlspecialchars($movie['duration']); ?> mins</td>
                                <td><?php echo htmlspecialchars($movie['rating']); ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="edit-movie.php?id=<?php echo (int)$movie['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                        <form action="delete-movie.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this movie?');" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="id" value="<?php echo (int)$movie['id']; ?>">
                                            <button type="submit" class="btn-sm btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">No movies found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">CineBook</div>
            <p>&copy; <?php echo date("Y"); ?> CineBook Admin Panel. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
