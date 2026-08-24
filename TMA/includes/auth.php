<?php
/**
 * auth.php - shared authentication helpers.
 *
 * Include this (after session_start() and db.php) on any page.
 * - Call require_login() at the top of pages that need a logged-in user.
 * - Use is_logged_in() / current_user_id() / current_user_name() in
 *   places like header.php that behave differently based on auth state.
 */

// Sends the visitor to the login page and stops the script if
// nobody is logged in. Put this at the very top of protected pages,
// right after session_start() + db.php are required.
function require_login() {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// True if someone is currently logged in. Used by header.php to decide
// which nav links to show.
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

// The logged-in user's id, or null if nobody is logged in.
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// The logged-in user's display name, or null if nobody is logged in.
function current_user_name() {
    return $_SESSION['user_name'] ?? null;
}