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
use Tests\Examples\Fakes\SpyLogger;

/**
 * SPY
 * ===
 * Ein Spy zeichnet auf, wie er aufgerufen wurde. Geprueft wird ERST DANACH
 * mit eigenen Assertions - im Gegensatz zum Mock, der die Erwartung vorher
 * festlegt und automatisch prueft.
 *
 * -> Behavior Verification "im Nachhinein": erst handeln lassen, dann
 *    nachschauen, was protokolliert wurde.
 *
 * Zwei Wege werden gezeigt:
 *   1) Selbstgebaute Spy-Klasse (SpyLogger) - das reine Prinzip.
 *   2) PHPUnit-Mock + willReturnCallback - die Framework-Variante.
 */
final class SpyExampleTest extends TestCase
{
    #[Test]
    #[TestDox('Selbstgebauter SpyLogger zeichnet alle Log-Meldungen auf')]
    public function handgebauterSpyZeichnetAufrufeAuf(): void
    {
        $spy = new SpyLogger();

        $payment = $this->createStub(PaymentGatewayInterface::class);
        $payment->method('charge')->willReturn('TXN-SPY');
        $email = $this->createStub(EmailServiceInterface::class);

        $service = new OrderService($payment, $email, $spy);
        $order   = new Order('ORDER-SPY', 'kunde@test.de', 42.00);

        // Akt
        $service->processOrder($order);

        // ERST JETZT pruefen wir das aufgezeichnete Verhalten.
        $this->assertCount(2, $spy->infoMessages);
        $this->assertSame([], $spy->errorMessages);
        $this->assertStringContainsString('Processing order: ORDER-SPY', $spy->infoMessages[0]);
        $this->assertStringContainsString('successfully: ORDER-SPY', $spy->infoMessages[1]);
    }

    #[Test]
    #[TestDox('PHPUnit-Mock als Spy: Aufrufe per Callback sammeln und danach pruefen')]
    public function spyMitPhpunitCallback(): void
    {
        $payment = $this->createStub(PaymentGatewayInterface::class);
        $payment->method('charge')->willReturn('TXN-SPY2');
        $email = $this->createStub(EmailServiceInterface::class);

        $logger = $this->createMock(LoggerInterface::class);

        // Statt expects()->with(...) sammeln wir die Argumente selbst ein.
        $captured = [];
        $logger->method('info')
            ->willReturnCallback(function (string $message) use (&$captured): void {
                $captured[] = $message;
            });

        $service = new OrderService($payment, $email, $logger);
        $order   = new Order('ORDER-SPY2', 'kunde@test.de', 10.00);

        // Akt
        $service->processOrder($order);

        // Pruefung im Nachhinein - wir entscheiden frei, was wir asserten.
        $this->assertCount(2, $captured);
        $this->assertStringContainsString('Processing order', $captured[0]);
        $this->assertStringContainsString('successfully', $captured[1]);
    }
}
