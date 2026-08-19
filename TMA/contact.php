<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']    = trim($_POST['name'] ?? '');
    $old['email']   = trim($_POST['email'] ?? '');
    $old['subject'] = trim($_POST['subject'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

    if ($old['name'] === '') {
        $errors[] = 'Please enter your name.';
    }
    if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($old['subject'] === '') {
        $errors[] = 'Please enter a subject.';
    }
    if ($old['message'] === '') {
        $errors[] = 'Please enter a message.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO `messages` (`name`, `email`, `subject`, `message`) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $old['name'], $old['email'], $old['subject'], $old['message']);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash'] = ['type' => 'success', 'text' => "<strong>Thank you!</strong> Your message has been sent. I'll get back to you soon."];
            mysqli_stmt_close($stmt);
            header("Location: contact.php");
            exit;
        } else {
            $errors[] = 'Something went wrong while sending your message: ' . htmlspecialchars(mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }
}

$flashMessages = [];
if (!empty($_SESSION['flash'])) {
    $flashMessages[] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
foreach ($errors as $err) {
    $flashMessages[] = ['type' => 'warning', 'text' => htmlspecialchars($err)];
}

$pageTitle  = 'Contact';
$activePage = 'contact';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5">
    <div class="row justify-content-center g-5">
      <div class="col-lg-5">
        <h2 class="fw-bold">Get in touch</h2>
        <p class="text-muted">Have a question, found a bug, or want to collaborate? Send a message below,
        or reach out directly.</p>
        <div class="d-flex align-items-center gap-2 mt-4">
          <i class="bi bi-envelope-fill text-primary fs-4"></i>
          <a href="mailto:dotabdulraheemofficial07@gmail.com" class="fs-5 text-decoration-none">
            dotabdulraheemofficial07@gmail.com
          </a>
        </div>
      </div>

      <div class="col-lg-6">
        <form action="contact.php" method="POST" class="card p-4 shadow-sm border-0">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($old['name']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($old['email']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($old['subject']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" required><?php echo htmlspecialchars($old['message']); ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
