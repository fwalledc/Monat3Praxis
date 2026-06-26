<?php

declare(strict_types=1);

namespace Tests;

use App\EmailServiceInterface;
use App\InMemoryUserRepository;
use App\LoggerInterface;
use App\User;
use App\UserAlreadyExistsException;
use App\UserNotFoundException;
use App\UserRepositoryInterface;
use App\UserService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Live-Coding-Startpunkt fuer Monat 3.
 *
 * Wir fuellen die Tests gemeinsam Schritt fuer Schritt aus. Die Reihenfolge
 * folgt der Folie "Live-Coding: UserService". Die fertige Fassung liegt zum
 * Spicken in loesung/UserServiceTest.php.
 *
 * Tipp: Mit jedem ausgefuellten Test das markTestIncomplete() loeschen und
 * "vendor/bin/phpunit" laufen lassen.
 */
#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private EmailServiceInterface&MockObject $emailService;
    private LoggerInterface&MockObject $logger;
    private UserService $userService;

    // ------------------------------------------------------------------
    // Schritt 1-3: Dependencies mocken/stubben und Service zusammenbauen
    // ------------------------------------------------------------------
    protected function setUp(): void
    {
        // Diese Methode ausfuellen (Schritt 1-3), dann laufen die Tests an.
        // TODO Schritt 1: UserRepository mocken (Datenbank isolieren)
        //$this->repository = $this->createMock(UserRepositoryInterface::class);

        // TODO Schritt 2: EmailService mocken (keine echten Mails)
        //$this->emailService = $this->createMock(EmailServiceInterface::class);

        // TODO Schritt 3: Logger mocken (Logging ignorieren)
        //$this->logger = $this->createMock(LoggerInterface::class);

        // TODO: Service mit den Mocks zusammenstecken
//         $this->userService = new UserService(
//             $this->repository, $this->emailService, $this->logger
//         );
    }

    // ------------------------------------------------------------------
    // Schritt 4: Happy Path - Erwartungen pruefen (expects, with, once)
    // ------------------------------------------------------------------
    #[Test]
    #[TestDox('Neuer Benutzer wird gespeichert und bekommt eine Willkommensmail')]
    public function registersNewUser(): void
    {
        // Hinweis:
        // - findByEmail() soll null liefern  -> ->method('findByEmail')->willReturn(null)
        // - save() wird genau 1x erwartet    -> ->expects($this->once())->method('save')
        // - sendWelcome() wird 1x mit der Adresse erwartet
        //   ->expects($this->once())->method('sendWelcome')->with('neu@example.com')
        // - Act:  $user = $this->userService->register('neu@example.com');
        // - Assert: $this->assertSame('neu@example.com', $user->getEmail());

        // Was wollen wir testen? Registrierung beinhaltet repository und mail service

        //wir möchten erstmal, dass kein user gefunden wird, es also eine neue registrierung ist @stub

//        $this->repository
//            ->method('findByEmail')
//            ->with('neu@example.com')
//            ->willReturn(null);

        //die registrierung ruft save auf @mock
//        $this->repository
//            ->expects($this->once())
//            ->method('save')
//            ->with($this->isInstanceOf(User::class));

        //save wird genau 1x aufgerufen @mock
//        $this->emailService
//            ->expects($this->once())
//            ->method('sendWelcome')
//            ->with('neu@example.com');
        // Act

        //wir erwarten, dass in der methode register das repository::save
        // genau 1x aufgerufen wird und eine email genau 1x rausgeht (anhand
        $user = $this->userService->register('neu@example.com');
        $this->assertSame('neu@example.com', $user->getEmail());


        //wir testen also das verhalten der methode, nicht die daten


    }

    // ------------------------------------------------------------------
    // Schritt 5: Edge Case - User existiert bereits
    // ------------------------------------------------------------------
    #[Test]
    #[TestDox('Bestehende E-Mail wirft Exception und sendet keine Mail')]
    public function throwsWhenUserAlreadyExists(): void
    {
        // Stub: Repository liefert einen bestehenden User


        // save() und sendWelcome() duerfen NICHT aufgerufen werden


        // Assert + Act

        $this->userService->register('schon@da.de');

        // Hinweis:
        // - findByEmail() liefert einen bestehenden User
        //   ->method('findByEmail')->willReturn(new User('schon@da.de'))
        // - save() und sendWelcome() duerfen NICHT aufgerufen werden
        //   ->expects($this->never())->method(...)
        // - $this->expectException(UserAlreadyExistsException::class);
        // - Act: $this->userService->register('schon@da.de');
    }

    // ------------------------------------------------------------------
    // Bonus: Stub-Beispiel (nur Rueckgabewert, kein expects)
    // ------------------------------------------------------------------
    #[Test]
    #[TestDox('Profil wird aus dem Repository geladen')]
    public function returnsUserProfile(): void
    {
        $this->markTestIncomplete('Stub-Beispiel noch schreiben');

        // Hinweis:
        // - findById(123) als Stub:
        //   ->method('findById')->with(123)->willReturn(new User('max@test.de', 'Max'))
        // - $profile = $this->userService->getUserProfile(123);
        // - $this->assertSame('Max', $profile['name']);
        // Merke: hier KEIN expects(), uns interessiert nur die Antwort -> Stub, kein Mock.
    }

    // ------------------------------------------------------------------
    // Bonus: Fake statt Mock (echte Klasse, kein createMock)
    // ------------------------------------------------------------------
    #[Test]
    #[TestDox('Mit dem InMemory-Fake funktioniert Speichern und Wiederfinden')]
    public function worksWithInMemoryFake(): void
    {
        $this->markTestIncomplete('Fake-Beispiel noch schreiben');

        // Hinweis:
        // - $fake = new InMemoryUserRepository();   // echte Klasse, kein Mock
        // - Service mit $fake bauen (EmailService/Logger duerfen Mocks bleiben)
        // - register('fake@example.com') aufrufen
        // - danach $fake->findByEmail('fake@example.com') ist nicht null
    }
}
