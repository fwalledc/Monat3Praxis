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
        // Alle Dependencies mocken
        $this->paymentGateway = $this->createMock(PaymentGatewayInterface::class);
        $this->emailService = $this->createMock(EmailServiceInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // OrderService mit Mocks erstellen
        $this->orderService = new OrderService(
            $this->paymentGateway,
            $this->emailService,
            $this->logger
        );
    }

    #[Test]
    #[TestDox('Order wird erfolgreich bezahlt und Status auf paid gesetzt')]
    public function chargesPaymentSuccessfully(): void
    {
        // Arrange
        $order = new Order('ORDER-123', 'test@example.com', 99.99);

        $this->paymentGateway
            ->expects($this->once())
            ->method('charge')
            ->with(99.99, 'EUR')
            ->willReturn('TXN-456');

        $this->emailService
            ->expects($this->once())
            ->method('sendOrderConfirmation')
            ->with('test@example.com', 'ORDER-123', 99.99);

        // Act
        $this->orderService->processOrder($order);

        // Assert
        $this->assertSame('paid', $order->getStatus());
        $this->assertSame('TXN-456', $order->getTransactionId());
    }

    #[Test]
    #[TestDox('Bestaetigungsmail wird mit korrekten Parametern versendet')]
    public function sendsConfirmationEmail(): void
    {
        // Arrange
        $order = new Order('ORDER-789', 'customer@test.de', 149.50);

        $this->paymentGateway
            ->method('charge')
            ->willReturn('TXN-999');

        $this->emailService
            ->expects($this->once())
            ->method('sendOrderConfirmation')
            ->with(
                $this->equalTo('customer@test.de'),
                $this->equalTo('ORDER-789'),
                $this->equalTo(149.50)
            );

        // Act
        $this->orderService->processOrder($order);
    }

    #[Test]
    #[TestDox('Ungueltiger Betrag wirft InvalidArgumentException')]
    public function throwsExceptionForInvalidAmount(): void
    {
        // Arrange
        $order = new Order('ORDER-BAD', 'test@test.com', 0.0);

        // Payment darf bei ungueltigem Betrag NICHT aufgerufen werden
        $this->paymentGateway
            ->expects($this->never())
            ->method('charge');

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('error');

        // Assert + Act
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive');

        $this->orderService->processOrder($order);
    }

    #[Test]
    #[TestDox('Fehlgeschlagene Zahlung wirft PaymentException und sendet keine Mail')]
    public function throwsExceptionWhenPaymentFails(): void
    {
        // Arrange
        $order = new Order('ORDER-FAIL', 'test@test.com', 100.00);

        $this->paymentGateway
            ->expects($this->once())
            ->method('charge')
            ->willThrowException(new PaymentException('Card declined'));

        // Email darf NICHT gesendet werden
        $this->emailService
            ->expects($this->never())
            ->method('sendOrderConfirmation');

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Payment failed'));

        // Assert + Act
        $this->expectException(PaymentException::class);

        try {
            $this->orderService->processOrder($order);
        } finally {
            // Status pruefen, auch wenn Exception fliegt
            $this->assertSame('failed', $order->getStatus());
        }
    }

    #[Test]
    #[TestDox('Bezahlte Bestellung wird storniert und Betrag erstattet')]
    public function refundsWhenOrderIsPaid(): void
    {
        // Arrange
        $order = new Order('ORDER-456', 'test@test.com', 75.00);
        $order->setStatus('paid');
        $order->setTransactionId('TXN-789');

        $this->paymentGateway
            ->expects($this->once())
            ->method('refund')
            ->with('TXN-789')
            ->willReturn(true);

        // Act
        $result = $this->orderService->cancelOrder($order);

        // Assert
        $this->assertTrue($result);
        $this->assertSame('refunded', $order->getStatus());
    }

    #[Test]
    #[TestDox('Fehlgeschlagene Erstattung laesst Status auf paid')]
    public function keepsStatusWhenRefundFails(): void
    {
        // Arrange
        $order = new Order('ORDER-999', 'test@test.com', 50.00);
        $order->setStatus('paid');
        $order->setTransactionId('TXN-111');

        $this->paymentGateway
            ->expects($this->once())
            ->method('refund')
            ->with('TXN-111')
            ->willReturn(false);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Refund failed'));

        // Act
        $result = $this->orderService->cancelOrder($order);

        // Assert
        $this->assertFalse($result);
        $this->assertSame('paid', $order->getStatus());
    }

    #[Test]
    #[TestDox('Unbezahlte Bestellung wird ohne Refund storniert')]
    public function cancelsWithoutRefundWhenNotPaid(): void
    {
        // Arrange
        $order = new Order('ORDER-777', 'test@test.com', 25.00);
        // Order ist noch 'pending'

        $this->paymentGateway
            ->expects($this->never())
            ->method('refund');

        // Act
        $result = $this->orderService->cancelOrder($order);

        // Assert
        $this->assertTrue($result);
        $this->assertSame('cancelled', $order->getStatus());
    }

    #[Test]
    #[TestDox('Logger schreibt Start- und Erfolgsmeldung in korrekter Reihenfolge')]
    public function logsProcessingInfoInOrder(): void
    {
        // Arrange
        $order = new Order('ORDER-001', 'test@test.com', 50.00);

        $this->paymentGateway
            ->method('charge')
            ->willReturn('TXN-001');

        // PHPUnit 10+: withConsecutive() existiert nicht mehr!
        // Stattdessen: Matcher mit willReturnCallback
        $matcher = $this->exactly(2);

        $this->logger
            ->expects($matcher)
            ->method('info')
            ->willReturnCallback(function (string $message) use ($matcher): void {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertStringContainsString('Processing order: ORDER-001', $message),
                    2 => $this->assertStringContainsString('successfully: ORDER-001', $message),
                    default => $this->fail('info() zu oft aufgerufen'),
                };
            });

        // Act
        $this->orderService->processOrder($order);
    }
}
