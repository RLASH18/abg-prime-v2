<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Inject DashboardService
     *
     * @param DashboardService $dashboardService
     */
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the admin dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        $countOrders = $this->dashboardService->countOrders();
        $getTotalRevenue = $this->dashboardService->getTotalRevenue();
        $countItems = $this->dashboardService->countItems();
        $countCustomers = $this->dashboardService->countCustomers();

        return Inertia::render('admin/Dashboard', [
            'countOrders' => $countOrders,
            'getTotalRevenue' => $getTotalRevenue,
            'countItems' => $countItems,
            'countCustomers' => $countCustomers,
        ]);
    }
}
