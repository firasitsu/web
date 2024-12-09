<?php
require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['postId']) && isset($data['username'])) {
    $postId = $data['postId'];
    $username = $data['username'];

    // Check if the user has already liked the post
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND username = ?");
    $stmt->execute([$postId, $username]);
    $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingLike) {
        // Remove the like
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND username = ?");
        $stmt->execute([$postId, $username]);
        echo json_encode(['success' => true, 'action' => 'unliked']);
    } else {
        // Add the like
        $stmt = $pdo->prepare("INSERT INTO likes (post_id, username) VALUES (?, ?)");
        $stmt->execute([$postId, $username]);
        echo json_encode(['success' => true, 'action' => 'liked']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
