<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php'; require_auth(); require_once __DIR__ . '/db.php'; require_once __DIR__ . '/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf(); $body = trim((string)($_POST['body'] ?? '')); $error = validate_text($body, 'Post', 5000);
if ($error) { flash('error', $error); redirect('index.php'); }
$statement = $pdo->prepare('INSERT INTO posts (user_id, body) VALUES (:user_id, :body)'); $statement->execute(['user_id' => $_SESSION['user_id'], 'body' => $body]); flash('success', 'Post published.'); redirect('index.php');
