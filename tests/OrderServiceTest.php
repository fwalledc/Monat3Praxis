<?php

declare(strict_types=1);

namespace Tests;

use App\EmailServiceInterface;
use App\LoggerInterface;
use App\Order;
use App\OrderService;
use App\PaymentException;
use App\PaymentGatewayInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderService::class)]
final class OrderServiceTest extends TestCase
{
    private PaymentGatewayInterface&MockObject $paymentGateway;
    private EmailServiceInterface&MockObject $emailService;
    private LoggerInterface&MockObject $logger;
    private OrderService $orderService;

    protected function setUp(): void
    {
        // TODO: Alle drei Dependencies mit createMock() erstellen
        // TODO: $this->orderService mit den Mocks instanziieren
    }

    // -------------------------------------------------------------------
    // TEIL 1 - PFLICHT
    // -------------------------------------------------------------------

    #[Test]
    #[TestDox('Order wird erfolgreich bezahlt und Status auf paid gesetzt')]
    public function chargesPaymentSuccessfully(): void
    {
        $this->markTestIncomplete('Test 1 noch schreiben');

        // Arrange: Order anlegen
        // Mock: charge() erwartet (99.99, 'EUR') -> willReturn('TXN-456')
        // Mock: sendOrderConfirmation() erwartet die Order-Daten
        // Act: processOrder()
        // Assert: Status 'paid', TransactionId gesetzt
    }

    #[Test]
    #[TestDox('Fehlgeschlagene Zahlung wirft PaymentException und sendet keine Mail')]
    public function throwsExceptionWhenPaymentFails(): void
    {
        $this->markTestIncomplete('Test 2 noch schreiben');

        // Mock: charge() -> willThrowException(new PaymentException(...))
        // Mock: sendOrderConfirmation() -> never()
        // expectException(PaymentException::class)
    }

    #[Test]
    #[TestDox('Ungueltiger Betrag wirft InvalidArgumentException')]
    public function throwsExceptionForInvalidAmount(): void
    {
        $this->markTestIncomplete('Test 3 noch schreiben');

        // Order mit Betrag 0.0
        // Mock: charge() -> never()
        // expectException(InvalidArgumentException::class)
    }

    // -------------------------------------------------------------------
    // TEIL 2 - BONUS
    // -------------------------------------------------------------------

    #[Test]
    #[TestDox('Bezahlte Bestellung wird storniert und Betrag erstattet')]
    public function refundsWhenOrderIsPaid(): void
    {
        $this->markTestIncomplete('Bonus-Test 4 noch schreiben');
    }

    #[Test]
    #[TestDox('Unbezahlte Bestellung wird ohne Refund storniert')]
    public function cancelsWithoutRefundWhenNotPaid(): void
    {
        $this->markTestIncomplete('Bonus-Test 5 noch schreiben');
    }

    #[Test]
    #[TestDox('Logger schreibt Start- und Erfolgsmeldung in korrekter Reihenfolge')]
    public function logsProcessingInfoInOrder(): void
    {
        $this->markTestIncomplete('Bonus-Test 6 noch schreiben');
    }
}
