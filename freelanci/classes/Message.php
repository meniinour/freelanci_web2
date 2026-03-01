<?php
class Message
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function send(int $commandeId, int $expediteurId, int $destinataireId, string $contenu): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO messages (commande_id, expediteur_id, destinataire_id, contenu) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$commandeId, $expediteurId, $destinataireId, $contenu]);
    }

    public static function getByCommande(int $commandeId): array
    {
        $stmt = self::getConnection()->prepare("SELECT m.*, u.prenom as exp_prenom, u.nom as exp_nom FROM messages m JOIN utilisateurs u ON m.expediteur_id = u.id WHERE m.commande_id = ? ORDER BY m.date_envoi ASC");
        $stmt->execute([$commandeId]);
        return $stmt->fetchAll();
    }

    public static function getByUserAndContact(int $userId, int $contactId): array
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            SELECT m.*, u.prenom as exp_prenom, u.nom as exp_nom
            FROM messages m
            JOIN utilisateurs u ON m.expediteur_id = u.id
            JOIN commandes c ON m.commande_id = c.id
            WHERE (c.client_id = ? AND c.freelance_id = ?) OR (c.freelance_id = ? AND c.client_id = ?)
            ORDER BY m.date_envoi ASC
        ");
        $stmt->execute([$userId, $contactId, $userId, $contactId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead(int $commandeId, int $destinataireId): void
    {
        self::getConnection()->prepare("UPDATE messages SET lu = 1 WHERE commande_id = ? AND destinataire_id = ?")->execute([$commandeId, $destinataireId]);
    }

    public static function markAsReadForUserAndContact(int $userId, int $contactId): void
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM commandes WHERE (client_id = ? AND freelance_id = ?) OR (freelance_id = ? AND client_id = ?)");
        $stmt->execute([$userId, $contactId, $userId, $contactId]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $cid) {
            self::markAsRead((int)$cid, $userId);
        }
    }
}
