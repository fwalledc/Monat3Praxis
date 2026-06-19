<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter\Fakes;

use Tests\examples\Newsletter\SubscriberRepositoryInterface;

/**
 * FAKE: voll funktionsfaehiges Repository - nur eben im RAM statt in einer DB.
 *
 * Im Gegensatz zu Stub/Mock hat ein Fake echte Logik und merkt sich Zustand
 * ueber mehrere Aufrufe hinweg: wer einmal gespeichert wurde, "existiert"
 * beim naechsten exists()-Aufruf.
 */
final class InMemorySubscriberRepository implements SubscriberRepositoryInterface
{
    /** @var list<string> */
    private array $emails = [];

    public function exists(string $email): bool
    {
        return in_array($email, $this->emails, true);
    }

    public function save(string $email): void
    {
        $this->emails[] = $email;
    }

    /** @return list<string> */
    public function all(): array
    {
        return $this->emails;
    }

    /** Hilfsmethode fuer Tests. */
    public function count(): int
    {
        return count($this->emails);
    }
}
