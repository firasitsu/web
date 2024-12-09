<?php
class Comment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    
    public function addComment($postId, $username, $text) {
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, username, text) VALUES (?, ?, ?)");
        return $stmt->execute([$postId, $username, $text]);
    }

    
    public function getCommentsByPostId($postId) {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY time DESC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = $_POST['post_id'];
            $username = htmlspecialchars($_POST['username']);
            $text = htmlspecialchars($_POST['text']);
    
            $commentModel = new Comment($this->db);
            $commentModel->addComment($postId, $username, $text);
    
            
            header("Location: index.php?action=viewPost&id=$postId");
            exit;
        }
    }
    public function viewPost($id) {
        $post = $this->postModel->getPostById($id);
    
        $commentModel = new Comment($this->db);
        $comments = $commentModel->getCommentsByPostId($id);
    
        require 'views/postView.php';
    }
    public function deleteComment($commentId) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
    }
     
    
}
?>
