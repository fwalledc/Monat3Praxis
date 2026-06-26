<?php

declare(strict_types=1);

namespace App;

/**
 * Fake-Implementierung fuer Tests (siehe Folie "5. Fake").
 * Funktioniert wie eine echte Repository, haelt die Daten aber nur im Speicher.
 */
final class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var array<int, User> */
    private array $users = [];

    private int $nextId = 1;

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }

        return null;
    }

    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function save(User $user): void
    {
        if ($user->getId() === null) {
            $user->setId($this->nextId++);
        }

        $this->users[$user->getId()] = $user;
    }
}
