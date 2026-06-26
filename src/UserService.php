<?php

declare(strict_types=1);

namespace App;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private EmailServiceInterface $emailService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Registriert einen neuen Benutzer.
     *
     * @throws UserAlreadyExistsException Wenn die E-Mail bereits existiert
     */
    public function register(string $email): User
    {
        $this->logger->info("Registering user: {$email}");

        // Edge Case: User existiert bereits
        if ($this->repository->findByEmail($email) !== null) {
            $this->logger->error("User already exists: {$email}");
            throw new UserAlreadyExistsException("User already exists: {$email}");
        }

        $user = new User($email);
        $this->repository->save($user);

        $this->emailService->sendWelcome($email);
        $this->logger->info("User registered: {$email}");

        return $user;
    }

    /**
     * Liefert das Profil eines Benutzers.
     *
     * @return array{name: ?string, email: string}
     *
     * @throws UserNotFoundException Wenn kein Benutzer gefunden wird
     */
    public function getUserProfile(int $userId): array
    {
        $user = $this->repository->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException("User not found: {$userId}");
        }

        return [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }
}
