<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\User;
use PDO;

class UserRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE username = ?'
        );

        $stmt->execute([$username]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return [
            'user' => $this->hydrate($row),
            'password' => $row['password']
        ];
    }

    public function register(
        string $username,
        string $email,
        string $passwordHash
    ): User {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password)
             VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $username,
            $email,
            $passwordHash
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return new User(
            id: $id,
            username: $username,
            email: $email
        );
    }

    private function hydrate(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            username: $row['username'],
            email: $row['email']
        );
    }
}