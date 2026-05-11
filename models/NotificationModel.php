<?php
// models/NotificationModel.php

class NotificationModel
{
    private PDO $db;

    public function __construct()
    {
        require_once __DIR__ . '/Database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ──────────────────────────────────────────────
    // CRUD de base
    // ──────────────────────────────────────────────

    /**
     * Crée une notification pour un utilisateur.
     * Vérifie les doublons sur 24h pour éviter le spam.
     */
    public function create(int $userId, string $type, string $titre, string $message): bool
    {
        // Anti-doublon : pas deux fois le même type dans les dernières 24h
        $check = $this->db->prepare(
            "SELECT id FROM notifications
             WHERE user_id = ? AND type = ?
               AND created_at >= NOW() - INTERVAL 24 HOUR
             LIMIT 1"
        );
        $check->execute([$userId, $type]);
        if ($check->fetch()) {
            return false; // déjà envoyée récemment
        }

        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id, type, titre, message)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$userId, $type, $titre, $message]);
    }

    /** Toutes les notifications d'un utilisateur, les plus récentes en premier */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Nombre de notifications non lues */
    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND lu = 0"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /** Marquer une notification comme lue */
    public function markRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET lu = 1, lu_le = NOW()
             WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    /** Marquer toutes les notifications d'un utilisateur comme lues */
    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET lu = 1, lu_le = NOW()
             WHERE user_id = ? AND lu = 0"
        );
        return $stmt->execute([$userId]);
    }

    /** Supprimer une notification */
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM notifications WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    // ──────────────────────────────────────────────
    // Préférences utilisateur
    // ──────────────────────────────────────────────

    public function getPreferences(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_preferences WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);

        // Créer des préférences par défaut si absentes
        if (!$prefs) {
            $this->db->prepare(
                "INSERT IGNORE INTO user_preferences (user_id) VALUES (?)"
            )->execute([$userId]);
            return $this->getPreferences($userId);
        }
        return $prefs;
    }

    public function savePreferences(int $userId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO user_preferences
                (user_id, notif_rappel_suivi, notif_eau, notif_objectif,
                 notif_poids_alerte, notif_encouragement, notif_consultation,
                 heure_rappel_suivi, objectif_eau_journalier)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                notif_rappel_suivi     = VALUES(notif_rappel_suivi),
                notif_eau              = VALUES(notif_eau),
                notif_objectif         = VALUES(notif_objectif),
                notif_poids_alerte     = VALUES(notif_poids_alerte),
                notif_encouragement    = VALUES(notif_encouragement),
                notif_consultation     = VALUES(notif_consultation),
                heure_rappel_suivi     = VALUES(heure_rappel_suivi),
                objectif_eau_journalier = VALUES(objectif_eau_journalier)"
        );
        return $stmt->execute([
            $userId,
            (int) ($data['notif_rappel_suivi']  ?? 1),
            (int) ($data['notif_eau']            ?? 1),
            (int) ($data['notif_objectif']       ?? 1),
            (int) ($data['notif_poids_alerte']   ?? 1),
            (int) ($data['notif_encouragement']  ?? 1),
            (int) ($data['notif_consultation']   ?? 1),
            $data['heure_rappel_suivi'] ?? '20:00:00',
            (float) ($data['objectif_eau_journalier'] ?? 1.5),
        ]);
    }

    // ──────────────────────────────────────────────
    // Log cron
    // ──────────────────────────────────────────────

    public function logCron(string $rule, int $notified, string $details = ''): void
    {
        $this->db->prepare(
            "INSERT INTO cron_log (rule, users_notified, details) VALUES (?, ?, ?)"
        )->execute([$rule, $notified, $details]);
    }

    public function getLastCronRuns(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cron_log ORDER BY run_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}