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
}
?>
