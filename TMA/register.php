<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// If already logged in, there's no need to register again.
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = trim($_POST['name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // ---- Validation ----
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Please enter your name (max 100 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Password and confirm password do not match.';
    }

    // ---- Make sure this email isn't already registered ----
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT `id` FROM `users` WHERE `email` = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'An account with that email already exists.';
        }
        mysqli_stmt_close($stmt);
    }

    // ---- Create the account ----
    if (empty($errors)) {
        // Never store the plain-text password - only the hash.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO `users` (`name`, `email`, `password`) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = ['type' => 'success', 'text' => '<strong>Account created!</strong> You can now log in.'];
            header("Location: login.php");
            exit;
        } else {
            $errors[] = 'Something went wrong creating your account. Please try again.';
            mysqli_stmt_close($stmt);
        }
    }
}

$flashMessages = [];
foreach ($errors as $error) {
    $flashMessages[] = ['type' => 'danger', 'text' => htmlspecialchars($error)];
}

$pageTitle  = 'Register';
$activePage = 'register';
require_once __DIR__ . '/includes/header.php';
?>

  <div class="container my-5" style="max-width: 480px;">
    <div class="d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-person-plus fs-3 text-primary"></i>
      <h2 class="mb-0">Create an Account</h2>
    </div>

    <form action="register.php" method="POST" novalidate>
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" maxlength="100"
               value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" maxlength="255"
               value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
        <div class="form-text">At least 8 characters.</div>
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <p class="text-center mt-3 mb-0">
      Already have an account? <a href="login.php">Log in</a>
    </p>
  </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>