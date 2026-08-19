<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// ---------------- NOTES: DELETE (soft) ----------------
if (isset($_GET['delete'])) {
    $sno = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "UPDATE `notes` SET `deleted_at` = NOW() WHERE `sno` = ?");
    mysqli_stmt_bind_param($stmt, "i", $sno);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => '<strong>Moved to Trash!</strong> Note will be permanently deleted in 7 days.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Could not delete that note: ' . htmlspecialchars(mysqli_error($conn))];
    }
    mysqli_stmt_close($stmt);
    header("Location: index.php");
    exit;
}

// ---------------- NOTES: INSERT & UPDATE ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // UPDATE (edit modal submits snoEdit)
    if (isset($_POST['snoEdit'])) {
        $sno         = (int) $_POST['snoEdit'];
        $title       = trim($_POST['titleEdit'] ?? '');
        $description = trim($_POST['descriptionEdit'] ?? '');

        if ($title === '' || $description === '') {
            $_SESSION['flash'] = ['type' => 'warning', 'text' => 'Title and content are required to update a note.'];
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE `notes` SET `title` = ?, `description` = ? WHERE `sno` = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $sno);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Success!</strong> Note updated successfully.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'text' => 'We could not update the record: ' . htmlspecialchars(mysqli_error($conn))];
            }
            mysqli_stmt_close($stmt);
        }
        header("Location: index.php");
        exit;
    }

    // INSERT (add note form submits title + desc)
    if (isset($_POST['title']) && isset($_POST['desc'])) {
        $title       = trim($_POST['title']);
        $description = trim($_POST['desc']);

        if ($title === '' || $description === '') {
            $_SESSION['flash'] = ['type' => 'warning', 'text' => 'Please fill in both the title and content.'];
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO `notes` (`title`, `description`) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $title, $description);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Success!</strong> Note added successfully.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'text' => 'The note was not inserted: ' . htmlspecialchars(mysqli_error($conn))];
            }
            mysqli_stmt_close($stmt);
        }
        header("Location: index.php");
        exit;
    }

    // INSERT reminder
    if (isset($_POST['reminder_title'])) {
        $title    = trim($_POST['reminder_title']);
        $desc     = trim($_POST['reminder_desc'] ?? '');
        $date     = $_POST['reminder_date'] ?? '';
        $time     = $_POST['reminder_time'] ?? '';
        $repeat   = $_POST['repeat_option'] ?? 'none';
        $custom   = isset($_POST['custom_days']) && $_POST['custom_days'] !== '' ? (int) $_POST['custom_days'] : null;

        $allowedRepeats = ['none', 'daily', 'weekly', 'monthly', 'custom'];
        if (!in_array($repeat, $allowedRepeats, true)) {
            $repeat = 'none';
        }

        if ($title === '' || $date === '' || $time === '') {
            $_SESSION['flash'] = ['type' => 'warning', 'text' => 'Title, date and time are all required for a reminder.'];
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO `reminders` (`title`, `description`, `remind_date`, `remind_time`, `repeat_option`, `custom_days`) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssi", $title, $desc, $date, $time, $repeat, $custom);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Success!</strong> Reminder scheduled.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Could not save reminder: ' . htmlspecialchars(mysqli_error($conn))];
            }
            mysqli_stmt_close($stmt);
        }
        header("Location: index.php#reminders-section");
        exit;
    }
}

