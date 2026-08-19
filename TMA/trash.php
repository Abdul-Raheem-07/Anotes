<?php
session_start();
require_once __DIR__ . '/includes/db.php';

/**
 * Trash holds soft-deleted notes & reminders for 7 days (auto-purge
 * runs in includes/db.php on every request). From here you can
 * Restore an item or delete it permanently right away.
 */

// ---- Restore ----
if (isset($_GET['restore_note'])) {
    $sno = (int) $_GET['restore_note'];
    $stmt = mysqli_prepare($conn, "UPDATE `notes` SET `deleted_at` = NULL WHERE `sno` = ?");
    mysqli_stmt_bind_param($stmt, "i", $sno);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Restored!</strong> Note is back in your notes list.'];
    header("Location: trash.php");
    exit;
}

if (isset($_GET['restore_reminder'])) {
    $id = (int) $_GET['restore_reminder'];
    $stmt = mysqli_prepare($conn, "UPDATE `reminders` SET `deleted_at` = NULL WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Restored!</strong> Reminder is back in your reminders list.'];
    header("Location: trash.php");
    exit;
}

// ---- Permanent delete (right now, doesn't wait for 7 days) ----
if (isset($_GET['purge_note'])) {
    $sno = (int) $_GET['purge_note'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `notes` WHERE `sno` = ? AND `deleted_at` IS NOT NULL");
    mysqli_stmt_bind_param($stmt, "i", $sno);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'danger', 'text' => '<strong>Deleted!</strong> Note removed permanently.'];
    header("Location: trash.php");
    exit;
}

if (isset($_GET['purge_reminder'])) {
    $id = (int) $_GET['purge_reminder'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `reminders` WHERE `id` = ? AND `deleted_at` IS NOT NULL");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'danger', 'text' => '<strong>Deleted!</strong> Reminder removed permanently.'];
    header("Location: trash.php");
    exit;
}

$flashMessages = [];
if (!empty($_SESSION['flash'])) {
    $flashMessages[] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// How many days are left before an item auto-purges
function daysLeft($deletedAt) {
    $deletedTime = strtotime($deletedAt);
    $purgeTime   = $deletedTime + (7 * 24 * 60 * 60);
    $left        = ceil(($purgeTime - time()) / (24 * 60 * 60));
    return max(0, (int) $left);
}

$trashedNotes = [];
$result = mysqli_query($conn, "SELECT * FROM `notes` WHERE `deleted_at` IS NOT NULL ORDER BY `deleted_at` DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $trashedNotes[] = $row;
    }
}

$trashedReminders = [];
$result = mysqli_query($conn, "SELECT * FROM `reminders` WHERE `deleted_at` IS NOT NULL ORDER BY `deleted_at` DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $trashedReminders[] = $row;
    }
}

$pageTitle  = 'Trash';
$activePage = 'trash';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5">
    <div class="d-flex align-items-center gap-2 mb-2">
      <i class="bi bi-trash3 fs-3 text-primary"></i>
      <h2 class="mb-0">Trash</h2>
    </div>
    <p class="text-muted mb-4">Deleted notes and reminders stay here for 7 days before being permanently removed.</p>

    <!-- Trashed Notes -->
    <h5 class="mt-4 mb-3"><i class="bi bi-journal-text"></i> Notes</h5>
    <table class="table table-striped border align-middle">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Deleted</th>
          <th>Auto-delete in</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($trashedNotes)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Trash is empty.</td></tr>
        <?php else: ?>
          <?php foreach ($trashedNotes as $n): ?>
            <tr>
              <td><?php echo htmlspecialchars($n['title']); ?></td>
              <td><?php echo htmlspecialchars($n['description']); ?></td>
              <td class="small text-muted"><?php echo htmlspecialchars($n['deleted_at']); ?></td>
              <td><span class="badge bg-secondary"><?php echo daysLeft($n['deleted_at']); ?> day(s)</span></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="trash.php?restore_note=<?php echo (int)$n['sno']; ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                  </a>
                  <a href="trash.php?purge_note=<?php echo (int)$n['sno']; ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Permanently delete this note? This cannot be undone.');">
                    <i class="bi bi-trash3"></i> Delete Forever
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Trashed Reminders -->
    <h5 class="mt-5 mb-3"><i class="bi bi-alarm"></i> Reminders</h5>
    <table class="table table-striped border align-middle">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Scheduled For</th>
          <th>Deleted</th>
          <th>Auto-delete in</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($trashedReminders)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Trash is empty.</td></tr>
        <?php else: ?>
          <?php foreach ($trashedReminders as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['title']); ?></td>
              <td class="small"><?php echo htmlspecialchars($r['remind_date']); ?> at <?php echo substr(htmlspecialchars($r['remind_time']), 0, 5); ?></td>
              <td class="small text-muted"><?php echo htmlspecialchars($r['deleted_at']); ?></td>
              <td><span class="badge bg-secondary"><?php echo daysLeft($r['deleted_at']); ?> day(s)</span></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="trash.php?restore_reminder=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                  </a>
                  <a href="trash.php?purge_reminder=<?php echo (int)$r['id']; ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Permanently delete this reminder? This cannot be undone.');">
                    <i class="bi bi-trash3"></i> Delete Forever
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>