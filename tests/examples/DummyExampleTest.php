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
 * DUMMY
 * =====
 * Ein Dummy wird nur uebergeben, um eine Parameter-/Konstruktorliste zu
 * fuellen. Er wird im Test NIE wirklich benutzt - seine Methoden duerfen
 * gar nicht aufgerufen werden.
 *
 * Faustregel: "Ich brauche irgendein Objekt, damit der Code ueberhaupt
 * laeuft - was es tut, ist mir egal."
 *
 * (Demonstriert am neutralen NewsletterService, nicht am OrderService.)
 */
final class DummyExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Ungueltige Adresse bricht sofort ab - Repository & Mailer sind reine Dummies')]
    public function dummiesFuellenNurDieKonstruktorliste(): void
    {
        // createStub() ohne jede Konfiguration -> reine Dummy-Objekte.
        // Bei ungueltiger E-Mail bricht subscribe() VOR Repository und
        // Mailer ab, deshalb werden diese beiden nie benutzt.
        $repositoryDummy = $this->createStub(SubscriberRepositoryInterface::class);
        $mailerDummy     = $this->createStub(MailerInterface::class);

        // Der AuditLog wird tatsaechlich benutzt (info/error) -> kein Dummy.
        $log = $this->createStub(AuditLogInterface::class);

        $service = new NewsletterService($repositoryDummy, $mailerDummy, $log);

        $this->expectException(\InvalidArgumentException::class);

        // 'keine-adresse' enthaelt kein '@' -> Abbruch vor Repo/Mailer.
        $service->subscribe('keine-adresse');
    }
}
