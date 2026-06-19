<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter;

/**
 * Kleines, neutrales Beispiel-Subjekt fuer die Test-Double-Demos.
 *
 * Hat - wie der OrderService der Praxisaufgabe - drei Dependencies,
 * damit sich alle fuenf Double-Typen daran zeigen lassen:
 *   - SubscriberRepository: liefert Daten        -> Stub / Fake
 *   - Mailer:               externer Seiteneffekt -> Mock / Spy
 *   - AuditLog:             Protokoll             -> Spy
 */
final class NewsletterService
{
    public function __construct(
        private SubscriberRepositoryInterface $repository,
        private MailerInterface $mailer,
        private AuditLogInterface $log,
    ) {
    }

    /**
     * Meldet eine E-Mail-Adresse zum Newsletter an.
     *
     * @return bool true = neu angemeldet, false = war bereits Abonnent
     * @throws \InvalidArgumentException bei ungueltiger Adresse
     */
    public function subscribe(string $email): bool
    {
        $this->log->info("Subscribe attempt: {$email}");

        if (!str_contains($email, '@')) {
            $this->log->error("Invalid email: {$email}");
            throw new \InvalidArgumentException('Invalid email address');
        }

        if ($this->repository->exists($email)) {
            $this->log->info("Already subscribed: {$email}");
            return false;
        }

        $this->repository->save($email);
        $this->mailer->sendWelcome($email);
        $this->log->info("Subscribed successfully: {$email}");

        return true;
    }

    /**
     * Verschickt eine Kampagne an alle Abonnenten.
     *
     * @return int Anzahl der erreichten Empfaenger
     */
    public function sendCampaign(string $subject, string $body): int
    {
        $recipients = $this->repository->all();

        foreach ($recipients as $email) {
            $this->mailer->send($email, $subject, $body);
        }

        $this->log->info("Campaign '{$subject}' sent to " . count($recipients) . ' recipient(s)');

        return count($recipients);
    }
}
