# Test Doubles - Die 5 Typen im Ueberblick

Diese Beispiele zeigen die fuenf klassischen Arten von Test Doubles
(nach Gerard Meszaros / Martin Fowler) - jeweils am `OrderService` und
seinen Dependencies demonstriert.

> **Begriff:** "Test Double" ist der Oberbegriff fuer jedes Ersatzobjekt,
> das im Test eine echte Dependency ersetzt - so wie ein Stunt-Double
> den Schauspieler ersetzt.

| Typ       | Frage, die er beantwortet                          | PHPUnit-Werkzeug                       | Beispiel-Datei              |
|-----------|----------------------------------------------------|----------------------------------------|-----------------------------|
| **Dummy** | "Ich muss nur den Parameter fuellen."              | `createStub()` (nie konfiguriert)      | `DummyExampleTest.php`      |
| **Stub**  | "Was gibt die Methode zurueck?"                    | `createStub()` + `willReturn()`        | `StubExampleTest.php`       |
| **Spy**   | "Wurde sie aufgerufen - und womit? (Pruefung danach)" | `createMock()` + Recording / Callback | `SpyExampleTest.php`        |
| **Mock**  | "Wurde sie korrekt aufgerufen? (Erwartung vorher)" | `createMock()` + `expects()`           | `MockExampleTest.php`       |
| **Fake**  | "Eine echte, aber leichtgewichtige Implementierung." | eigene Klasse (z. B. In-Memory)        | `FakeExampleTest.php`       |

## Die wichtigste Unterscheidung: State vs. Behavior

- **Stub / Fake / Dummy** -> *State Verification*
  Wir pruefen am Ende das Ergebnis/den Zustand (`assertSame(...)`).
  Das Double ist nur Mittel zum Zweck.

- **Mock / Spy** -> *Behavior Verification*
  Wir pruefen, **wie** mit der Dependency interagiert wurde
  (welche Methode, wie oft, mit welchen Argumenten).

### Mock vs. Spy - der feine Unterschied

Beide pruefen das Verhalten, aber zu unterschiedlichen Zeitpunkten:

- **Mock:** Erwartung wird **vor** dem Akt definiert (`expects($this->once())`).
  Schlaegt der Aufruf nicht wie erwartet ein, scheitert der Test automatisch.
  -> "Erst sagen, was passieren soll, dann handeln."

- **Spy:** Das Double **zeichnet auf**, was passiert; geprueft wird erst
  **nach** dem Akt mit eigenen Assertions.
  -> "Erst handeln lassen, dann nachschauen, was passiert ist."

## Ausfuehren

```bash
# Nur die Beispiele
vendor/bin/phpunit --testsuite Examples --testdox

# Alles (Praxisaufgabe + Beispiele)
vendor/bin/phpunit --testdox
```
