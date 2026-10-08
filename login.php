<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_guest();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } else {
        $statement = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('index.php');
        }
        $error = 'The email or password is incorrect.';
    }
}
$pageTitle = 'Log in';
require __DIR__ . '/header.php';
?>
<section class="panel narrow">
    <p class="muted">YOUR DAILY FEED</p><h1>Log in</h1>
    <?php if ($error): ?><div class="alert error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="email">Email</label><input id="email" type="email" name="email" required maxlength="255" value="<?= old('email') ?>">
        <label for="password">Password</label><input id="password" type="password" name="password" required maxlength="72">
        <p><button type="submit">Log in</button></p>
    </form>
    <p class="muted">New here? <a href="register.php">Create an account</a>.</p>
</section>
<?php require __DIR__ . '/footer.php'; ?>
