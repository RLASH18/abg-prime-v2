<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    /**
     * Inject the OrderItem model.
     *
     * @param OrderItem $orderItem
     */
    public function __construct(OrderItem $orderItem)
    {
        parent::__construct($orderItem);
    }

    /**
     * Get sales data for a specific item within a given number of past days.
     * Only considers orders with 'completed' or 'pending' status.
     *
     * @param int $itemId
     * @param int $days
     * @return \Illuminate\Support\Collection
     */
    public function getSalesHistory(int $itemId, int $days): Collection
    {
        return $this->query()
            ->where('item_id', $itemId)
            ->whereHas('order', function ($query) use ($days) {
                $query->whereIn('status', ['completed', 'pending'])
                    ->where('created_at', '>=', now()->subDays($days));
            })
            ->get();
    }

    /**
     * Get the most recent order item record for a specific item.
     *
     * @param int $itemId
     * @return OrderItem|null
     */
    public function getLatestSale(int $itemId): ?OrderItem
    {
        return $this->query()
            ->where('item_id', $itemId)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['completed', 'pending']))
            ->latest('created_at')
            ->first();
    }
}
