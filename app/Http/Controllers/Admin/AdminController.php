<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Inject AdminService
     *
     * @param AdminService $adminService
     */
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * Display the admin dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboard()
    {
        $countOrders = $this->adminService->countOrders();
        $getTotalRevenue = $this->adminService->getTotalRevenue();
        $countItems = $this->adminService->countItems();
        $countCustomers = $this->adminService->countCustomers();

        return Inertia::render('admin/Dashboard', [
            'countOrders' => $countOrders,
            'getTotalRevenue' => $getTotalRevenue,
            'countItems' => $countItems,
            'countCustomers' => $countCustomers,
        ]);
    }
}
