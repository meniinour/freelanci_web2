<?php
class Service
{
    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function getActifs(array $filters = []): array
    {
        $pdo = self::getConnection();
        $sql = "SELECT s.*, u.nom as freelance_nom, u.prenom as freelance_prenom, u.email as freelance_email, c.nom as categorie_nom 
                FROM services s 
                JOIN utilisateurs u ON s.freelance_id = u.id 
                LEFT JOIN categories c ON s.categorie_id = c.id 
                WHERE s.statut = 'actif'";
        $params = [];
        if (!empty($filters['categorie_id'])) {
            $sql .= " AND s.categorie_id = ?";
            $params[] = $filters['categorie_id'];
        }
        if (isset($filters['prix_min']) && $filters['prix_min'] > 0) {
            $sql .= " AND s.prix >= ?";
            $params[] = $filters['prix_min'];
        }
        if (isset($filters['prix_max']) && $filters['prix_max'] > 0) {
            $sql .= " AND s.prix <= ?";
            $params[] = $filters['prix_max'];
        }
        if (isset($filters['delai_max']) && $filters['delai_max'] > 0) {
            $sql .= " AND (s.delai_jours IS NULL OR s.delai_jours <= ?)";
            $params[] = $filters['delai_max'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.titre LIKE ? OR s.description LIKE ?)";
            $q = '%' . $filters['search'] . '%';
            $params[] = $q;
            $params[] = $q;
        }
        $tri = $filters['tri'] ?? 'date';
        if ($tri === 'prix_asc') $sql .= " ORDER BY s.prix ASC";
        elseif ($tri === 'prix_desc') $sql .= " ORDER BY s.prix DESC";
        elseif ($tri === 'delai') $sql .= " ORDER BY s.delai_jours ASC";
        else $sql .= " ORDER BY s.date_creation DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::getConnection()->prepare("SELECT s.*, c.nom as categorie_nom FROM services s LEFT JOIN categories c ON s.categorie_id = c.id WHERE s.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByFreelance(int $freelanceId): array
    {
        $stmt = self::getConnection()->prepare("SELECT s.*, c.nom as categorie_nom FROM services s LEFT JOIN categories c ON s.categorie_id = c.id WHERE s.freelance_id = ? ORDER BY s.date_creation DESC");
        $stmt->execute([$freelanceId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): bool
    {
        $stmt = self::getConnection()->prepare("INSERT INTO services (titre, description, prix, delai_jours, categorie_id, freelance_id, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['prix'],
            $data['delai_jours'] ?? null,
            $data['categorie_id'] ?? null,
            $data['freelance_id'],
            $data['statut'] ?? 'actif',
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::getConnection()->prepare("UPDATE services SET titre = ?, description = ?, prix = ?, delai_jours = ?, categorie_id = ?, statut = ? WHERE id = ?");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['prix'],
            $data['delai_jours'] ?? null,
            $data['categorie_id'] ?? null,
            $data['statut'] ?? 'actif',
            $id,
        ]);
    }

    public static function delete(int $id, int $freelanceId): bool
    {
        $stmt = self::getConnection()->prepare("DELETE FROM services WHERE id = ? AND freelance_id = ?");
        return $stmt->execute([$id, $freelanceId]);
    }

    public static function getFreelanceId(int $serviceId): ?int
    {
        $stmt = self::getConnection()->prepare("SELECT freelance_id FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['freelance_id'] : null;
    }

    public static function count(): int
    {
        $stmt = self::getConnection()->query("SELECT COUNT(*) FROM services");
        return (int) $stmt->fetchColumn();
    }
}
