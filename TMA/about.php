<?php
$pageTitle  = 'About';
$activePage = 'about';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h2 class="fw-bold mb-3">About ANotes</h2>
        <p class="lead text-muted">
          ANotes is a lightweight personal productivity app built to help you capture ideas,
          organize your daily tasks, and stay on top of what matters.
        </p>
        <p>
          Jot down quick notes the moment an idea strikes, keep a running list of things to do,
          and set timely reminders so nothing important slips through the cracks. Whether it's a
          shopping list, a study plan, or a deadline you can't afford to miss, ANotes keeps it
          organized in one simple place - accessible whenever you need it.
        </p>
      </div>
    </div>

    <div class="row mt-5 g-4 text-center">
      <div class="col-md-4">
        <i class="bi bi-journal-text fs-1 text-primary"></i>
        <h5 class="mt-3">Note Taking</h5>
        <p class="text-muted small">Create, edit, and delete notes in seconds with a clean, distraction-free interface.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-check2-square fs-1 text-primary"></i>
        <h5 class="mt-3">Task Organization</h5>
        <p class="text-muted small">Keep daily tasks structured so you always know what's next.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-alarm fs-1 text-primary"></i>
        <h5 class="mt-3">Timely Reminders</h5>
        <p class="text-muted small">Schedule one-off or repeating reminders and get notified right in your browser.</p>
      </div>
    </div>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
