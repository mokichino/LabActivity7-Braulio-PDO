<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_auth();
require_once __DIR__ . '/db.php';

$statement = $pdo->query('SELECT p.post_id, p.user_id, p.body, p.created_at, p.updated_at, u.name,
    (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) AS comment_count
    FROM posts p INNER JOIN users u ON u.user_id = p.user_id ORDER BY p.created_at DESC, p.post_id DESC');
$posts = $statement->fetchAll();
$pageTitle = 'News feed';
require __DIR__ . '/header.php';
?>
<section class="intro"><p class="muted">THE COMMUNITY FEED</p><h1>What is happening?</h1><p class="muted">Share a thought, then join the conversation.</p></section>
<section class="panel">
    <form action="create_post.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="post_body">Write a post</label>
        <textarea id="post_body" name="body" required maxlength="5000" placeholder="Share something with the community..."></textarea>
        <p><button type="submit">Publish post</button></p>
    </form>
</section>
<?php if (!$posts): ?><p class="muted">No posts yet. Be the first to publish.</p><?php endif; ?>
<?php foreach ($posts as $post): ?>
<article class="post">
    <div class="post-header"><div><span class="author"><?= e($post['name']) ?></span><div class="meta"><?= e($post['created_at']) ?><?php if ($post['updated_at']): ?> &middot; edited<?php endif; ?></div></div><?php if ((int)$post['user_id'] === (int)$_SESSION['user_id']): ?><div class="actions"><a class="button secondary" href="edit_post.php?id=<?= (int)$post['post_id'] ?>">Edit</a><form class="inline" action="delete_post.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="post_id" value="<?= (int)$post['post_id'] ?>"><button class="danger" type="submit">Delete</button></form></div><?php endif; ?></div>
    <div class="post-body"><?= e($post['body']) ?></div>
    <div class="comments"><h3><?= (int)$post['comment_count'] ?> comment<?= (int)$post['comment_count'] === 1 ? '' : 's' ?></h3>
        <?php $commentStatement = $pdo->prepare('SELECT c.comment_id, c.user_id, c.body, c.created_at, c.updated_at, u.name FROM comments c INNER JOIN users u ON u.user_id = c.user_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC, c.comment_id ASC'); $commentStatement->execute(['post_id' => $post['post_id']]); $comments = $commentStatement->fetchAll(); ?>
        <?php foreach ($comments as $comment): ?><div class="comment"><div><strong><?= e($comment['name']) ?></strong> <span class="meta"><?= e($comment['created_at']) ?><?php if ($comment['updated_at']): ?> &middot; edited<?php endif; ?></span></div><div class="comment-body"><?= e($comment['body']) ?></div><?php if ((int)$comment['user_id'] === (int)$_SESSION['user_id']): ?><div class="actions"><a href="edit_comment.php?id=<?= (int)$comment['comment_id'] ?>">Edit</a><form class="inline" action="delete_comment.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="comment_id" value="<?= (int)$comment['comment_id'] ?>"><button class="danger" type="submit">Delete</button></form></div><?php endif; ?></div><?php endforeach; ?>
        <form action="add_comment.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="post_id" value="<?= (int)$post['post_id'] ?>"><label for="comment_<?= (int)$post['post_id'] ?>">Add a comment</label><textarea id="comment_<?= (int)$post['post_id'] ?>" name="body" required maxlength="2000" placeholder="Write a reply..."></textarea><p><button type="submit">Comment</button></p></form>
    </div>
</article>
<?php endforeach; ?>
<?php require __DIR__ . '/footer.php'; ?>
