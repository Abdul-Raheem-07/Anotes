<?php
/**
 * db.php - single shared database connection.
 * Every page includes this file instead of opening its own connection.
 */

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "notes";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Die with a clear message if the connection failed
if (!$conn) {
    die("Sorry, we failed to connect to the database: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

/**
 * Trash auto-cleanup: anything soft-deleted (deleted_at set) more than
 * 7 days ago is permanently removed. This runs on every page load since
 * there's no cron job in a typical XAMPP setup - cheap enough to do here.
 */
mysqli_query($conn, "DELETE FROM `notes` WHERE `deleted_at` IS NOT NULL AND `deleted_at` <= (NOW() - INTERVAL 7 DAY)");
mysqli_query($conn, "DELETE FROM `reminders` WHERE `deleted_at` IS NOT NULL AND `deleted_at` <= (NOW() - INTERVAL 7 DAY)");