<?php

declare(strict_types=1);

namespace Tests\Examples;

use App\EmailServiceInterface;
use App\LoggerInterface;
use App\Order;
use App\OrderService;
use App\PaymentGatewayInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * DUMMY
 * =====
 * Ein Dummy wird nur uebergeben, um eine Parameter-/Konstruktorliste zu
 * fuellen. Er wird im Test NIE wirklich benutzt - seine Methoden duerfen
 * gar nicht aufgerufen werden.
 *
 * Faustregel: "Ich brauche irgendein Objekt, damit der Code ueberhaupt
 * laeuft - was es tut, ist mir egal."
 */
final class DummyExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Eine unbezahlte Bestellung wird storniert - Payment & Email sind reine Dummies')]
    public function dummiesFuellenNurDieKonstruktorliste(): void
    {
        // createStub() ohne jede Konfiguration -> reines Dummy-Objekt.
        // Wir konfigurieren NICHTS, weil wir erwarten, dass es nie benutzt wird.
        $paymentDummy = $this->createStub(PaymentGatewayInterface::class);
        $emailDummy   = $this->createStub(EmailServiceInterface::class);

        // Der Logger wird hier tatsaechlich benutzt (info), ist also KEIN Dummy.
        $logger = $this->createStub(LoggerInterface::class);

        $service = new OrderService($paymentDummy, $emailDummy, $logger);

        // Eine 'pending' Order loest keinen Refund und keine Mail aus.
        $order = new Order('ORDER-DUMMY', 'kunde@test.de', 25.00);

        $result = $service->cancelOrder($order);

        // Wir pruefen nur den Zustand (State Verification).
        // Payment/Email mussten existieren, wurden aber nie angefasst.
        $this->assertTrue($result);
        $this->assertSame('cancelled', $order->getStatus());
    }
}
