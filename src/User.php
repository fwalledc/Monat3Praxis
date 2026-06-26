<?php

declare(strict_types=1);

namespace App;

class User
{
    private ?int $id = null;

    public function __construct(
        private string $email,
        private ?string $name = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
