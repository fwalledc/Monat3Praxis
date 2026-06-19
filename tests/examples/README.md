# Test Doubles - Die 5 Typen im Ueberblick

Diese Beispiele zeigen die fuenf klassischen Arten von Test Doubles
(nach Gerard Meszaros / Martin Fowler).

> **Hinweis:** Demonstriert wird bewusst an einem neutralen
> `NewsletterService` (mit Repository, Mailer und AuditLog) - **nicht** am
> `OrderService` der Praxisaufgabe. So siehst du jede Technik im Einsatz,
> ohne dass die Loesung des Praxisteils vorweggenommen wird.

> **Begriff:** "Test Double" ist der Oberbegriff fuer jedes Ersatzobjekt,
> das im Test eine echte Dependency ersetzt - so wie ein Stunt-Double
> den Schauspieler ersetzt.

## Merksatz

> **Dummy fuellt, Stub antwortet, Fake funktioniert, Mock erwartet, Spy beobachtet.**

## Wann verwende ich welches Double?

Die eine Leitfrage zuerst: **Pruefe ich ein Ergebnis (Zustand) oder eine
Interaktion (Verhalten)?**

- Zustand ("was kommt am Ende raus?") -> Dummy, Stub, Fake
- Verhalten ("wurde die Dependency richtig benutzt?") -> Mock, Spy

```
Brauche ich von der Dependency ueberhaupt eine Antwort/Reaktion?
|
+-- NEIN, sie wird im Testpfad gar nicht aufgerufen
|       -> DUMMY   (nur Konstruktor fuellen)
|
+-- JA
    |
    +-- Ich pruefe das ERGEBNIS, die Dependency ist nur Mittel zum Zweck
    |   |
    |   +-- Eine feste Antwort reicht         -> STUB   (willReturn)
    |   +-- Ich brauche echte Logik ueber
    |       mehrere Aufrufe hinweg            -> FAKE   (In-Memory-Klasse)
    |
    +-- Ich pruefe die INTERAKTION selbst (welche Methode, wie oft, womit)
        |
        +-- Erwartung VORHER festlegen,
        |   PHPUnit prueft automatisch        -> MOCK   (expects)
        +-- Erst laufen lassen, DANACH
            die Aufrufe auswerten             -> SPY    (Callback/eigene Klasse)
```

### Faustregeln fuer die Praxis

1. **Im Zweifel Stub.** Die meisten Tests pruefen ein Ergebnis - ein Stub
   reicht da. Erst zum Mock greifen, wenn der *Aufruf selbst* die
   Geschaeftsregel ist (z. B. "bei Fehler darf keine Mail raus").
2. **Mock sparsam.** Zu viele `expects(...)` koppeln den Test eng an die
   Implementierung -> bricht bei jedem Refactoring. Pro Test idealerweise
   *eine* Verhaltens-Erwartung, der Rest sind Stubs.
3. **Mock vs. Spy:** Mock = "erst sagen, was passieren soll". Spy = "erst
   handeln lassen, dann nachschauen". Spy ist flexibler bei komplexen
   Auswertungen, Mock kompakter bei einfachen "genau 1x"-Checks.
4. **Fake nur bei echtem Bedarf.** Lohnt sich, wenn dieselbe Dependency in
   vielen Tests realistisch reagieren muss (klassisch: In-Memory-Repository
   statt DB). Fuer einen Einzeltest ist ein Stub billiger.

### Konkret am NewsletterService

| Situation im `NewsletterService`                                        | Double    |
|-------------------------------------------------------------------------|-----------|
| `subscribe()` mit ungueltiger Adresse -> Repository & Mailer nie genutzt | **Dummy** |
| `exists()` soll true/false liefern, damit der Ablauf weiterlaeuft        | **Stub**  |
| "`sendWelcome()` genau 1x" / "bei bekanntem Abo nie" / "`send()` 2x"     | **Mock**  |
| `save()` und `exists()` spielen realistisch zusammen (Abo gemerkt)       | **Fake**  |
| Reihenfolge/Inhalte der Log-Meldungen *im Nachhinein* pruefen            | **Spy**   |

Die gleichen Techniken wendest du in der Praxisaufgabe dann auf den
`OrderService` an.

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
