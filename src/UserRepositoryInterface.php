<?php

declare(strict_types=1);

namespace App;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function save(User $user): void;
}
