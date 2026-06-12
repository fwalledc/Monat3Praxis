<?php

namespace App;

class OrderService
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway,
        private EmailServiceInterface $emailService,
        private LoggerInterface $logger
    ) {
    }
    
    /**
     * Process an order: charge payment and send confirmation
     * 
     * @param Order $order
     * @return void
     * @throws PaymentException When payment fails
     */
    public function processOrder(Order $order): void
    {
        $this->logger->info("Processing order: {$order->getId()}");
        
        // Validate amount
        if ($order->getAmount() <= 0) {
            $this->logger->error("Invalid amount: {$order->getAmount()}");
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        try {
            // Charge payment
            $transactionId = $this->paymentGateway->charge(
                $order->getAmount(),
                'EUR'
            );
            
            // Update order
            $order->setTransactionId($transactionId);
            $order->setStatus('paid');
            
            // Send confirmation email
            $this->emailService->sendOrderConfirmation(
                $order->getCustomerEmail(),
                $order->getId(),
                $order->getAmount()
            );
            
            $this->logger->info("Order processed successfully: {$order->getId()}");
            
        } catch (PaymentException $e) {
            $this->logger->error("Payment failed for order {$order->getId()}: {$e->getMessage()}");
            $order->setStatus('failed');
            throw $e;
        }
    }
    
    /**
     * Cancel an order and refund payment if already paid
     * 
     * @param Order $order
     * @return bool Success
     */
    public function cancelOrder(Order $order): bool
    {
        $this->logger->info("Cancelling order: {$order->getId()}");
        
        // If already paid, refund
        if ($order->getStatus() === 'paid' && $order->getTransactionId()) {
            $refunded = $this->paymentGateway->refund($order->getTransactionId());
            
            if ($refunded) {
                $order->setStatus('refunded');
                $this->logger->info("Order refunded: {$order->getId()}");
                return true;
            } else {
                $this->logger->error("Refund failed for order: {$order->getId()}");
                return false;
            }
        }
        
        // Not paid yet, just cancel
        $order->setStatus('cancelled');
        return true;
    }
}
