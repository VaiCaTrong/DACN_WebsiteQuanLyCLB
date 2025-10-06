<?php
require_once __DIR__ . '/../config/Database.php';

class PostModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        if ($this->db === null) {
            die("Không thể kết nối tới cơ sở dữ liệu.");
        }
    }
    

    public function getAllPosts()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT p.* FROM posts p ORDER BY p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getPostImages($post_id)
    {
        $stmt = $this->db->prepare("SELECT id, image_path FROM post_images WHERE post_id = :post_id");
        $stmt->execute([':post_id' => $post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPost($title, $content, $author_id, $team_id = null)
    {
        $created_at = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("INSERT INTO posts (title, content, author_id, team_id, created_at) VALUES (:title, :content, :author_id, :team_id, :created_at)");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':author_id' => $author_id,
            ':team_id' => $team_id,
            ':created_at' => $created_at
        ]);
        return $this->db->lastInsertId();
    }

    public function updatePost($id, $title, $content, $team_id = null)
    {
        $stmt = $this->db->prepare("UPDATE posts SET title = :title, content = :content, team_id = :team_id WHERE id = :id");
        return $stmt->execute([':title' => $title, ':content' => $content, ':team_id' => $team_id, ':id' => $id]);
    }

    public function deletePost($id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM post_images WHERE post_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }

    public function addPostImage($post_id, $image_path)
    {
        $stmt = $this->db->prepare("INSERT INTO post_images (post_id, image_path) VALUES (:post_id, :image_path)");
        return $stmt->execute([':post_id' => $post_id, ':image_path' => $image_path]);
    }

    public function deletePostImage($image_id)
    {
        $stmt = $this->db->prepare("DELETE FROM post_images WHERE id = :id");
        return $stmt->execute([':id' => $image_id]);
    }

    public function getPostImageById($image_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM post_images WHERE id = :id");
        $stmt->execute([':id' => $image_id]);
        return $stmt->fetch();
    }

    public function addComment($post_id, $user_id, $content)
    {
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)");
        return $stmt->execute([':post_id' => $post_id, ':user_id' => $user_id, ':content' => $content]);
    }

    public function getCommentsByPostId($post_id)
    {
        $stmt = $this->db->prepare("
        SELECT c.*, a.role, a.fullname, a.avatar
        FROM comments c
        JOIN account a ON c.user_id = a.id
        WHERE c.post_id = :post_id
        ORDER BY c.created_at DESC
    ");
        $stmt->execute([':post_id' => $post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT avatar, username FROM account WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteComment($comment_id)
    {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute([':id' => $comment_id]);
    }

    public function getCommentById($comment_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE id = :id");
        $stmt->execute([':id' => $comment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}