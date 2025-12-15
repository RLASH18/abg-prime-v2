<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Interfaces\BillingRepositoryInterface;
use App\Repositories\Interfaces\DeliveryRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class AdminService
{
    /**
     * Inject repositories
     *
     * @param OrderRepositoryInterface $orderRepo
     * @param BillingRepositoryInterface $billingRepo
     * @param ItemRepositoryInterface $itemRepo
     * @param DeliveryRepositoryInterface $deliveryRepo
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected BillingRepositoryInterface $billingRepo,
        protected ItemRepositoryInterface $itemRepo,
        protected DeliveryRepositoryInterface $deliveryRepo,
    ) {}

    /**
     * Get total count of orders
     *
     * @return int
     */
    public function countOrders(): int
    {
        return $this->orderRepo->query()->count();
    }

    /**
     * Get total revenue of billings
     *
     * @return float
     */
    public function getTotalRevenue(): float
    {
        return $this->billingRepo->query()->sum('amount');
    }

    /**
     * Get total count of items
     *
     * @return int
     */
    public function countItems(): int
    {
        return $this->itemRepo->query()->count();
    }

    /**
     * Get total count of customers
     *
     * @return int
     */
    public function countCustomers(): int
    {
        return User::where('role', UserRole::Customer)->count();
    }
}
