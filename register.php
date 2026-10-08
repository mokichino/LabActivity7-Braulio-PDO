<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_guest();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Name is required and must be 100 characters or fewer.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) $errors[] = 'Enter a valid email address.';
    if (mb_strlen($password) < 8 || mb_strlen($password) > 72) $errors[] = 'Password must be between 8 and 72 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $statement = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
            $statement->execute(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Account created. You can now log in.');
            redirect('login.php');
        } catch (PDOException $exception) {
            if ((int)$exception->errorInfo[1] === 1062) $errors[] = 'That email address is already registered.';
            else $errors[] = 'Registration could not be completed.';
        }
    }
}
$pageTitle = 'Create account';
require __DIR__ . '/header.php';
?>
<section class="panel narrow">
    <p class="muted">WELCOME TO G-SITE</p><h1>Create account</h1>
    <?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="name">Name</label><input id="name" name="name" required maxlength="100" value="<?= old('name') ?>">
        <label for="email">Email</label><input id="email" type="email" name="email" required maxlength="255" value="<?= old('email') ?>">
        <label for="password">Password</label><input id="password" type="password" name="password" required minlength="8" maxlength="72">
        <label for="confirm_password">Confirm password</label><input id="confirm_password" type="password" name="confirm_password" required minlength="8" maxlength="72">
        <p><button type="submit">Register</button></p>
    </form>
    <p class="muted">Already have an account? <a href="login.php">Log in</a>.</p>
</section>
<?php require __DIR__ . '/footer.php'; ?>
