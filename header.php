<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'G-Site';
$flash = get_flash();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | G-Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">G-Site</a>
    <?php if (is_authenticated()): ?>
        <nav><span>Hi, <?= e($_SESSION['user_name']) ?></span> <a href="logout.php">Log out</a></nav>
    <?php endif; ?>
</header>
<main class="container">
    <?php if ($flash): ?><div class="alert <?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div><?php endif; ?>
