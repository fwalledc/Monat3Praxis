# Monat 3 - Praxisaufgabe: OrderService mit Mocking testen

> **PHP 8.2+ / PHPUnit 11** - moderne Attribute statt Annotations

## Ziel

Lerne Test Doubles praktisch anzuwenden, indem du den `OrderService` mit
gemockten Dependencies testest. Wir nutzen die moderne PHPUnit-11-Syntax mit
PHP-8-Attributen.

---

## Neu: Attribute statt Annotations

PHPUnit 11 verwendet PHP-8-Attribute. Die alten Docblock-Annotations sind
deprecated.

```php
// ALT (PHPUnit 9, deprecated):
/**
 * @test
 * @dataProvider amountProvider
 * @covers \App\OrderService
 */
public function it_charges_payment() { }

// NEU (PHPUnit 11):
#[Test]
#[DataProvider('amountProvider')]
#[CoversClass(OrderService::class)]
public function chargesPayment(): void { }
```

### Die wichtigsten Attribute

| Attribut | Zweck | Import |
|----------|-------|--------|
| `#[Test]` | Markiert Test-Methode (statt test-Prefix) | PHPUnit\Framework\Attributes\Test |
| `#[TestDox('...')]` | Lesbare Test-Beschreibung | PHPUnit\Framework\Attributes\TestDox |
| `#[DataProvider('m')]` | Data Provider verknuepfen | PHPUnit\Framework\Attributes\DataProvider |
| `#[CoversClass(X::class)]` | Coverage-Ziel | PHPUnit\Framework\Attributes\CoversClass |
| `#[Group('slow')]` | Tests gruppieren | PHPUnit\Framework\Attributes\Group |

---

## Wichtig: withConsecutive() ist weg!

Seit PHPUnit 10 gibt es `withConsecutive()` nicht mehr. Fuer mehrere
aufeinanderfolgende Aufrufe nutzt du einen Matcher mit Callback:

```php
$matcher = $this->exactly(2);

$this->logger
    ->expects($matcher)
    ->method('info')
    ->willReturnCallback(function (string $message) use ($matcher): void {
        match ($matcher->numberOfInvocations()) {
            1 => $this->assertStringContainsString('Processing order', $message),
            2 => $this->assertStringContainsString('successfully', $message),
        };
    });
```

---

## Aufgabe

Der `OrderService` hat 3 Dependencies:
1. PaymentGatewayInterface - Externe Zahlungs-API
2. EmailServiceInterface - Email-Versand
3. LoggerInterface - Logging

Schreibe Tests fuer `OrderService`, ohne echte Implementierungen zu verwenden!

### Typisierte Mock-Properties (PHP 8)

Nutze Intersection Types fuer saubere IDE-Unterstuetzung:

```php
private PaymentGatewayInterface&MockObject $paymentGateway;

protected function setUp(): void
{
    $this->paymentGateway = $this->createMock(PaymentGatewayInterface::class);
    // ...
}
```

---

## Teil 1: Grundlagen (Pflicht)

### Test 1: Happy Path - processOrder()

Szenario: Order wird erfolgreich verarbeitet

Was testen:
- PaymentGateway.charge() wird genau 1x mit (99.99, 'EUR') aufgerufen
- EmailService.sendOrderConfirmation() wird genau 1x aufgerufen
- Order Status wird auf 'paid' gesetzt
- Transaction ID wird gespeichert

```php
#[Test]
#[TestDox('Order wird erfolgreich bezahlt')]
public function chargesPaymentSuccessfully(): void
{
    $order = new Order('ORDER-123', 'test@example.com', 99.99);

    $this->paymentGateway
        ->expects($this->once())
        ->method('charge')
        ->with(99.99, 'EUR')
        ->willReturn('TXN-456');

    // ...
}
```

### Test 2: Exception Handling - Payment schlaegt fehl

- PaymentException wird weitergereicht
- Order Status wird 'failed'
- EmailService wird nie aufgerufen ($this->never())
- Logger.error() wird aufgerufen

### Test 3: Input Validation - Ungueltiger Betrag

- InvalidArgumentException bei Betrag <= 0
- PaymentGateway wird nie aufgerufen

---

## Teil 2: Fortgeschritten (Bonus)

### Test 4: cancelOrder() mit Refund
Bezahlte Order -> refund() mit Transaction ID -> Status 'refunded'

### Test 5: cancelOrder() ohne Payment
Pending Order -> kein refund() -> Status 'cancelled'

### Test 6: Logging-Reihenfolge pruefen
info() genau 2x: erst "Processing order", dann "successfully"
(mit Matcher-Callback, siehe oben!)

---

## Checkliste

- [ ] declare(strict_types=1); am Dateianfang
- [ ] Klasse ist final
- [ ] Mock-Properties mit Intersection Type &MockObject
- [ ] #[Test] statt test-Prefix
- [ ] #[CoversClass(OrderService::class)] auf Klassenebene
- [ ] Test 1-3 (Pflicht) gruen
- [ ] Test 4-6 (Bonus) gruen
- [ ] Kein withConsecutive() verwendet

---

## Lernziele

Nach dieser Aufgabe kannst du:
- Dependencies mit createMock() mocken
- Moderne PHP-8-Attribute statt Annotations nutzen
- Intersection Types fuer Mock-Properties einsetzen
- Erwartungen mit expects(), with(), willReturn() definieren
- Exceptions mit willThrowException() simulieren
- Aufruf-Haeufigkeit mit once(), never(), exactly() pruefen
- Mehrere Aufrufe ohne withConsecutive() testen

---

## Setup

```bash
composer install
vendor/bin/phpunit
```

Viel Erfolg!
