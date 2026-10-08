<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$postId) redirect('index.php');
$statement = $pdo->prepare('SELECT post_id, body FROM posts WHERE post_id = :post_id AND user_id = :user_id'); $statement->execute(['post_id' => $postId, 'user_id' => $_SESSION['user_id']]); $post = $statement->fetch();
if (!$post) { flash('error', 'Post not found or not owned by you.'); redirect('index.php'); }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $body = trim((string)($_POST['body'] ?? '')); $error = validate_text($body, 'Post', 5000);
    if (!$error) { $update = $pdo->prepare('UPDATE posts SET body = :body, updated_at = CURRENT_TIMESTAMP WHERE post_id = :post_id AND user_id = :user_id'); $update->execute(['body' => $body, 'post_id' => $postId, 'user_id' => $_SESSION['user_id']]); flash('success', 'Post updated.'); redirect('index.php'); }
    $post['body'] = $body;
}
$pageTitle = 'Edit post'; require __DIR__ . '/header.php';
?>
<section class="panel"><p class="muted">YOUR POST</p><h1>Edit post</h1><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="body">Post text</label><textarea id="body" name="body" required maxlength="5000"><?= e($post['body']) ?></textarea><p class="actions"><button type="submit">Save changes</button><a class="button secondary" href="index.php">Cancel</a></p></form></section>
<?php require __DIR__ . '/footer.php'; ?>