// ---------------- REMINDERS: DELETE (soft) & TOGGLE DONE ----------------
if (isset($_GET['delete_reminder'])) {
    $id = (int) $_GET['delete_reminder'];
    $stmt = mysqli_prepare($conn, "UPDATE `reminders` SET `deleted_at` = NOW() WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'danger', 'text' => '<strong>Moved to Trash!</strong> Reminder will be permanently deleted in 7 days.'];
    header("Location: index.php#reminders-section");
    exit;
}

if (isset($_GET['toggle_reminder'])) {
    $id = (int) $_GET['toggle_reminder'];
    $stmt = mysqli_prepare($conn, "UPDATE `reminders` SET `is_done` = 1 - `is_done` WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: index.php#reminders-section");
    exit;
}

// ---------------- Fetch data for display ----------------
$flashMessages = [];
if (!empty($_SESSION['flash'])) {
    $flashMessages[] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$reminders = [];
$result = mysqli_query($conn, "SELECT * FROM `reminders` WHERE `deleted_at` IS NULL ORDER BY `remind_date` ASC, `remind_time` ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reminders[] = $row;
    }
}

$pageTitle  = 'Home';
$activePage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

  <!-- Edit Note Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Note</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="index.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="mb-3">
              <label for="titleEdit" class="form-label">Note Title</label>
              <input type="text" class="form-control" id="titleEdit" name="titleEdit" required>
            </div>
            <div class="mb-3">
              <label for="descriptionEdit" class="form-label">Note Content</label>
              <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===================== SECTION 1: NOTES ===================== -->
  <div class="container my-5" id="notes-section">
    <div class="d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-journal-text fs-3 text-primary"></i>
      <h2 class="mb-0">Notes</h2>
    </div>

    <form action="index.php" method="POST" class="row g-3 mb-4">
      <div class="col-md-5">
        <label for="title" class="form-label">Note Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
      </div>
      <div class="col-md-6">
        <label for="desc" class="form-label">Note Content</label>
        <textarea class="form-control" id="desc" name="desc" rows="1" required></textarea>
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Add</button>
      </div>
    </form>

    <table class="table table-striped table-hover border align-middle">
      <thead class="table-dark">
        <tr>
          <th scope="col">S.No</th>
          <th scope="col">Title</th>
          <th scope="col">Description</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM `notes` WHERE `deleted_at` IS NULL ORDER BY `sno` ASC");
        $sno = 0;

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $sno++;
                echo "<tr>
                  <th scope='row'>" . $sno . "</th>
                  <td>" . htmlspecialchars($row['title']) . "</td>
                  <td>" . htmlspecialchars($row['description']) . "</td>
                  <td>
                    <div class='d-flex gap-2'>
                      <button class='edit btn btn-sm btn-primary' data-id='" . (int)$row['sno'] . "' data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "' data-desc='" . htmlspecialchars($row['description'], ENT_QUOTES) . "'>Edit</button>
                      <button class='delete btn btn-sm btn-danger' data-id='" . (int)$row['sno'] . "'>Delete</button>
                    </div>
                  </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center text-muted py-4'>No notes yet - add your first one above.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <hr class="container">

  <!-- ===================== SECTION 2: REMINDERS ===================== -->
  <div class="container my-5" id="reminders-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-alarm fs-3 text-primary"></i>
        <h2 class="mb-0">Reminders</h2>
      </div>
      <button id="enableNotifs" type="button" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-bell"></i> Enable Notifications
      </button>
    </div>

    <form action="index.php" method="POST" class="row g-3 mb-4">
      <div class="col-md-6">
        <label for="reminder_title" class="form-label">Title</label>
        <input type="text" class="form-control" id="reminder_title" name="reminder_title" required>
      </div>
      <div class="col-md-6">
        <label for="reminder_desc" class="form-label">Notes (optional)</label>
        <input type="text" class="form-control" id="reminder_desc" name="reminder_desc">
      </div>
      <div class="col-md-3">
        <label for="reminder_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="reminder_date" name="reminder_date" required>
      </div>
      <div class="col-md-3">
        <label for="reminder_time" class="form-label">Time</label>
        <input type="time" class="form-control" id="reminder_time" name="reminder_time" required>
      </div>
      <div class="col-md-3">
        <label for="repeat_option" class="form-label">Repeat</label>
        <select class="form-select" id="repeat_option" name="repeat_option">
          <option value="none">Never</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
          <option value="custom">Custom (every N days)</option>
        </select>
      </div>
      <div class="col-md-3" id="customDaysWrap" style="display:none;">
        <label for="custom_days" class="form-label">Every N days</label>
        <input type="number" min="1" class="form-control" id="custom_days" name="custom_days" placeholder="e.g. 3">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary"><i class="bi bi-alarm"></i> Set Reminder</button>
      </div>
    </form>

    <div id="notifBanner" class="alert alert-info d-none">
      Notifications are blocked in your browser. Enable them from your browser's site settings
      to get alerts when a reminder is due.
    </div>

    <div class="list-group" id="reminderList">
      <?php if (empty($reminders)): ?>
        <div class="list-group-item text-center text-muted py-4">No reminders yet - schedule your first one above.</div>
      <?php endif; ?>
      <?php foreach ($reminders as $r): ?>
        <div class="list-group-item d-flex justify-content-between align-items-start <?php echo $r['is_done'] ? 'bg-light' : ''; ?>">
          <div>
            <div class="fw-semibold <?php echo $r['is_done'] ? 'text-decoration-line-through text-muted' : ''; ?>">
              <?php echo htmlspecialchars($r['title']); ?>
            </div>
            <?php if (!empty($r['description'])): ?>
              <div class="small text-muted"><?php echo htmlspecialchars($r['description']); ?></div>
            <?php endif; ?>
            <div class="small text-secondary mt-1">
              <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($r['remind_date']); ?>
              <i class="bi bi-clock ms-2"></i> <?php echo substr(htmlspecialchars($r['remind_time']), 0, 5); ?>
              <?php if ($r['repeat_option'] !== 'none'): ?>
                <span class="badge bg-secondary ms-2">
                  <i class="bi bi-arrow-repeat"></i>
                  <?php echo $r['repeat_option'] === 'custom' ? 'Every ' . (int)$r['custom_days'] . ' days' : ucfirst($r['repeat_option']); ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
          <div class="d-flex gap-2">
            <a href="index.php?toggle_reminder=<?php echo (int)$r['id']; ?>#reminders-section" class="btn btn-sm btn-outline-success" title="Mark done">
              <i class="bi bi-check-lg"></i>
            </a>
            <a href="index.php?delete_reminder=<?php echo (int)$r['id']; ?>#reminders-section"
               class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Move this reminder to Trash?');" title="Delete">
              <i class="bi bi-trash"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- In-app toast for reminder alerts -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="reminderToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="reminderToastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <script>
    // Edit button click - read data-* attributes instead of scraping table text
    document.querySelectorAll('.edit').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.getElementById('titleEdit').value = btn.dataset.title;
        document.getElementById('descriptionEdit').value = btn.dataset.desc;
        document.getElementById('snoEdit').value = btn.dataset.id;
        new bootstrap.Modal(document.getElementById('editModal')).show();
      });
    });

    // Delete button click (notes) -> soft delete, goes to Trash
    document.querySelectorAll('.delete').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (confirm('Move this note to Trash? You can restore it within 7 days.')) {
          window.location = `index.php?delete=${btn.dataset.id}`;
        }
      });
    });

    // Show/hide the custom-days field for reminders
    const repeatSelect = document.getElementById('repeat_option');
    const customWrap = document.getElementById('customDaysWrap');
    repeatSelect.addEventListener('change', () => {
      customWrap.style.display = repeatSelect.value === 'custom' ? 'block' : 'none';
    });

    // ---- Notification permission ----
    const enableBtn = document.getElementById('enableNotifs');
    const notifBanner = document.getElementById('notifBanner');

    function refreshNotifButton() {
      if (!('Notification' in window)) {
        enableBtn.disabled = true;
        enableBtn.textContent = 'Notifications not supported';
        return;
      }
      if (Notification.permission === 'granted') {
        enableBtn.innerHTML = '<i class="bi bi-bell-fill"></i> Notifications On';
        enableBtn.classList.replace('btn-outline-primary', 'btn-success');
        notifBanner.classList.add('d-none');
      } else if (Notification.permission === 'denied') {
        notifBanner.classList.remove('d-none');
      }
    }
    enableBtn.addEventListener('click', () => {
      if (!('Notification' in window)) return;
      Notification.requestPermission().then(refreshNotifButton);
    });
    refreshNotifButton();

    // ---- Reminder due-checking engine ----
    const reminders = <?php echo json_encode(array_map(function ($r) {
        return [
            'id'          => (int) $r['id'],
            'title'       => $r['title'],
            'description' => $r['description'],
            'date'        => $r['remind_date'],
            'time'        => $r['remind_time'],
            'repeat'      => $r['repeat_option'],
            'customDays'  => $r['custom_days'] !== null ? (int) $r['custom_days'] : null,
            'isDone'      => (bool) $r['is_done'],
        ];
    }, $reminders), JSON_UNESCAPED_UNICODE); ?>;

    const alertedKeys = new Set();

    function nextOccurrence(reminder) {
      let dt = new Date(`${reminder.date}T${reminder.time}`);
      const now = new Date();
      if (reminder.repeat === 'none') {
        return dt;
      }
      while (dt < now) {
        if (reminder.repeat === 'daily') {
          dt.setDate(dt.getDate() + 1);
        } else if (reminder.repeat === 'weekly') {
          dt.setDate(dt.getDate() + 7);
        } else if (reminder.repeat === 'monthly') {
          dt.setMonth(dt.getMonth() + 1);
        } else if (reminder.repeat === 'custom' && reminder.customDays) {
          dt.setDate(dt.getDate() + reminder.customDays);
        } else {
          break;
        }
      }
      return dt;
    }

    function showToast(text) {
      const toastEl = document.getElementById('reminderToast');
      document.getElementById('reminderToastBody').textContent = text;
      new bootstrap.Toast(toastEl).show();
    }

    function checkReminders() {
      const now = new Date();
      reminders.forEach((reminder) => {
        if (reminder.isDone) return;
        const due = nextOccurrence(reminder);
        const key = `${reminder.id}-${due.toISOString()}`;
        const diffMs = due - now;

        if (diffMs <= 0 && diffMs > -60000 && !alertedKeys.has(key)) {
          alertedKeys.add(key);
          const message = reminder.description
            ? `${reminder.title} - ${reminder.description}`
            : reminder.title;

          showToast(`Reminder: ${message}`);

          if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('ANotes Reminder', { body: message, icon: undefined });
          }
        }
      });
    }

    checkReminders();
    setInterval(checkReminders, 20000);
  </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>