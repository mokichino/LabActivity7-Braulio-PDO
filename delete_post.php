<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php'); verify_csrf(); $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
if (!$postId) { flash('error', 'Invalid post.'); redirect('index.php'); }
$statement = $pdo->prepare('DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id'); $statement->execute(['post_id' => $postId, 'user_id' => $_SESSION['user_id']]); flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Post deleted.' : 'Post not found or not owned by you.'); redirect('index.php');
