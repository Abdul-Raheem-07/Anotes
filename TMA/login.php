<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// If already logged in, skip straight to the notes.
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT `id`, `name`, `password` FROM `users` WHERE `email` = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        // Same generic error whether the email doesn't exist or the
        // password is wrong - don't reveal which one it was.
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate the session id on login to prevent session fixation.
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$flashMessages = [];
foreach ($errors as $error) {
    $flashMessages[] = ['type' => 'danger', 'text' => htmlspecialchars($error)];
}

$pageTitle  = 'Login';
$activePage = 'login';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5" style="max-width: 420px;">
    <div class="d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-box-arrow-in-right fs-3 text-primary"></i>
      <h2 class="mb-0">Log In</h2>
    </div>

    <form action="login.php" method="POST" novalidate>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email"
               value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Log In</button>
    </form>

    <p class="text-center mt-3 mb-0">
      Don't have an account? <a href="register.php">Register</a>
    </p>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>