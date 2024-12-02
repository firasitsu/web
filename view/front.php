<?php
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../controller/postcontroller.php';


$controller = new PostController($pdo);


$posts = $controller->getPosts();


function getCommentsForPost($postId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function formatTimestamp($timestamp) {
    return date("d M Y, H:i", strtotime($timestamp)); // Example: 01 Dec 2024, 15:30
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Posts</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: url('sm.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        header {
            background-color: #059ea3; 
            color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        header h1 {
            margin: 0;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .post-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            padding: 40px 20px;
        }

        .post {
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 300px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            color: #333;
            margin-bottom: 40px;
        }

        .post:hover {
            transform: scale(1.05);
        }

        .post img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .post h2 {
            font-size: 22px;
            color: #333;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .post p {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            text-overflow: ellipsis;
            overflow: hidden;
            height: 100px;
        }

        .comments {
            background-color: #f1f1f1;
            padding: 10px;
            margin-top: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            max-height: 120px;
            overflow-y: auto;
            color: #333;
        }

        .comments p {
            margin: 0;
            word-wrap: break-word;
        }

        .comments strong {
            font-weight: 600;
            color: #059ea3;
        }

        .comment-time {
            display: block;
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .no-comments {
            color: #aaa;
            font-style: italic;
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #ddd;
            background-color: #222;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <header>
        <h1>MINDBLOOM</h1>
    </header>

    <div class="post-container">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <img src="../public/<?= htmlspecialchars($post['picture']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                    <p><?= nl2br(htmlspecialchars($post['paragraph'])) ?></p>

                    
                    <div class="comments">
                        <?php
                        
                        $comments = getCommentsForPost($post['id']);
                        if (!empty($comments)): 
                            foreach ($comments as $comment): ?>
                                <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= nl2br(htmlspecialchars($comment['text'])) ?>
                                    <span class="comment-time"><?= formatTimestamp($comment['time']) ?></span>
                                </p>
                            <?php endforeach;
                        else: ?>
                            <p class="no-comments">Aucun commentaire pour ce post.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-comments">Aucun post à afficher.</p>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?= date('Y') ?> MINDBLOOM - Tous droits réservés.
    </footer>
</body>
</html>
