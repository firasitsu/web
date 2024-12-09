<?php
require_once __DIR__ . '/../db/connection.php';

// List of bad words to filter
$badWords = ['fuck', 'badword2', 'badword3']; // Add your list of bad words here

// Function to filter bad words
function filterBadWords($text, $badWords) {
    foreach ($badWords as $badWord) {
        $text = str_ireplace($badWord, str_repeat('*', strlen($badWord)), $text);
    }
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $postId = $_POST['post_id'];
    $username = $_POST['username'];
    $text = $_POST['text'];

    // Apply bad word filter to comment text
    $filteredText = filterBadWords($text, $badWords);

    // Insert the filtered comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, username, text) VALUES (?, ?, ?)");
    $stmt->execute([$postId, $username, $filteredText]);

    header("Location: back.php");
    exit;
}

if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];

    $stmt = $pdo->prepare("SELECT * FROM post WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Commentaire</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form input[type="text"], form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            width: 150px;
            margin: 0 auto;
            display: block;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<h1>Ajouter un Commentaire pour le Post: <?= htmlspecialchars($post['title']) ?></h1>

<div class="form-container">
    <form action="comment.php" method="POST">
        <input type="hidden" name="post_id" value="<?= $postId ?>">

        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" required><br><br>

        <label for="text">Commentaire:</label>
        <textarea name="text" required></textarea><br><br>

        <button type="submit" name="submit_comment">Soumettre le Commentaire</button>
    </form>
</div>

</body>
</html>
