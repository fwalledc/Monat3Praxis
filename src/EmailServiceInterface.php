<?php

namespace App;

interface EmailServiceInterface
{
    /**
     * Send order confirmation email
     * 
     * @param string $email Customer email
     * @param string $orderId Order ID
     * @param float $amount Total amount
     */
    public function sendOrderConfirmation(string $email, string $orderId, float $amount): void;
}
