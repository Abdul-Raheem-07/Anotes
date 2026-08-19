<?php
// $activePage should be set by the including page, e.g. $activePage = 'home';
if (!isset($activePage)) {
    $activePage = '';
}

function navClass($page, $active) {
    return $page === $active ? 'nav-link active px-3' : 'nav-link px-3';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ANotes' : 'ANotes - Notes Manager'; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold fs-4" href="index.php">
        <span class="text-primary">A</span>Notes
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="<?php echo navClass('home', $activePage); ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="<?php echo navClass('about', $activePage); ?>" href="about.php">About</a></li>
          <li class="nav-item"><a class="<?php echo navClass('services', $activePage); ?>" href="services.php">Services</a></li>
          <li class="nav-item"><a class="<?php echo navClass('contact', $activePage); ?>" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="<?php echo navClass('trash', $activePage); ?>" href="trash.php"><i class="bi bi-trash3"></i> Trash</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Flash alerts (set $flashMessages = [['type' => 'success', 'text' => '...'], ...] before include) -->
  <?php if (!empty($flashMessages)): ?>
    <div class="container mt-3">
      <?php foreach ($flashMessages as $flash): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?> alert-dismissible fade show" role="alert">
          <?php echo $flash['text']; // pre-built safe strings only ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>