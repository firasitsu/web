<?php
require_once __DIR__ . '/../model/post.php';

class PostController {
    private $postModel;

    public function __construct($pdo) {
        $this->postModel = new Post($pdo);
    }

    
    public function createPost($title, $paragraph, $picture = null) {
        if ($picture) {
            
            $this->postModel->create($title, $paragraph, $picture);
        } else {
            
            $this->postModel->create($title, $paragraph, null);
        }
    }

    
    public function getPosts() {
        return $this->postModel->getAll();
    }

    
    public function deletePost($postId) {
        $this->postModel->delete($postId);
    }

    
    public function getPostById($id) {
        return $this->postModel->getById($id);
    }

    
    public function updatePost($id, $title, $paragraph, $picturePath = null) {
        if ($picturePath) {
            
            $this->postModel->updateWithImage($id, $title, $paragraph, $picturePath);
        } else {
            
            $this->postModel->update($id, $title, $paragraph);
        }
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
