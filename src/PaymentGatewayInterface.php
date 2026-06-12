<?php

namespace App;

interface PaymentGatewayInterface
{
    /**
     * Charge a payment
     * 
     * @param float $amount Amount in EUR
     * @param string $currency Currency code (EUR, USD, etc.)
     * @return string Transaction ID
     * @throws PaymentException When payment fails
     */
    public function charge(float $amount, string $currency = 'EUR'): string;
    
    /**
     * Refund a payment
     * 
     * @param string $transactionId Original transaction ID
     * @return bool Success
     */
    public function refund(string $transactionId): bool;
}
