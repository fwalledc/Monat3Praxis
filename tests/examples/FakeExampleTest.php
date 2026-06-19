<?php

declare(strict_types=1);

namespace Tests\examples;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\examples\Newsletter\AuditLogInterface;
use Tests\examples\Newsletter\Fakes\InMemorySubscriberRepository;
use Tests\examples\Newsletter\MailerInterface;
use Tests\examples\Newsletter\NewsletterService;

/**
 * FAKE
 * ====
 * Ein Fake ist eine ECHTE, funktionierende Implementierung des Interfaces -
 * nur nicht produktionstauglich. Hier: ein In-Memory-Repository, das
 * Abonnenten in einem Array verwaltet (siehe Newsletter/Fakes/InMemory...).
 *
 * -> State Verification: wir lassen echte Logik laufen und pruefen am Ende
 *    den Zustand - sowohl im Fake selbst als auch im Ergebnis.
 *
 * Vorteil ggue. Stub: realistisches Verhalten ueber mehrere Aufrufe hinweg
 * (save -> exists haengen zusammen), ohne jeden Rueckgabewert von Hand
 * vorzugeben.
 */
final class FakeExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Fake merkt sich Abonnenten: dieselbe Adresse zweimal -> beim 2. Mal false')]
    public function fakeMerktSichZustand(): void
    {
        $fakeRepo = new InMemorySubscriberRepository();

        $mailerStub = $this->createStub(MailerInterface::class);
        $logStub    = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($fakeRepo, $mailerStub, $logStub);

        // 1. Anmeldung: neu -> true
        $this->assertTrue($service->subscribe('fan@x.de'));

        // 2. Anmeldung derselben Adresse: der Fake "weiss" jetzt davon -> false
        $this->assertFalse($service->subscribe('fan@x.de'));

        $this->assertTrue($fakeRepo->exists('fan@x.de'));
        $this->assertSame(1, $fakeRepo->count());
    }

    #[Test]
    #[TestDox('Fake liefert die echte Empfaengerliste fuer die Kampagne')]
    public function fakeUeberMehrereAufrufeHinweg(): void
    {
        $fakeRepo = new InMemorySubscriberRepository();
        $mailerStub = $this->createStub(MailerInterface::class);
        $logStub    = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($fakeRepo, $mailerStub, $logStub);

        $service->subscribe('a@x.de');
        $service->subscribe('b@x.de');

        // sendCampaign liest die echte, vom Fake aufgebaute Liste.
        $reached = $service->sendCampaign('News', 'Inhalt');

        $this->assertSame(2, $reached);
        $this->assertEqualsCanonicalizing(['a@x.de', 'b@x.de'], $fakeRepo->all());
    }
}
