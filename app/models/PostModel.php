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
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.content, p.category, p.author_id, p.team_id, p.created_at, t.name as team_name
            FROM posts p
            LEFT JOIN team t ON p.team_id = t.id
            WHERE p.parent_id IS NULL -- Chỉ lấy bài viết chính
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.content, p.category, p.author_id, p.team_id, p.created_at, t.name as team_name
            FROM posts p
            LEFT JOIN team t ON p.team_id = t.id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // === MỚI: Lấy danh sách bài viết phụ ===
    public function getSubPosts($parent_id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM posts 
            WHERE parent_id = :parent_id 
            ORDER BY id ASC
        ");
        $stmt->execute([':parent_id' => $parent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostImages($post_id)
    {
        $stmt = $this->db->prepare("SELECT id, image_path FROM post_images WHERE post_id = :post_id");
        $stmt->execute([':post_id' => $post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === CẬP NHẬT: Thêm tham số parent_id ===
    public function addPost($title, $content, $category, $author_id, $team_id = null, $parent_id = null)
    {
        $created_at = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("
            INSERT INTO posts (title, content, category, author_id, team_id, created_at, parent_id)
            VALUES (:title, :content, :category, :author_id, :team_id, :created_at, :parent_id)
        ");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category' => $category,
            ':author_id' => $author_id,
            ':team_id' => $team_id,
            ':created_at' => $created_at,
            ':parent_id' => $parent_id
        ]);
        return $this->db->lastInsertId();
    }

    public function updatePost($id, $title, $content, $category, $team_id = null)
    {
        $stmt = $this->db->prepare("
            UPDATE posts 
            SET title = :title, 
                content = :content, 
                category = :category,
                team_id = :team_id 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category' => $category,
            ':team_id' => $team_id,
            ':id' => $id
        ]);
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

    public function addOrUpdateReaction($post_id, $user_id, $reaction_type)
    {
        $sql = "
            INSERT INTO post_reactions (post_id, user_id, reaction_type)
            VALUES (:post_id, :user_id, :reaction_type)
            ON DUPLICATE KEY UPDATE reaction_type = VALUES(reaction_type)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id,
            ':reaction_type' => $reaction_type
        ]);
    }

    public function removeReaction($post_id, $user_id)
    {
        $sql = "DELETE FROM post_reactions WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id
        ]);
    }

    public function getUserReaction($post_id, $user_id)
    {
        $sql = "SELECT reaction_type FROM post_reactions WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':post_id' => $post_id, ':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getReactionsSummary($post_id)
    {
        $sql = "
            SELECT reaction_type, COUNT(*) as count
            FROM post_reactions
            WHERE post_id = :post_id
            GROUP BY reaction_type
            ORDER BY count DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':post_id' => $post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostsByTeamId($team_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.content, p.category, p.author_id, p.created_at, 
                   a.username as author_name, a.avatar as author_avatar
            FROM posts p
            JOIN account a ON p.author_id = a.id
            WHERE p.team_id = :team_id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([':team_id' => $team_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePost($post_id, $user_id, $is_admin = false)
    {
        // Kiểm tra quyền: admin hoặc là tác giả
        $post = $this->getPostById($post_id);
        if (!$post) return false;
        if (!$is_admin && $post['author_id'] != $user_id) return false;

        // Xóa các bài viết con (sub-posts) nếu có
        $subPosts = $this->getSubPosts($post_id);
        foreach ($subPosts as $sub) {
            $this->deletePost($sub['id'], $user_id, true); // Gọi đệ quy để xóa sạch ảnh/reaction của bài con
        }

        // Xóa ảnh liên quan
        $this->db->prepare("DELETE FROM post_images WHERE post_id = :id")->execute([':id' => $post_id]);
        // Xóa reaction
        $this->db->prepare("DELETE FROM post_reactions WHERE post_id = :id")->execute([':id' => $post_id]);
        // Xóa comments
        $this->db->prepare("DELETE FROM comments WHERE post_id = :id")->execute([':id' => $post_id]);
        // Xóa bài viết
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute([':id' => $post_id]);
    }

    public function getLatestPosts($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.content, p.category, p.author_id, p.team_id, p.created_at, t.name as team_name
            FROM posts p
            LEFT JOIN team t ON p.team_id = t.id
            WHERE p.parent_id IS NULL
            ORDER BY p.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}