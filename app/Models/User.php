<?php

namespace App\Models;

use App\Database;

class User
{
    public static function all(): array
    {
        return Database::connect()->query('SELECT id, name, email, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT id, name, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): bool
    {
        $stmt = Database::connect()->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
        return $stmt->execute([$data['name'], $data['email']]);
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = Database::connect()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
        return $stmt->execute([$data['name'], $data['email'], $id]);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connect()->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
