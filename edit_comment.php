<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
$commentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$commentId) redirect('index.php');
$statement = $pdo->prepare('SELECT comment_id, body FROM comments WHERE comment_id = :comment_id AND user_id = :user_id'); $statement->execute(['comment_id' => $commentId, 'user_id' => $_SESSION['user_id']]); $comment = $statement->fetch();
if (!$comment) { flash('error', 'Comment not found or not owned by you.'); redirect('index.php'); }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $body = trim((string)($_POST['body'] ?? '')); $error = validate_text($body, 'Comment', 2000);
    if (!$error) { $update = $pdo->prepare('UPDATE comments SET body = :body, updated_at = CURRENT_TIMESTAMP WHERE comment_id = :comment_id AND user_id = :user_id'); $update->execute(['body' => $body, 'comment_id' => $commentId, 'user_id' => $_SESSION['user_id']]); flash('success', 'Comment updated.'); redirect('index.php'); }
    $comment['body'] = $body;
}
$pageTitle = 'Edit comment'; require __DIR__ . '/header.php';
?>
<section class="panel"><p class="muted">YOUR COMMENT</p><h1>Edit comment</h1><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="body">Comment text</label><textarea id="body" name="body" required maxlength="2000"><?= e($comment['body']) ?></textarea><p class="actions"><button type="submit">Save changes</button><a class="button secondary" href="index.php">Cancel</a></p></form></section>
<?php require __DIR__ . '/footer.php'; ?>
