<?php
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../model/comment.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $username = $_POST['username'];
    $text = $_POST['text'];

    $commentModel = new Comment($pdo);
    $commentModel->addComment($postId, $username, $text);

    // Redirect back to the front page after comment submission
    header("Location: front.php");
    exit;
}
