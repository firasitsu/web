<?php
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../controller/postcontroller.php';


$controller = new PostController($pdo);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['title'];
    $paragraph = $_POST['paragraph'];
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Post</title>
</head>
<body>
    <h1>Ajouter un Nouveau Post</h1>

    <form action="add.php" method="POST" enctype="multipart/form-data">
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
