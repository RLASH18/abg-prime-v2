<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Inject Report Service
     *
     * @param ReportService $reportService
     */
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display report dashboard with overview
     */
    public function index()
    {
        // Get summary metrics for dashboard cards
        $overview = [
            'sales_summary' => $this->reportService->getSalesMetrics()['summary'],
            'order_summary' => $this->reportService->getOrderMetrics()['fulfillment'],
            'billing_summary' => $this->reportService->getBillingMetrics()['summary'],
            'delivery_summary' => $this->reportService->getDeliveryMetrics()['performance'],
            'inventory_summary' => [
                'total_items' => $this->reportService->getInventoryMetrics()['total_items'],
                'total_stock_value' => $this->reportService->getInventoryMetrics()['total_stock_value'],
                'low_stock_count' => $this->reportService->getInventoryMetrics()['low_stock_items']->count(),
            ],
        ];

        return Inertia::render('admin/Reports/Index', [
            'overview' => $overview,
        ]);
    }

    /**
     * Display sales report
     */
    public function sales()
    {
        $report = $this->reportService->getSalesMetrics();

        return Inertia::render('admin/Reports/Sales', [
            'report' => $report,
        ]);
    }

    /**
     * Display inventory report
     */
    public function inventory()
    {
        $report = $this->reportService->getInventoryMetrics();

        return Inertia::render('admin/Reports/Inventory', [
            'report' => $report,
        ]);
    }

    /**
     * Display order report
     */
    public function orders()
    {
        $report = $this->reportService->getOrderMetrics();

        return Inertia::render('admin/Reports/Orders', [
            'report' => $report,
        ]);
    }

    /**
     * Display billing report
     */
    public function billing()
    {
        $report = $this->reportService->getBillingMetrics();

        return Inertia::render('admin/Reports/Billing', [
            'report' => $report,
        ]);
    }

    /**
     * Display delivery report
     */
    public function delivery()
    {
        $report = $this->reportService->getDeliveryMetrics();

        return Inertia::render('admin/Reports/Delivery', [
            'report' => $report,
        ]);
    }
}
