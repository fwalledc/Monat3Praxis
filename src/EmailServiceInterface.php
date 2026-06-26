<?php

declare(strict_types=1);

namespace App;

interface EmailServiceInterface
{
    public function sendWelcome(string $email): void;

    public function sendOrderConfirmation(string $email, string $orderId, float $amount): void;
}
