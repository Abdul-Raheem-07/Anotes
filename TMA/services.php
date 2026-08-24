<?php
/**
 * Services is now an informational overview - the actual Notes and
 * Reminders tools live on the Home page (index.php) so there's a
 * single source of truth instead of duplicate forms in two places.
 */
session_start();
$pageTitle  = 'Services';
$activePage = 'services';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5">
    <div class="text-center mb-5">
      <h2 class="fw-bold">What ANotes Offers</h2>
      <p class="text-muted">Two simple tools, both available right on the <a href="index.php">Home page</a>.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <i class="bi bi-journal-text fs-1 text-primary"></i>
            <h4 class="mt-3">Notes</h4>
            <p class="text-muted">Add, edit, and delete notes instantly. Deleted notes go to
              <a href="trash.php">Trash</a> for 7 days in case you change your mind.</p>
            <a href="index.php#notes-section" class="btn btn-primary">Go to Notes</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <i class="bi bi-alarm fs-1 text-primary"></i>
            <h4 class="mt-3">Reminders</h4>
            <p class="text-muted">Schedule one-off or repeating reminders (daily, weekly, monthly,
              or custom) with in-browser notifications when they're due.</p>
            <a href="index.php#reminders-section" class="btn btn-primary">Go to Reminders</a>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>