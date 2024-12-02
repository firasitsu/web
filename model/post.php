<?php
require_once __DIR__ . '/../db/connection.php';

class Post {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function create($title, $paragraph, $picturePath = null) {
        $stmt = $this->pdo->prepare("INSERT INTO post (title, paragraph, picture) VALUES (?, ?, ?)");
        $stmt->execute([$title, $paragraph, $picturePath]);
    }

    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM post ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function delete($postId) {
        $stmt = $this->pdo->prepare("DELETE FROM post WHERE id = ?");
        $stmt->execute([$postId]);
    }

    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM post WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function updateWithImage($id, $title, $paragraph, $picturePath) {
        $stmt = $this->pdo->prepare("UPDATE post SET title = ?, paragraph = ?, picture = ? WHERE id = ?");
        $stmt->execute([$title, $paragraph, $picturePath, $id]);
    }

    
    public function update($id, $title, $paragraph) {
        $stmt = $this->pdo->prepare("UPDATE post SET title = ?, paragraph = ? WHERE id = ?");
        $stmt->execute([$title, $paragraph, $id]);
    }
}
?>
