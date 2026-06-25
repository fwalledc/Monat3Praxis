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
use PHPUnit\Framework\Attributes\DataProvider;
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

    // -------------------------------------------------------------------
    // TEIL 3 - ZUSATZ (optional, weitere Techniken)
    // -------------------------------------------------------------------

    #[Test]
    #[TestDox('Fehlgeschlagener Refund laesst die Order bezahlt und liefert false')]
    public function refundFailureKeepsOrderPaid(): void
    {
        $this->markTestIncomplete('Zusatz-Test 7 noch schreiben');

        // Order: status 'paid', transactionId gesetzt
        // Mock: refund() -> willReturn(false)
        // Mock: logger->error() once()
        // Assert: cancelOrder() === false, Status bleibt 'paid'
    }

    #[Test]
    #[TestDox('Exception traegt die erwartete Fehlermeldung')]
    public function exposesPaymentErrorMessage(): void
    {
        $this->markTestIncomplete('Zusatz-Test 8 noch schreiben');

        // Mock: charge() -> willThrowException(new PaymentException('Card declined'))
        // expectException(PaymentException::class)
        // expectExceptionMessage('Card declined')
    }

    /**
     * @return array<string, array{float}>
     */
    public static function invalidAmountProvider(): array
    {
        return [
            'null'    => [0.0],
            'negativ' => [-5.0],
        ];
    }

    #[Test]
    #[TestDox('Ungueltige Betraege werden alle abgewiesen')]
    #[DataProvider('invalidAmountProvider')]
    public function rejectsEveryInvalidAmount(float $amount): void
    {
        $this->markTestIncomplete('Zusatz-Test 9 noch schreiben');

        // Order mit $amount aus dem DataProvider
        // Mock: charge() -> never()
        // expectException(InvalidArgumentException::class)
    }

    #[Test]
    #[TestDox('Argument-Matcher pruefen einzelne Parameter flexibel')]
    public function verifiesArgumentsWithMatchers(): void
    {
        $this->markTestIncomplete('Zusatz-Test 10 noch schreiben');

        // Mock: charge() -> willReturn('TXN-999')
        // Mock: sendOrderConfirmation()->with(
        //         $this->stringContains('@'),
        //         $this->anything(),
        //         $this->callback(fn (float $a) => $a > 0),
        //       )
    }

    #[Test]
    #[TestDox('Stub statt Mock, wenn der Rueckgabewert genuegt')]
    public function usesStubWhenInteractionDoesNotMatter(): void
    {
        $this->markTestIncomplete('Zusatz-Test 11 noch schreiben');

        // createStub() statt createMock() fuer alle drei Dependencies
        // charge() -> willReturn('TXN-STUB')
        // Assert nur den Zustand: Status 'paid', TransactionId gesetzt
    }
}
