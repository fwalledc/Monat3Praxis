<?php

declare(strict_types=1);

namespace Tests\Examples\Fakes;

use App\PaymentException;
use App\PaymentGatewayInterface;

/**
 * FAKE: Eine voll funktionsfaehige, aber leichtgewichtige Implementierung.
 *
 * Im Gegensatz zu Stub/Mock hat ein Fake echte Logik - hier eine
 * In-Memory-"Bank", die Transaktionen verwaltet. Sie ist nur nicht
 * produktionstauglich (keine echte Zahlungs-API, alles im RAM).
 *
 * Typische echte Beispiele: In-Memory-SQLite statt MySQL, ein
 * Array-basiertes Repository statt Datenbank.
 */
final class InMemoryPaymentGateway implements PaymentGatewayInterface
{
    /** @var array<string, float> transactionId => amount */
    private array $transactions = [];

    private int $counter = 0;

    public function __construct(private bool $shouldFail = false)
    {
    }

    public function charge(float $amount, string $currency = 'EUR'): string
    {
        if ($this->shouldFail) {
            throw new PaymentException('Karte abgelehnt (Fake)');
        }

        $transactionId = sprintf('FAKE-TXN-%04d', ++$this->counter);
        $this->transactions[$transactionId] = $amount;

        return $transactionId;
    }

    public function refund(string $transactionId): bool
    {
        if (!isset($this->transactions[$transactionId])) {
            return false;
        }

        unset($this->transactions[$transactionId]);

        return true;
    }

    /** Hilfsmethode fuer Tests: existiert eine Transaktion noch? */
    public function hasTransaction(string $transactionId): bool
    {
        return isset($this->transactions[$transactionId]);
    }
}
