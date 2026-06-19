<?php

declare(strict_types=1);

namespace Tests\examples;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\examples\Newsletter\AuditLogInterface;
use Tests\examples\Newsletter\Fakes\SpyAuditLog;
use Tests\examples\Newsletter\MailerInterface;
use Tests\examples\Newsletter\NewsletterService;
use Tests\examples\Newsletter\SubscriberRepositoryInterface;

/**
 * SPY
 * ===
 * Ein Spy zeichnet auf, wie er aufgerufen wurde. Geprueft wird ERST DANACH
 * mit eigenen Assertions - im Gegensatz zum Mock, der die Erwartung vorher
 * festlegt und automatisch prueft.
 *
 * -> Behavior Verification "im Nachhinein": erst handeln lassen, dann
 *    nachschauen, was protokolliert wurde.
 *
 * Zwei Wege werden gezeigt:
 *   1) Selbstgebaute Spy-Klasse (SpyAuditLog) - das reine Prinzip.
 *   2) PHPUnit-Mock + willReturnCallback - die Framework-Variante.
 */
final class SpyExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Selbstgebauter SpyAuditLog zeichnet alle Log-Meldungen auf')]
    public function handgebauterSpyZeichnetAufrufeAuf(): void
    {
        $spy = new SpyAuditLog();

        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('exists')->willReturn(false);
        $mailerStub = $this->createStub(MailerInterface::class);

        $service = new NewsletterService($repositoryStub, $mailerStub, $spy);

        // Akt
        $service->subscribe('neu@x.de');

        // ERST JETZT pruefen wir das aufgezeichnete Verhalten.
        $this->assertCount(2, $spy->infoMessages);
        $this->assertSame([], $spy->errorMessages);
        $this->assertStringContainsString('Subscribe attempt: neu@x.de', $spy->infoMessages[0]);
        $this->assertStringContainsString('Subscribed successfully: neu@x.de', $spy->infoMessages[1]);
    }

    #[Test]
    #[TestDox('PHPUnit-Mock als Spy: Aufrufe per Callback sammeln und danach pruefen')]
    public function spyMitPhpunitCallback(): void
    {
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('exists')->willReturn(false);
        $mailerStub = $this->createStub(MailerInterface::class);

        $log = $this->createMock(AuditLogInterface::class);

        // Statt expects()->with(...) sammeln wir die Argumente selbst ein.
        $captured = [];
        $log->method('info')
            ->willReturnCallback(function (string $message) use (&$captured): void {
                $captured[] = $message;
            });

        $service = new NewsletterService($repositoryStub, $mailerStub, $log);

        // Akt
        $service->subscribe('neu2@x.de');

        // Pruefung im Nachhinein - wir entscheiden frei, was wir asserten.
        $this->assertCount(2, $captured);
        $this->assertStringContainsString('Subscribe attempt', $captured[0]);
        $this->assertStringContainsString('successfully', $captured[1]);
    }
}
