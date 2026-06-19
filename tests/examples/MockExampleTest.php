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
 * MOCK
 * ====
 * Ein Mock wird VORHER mit Erwartungen programmiert: welche Methode,
 * wie oft, mit welchen Argumenten. PHPUnit prueft diese Erwartungen
 * automatisch am Ende des Tests - ohne ein einziges assert...() von uns.
 *
 * -> Behavior Verification: "Wurde korrekt mit der Dependency interagiert?"
 *
 * Werkzeug: createMock() + expects()->method()->with(). Der Unterschied
 * zum Stub ist das expects() (= Erwartung, die scheitern kann).
 */
final class MockExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Mock erwartet, dass genau 1x eine Willkommensmail rausgeht (once)')]
    public function mockErwartetAufrufAutomatisch(): void
    {
        // Stub: liefert nur die Antwort, damit der Ablauf weiterlaeuft.
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('exists')->willReturn(false);

        // ERWARTUNG (vor dem Akt): sendWelcome() muss genau 1x mit dieser
        // Adresse kommen. Tut es das nicht, scheitert der Test - auch ohne
        // eigenes assert.
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects($this->once())
            ->method('sendWelcome')
            ->with('neu@kunde.de');

        $logStub = $this->createStub(AuditLogInterface::class);
        $service = new NewsletterService($repositoryStub, $mailerMock, $logStub);

        $service->subscribe('neu@kunde.de');
        // Pruefung passiert implizit beim Teardown durch PHPUnit.
    }

    #[Test]
    #[TestDox('Mock stellt sicher: bei bekanntem Abo geht KEINE Mail raus (never)')]
    public function mockVerbietetUnerwuenschtenAufruf(): void
    {
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('exists')->willReturn(true); // schon Abonnent

        // ERWARTUNG: sendWelcome darf NIE aufgerufen werden.
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects($this->never())
            ->method('sendWelcome');

        $logStub = $this->createStub(AuditLogInterface::class);
        $service = new NewsletterService($repositoryStub, $mailerMock, $logStub);

        $service->subscribe('bekannt@kunde.de');
    }

    #[Test]
    #[TestDox('Mock erwartet genau so viele send()-Aufrufe wie Empfaenger (exactly)')]
    public function mockPrueftAufrufHaeufigkeit(): void
    {
        $repositoryStub = $this->createStub(SubscriberRepositoryInterface::class);
        $repositoryStub->method('all')->willReturn(['a@x.de', 'b@x.de']);

        // ERWARTUNG: send() muss genau 2x kommen - einmal pro Empfaenger.
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects($this->exactly(2))
            ->method('send');

        $logStub = $this->createStub(AuditLogInterface::class);
        $service = new NewsletterService($repositoryStub, $mailerMock, $logStub);

        $service->sendCampaign('Newsletter 06/2026', 'Inhalt ...');
    }
}
