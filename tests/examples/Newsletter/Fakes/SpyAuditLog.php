<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter\Fakes;

use Tests\examples\Newsletter\AuditLogInterface;

/**
 * SPY (von Hand): zeichnet alle Aufrufe auf, statt sie zu verwerfen.
 *
 * Ein Spy ist im Kern ein Fake, dessen einziger Zweck das Protokollieren ist.
 * Nach dem Akt liest der Test die aufgezeichneten Daten aus und prueft sie
 * (Behavior Verification "im Nachhinein").
 */
final class SpyAuditLog implements AuditLogInterface
{
    /** @var list<string> */
    public array $infoMessages = [];

    /** @var list<string> */
    public array $errorMessages = [];

    public function info(string $message): void
    {
        $this->infoMessages[] = $message;
    }

    public function error(string $message): void
    {
        $this->errorMessages[] = $message;
    }
}
