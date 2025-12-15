<?php

namespace App\Services\Admin;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\BillingRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\DeliveryRepositoryInterface;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Inject repositories
     *
     * @param OrderRepositoryInterface $orderRepo
     * @param BillingRepositoryInterface $billingRepo
     * @param ItemRepositoryInterface $itemRepo
     * @param DeliveryRepositoryInterface $deliveryRepo
     * @param DamagedItemRepositoryInterface $damagedItemRepo
     * @param SupplierRepositoryInterface $supplierRepo
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected BillingRepositoryInterface $billingRepo,
        protected ItemRepositoryInterface $itemRepo,
        protected DeliveryRepositoryInterface $deliveryRepo,
        protected DamagedItemRepositoryInterface $damagedItemRepo,
        protected SupplierRepositoryInterface $supplierRepo
    ) {}

    /**
     * Get sales metrics
     *
     * @return array
     */
    public function getSalesMetrics(): array
    {
        $summary = $this->orderRepo->query()
            ->whereIn('status', ['paid', 'assembled', 'shipped', 'delivered'])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_order_value
            ')->first();

        // Sales by period (daily)
        $salesByPeriod = $this->orderRepo->query()
            ->whereIn('status', ['paid', 'assembled', 'shipped', 'delivered'])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as period")
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total_amount) as revenue')
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Top selling items
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->whereIn('orders.status', ['paid', 'assembled', 'shipped', 'delivered'])
            ->select('items.id', 'items.item_name', 'items.item_code')
            ->selectRaw('SUM(order_items.quantity) as total_quantity')
            ->selectRaw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            ->groupBy('items.id', 'items.item_name', 'items.item_code')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Sales by payment method
        $byPaymentMethod = $this->orderRepo->query()
            ->whereIn('status', ['paid', 'assembled', 'shipped', 'delivered'])
            ->select('payment_method')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total_amount) as revenue')
            ->groupBy('payment_method')
            ->get();

        return [
            'summary' => [
                'total_orders' => $summary->total_orders ?? 0,
                'total_revenue' => $summary->total_revenue ?? 0,
                'average_order_value' => $summary->average_order_value ?? 0,
            ],
            'by_period' => $salesByPeriod,
            'top_items' => $topItems,
            'by_payment_method' => $byPaymentMethod,
        ];
    }

    /**
     * Get inventory metrics
     *
     * @return array
     */
    public function getInventoryMetrics(): array
    {
        // Stock levels
        $stockLevels = $this->itemRepo->query()
            ->with('supplier')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'stock_value' => $item->quantity * $item->unit_price,
                    'supplier_name' => $item->supplier?->supplier_name,
                ];
            });

        // Low stock items (threshold: 10)
        $lowStockItems = $this->itemRepo->query()
            ->with('supplier')
            ->where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->get();

        // Damaged items summary
        $damagedSummary = $this->damagedItemRepo->query()
            ->join('items', 'damaged_items.item_id', '=', 'items.id')
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(damaged_items.quantity) as total_damaged_quantity,
                SUM(damaged_items.quantity * items.unit_price) as estimated_loss
            ')->first();

        // Inventory by supplier
        $bySupplier = DB::table('items')
            ->join('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.id', 'suppliers.supplier_name')
            ->selectRaw('COUNT(items.id) as item_count')
            ->selectRaw('SUM(items.quantity) as total_quantity')
            ->selectRaw('SUM(items.quantity * items.unit_price) as total_value')
            ->groupBy('suppliers.id', 'suppliers.supplier_name')
            ->orderByDesc('total_value')
            ->get();

        return [
            'stock_levels' => $stockLevels,
            'low_stock_items' => $lowStockItems,
            'damaged_summary' => [
                'total_records' => $damagedSummary->total_records ?? 0,
                'total_damaged_quantity' => $damagedSummary->total_damaged_quantity ?? 0,
                'estimated_loss' => $damagedSummary->estimated_loss ?? 0,
            ],
            'by_supplier' => $bySupplier,
            'total_stock_value' => $stockLevels->sum('stock_value'),
            'total_items' => $stockLevels->count(),
        ];
    }

    /**
     * Get order metrics
     *
     * @return array
     */
    public function getOrderMetrics(): array
    {
        // Orders by status
        $byStatus = $this->orderRepo->query()
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total_amount) as total_amount')
            ->groupBy('status')
            ->get();

        // Orders by delivery method
        $byDeliveryMethod = $this->orderRepo->query()
            ->select('delivery_method')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total_amount) as total_amount')
            ->groupBy('delivery_method')
            ->get();

        // Fulfillment metrics
        $fulfillment = $this->orderRepo->query()->selectRaw('
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_orders,
            SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_orders,
            SUM(CASE WHEN status = "assembled" THEN 1 ELSE 0 END) as assembled_orders,
            SUM(CASE WHEN status = "shipped" THEN 1 ELSE 0 END) as shipped_orders,
            SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_orders,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders
        ')->first();

        return [
            'by_status' => $byStatus,
            'by_delivery_method' => $byDeliveryMethod,
            'fulfillment' => $fulfillment->toArray(),
        ];
    }

    /**
     * Get billing metrics
     *
     * @return array
     */
    public function getBillingMetrics(): array
    {
        $summary = $this->billingRepo->query()->selectRaw('
            COUNT(*) as total_billings,
            SUM(amount) as total_amount,
            SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid_amount,
            SUM(CASE WHEN status = "unpaid" THEN amount ELSE 0 END) as unpaid_amount,
            SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN status = "unpaid" THEN 1 ELSE 0 END) as unpaid_count
        ')->first();

        // Outstanding payments
        $outstanding = $this->billingRepo->query()
            ->with(['order.user'])
            ->where('status', 'unpaid')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'summary' => [
                'total_billings' => $summary->total_billings ?? 0,
                'total_amount' => $summary->total_amount ?? 0,
                'paid_amount' => $summary->paid_amount ?? 0,
                'unpaid_amount' => $summary->unpaid_amount ?? 0,
                'paid_count' => $summary->paid_count ?? 0,
                'unpaid_count' => $summary->unpaid_count ?? 0,
            ],
            'outstanding_payments' => $outstanding,
        ];
    }

    /**
     * Get delivery metrics
     *
     * @return array
     */
    public function getDeliveryMetrics(): array
    {
        // Status overview
        $statusOverview = $this->deliveryRepo->query()
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Performance metrics
        $performance = $this->deliveryRepo->query()->selectRaw('
            COUNT(*) as total_deliveries,
            SUM(CASE WHEN status = "scheduled" THEN 1 ELSE 0 END) as pending_deliveries,
            SUM(CASE WHEN status = "in_transit" THEN 1 ELSE 0 END) as in_transit_deliveries,
            SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_deliveries,
            SUM(CASE WHEN proof_of_delivery IS NOT NULL THEN 1 ELSE 0 END) as with_proof
        ')->first();

        return [
            'status_overview' => $statusOverview,
            'performance' => $performance->toArray(),
        ];
    }
}
