<?php
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../controller/postcontroller.php';

$controller = new PostController($pdo);

// List of bad words to filter
$badWords = ['fuck', 'badword2', 'badword3']; // Add your list of bad words here

// Function to filter bad words
function filterBadWords($text, $badWords) {
    foreach ($badWords as $badWord) {
        $text = str_ireplace($badWord, str_repeat('*', strlen($badWord)), $text);
    }
    return $text;
}

// Delete a comment
if (isset($_POST['delete_comment'])) {
    $commentId = $_POST['delete_comment'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    header("Location: back.php");
    exit;
}

// Delete a post
if (isset($_POST['delete'])) {
    $postId = $_POST['delete'];
    $controller->deletePost($postId);
    header("Location: back.php"); 
    exit;
}

// Add a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
    $title = filterBadWords($_POST['title'], $badWords);
    $paragraph = filterBadWords($_POST['paragraph'], $badWords);
    $picture = $_FILES['picture'];

    if ($picture['error'] === UPLOAD_ERR_OK) {
        $pictureName = basename($picture['name']);
        $picturePath = 'uploads/' . $pictureName;
        $targetPath = __DIR__ . '/../public/' . $picturePath;
        move_uploaded_file($picture['tmp_name'], $targetPath);
        $controller->createPost($title, $paragraph, $picturePath);
    } else {
        $controller->createPost($title, $paragraph, null);
    }

    header("Location: back.php");
    exit;
}

$posts = $controller->getPosts();

// Truncate paragraph to a limit of 5 words for display purposes
function truncateParagraph($text, $wordLimit = 5) {
    $words = explode(' ', $text);
    if (count($words) > $wordLimit) {
        $words = array_slice($words, 0, $wordLimit);
        return implode(' ', $words) . '...';
    }
    return $text;
}

// Fetch comments for a specific post
function getCommentsForPost($postId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch like count for a specific post
function getLikeCountForPost($postId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
    $stmt->execute([$postId]);
    $likeData = $stmt->fetch(PDO::FETCH_ASSOC);
    return $likeData['like_count'];
}

// Filter comments before saving to the database
if (isset($_POST['add_comment'])) {
    $commentText = filterBadWords($_POST['comment_text'], $badWords);
    $postId = $_POST['post_id'];
    $username = $_POST['username']; // You can fetch this from session if needed

    // Insert the comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, username, text) VALUES (?, ?, ?)");
    $stmt->execute([$postId, $username, $commentText]);

    header("Location: back.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Posts</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 95%;
            max-width: 1000px;
            border-collapse: collapse;
            margin: 0 auto 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        td {
            background-color: #f8f8f8;
        }

        td img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        td form {
            display: inline-block;
            margin: 5px 0;  
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            width: 150px;
            text-align: center;
            margin: 5px 0;  
        }

        button:hover {
            background-color: #2980b9;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        form input[type="text"],
        form textarea,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
            margin: 0 auto;
        }

        form button:hover {
            background-color: #2980b9;
        }

        .no-posts {
            text-align: center;
            font-size: 1.2em;
            color: #777;
        }

        .comments {
            font-size: 0.9em;
            color: #555;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<h1>Gestion des Posts</h1>

<div class="table-container">
    <?php if (!empty($posts)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Picture</th>
                    <th>Paragraph</th>
                    <th>Likes</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): 
                    $likeCount = getLikeCountForPost($post['id']);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($post['id']) ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td>
                            <img src="../public/<?= htmlspecialchars($post['picture']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </td>
                        <td>
                            <p><?= htmlspecialchars(truncateParagraph($post['paragraph'])) ?></p>
                        </td>
                        <td><?= $likeCount ?></td>
                        <td>
                            <?php
                            $comments = getCommentsForPost($post['id']);
                            if (!empty($comments)): 
                                foreach ($comments as $comment): ?>
                                    <div class="comments">
                                        <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                                        <p><?= htmlspecialchars($comment['text']) ?></p>
                                        <form action="back.php" method="POST">
                                            <button type="submit" name="delete_comment" value="<?= $comment['id'] ?>">Supprimer</button>
                                        </form>
                                        <form action="edit_comment.php" method="GET">
                                            <button type="submit" name="comment_id" value="<?= $comment['id'] ?>">Modifier</button>
                                        </form>
                                    </div>
                                <?php endforeach;
                            else: ?>
                                <p>Aucun commentaire.</p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="back.php" method="POST">
                                <button type="submit" name="delete" value="<?= $post['id'] ?>">Supprimer</button>
                            </form>

                            <form action="edit.php" method="GET">
                                <button type="submit" name="id" value="<?= $post['id'] ?>">Modifier</button>
                            </form>

                            <form action="comment.php" method="GET">
                                <button type="submit" name="post_id" value="<?= $post['id'] ?>">Commenter</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-posts">Aucun post à afficher.</p>
    <?php endif; ?>
</div>

<h2>Ajouter un Nouveau Post</h2>
<form action="back.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="add_post" value="1">
    <label for="title">Titre:</label>
    <input type="text" name="title" required><br><br>

    <label for="paragraph">Paragraphe:</label>
    <textarea name="paragraph" required></textarea><br><br>

    <label for="picture">Image:</label>
    <input type="file" name="picture"><br><br>

    <button type="submit">Ajouter le Post</button>
</form>

</body>
</html>
