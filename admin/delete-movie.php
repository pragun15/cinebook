<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: manage-movies.php?err=" . urlencode("Invalid request."));
    exit;
}

$id = (int)$_POST['id'];

// Check if movie exists
$check_stmt = $conn->prepare("SELECT id FROM movies WHERE id = ?");
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_stmt->store_result();
if ($check_stmt->num_rows === 0) {
    header("Location: manage-movies.php?err=" . urlencode("Movie not found."));
    exit;
}
$check_stmt->close();

// Dependency Check: Are there shows associated with this movie?
$dep_stmt = $conn->prepare("SELECT COUNT(*) FROM shows WHERE movie_id = ?");
$dep_stmt->bind_param("i", $id);
$dep_stmt->execute();
$dep_stmt->bind_result($show_count);
$dep_stmt->fetch();
$dep_stmt->close();

if ($show_count > 0) {
    header("Location: manage-movies.php?err=" . urlencode("Cannot delete movie because shows/bookings exist."));
    exit;
}

// Proceed to delete
$delete_stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    header("Location: manage-movies.php?msg=" . urlencode("Movie deleted successfully."));
} else {
    header("Location: manage-movies.php?err=" . urlencode("Failed to delete movie."));
}
$delete_stmt->close();
