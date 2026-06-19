<?php

declare(strict_types=1);

namespace Tests\Examples;

use App\EmailServiceInterface;
use App\LoggerInterface;
use App\Order;
use App\OrderService;
use App\PaymentException;
use App\PaymentGatewayInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

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
    #[TestDox('Mock erwartet charge() genau 1x mit (99.99, EUR)')]
    public function mockPrueftErwartungAutomatisch(): void
    {
        $paymentMock = $this->createMock(PaymentGatewayInterface::class);

        // ERWARTUNG (vor dem Akt): charge() muss genau 1x mit diesen
        // Argumenten kommen. Tut es das nicht, scheitert der Test - auch
        // ohne eigenes assert.
        $paymentMock->expects($this->once())
            ->method('charge')
            ->with(99.99, 'EUR')
            ->willReturn('TXN-MOCK');

        // Auch eine Erwartung: die Bestaetigungsmail muss genau 1x raus.
        $emailMock = $this->createMock(EmailServiceInterface::class);
        $emailMock->expects($this->once())
            ->method('sendOrderConfirmation')
            ->with('kunde@test.de', 'ORDER-MOCK', 99.99);

        // Logger interessiert uns hier nicht -> Dummy/Stub reicht.
        $logger = $this->createStub(LoggerInterface::class);

        $service = new OrderService($paymentMock, $emailMock, $logger);
        $order   = new Order('ORDER-MOCK', 'kunde@test.de', 99.99);

        // Akt - die Pruefung passiert implizit beim Teardown durch PHPUnit.
        $service->processOrder($order);
    }

    #[Test]
    #[TestDox('Mock stellt sicher, dass bei Fehler KEINE Mail versendet wird (never)')]
    public function mockVerbietetUnerwuenschtenAufruf(): void
    {
        $paymentMock = $this->createMock(PaymentGatewayInterface::class);
        $paymentMock->method('charge')
            ->willThrowException(new PaymentException('Abgelehnt'));

        // ERWARTUNG: sendOrderConfirmation darf NIE aufgerufen werden.
        $emailMock = $this->createMock(EmailServiceInterface::class);
        $emailMock->expects($this->never())
            ->method('sendOrderConfirmation');

        $logger  = $this->createStub(LoggerInterface::class);
        $service = new OrderService($paymentMock, $emailMock, $logger);
        $order   = new Order('ORDER-FAIL', 'kunde@test.de', 50.00);

        $this->expectException(PaymentException::class);
        $service->processOrder($order);
    }
}
