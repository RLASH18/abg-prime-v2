<?php

namespace App\Repositories;

use App\Models\Billing;
use App\Repositories\Interfaces\BillingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BillingRepository extends BaseRepository implements BillingRepositoryInterface
{
    /**
     * Inject the Billing model
     *
     * @param Billing $billing
     */
    public function __construct(Billing $billing)
    {
        parent::__construct($billing);
    }

    /**
     * Find billing by order ID
     *
     * @param int $orderId
     * @return \App\Models\Billing|null
     */
    public function findByOrderId(int $orderId)
    {
        return $this->query()
            ->where('order_id', $orderId)
            ->first();
    }

    /**
     * Generate unique billing number
     *
     * @return string
     */
    public function generateBillingNumber(): string
    {
        $prefix = 'BL';
        $date = now()->format('Ymd');

        $lastBilling = $this->query()
            ->whereDate('created_at', today())
            ->latest('id')
            ->first();

        $sequence = $lastBilling ? (int) substr($lastBilling->billing_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
