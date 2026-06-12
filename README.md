# Monat 3 - Test Doubles & Mocking - Praxisaufgabe

> PHP 8.2+ / PHPUnit 11 - moderne Attribute-Syntax

## Ueberblick

Du testest einen `OrderService` mit 3 externen Dependencies:
- PaymentGateway - Externe Zahlungs-API
- EmailService - Email-Versand
- Logger - Logging

Das Problem: Wir wollen nicht die echte Payment-API aufrufen oder echte
Emails senden. Die Loesung: Wir mocken die Dependencies mit PHPUnit!

## Struktur

```
monat3_praxis/
├── src/                       Klassen (fertig, nicht aendern)
│   ├── OrderService.php       <- ZU TESTEN
│   ├── Order.php
│   ├── PaymentGatewayInterface.php
│   ├── EmailServiceInterface.php
│   ├── LoggerInterface.php
│   └── PaymentException.php
├── tests/
│   └── OrderServiceTest.php    <- HIER schreibst du die Tests (Starter)
├── loesung/
│   └── OrderServiceTest.php    <- Musterloesung (erst danach anschauen!)
├── AUFGABE.md                  Aufgabenstellung mit Hinweisen
├── composer.json
└── phpunit.xml
```

## Quick Start

```bash
composer install
vendor/bin/phpunit
```

Der Starter in `tests/` nutzt `markTestIncomplete()` - die Tests sind also
"gelb", bis du sie ausfuellst. Ziel: alle gruen!

## Was ist neu gegenueber Monat 2

- PHP-8-Attribute (`#[Test]`, `#[TestDox]`, `#[CoversClass]`) statt Annotations
- Intersection Types fuer Mock-Properties (`Interface&MockObject`)
- `withConsecutive()` gibt es nicht mehr -> Matcher-Callback (siehe AUFGABE.md)

## Lernziele

- Dependencies mit createMock() isolieren
- expects() / with() / willReturn() / willThrowException()
- Aufruf-Haeufigkeit: once(), never(), exactly()
- Mock vs. Stub vs. Dummy unterscheiden

Viel Erfolg! Happy Mocking.
