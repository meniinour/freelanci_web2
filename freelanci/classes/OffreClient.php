<?php
class OffreClient
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function tableExists(): bool
    {
        try {
            self::getConnection()->query("SELECT 1 FROM offres_client LIMIT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getOuvertes(): array
    {
        if (!self::tableExists()) return [];
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT o.*, u.prenom as client_prenom, u.nom as client_nom, 
            (SELECT COUNT(*) FROM participations_offre p WHERE p.offre_id = o.id) as nb_participations 
            FROM offres_client o JOIN utilisateurs u ON o.client_id = u.id 
            WHERE o.statut = 'ouverte' ORDER BY o.date_creation DESC");
        return $stmt->fetchAll();
    }

    public static function getByClient(int $clientId): array
    {
        if (!self::tableExists()) return [];
        $stmt = self::getConnection()->prepare("SELECT o.*, (SELECT COUNT(*) FROM participations_offre p WHERE p.offre_id = o.id) as nb_participations 
            FROM offres_client o WHERE o.client_id = ? ORDER BY o.date_creation DESC");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        if (!self::tableExists()) return null;
        $stmt = self::getConnection()->prepare("SELECT * FROM offres_client WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $clientId, string $titre, string $description, ?float $budgetMax = null): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO offres_client (client_id, titre, description, budget_max, statut) VALUES (?, ?, ?, ?, 'ouverte')");
        return $stmt->execute([$clientId, $titre, $description, $budgetMax]);
    }

    public static function delete(int $id, int $clientId): bool
    {
        $stmt = self::getConnection()->prepare("DELETE FROM offres_client WHERE id = ? AND client_id = ?");
        return $stmt->execute([$id, $clientId]);
    }
}
