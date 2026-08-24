<?php
session_start();

// Clear all session data, then destroy the session itself.
$_SESSION = [];
session_unset();
session_destroy();

header("Location: login.php");
exit;