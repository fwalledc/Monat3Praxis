<?php

declare(strict_types=1);

namespace Tests\Examples\Fakes;

use App\LoggerInterface;

/**
 * SPY (von Hand): zeichnet alle Aufrufe auf, statt sie nur zu verwerfen.
 *
 * Ein Spy ist im Kern ein Fake, dessen einziger Zweck das Protokollieren
 * ist. Nach dem Akt liest der Test die aufgezeichneten Daten aus und
 * stellt seine Assertions darauf an (Behavior Verification "im Nachhinein").
 *
 * Den gleichen Effekt erreicht man mit PHPUnit ueber einen Mock +
 * willReturnCallback (siehe SpyExampleTest::spyMitPhpunitCallback) -
 * diese Klasse zeigt das Prinzip ganz ohne Framework-Magie.
 */
final class SpyLogger implements LoggerInterface
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
