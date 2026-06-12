<?php

namespace App;

class Order
{
    private string $id;
    private string $customerEmail;
    private float $amount;
    private string $status = 'pending';
    private ?string $transactionId = null;
    
    public function __construct(string $id, string $customerEmail, float $amount)
    {
        $this->id = $id;
        $this->customerEmail = $customerEmail;
        $this->amount = $amount;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
    
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
    
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }
}
