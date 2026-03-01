<?php
class Commande
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function create(int $serviceId, int $clientId, int $freelanceId, string $message = ''): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO commandes (service_id, client_id, freelance_id, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$serviceId, $clientId, $freelanceId, $message]);
    }

    public static function find(int $id): ?array
    {
        $stmt = self::getConnection()->prepare("SELECT c.*, s.titre as service_titre FROM commandes c JOIN services s ON c.service_id = s.id WHERE c.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByUser(int $userId): array
    {
        $stmt = self::getConnection()->prepare("SELECT c.id, c.date_commande, s.titre FROM commandes c JOIN services s ON c.service_id = s.id WHERE c.client_id = ? OR c.freelance_id = ? ORDER BY c.date_commande DESC LIMIT 20");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    public static function getByFreelance(int $freelanceId): array
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT c.*, s.titre as service_titre, s.prix as service_prix, u.prenom as client_prenom, u.nom as client_nom, u.email as client_email 
            FROM commandes c 
            JOIN services s ON c.service_id = s.id 
            JOIN utilisateurs u ON c.client_id = u.id 
            WHERE c.freelance_id = ? ORDER BY c.date_commande DESC");
        $stmt->execute([$freelanceId]);
        return $stmt->fetchAll();
    }

    public static function updateStatut(int $id, string $statut, int $freelanceId): bool
    {
        $stmt = self::getConnection()->prepare("UPDATE commandes SET statut = ? WHERE id = ? AND freelance_id = ?");
        return $stmt->execute([$statut, $id, $freelanceId]);
    }

    public static function setLivrable(int $id, string $url, int $freelanceId): bool
    {
        $stmt = self::getConnection()->prepare("UPDATE commandes SET livrable_url = ?, date_livraison = NOW(), statut = 'livree' WHERE id = ? AND freelance_id = ?");
        return $stmt->execute([$url, $id, $freelanceId]);
    }

    public static function terminerParClient(int $id, int $clientId): bool
    {
        $stmt = self::getConnection()->prepare("UPDATE commandes SET statut = 'terminee' WHERE id = ? AND client_id = ? AND statut = 'livree'");
        return $stmt->execute([$id, $clientId]);
    }

    public static function getByClient(int $clientId): array
    {
        $stmt = self::getConnection()->prepare("SELECT c.*, s.titre as service_titre, u.nom as freelance_nom, u.prenom as freelance_prenom 
            FROM commandes c JOIN services s ON c.service_id = s.id JOIN utilisateurs u ON c.freelance_id = u.id 
            WHERE c.client_id = ? ORDER BY c.date_commande DESC");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public static function getForUserAndContact(int $userId, int $contactId): array
    {
        $stmt = self::getConnection()->prepare("SELECT id FROM commandes WHERE (client_id = ? AND freelance_id = ?) OR (freelance_id = ? AND client_id = ?) ORDER BY date_commande DESC");
        $stmt->execute([$userId, $contactId, $userId, $contactId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getContactsForUser(int $userId): array
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.prenom, u.nom,
                   (SELECT MAX(m.date_envoi) FROM messages m
                    JOIN commandes c ON m.commande_id = c.id
                    WHERE (c.client_id = ? AND c.freelance_id = u.id) OR (c.freelance_id = ? AND c.client_id = u.id)) as last_msg
            FROM utilisateurs u
            WHERE u.id IN (
                SELECT CASE WHEN client_id = ? THEN freelance_id ELSE client_id END
                FROM commandes WHERE client_id = ? OR freelance_id = ?
            ) AND u.id != ?
            ORDER BY last_msg DESC
        ");
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    public static function getFirstCommandeIdForContact(int $userId, int $contactId): ?int
    {
        $stmt = self::getConnection()->prepare("SELECT id FROM commandes WHERE (client_id = ? AND freelance_id = ?) OR (freelance_id = ? AND client_id = ?) ORDER BY date_commande DESC LIMIT 1");
        $stmt->execute([$userId, $contactId, $userId, $contactId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
}
