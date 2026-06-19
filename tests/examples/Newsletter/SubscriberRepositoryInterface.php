<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter;

/**
 * Neutrales Beispiel-Modell fuer die Test-Double-Demos.
 * Bewusst NICHT der OrderService - damit der Praxisteil nicht
 * vorweggenommen wird.
 */
interface SubscriberRepositoryInterface
{
    public function exists(string $email): bool;

    public function save(string $email): void;

    /** @return list<string> */
    public function all(): array;
}
