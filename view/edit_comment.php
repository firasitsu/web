<?php
require_once __DIR__ . '/../db/connection.php';


if (isset($_GET['comment_id'])) {
    $commentId = $_GET['comment_id'];

    
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment) {
        echo "Comment not found!";
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_comment'])) {
    $commentId = $_POST['comment_id'];
    $text = $_POST['text'];

    
    $stmt = $pdo->prepare("UPDATE comments SET text = ? WHERE id = ?");
    $stmt->execute([$text, $commentId]);

    header("Location: back.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Commentaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            height: 100px;
        }

        form button {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            width: 150px;
        }

        form button:hover {
            background-color: #2980b9;
        }

        h2 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

<h2>Modifier Commentaire</h2>

<form action="edit_comment.php" method="POST">
    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">

    <label for="text">Commentaire:</label>
    <textarea name="text" required><?= htmlspecialchars($comment['text']) ?></textarea>

    <button type="submit" name="update_comment">Mettre à jour le Commentaire</button>
</form>

</body>
</html>
