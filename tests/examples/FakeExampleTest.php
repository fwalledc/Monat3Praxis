<?php

declare(strict_types=1);

namespace Tests\Examples;

use App\EmailServiceInterface;
use App\LoggerInterface;
use App\Order;
use App\OrderService;
use App\PaymentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Examples\Fakes\InMemoryPaymentGateway;

/**
 * FAKE
 * ====
 * Ein Fake ist eine ECHTE, funktionierende Implementierung des Interfaces -
 * nur nicht produktionstauglich. Hier: ein In-Memory-Payment-Gateway, das
 * Transaktionen in einem Array verwaltet (siehe Fakes/InMemoryPaymentGateway).
 *
 * -> State Verification: wir lassen echte Logik laufen und pruefen am Ende
 *    den Zustand - sowohl im Fake selbst als auch in der Order.
 *
 * Vorteil ggue. Stub: realistisches Verhalten ueber mehrere Aufrufe hinweg
 * (charge -> refund haengen zusammen), ohne jeden Rueckgabewert von Hand
 * vorzugeben.
 */
final class FakeExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Fake-Gateway vergibt echte Transaction-IDs und merkt sich die Zahlung')]
    public function fakeMitEchterLogik(): void
    {
        $fakeGateway = new InMemoryPaymentGateway();

        $email  = $this->createStub(EmailServiceInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $service = new OrderService($fakeGateway, $email, $logger);
        $order   = new Order('ORDER-FAKE', 'kunde@test.de', 120.00);

        $service->processOrder($order);

        $this->assertSame('paid', $order->getStatus());
        // Die ID kommt aus der echten Fake-Logik, nicht aus willReturn().
        $this->assertSame('FAKE-TXN-0001', $order->getTransactionId());
        $this->assertTrue($fakeGateway->hasTransaction('FAKE-TXN-0001'));
    }

    #[Test]
    #[TestDox('charge() und refund() arbeiten ueber den Fake zusammen (Storno)')]
    public function fakeUeberMehrereAufrufeHinweg(): void
    {
        $fakeGateway = new InMemoryPaymentGateway();
        $email       = $this->createStub(EmailServiceInterface::class);
        $logger      = $this->createStub(LoggerInterface::class);

        $service = new OrderService($fakeGateway, $email, $logger);
        $order   = new Order('ORDER-FAKE-2', 'kunde@test.de', 80.00);

        // 1. bezahlen
        $service->processOrder($order);
        $txn = $order->getTransactionId();
        $this->assertNotNull($txn);
        $this->assertTrue($fakeGateway->hasTransaction($txn));

        // 2. stornieren -> Fake fuehrt echten Refund aus und vergisst die Transaktion
        $result = $service->cancelOrder($order);

        $this->assertTrue($result);
        $this->assertSame('refunded', $order->getStatus());
        $this->assertFalse($fakeGateway->hasTransaction($txn));
    }

    #[Test]
    #[TestDox('Fake kann auch den Fehlerfall realistisch nachstellen')]
    public function fakeKannFehlerSimulieren(): void
    {
        // Fake im "shouldFail"-Modus -> charge() wirft echte PaymentException.
        $fakeGateway = new InMemoryPaymentGateway(shouldFail: true);
        $email       = $this->createStub(EmailServiceInterface::class);
        $logger      = $this->createStub(LoggerInterface::class);

        $service = new OrderService($fakeGateway, $email, $logger);
        $order   = new Order('ORDER-FAKE-3', 'kunde@test.de', 30.00);

        $this->expectException(PaymentException::class);

        try {
            $service->processOrder($order);
        } finally {
            $this->assertSame('failed', $order->getStatus());
        }
    }
}
