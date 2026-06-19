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
    #[TestDox('Stub gibt eine feste Transaction-ID zurueck, damit der Ablauf testbar ist')]
    public function stubLiefertFesteAntwort(): void
    {
        // Stub: charge() gibt IMMER 'TXN-STUB' zurueck - egal mit welchen Argumenten.
        $paymentStub = $this->createStub(PaymentGatewayInterface::class);
        $paymentStub->method('charge')->willReturn('TXN-STUB');

        $emailStub  = $this->createStub(EmailServiceInterface::class);
        $loggerStub = $this->createStub(LoggerInterface::class);

        $service = new OrderService($paymentStub, $emailStub, $loggerStub);
        $order   = new Order('ORDER-STUB', 'kunde@test.de', 99.99);

        $service->processOrder($order);

        // Wir pruefen nur den resultierenden Zustand.
        $this->assertSame('paid', $order->getStatus());
        $this->assertSame('TXN-STUB', $order->getTransactionId());
    }

    #[Test]
    #[TestDox('Stub kann je nach Argument unterschiedliche Werte liefern (willReturnMap)')]
    public function stubMitArgumentAbhaengigerAntwort(): void
    {
        $paymentStub = $this->createStub(PaymentGatewayInterface::class);

        // willReturnMap: [arg1, arg2, ..., returnValue]
        $paymentStub->method('charge')->willReturnMap([
            [10.00, 'EUR', 'TXN-CHEAP'],
            [500.00, 'EUR', 'TXN-EXPENSIVE'],
        ]);

        $emailStub  = $this->createStub(EmailServiceInterface::class);
        $loggerStub = $this->createStub(LoggerInterface::class);

        $service = new OrderService($paymentStub, $emailStub, $loggerStub);

        $cheap = new Order('A', 'a@test.de', 10.00);
        $service->processOrder($cheap);
        $this->assertSame('TXN-CHEAP', $cheap->getTransactionId());

        $expensive = new Order('B', 'b@test.de', 500.00);
        $service->processOrder($expensive);
        $this->assertSame('TXN-EXPENSIVE', $expensive->getTransactionId());
    }
}
