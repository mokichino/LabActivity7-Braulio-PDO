<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php'); verify_csrf(); $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
if (!$commentId) { flash('error', 'Invalid comment.'); redirect('index.php'); }
$statement = $pdo->prepare('DELETE FROM comments WHERE comment_id = :comment_id AND user_id = :user_id'); $statement->execute(['comment_id' => $commentId, 'user_id' => $_SESSION['user_id']]); flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Comment deleted.' : 'Comment not found or not owned by you.'); redirect('index.php');
