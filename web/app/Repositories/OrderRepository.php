<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * Inject the Order model
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }
}
