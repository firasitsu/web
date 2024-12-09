<?php
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../controller/postcontroller.php';

// List of bad words to filter
$badWords = ['fuck', 'badword2', 'badword3']; // Add your list of bad words here

// Function to filter bad words
function filterBadWords($text, $badWords) {
    foreach ($badWords as $badWord) {
        $text = str_ireplace($badWord, str_repeat('*', strlen($badWord)), $text);
    }
    return $text;
}

$controller = new PostController($pdo);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];
    
    $post = $controller->getPostById($postId);
} else {
    echo "Aucun post trouvé pour l'édition.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['title'];
    $paragraph = $_POST['paragraph'];
    $picture = $_FILES['picture'];

    // Apply bad word filter to title and paragraph
    $filteredTitle = filterBadWords($title, $badWords);
    $filteredParagraph = filterBadWords($paragraph, $badWords);

    if ($picture['error'] === UPLOAD_ERR_OK) {
        
        $pictureName = basename($picture['name']);
        $picturePath = 'uploads/' . $pictureName;

        
        $targetPath = __DIR__ . '/../public/' . $picturePath;
        move_uploaded_file($picture['tmp_name'], $targetPath);

        // Update post with filtered title and paragraph, and new picture
        $controller->updatePost($postId, $filteredTitle, $filteredParagraph, $picturePath);
    } else {
        // Update post with filtered title and paragraph (without changing picture)
        $controller->updatePost($postId, $filteredTitle, $filteredParagraph);
    }

    header("Location: back.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Post</title>
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

        form img {
            display: block;
            margin: 10px 0;
            max-width: 100%;
            border-radius: 5px;
        }

        form button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            display: block;
            width: 150px;
            margin: 0 auto;
        }

        form button:hover {
            background-color: #2980b9;
        }

        .error-message {
            text-align: center;
            font-size: 1.2em;
            color: #e74c3c;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Modifier le Post</h1>

    <?php if (isset($post)): ?>
        <form action="edit.php?id=<?= htmlspecialchars($post['id']) ?>" method="POST" enctype="multipart/form-data">
            <label for="title">Titre:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

            <label for="paragraph">Paragraphe:</label>
            <textarea name="paragraph" required><?= htmlspecialchars($post['paragraph']) ?></textarea>

            <label for="picture">Modifier l'image:</label>
            <input type="file" name="picture">
            <img src="../public/<?= htmlspecialchars($post['picture']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">

            <button type="submit">Modifier le Post</button>
        </form>
    <?php else: ?>
        <p class="error-message">Post non trouvé.</p>
    <?php endif; ?>
</body>
</html>
