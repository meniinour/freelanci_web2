<?php
class ParticipationOffre
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function tableExists(): bool
    {
        try {
            self::getConnection()->query("SELECT 1 FROM participations_offre LIMIT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function create(int $offreId, int $freelanceId, string $message): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO participations_offre (offre_id, freelance_id, message) VALUES (?, ?, ?)");
        return $stmt->execute([$offreId, $freelanceId, $message]);
    }

    public static function hasParticipated(int $offreId, int $freelanceId): bool
    {
        if (!self::tableExists()) return false;
        $stmt = self::getConnection()->prepare("SELECT id FROM participations_offre WHERE offre_id = ? AND freelance_id = ?");
        $stmt->execute([$offreId, $freelanceId]);
        return $stmt->fetch() !== false;
    }

    public static function getByOffre(int $offreId): array
    {
        if (!self::tableExists()) return [];
        $stmt = self::getConnection()->prepare("SELECT p.*, u.nom, u.prenom, u.email, u.telephone, u.bio, u.competences FROM participations_offre p JOIN utilisateurs u ON p.freelance_id = u.id WHERE p.offre_id = ? ORDER BY p.date_participation DESC");
        $stmt->execute([$offreId]);
        return $stmt->fetchAll();
    }

    public static function getByFreelance(int $freelanceId): array
    {
        if (!self::tableExists()) return [];
        $stmt = self::getConnection()->prepare("SELECT p.*, o.titre as offre_titre, o.description as offre_description, o.budget_max, o.statut as offre_statut, u.prenom as client_prenom, u.nom as client_nom 
            FROM participations_offre p JOIN offres_client o ON p.offre_id = o.id JOIN utilisateurs u ON o.client_id = u.id 
            WHERE p.freelance_id = ? ORDER BY p.date_participation DESC");
        $stmt->execute([$freelanceId]);
        return $stmt->fetchAll();
    }
}
