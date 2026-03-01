<?php
class Utilisateur
{
    public ?int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public ?string $telephone;
    public ?string $gouvernorat;
    public ?string $bio;
    public ?string $competences;
    public string $type_utilisateur;
    public ?string $date_inscription;
    public string $statut;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->nom = $data['nom'] ?? '';
        $this->prenom = $data['prenom'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->telephone = $data['telephone'] ?? null;
        $this->gouvernorat = $data['gouvernorat'] ?? null;
        $this->bio = $data['bio'] ?? null;
        $this->competences = $data['competences'] ?? null;
        $this->type_utilisateur = $data['type_utilisateur'] ?? 'client';
        $this->date_inscription = $data['date_inscription'] ?? null;
        $this->statut = $data['statut'] ?? 'actif';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'gouvernorat' => $this->gouvernorat,
            'bio' => $this->bio,
            'competences' => $this->competences,
            'type_utilisateur' => $this->type_utilisateur,
            'date_inscription' => $this->date_inscription,
            'statut' => $this->statut,
        ];
    }

    public static function getConnection(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::getConnection()->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = self::getConnection()->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM utilisateurs WHERE email = ?";
        $params = [$email];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    public static function create(array $data): bool
    {
        $pdo = self::getConnection();
        $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, type_utilisateur, gouvernorat, bio, competences) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['telephone'] ?? null,
            $data['type_utilisateur'] ?? 'client',
            $data['gouvernorat'] ?? null,
            $data['bio'] ?? null,
            $data['competences'] ?? null,
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = self::getConnection();
        $sets = [];
        $params = [];
        $allowed = ['nom', 'prenom', 'email', 'telephone', 'gouvernorat', 'bio', 'competences', 'type_utilisateur', 'statut'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = "$key = ?";
                $params[] = $data[$key];
            }
        }
        if (isset($data['mot_de_passe']) && $data['mot_de_passe'] !== '') {
            $sets[] = "mot_de_passe = ?";
            $params[] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }
        if (empty($sets)) {
            return false;
        }
        $params[] = $id;
        $sql = "UPDATE utilisateurs SET " . implode(', ', $sets) . " WHERE id = ?";
        return $pdo->prepare($sql)->execute($params);
    }

    public static function delete(int $id): bool
    {
        return self::getConnection()->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
    }

    public static function getAll(?string $type = null): array
    {
        $pdo = self::getConnection();
        if ($type && in_array($type, ['freelance', 'client', 'admin'])) {
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE type_utilisateur = ? ORDER BY date_inscription DESC");
            $stmt->execute([$type]);
        } else {
            $stmt = $pdo->query("SELECT * FROM utilisateurs WHERE type_utilisateur != 'admin' ORDER BY date_inscription DESC");
        }
        return $stmt->fetchAll();
    }
}
