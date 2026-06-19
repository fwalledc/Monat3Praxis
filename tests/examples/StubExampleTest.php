<?php

declare(strict_types=1);

namespace Tests\examples;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\examples\Newsletter\AuditLogInterface;
use Tests\examples\Newsletter\MailerInterface;
use Tests\examples\Newsletter\NewsletterService;
use Tests\examples\Newsletter\SubscriberRepositoryInterface;

/**
 * STUB
 * ====
 * Ein Stub liefert vorgegebene Antworten ("canned answers") auf Aufrufe.
 * Wir interessieren uns NICHT dafuer, OB oder WIE OFT er aufgerufen wird -
 * nur dafuer, WAS er zurueckgibt, damit unser Code weiterlaufen kann.
 *
 * -> State Verification: am Ende pruefen wir das Ergebnis, nicht die Interaktion.
 *
 * Werkzeug: createStub() + method()->willReturn(). Bewusst KEIN expects()!
 */
final class StubExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Stub meldet "Adresse existiert bereits" -> subscribe() liefert false')]
    public function stubLiefertFesteAntwort(): void
    {
        // Stub: exists() gibt IMMER true zurueck.
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('exists')->willReturn(true);

        $mailerStub = $this->createStub(MailerInterface::class);
        $logStub    = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($repositoryStub, $mailerStub, $logStub);

        // Wir pruefen nur das Ergebnis.
        $this->assertFalse($service->subscribe('schon@da.de'));
    }

    #[Test]
    #[TestDox('Stub liefert je nach Argument unterschiedliche Werte (willReturnMap)')]
    public function stubMitArgumentAbhaengigerAntwort(): void
    {
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);

        // willReturnMap: [arg1, ..., returnValue]
        $repositoryStub->method('exists')->willReturnMap([
            ['bekannt@x.de', true],
            ['neu@x.de', false],
        ]);

        $mailerStub = $this->createStub(MailerInterface::class);
        $logStub    = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($repositoryStub, $mailerStub, $logStub);

        $this->assertFalse($service->subscribe('bekannt@x.de')); // existiert -> false
        $this->assertTrue($service->subscribe('neu@x.de'));      // neu      -> true
    }

    #[Test]
    #[TestDox('Stub liefert eine Empfaengerliste, damit sendCampaign() zaehlen kann')]
    public function stubLiefertListe(): void
    {
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('all')->willReturn(['a@x.de', 'b@x.de', 'c@x.de']);

        $mailerStub = $this->createStub(MailerInterface::class);
        $logStub    = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($repositoryStub, $mailerStub, $logStub);

        $this->assertSame(3, $service->sendCampaign('Sommer-Sale', 'Bis zu 50%'));
    }
}
