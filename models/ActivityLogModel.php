<?php

class ActivityLogModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Insert activity log
     */
    public function insertLog(int $user_id, string $action, ?string $description = null): bool {
        $sql = "INSERT INTO activity_logs (user_id, action, description)
                VALUES (:user_id, :action, :description)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id'     => $user_id,
            ':action'      => $action,
            ':description' => $description
        ]);
    }

    /**
     * Get logs with user info
     */
    public function getLogs($user_id = null, $action = null, int $limit = 50, int $offset = 0): array {
        $sql = "
            SELECT 
                logs.id,
                logs.user_id,
                logs.action,
                logs.description,
                logs.created_at,
                users.prenom,
                users.nom,
                users.email,
                CONCAT(users.prenom, ' ', users.nom) AS username
            FROM activity_logs AS logs
            LEFT JOIN users ON users.id = logs.user_id
            WHERE 1=1
        ";

        $params = [];

        // ✅ correct checks
        if ($user_id !== null) {
            $sql .= " AND logs.user_id = :user_id";
            $params[':user_id'] = (int)$user_id;
        }

        if ($action !== null && $action !== '') {
            $sql .= " AND logs.action = :action";
            $params[':action'] = $action;
        }

        $sql .= " ORDER BY logs.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // bind filters
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        // bind pagination (VERY IMPORTANT)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count logs (for pagination)
     */
    public function countLogs($user_id = null, $action = null): int {
        $sql = "SELECT COUNT(*) FROM activity_logs WHERE 1=1";
        $params = [];

        if ($user_id !== null) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = (int)$user_id;
        }

        if ($action !== null && $action !== '') {
            $sql .= " AND action = :action";
            $params[':action'] = $action;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    /**
     * Delete one log
     */
    public function deleteLog(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM activity_logs WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}