<?php

namespace App\Traits;

trait SyncOrderDeliveryStatus
{
    /**
     * Get delivery status mapping from order status
     *
     * @return array<string, string>
     */
    protected function getOrderToDeliveryMapping(): array
    {
        return [
            'assembled' => 'scheduled',
            'shipped' => 'in_transit',
            'delivered' => 'delivered',
        ];
    }

    /**
     * Get order status mapping from delivery status
     *
     * @return array<string, string>
     */
    protected function getDeliveryToOrderMapping(): array
    {
        return [
            'scheduled' => 'assembled',
            'rescheduled' => 'assembled',
            'in_transit' => 'shipped',
            'delivered' => 'delivered',
            'failed' => 'assembled',
        ];
    }

    /**
     * Check if status should trigger synchronization
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    protected function shouldSync(string $currentStatus, string $newStatus): bool
    {
        return $currentStatus !== $newStatus;
    }
}
