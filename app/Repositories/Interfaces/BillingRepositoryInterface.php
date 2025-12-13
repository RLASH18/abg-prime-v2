<?php

namespace App\Repositories\Interfaces;

interface BillingRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderId(int $orderId);
    public function generateBillingNumber(): string;
}
