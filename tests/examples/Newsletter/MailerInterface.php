<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter;

interface MailerInterface
{
    /** Willkommensmail an einen neuen Abonnenten. */
    public function sendWelcome(string $email): void;

    /** Eine Kampagnen-Mail an einen Empfaenger. */
    public function send(string $email, string $subject, string $body): void;
}
