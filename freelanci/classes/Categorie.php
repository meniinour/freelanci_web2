<?php
class Categorie
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function getAll(): array
    {
        $stmt = self::getConnection()->query("SELECT * FROM categories ORDER BY nom");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::getConnection()->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO categories (nom, description, icone) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['icone'] ?? null,
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::getConnection()->prepare("UPDATE categories SET nom = ?, description = ?, icone = ? WHERE id = ?");
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['icone'] ?? null,
            $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        return self::getConnection()->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    }
}
