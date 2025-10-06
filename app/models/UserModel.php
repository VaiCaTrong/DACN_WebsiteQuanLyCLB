<?php
class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getUserById($userId)
    {
        $stmt = $this->db->prepare("SELECT id, username, avatar FROM account WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}