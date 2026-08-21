<?php
session_start();
require 'config/database.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only accept POST requests (not direct URL visits)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Fetch the blog to check ownership
$stmt = $pdo->prepare("SELECT user_id FROM blogpost WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

// Authorization check: only the owner can delete
if (!$blog || $blog['user_id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit;
}

// Safe to delete
$stmt = $pdo->prepare("DELETE FROM blogpost WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>