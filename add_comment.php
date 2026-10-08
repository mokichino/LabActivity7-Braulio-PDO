<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf(); $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT); $body = trim((string)($_POST['body'] ?? '')); $error = validate_text($body, 'Comment', 2000);
if (!$postId || $error) { flash('error', $error ?? 'The selected post is invalid.'); redirect('index.php'); }
$exists = $pdo->prepare('SELECT post_id FROM posts WHERE post_id = :post_id'); $exists->execute(['post_id' => $postId]);
if (!$exists->fetch()) { flash('error', 'That post no longer exists.'); redirect('index.php'); }
$statement = $pdo->prepare('INSERT INTO comments (post_id, user_id, body) VALUES (:post_id, :user_id, :body)'); $statement->execute(['post_id' => $postId, 'user_id' => $_SESSION['user_id'], 'body' => $body]); flash('success', 'Comment added.'); redirect('index.php');
