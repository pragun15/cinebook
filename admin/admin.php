<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CineBook</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
            min-height: 50vh;
        }
        .admin-card {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .admin-title {
            color: #ef4444;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">CineBook <span style="font-size: 0.8rem; color: #ef4444;">Admin</span></a>
            <div class="nav-links">
                <a href="manage-movies.php">Manage Movies</a>
                <a href="add-movie.php">Add Movie</a>
                <a href="../index.php">Back to Home</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-card">
            <h1 class="admin-title">Admin Dashboard</h1>
            <p style="color: #94a3b8; margin-bottom: 2rem;">Welcome to the CineBook backend management panel.</p>
            
            <div class="admin-dashboard-grid">
                <a href="manage-movies.php" class="admin-card-link">
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px;">🎞️ Manage Movies</h3>
                    <p style="color: #94a3b8; font-size: 0.9rem;">View, edit, and delete existing movies from the database.</p>
                </a>
                
                <a href="add-movie.php" class="admin-card-link">
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px;">➕ Add Movie</h3>
                    <p style="color: #94a3b8; font-size: 0.9rem;">Add a new movie to the platform's catalog.</p>
                </a>
            </div>
            
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">CineBook</div>
            <p>&copy; <?php echo date("Y"); ?> CineBook Admin Panel. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
