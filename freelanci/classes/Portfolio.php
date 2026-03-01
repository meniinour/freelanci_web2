<?php
class Portfolio
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function tableExists(): bool
    {
        try {
            self::getConnection()->query("SELECT 1 FROM portfolio LIMIT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getByFreelance(int $freelanceId): array
    {
        if (!self::tableExists()) return [];
        $stmt = self::getConnection()->prepare("SELECT * FROM portfolio WHERE freelance_id = ? ORDER BY date_ajout DESC");
        $stmt->execute([$freelanceId]);
        return $stmt->fetchAll();
    }

    public static function add(int $freelanceId, string $titre, string $typeMedia, string $url): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO portfolio (freelance_id, titre, type_media, url) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$freelanceId, $titre, $typeMedia, $url]);
    }

    public static function delete(int $id, int $freelanceId): bool
    {
        $stmt = self::getConnection()->prepare("DELETE FROM portfolio WHERE id = ? AND freelance_id = ?");
        return $stmt->execute([$id, $freelanceId]);
    }
}
